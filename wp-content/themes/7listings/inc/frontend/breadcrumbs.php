<?php

/**
 * Display breadcrumbs for posts, pages, archive page with the microdata that search engines understand
 *
 * @see https://developers.google.com/structured-data/breadcrumbs
 *
 * @return void
 */
function sl_breadcrumbs()
{
	if ( ! sl_setting( 'design_breadcrumbs_enable' ) )
		return;

	if ( is_front_page() )
		return;

	$post_type = get_post_type();

	/**
	 * Store all items of the breadcrumbs
	 * Each item is an array [url, title]
	 * The code below will add item to this array
	 */
	$items = array();

	// Home
	$items[] = array( HOME_URL, __( 'Home', '7listings' ) );

	// Static blog page: Home > Blog
	if ( is_home() )
	{
		if ( $blog_page = get_option( 'page_for_posts' ) )
		{
			$items[] = array( get_permalink( $blog_page ), get_the_title( $blog_page ) );
		}
	}

	// Single
	elseif ( is_single() )
	{
		// For post: Home > Blog > Category > Post title
		if ( 'post' == $post_type )
		{
			if ( 'page' == get_option( 'show_on_front' ) && ( $blog_page = get_option( 'page_for_posts' ) ) )
			{
				$items[] = array( get_permalink( $blog_page ), get_the_title( $blog_page ) );
			}

			$terms = get_the_terms( get_the_ID(), 'category' );
			if ( $terms )
			{
				$term    = current( $terms );
				$parents = sl_get_term_parents( $term->term_id, 'category' );
				foreach ( $parents as $parent_id )
				{
					$parent  = get_term( $parent_id, 'category' );
					$items[] = array( get_term_link( $parent, 'category' ), $parent->name );
				}
				$items[] = array( get_term_link( $term, 'category' ), $term->name );
			}
		}
		// For custom post type: use filter
		else
		{
			$item = apply_filters( 'sl_breadcrumbs_single', array(), $post_type );
			if ( $item )
				$items[] = $item;
		}

		// Add current post
		$items[] = array( get_the_permalink(), get_the_title() );
	}

	// Single page: show page parents
	elseif ( is_page() )
	{
		$pages = sl_get_post_parents( get_the_ID() );
		foreach ( $pages as $page )
		{
			$items[] = array( get_permalink( $page ), get_the_title( $page ) );
		}

		$items[] = array( get_the_permalink(), get_the_title() );
	}

	// Post type archive: use filter
	elseif ( is_post_type_archive() )
	{
		$item = apply_filters( 'sl_breadcrumbs_post_type_archive', array(), $post_type );
		if ( $item )
			$items[] = $item;
	}

	// Category or tag: Blog > Category / Tag
	elseif ( is_category() || is_tag() )
	{
		if ( 'page' == get_option( 'show_on_front' ) && ( $blog_page = get_option( 'page_for_posts' ) ) )
		{
			$items[] = array( get_permalink( $blog_page ), get_the_title( $blog_page ) );
		}
		$current_term = get_queried_object();
		$terms        = sl_get_term_parents( get_queried_object_id(), 'category' );
		foreach ( $terms as $term_id )
		{
			$term    = get_term( $term_id, $current_term->taxonomy );
			$items[] = array( get_category_link( $term_id ), $term->name );
		}

		$items[] = array( get_category_link( $current_term ), $current_term->name );
	}

	// Custom taxonomy
	elseif ( is_tax() )
	{
		// Use filter to get link to post type archive
		$item = apply_filters( 'sl_breadcrumbs_tax', array(), $post_type );
		if ( $item )
			$items[] = $item;

		$current_term = get_queried_object();
		$terms        = sl_get_term_parents( get_queried_object_id(), $current_term->taxonomy );
		foreach ( $terms as $term_id )
		{
			$term    = get_term( $term_id, $current_term->taxonomy );
			$items[] = array( get_category_link( $term_id ), $term->name );
		}

		$items[] = array( get_category_link( $current_term ), $current_term->name );
	}

	// Otherwise
	else
	{
		global $wp;
		$current_url  = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$current_item = apply_filters( 'sl_breadcrumbs_general_text', array( $current_url, __( 'Archives', '7listings' ) ), $post_type );
		$items[]      = $current_item;
	}

	// Allow developers to change item URL/text
	$items = apply_filters( 'sl_breadcrumbs_items', $items );

	/**
	 * Now we have all breadcrumb items in $items
	 * We need to loop through all of them and output correctly
	 */

	// HTML template for each item
	$item_tpl = '
		<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"%s>
			<a href="%s" itemprop="item"><span itemprop="name">%s</span></a>
			<meta itemprop="position" content="%s">
		</li>
	';
	$html     = '';
	foreach ( $items as $k => $item )
	{
		$html .= sprintf(
			$item_tpl,
			$k == count( $items ) - 1 ? ' class="active"' : '',
			$item[0], // Item URL
			$item[1], // Item title
			$k + 1
		);
	}

	echo '
		<section id="breadcrumbs">
			<div class="container">
				<ul class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">' . $html . '</ul>
			</div>
		</section>
	';
}

/**
 * Searches for term parents' IDs of hierarchical taxonomies, including current term.
 * This function is similar to the WordPress function get_category_parents() but handles any type of taxonomy.
 * Modified from Hybrid Framework
 *
 * @param int|string    $term_id  The term ID
 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
 *
 * @return array Array of parent terms' IDs.
 */
function sl_get_term_parents( $term_id = '', $taxonomy = '' )
{
	// Set up some default arrays.
	$list = array();

	// If no term ID or taxonomy is given, return an empty array.
	if ( empty( $term_id ) || empty( $taxonomy ) )
		return $list;

	do
	{
		$list[] = $term_id;

		// Get next parent term
		$term    = get_term( $term_id, $taxonomy );
		$term_id = $term->parent;
	} while ( $term_id );

	// Reverse the array to put them in the proper order for the trail.
	$list = array_reverse( $list );
	array_pop( $list );

	return $list;
}

/**
 * Gets parent posts' IDs of any post type, include current post
 * Modified from Hybrid Framework
 *
 * @param int|string $post_id ID of the post whose parents we want.
 *
 * @return array Array of parent posts' IDs.
 */
function sl_get_post_parents( $post_id = '' )
{
	// Set up some default array.
	$list = array();

	// If no post ID is given, return an empty array.
	if ( empty( $post_id ) )
		return $list;

	do
	{
		$list[] = $post_id;

		// Get next parent post
		$post    = get_post( $post_id );
		$post_id = $post->post_parent;
	} while ( $post_id );

	// Reverse the array to put them in the proper order for the trail.
	$list = array_reverse( $list );
	array_pop( $list );

	return $list;
}
