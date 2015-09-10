<?php
add_filter( 'template_include', 'sl_template_include', 20 );    // 20 = overwrite template set by plugins (WooCommerce)

/**
 * Get template file for inclusion, based on query var and conditions
 *
 * @param string $template
 *
 * @return string
 */
function sl_template_include( $template )
{
	global $wp_query, $post, $resources, $resource, $resource_index;

	$post_type = get_post_type();

	// Booking page
	if ( get_query_var( 'book' ) )
	{
		/**
		 * Get the listing post and set it up as a single page
		 * Also use it to set up global variables
		 */
		$posts = get_posts( array(
			'name'        => get_query_var( 'book_slug' ),
			'post_type'   => sl_setting( 'listing_types' ),
			'numberposts' => 1,
		) );
		if ( empty( $posts ) )
		{
			header( 'Location: ' . HOME_URL );
			die;
		}

		$post      = $posts[0];
		$post_type = get_post_type( $post );
		$resources = get_post_meta( $post->ID, sl_meta_key( 'booking', $post_type ), true );

		if ( empty( $resources ) )
		{
			header( 'Location: ' . HOME_URL );
			die;
		}

		$resource_slug = get_query_var( 'resource' );
		$found         = false;
		foreach ( $resources as $resource_index => $resource )
		{
			if ( $resource_slug == sanitize_title( $resource['title'] ) )
			{
				$resource['resource_id'] = $resource_index;
				$found                   = true;
				break;
			}
		}
		if ( ! $found )
		{
			header( 'Location: ' . HOME_URL );
			die;
		}

		setup_postdata( $post );

		/**
		 * Fake WP_Query to make WordPress understand this is singular post
		 * Post object is the like single listing and is set up below
		 */
		$wp_query->is_home       = false;
		$wp_query->is_front_page = false;
		$wp_query->is_singular   = true;
		$wp_query->post          = $post;

		return locate_template( 'templates/booking/index.php' );
	}

	// Use 7listings homepage
	if ( is_front_page() && sl_setting( 'homepage_enable' ) )
	{
		return locate_template( 'templates/home/home.php' );
	}

	// Single template
	if ( is_single() )
	{
		if ( in_array( $post_type, sl_setting( 'listing_types' ) ) )
		{
			return locate_template( "templates/$post_type/single.php" );
		}

		if ( 'booking' == $post_type )
		{
			// Allow requests from other domain
			// In case using domain mapping in MultiSite
			header( 'Access-Control-Allow-Origin: *' );
			$type = get_post_meta( get_the_ID(), 'type', true );

			return locate_template( "templates/$type/booking-report.php" );
		}
	}

	// Post type archive
	if ( $post_type = sl_is_listing_post_type_archive() )
	{
		return locate_template( "templates/$post_type/archive.php" );
	}

	// Taxonomy archive
	if ( $post_type = sl_is_listing_tax_archive() )
	{
		return locate_template( array(
			"templates/$post_type/taxonomy.php",
			"templates/$post_type/archive.php",
		) );
	}

	return $template;
}

/**
 * Get and include template file with ability to pass variables to that file
 * The idea is taken from WooCommerce
 *
 * @see wc_get_template()
 * @see locate_template()
 *
 * @param string|array $names  Template file names. Can be array.
 *                             If array then search for first template file exists, similar to get_template_part()
 * @param array        $params Arguments which are passed to the template
 *
 * @return void
 */
function sl_get_template( $names, $params = array() )
{
	// Append '.php' to template names
	$names = (array) $names;
	foreach ( $names as $k => $name )
	{
		$names[$k] = "$name.php";
	}

	$located = locate_template( $names );

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'sl_get_template', $located, $names, $params );

	if ( ! file_exists( $located ) )
	{
		_doing_it_wrong( __FUNCTION__, sprintf( __( '<code>%s</code> does not exist.', '7listings' ), reset( $names ) ), '5.6.6' );
		return;
	}

	/**
	 * Include template file
	 * Note 1: that all arguments are extracted here, so we can use them directly in template file
	 * Note 2: we still can use $params! This way we can inherit params from the parent template file
	 */
	if ( $params && is_array( $params ) )
	{
		extract( $params );
	}
	include( $located );
}

add_filter( 'pre_get_posts', 'sl_pre_get_posts' );

/**
 * Make sure the listing archive works correctly
 * This sets up the number of listings in archive pages regardless WordPress settings
 * And also fixes the pagination bug
 *
 * @param WP_Query $query
 *
 * @return void
 */
function sl_pre_get_posts( $query )
{
	if ( ! $query->is_main_query() )
		return;

	$post_type = sl_is_listing_archive();
	if ( ! $post_type || ! sl_setting( $post_type . '_archive_num' ) )
		return;
	$query->set( 'posts_per_page', sl_setting( $post_type . '_archive_num' ) );
	$query->is_404     = false;
	$query->is_archive = true;
}

/**
 * Check if we're on listing archive page
 *
 * @return string|bool false if not listing archive, post type otherwise
 */
function sl_is_listing_archive()
{
	if ( $post_type = sl_is_listing_post_type_archive() )
		return $post_type;

	return sl_is_listing_tax_archive();
}

/**
 * Check if we're on listing post type archive page
 *
 * @return string|bool false if not listing archive, post type otherwise
 */
function sl_is_listing_post_type_archive()
{
	if ( is_singular() )
		return false;

	// Custom post types
	$post_types = sl_setting( 'listing_types' );
	foreach ( $post_types as $post_type )
	{
		if ( is_post_type_archive( $post_type ) )
			return $post_type;
	}

	// Special case for custom homepage
	if ( is_front_page() && sl_setting( 'homepage_enable' ) )
		return false;

	// Post: this is not actually "post" main archive page, but include all kind of archive page
	if ( ( is_home() || is_archive() || is_search() ) && 'post' == get_post_type() )
		return 'post';

	return false;
}

/**
 * Check if we're on listing tax archive page
 *
 * @return string|bool false if not listing archive, post type otherwise
 */
function sl_is_listing_tax_archive()
{
	$post_types   = sl_setting( 'listing_types' );
	$post_types[] = 'post';
	foreach ( $post_types as $post_type )
	{
		if ( is_tax( get_object_taxonomies( $post_type ) ) )
			return $post_type;
	}

	return false;
}

add_filter( 'comments_template', 'sl_comments_template', 20 ); // 100 = overwrite template set by plugins (WooCommerce)

/**
 * Get the reviews template (comments)
 *
 * @param string $template
 *
 * @return string
 */
function sl_comments_template( $template )
{
	if ( ! in_array( get_post_type(), sl_setting( 'listing_types' ) ) )
		return $template;

	return locate_template( array(
		'templates/' . get_post_type() . '/reviews.php',
		'templates/reviews.php',
	) );
}
