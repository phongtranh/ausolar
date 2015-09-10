<?php
/**
 * This is the main archive template for ATR modules
 *
 * @since 5.0
 */
?>

	<div id="main-wrapper" class="container">

		<?php
		the_post();
		$post_type = get_post_type();
		$prefix    = "{$post_type}_archive_";

		$content_class = "sl-list archive posts {$post_type}s";
		$content_class .= 'list' == sl_setting( "{$prefix}layout" ) ? ' list' : ' columns-' . sl_setting( "{$prefix}columns" );

		$sidebar_layout = sl_sidebar_layout();
		$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
		$content_class = $content_class ? ' class="' . $content_class . '"' : '';

		echo '<div id="content"' . $content_class . '>';

		global $query_string;

		// Common query args
		$args = wp_parse_args( $query_string );
		unset( $args['paged'] );
		$args['posts_per_page'] = - 1;

		// Order listings by
		switch ( sl_setting( "{$prefix}orderby" ) )
		{
			case 'views':
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'views';
				break;
			case 'price-asc':
				$args['meta_key'] = 'price_from';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = 'price_from';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
		}

		// Filter with price
		if ( isset( $_GET['min_price'] ) && isset( $_GET['max_price'] ) )
		{
			$args['meta_query'] =
				array(
					'key'     => 'price_from',
					'value'   => array( intval( $_GET['min_price'] ), intval( $_GET['max_price'] ) ),
					'type'    => 'numeric',
					'compare' => 'BETWEEN'
				);
		}

		// Filter with post type and taxonomy respectively
		$taxonomies = array( 'tour_type', 'type', 'rental_type', 'category', 'features', 'location' );

		$args['tax_query'] = array( 'relation' => 'AND' );

		foreach ( $taxonomies as $taxonomy )
		{
			if ( isset( $_GET["filter_{$taxonomy}"] ) )
			{
				$query_type      = 'and' == isset( $_GET["query_type_{$taxonomy}"] ) ? 'AND' : 'IN';
				$filter_features = isset( $_GET["filter_{$taxonomy}"] ) ? explode( ',', $_GET["filter_{$taxonomy}"] ) : array();

				$args['tax_query'][] = array(
					'taxonomy' => $taxonomy,
					'terms'    => $filter_features,
					'field'    => 'id',
					'operator' => $query_type
				);
			}
		}

		// Filter by alphabet
		if ( isset( $_GET['start'] ) && preg_match( '#^[a-z]$#', $_GET['start'] ) )
			add_filter( 'posts_where', array( 'Sl_' . ucfirst( $post_type ) . '_Frontend', 'filter_by_alphabet' ) );

		/**
		 * Allow developers, modules to change query vars for listing archive page
		 * This filter is used in 7 Listing Search Widget to show search results (but still use archive template)
		 *
		 * @param array $args Query vars array
		 */
		$args = apply_filters( 'sl_listing_archive_query_args', $args );

		// Use global query to store the archive query (run with priority)
		// This query will be used to show listings in the map
		global $sl_archive_query;

		// Sort by priority
		if ( ! sl_setting( "{$prefix}priority" ) )
		{
			$query = new WP_Query( $args );
			sl_archive_callback( $query, 999 );
			$sl_archive_query = $query;
		}
		else
		{
			sl_query_with_priority( $args, 'sl_archive_callback', '', '', true, 0, sl_setting( "{$prefix}num" ) );
		}

		if ( current_user_can( 'manage_options' ) )
			echo '<span class="edit-link button small page-settings"><a class="post-edit-link" href="' . admin_url( "edit.php?post_type=page&page={$post_type}" ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';

		/**
		 * Show Javascript for map
		 * The map placeholder is outputted in the feature title area, @see Sl_Core_Featured_Title::archive_map()
		 * This function only display Javascript code to show the map
		 */
		if ( sl_setting( $post_type . '_archive_map' ) )
		{
			sl_map_query( $sl_archive_query, array(
				'zoom'     => sl_setting( 'design_map_zoom' ),
				'sl-input' => 'pan,zoom',
			) );
		}

		echo '</div>';

		if ( 'none' != $sidebar_layout )
		{
			echo "<aside id='sidebar' class='$sidebar_layout'>";
			get_sidebar();
			echo '</aside>';
		}
		?>

	</div><!-- #main-wrapper -->

<?php
/**
 * Show listings on archive page
 *
 * @param object $query
 * @param int    $limit
 *
 * @return void
 */
function sl_archive_callback( $query, $limit = 5 )
{
	global $sl_archive_query;
	if ( ! sl_setting( get_post_type() . '_archive_priority' ) )
	{
		$num_posts  = count( $query->posts );
		$paged      = absint( max( 1, get_query_var( 'paged' ) ) ) - 1;
		$pagination = absint( sl_setting( get_post_type() . '_archive_num' ) );
		$pagination = $pagination ? $pagination : get_option( 'posts_per_page' );

		$query->max_num_pages = ceil( $num_posts / $pagination );
		$query->posts         = array_slice( $query->posts, $paged * $pagination, $pagination );
		$query->post_count    = count( $query->posts );
	}

	// Set the global query to show the map
	$sl_archive_query = $query;

	/**
	 * As we use same archive template for search functionality (see 7 Listing Search widget), there're cases when
	 * there is no results. Display friendly message to let people know that.
	 */
	if ( ! $query->post_count )
	{
		echo '<h2>' . __( 'Sorry! No listings found', '7listings' ) . '</h2>';
		echo '<p>' . __( 'Please try again with more generic search parameters.', '7listings' ) . '</p>';
		return;
	}

	while ( $limit && $query->have_posts() ):
		$query->the_post();
		$limit --;
		?>

		<article <?php post_class( 'post' ); ?>>

			<?php
			echo sl_listing_element( 'author' );
			echo sl_listing_element( 'thumbnail', array( 'image_size' => sl_setting( get_post_type() . '_archive_image_size' ) ) );

			echo '<div class="details">';

			// Featured graphics
			get_template_part( 'templates/parts/featured-ribbon' );

			echo sl_listing_element( 'post_title', array( 'title_tag' => 'h2' ) );

			if ( sl_setting( get_post_type() . '_archive_rating' ) )
			{
				echo sl_listing_element( 'rating' );
			}

			if ( sl_setting( get_post_type() . '_archive_star_rating' ) )
			{
				echo sl_listing_element( 'star_rating' );
			}

			if ( sl_setting( get_post_type() . '_archive_desc_enable' ) )
				echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( get_post_type() . '_archive_desc' ) ) );

			// Show price and booking button for listing if:
			// - Layout is list and booking resource is off
			// - Or layout is grid
			if (
				( 'list' == sl_setting( get_post_type() . '_archive_layout' ) && ! sl_setting( get_post_type() . '_book_in_archive' ) )
				|| 'grid' == sl_setting( get_post_type() . '_archive_layout' )
			)
			{
				if ( sl_setting( get_post_type() . '_archive_price' ) )
					echo sl_listing_element( 'price' );

				if ( sl_setting( get_post_type() . '_archive_booking' ) )
					echo sl_listing_element( 'booking', array( 'post_type' => get_post_type() ) );
			}

			// Read more
			if ( sl_setting( get_post_type() . '_archive_readmore' ) )
			{
				echo sl_listing_element( 'more_link', array(
					'more_link_type' => sl_setting( get_post_type() . '_archive_readmore_type' ),
					'more_link_text' => sl_setting( get_post_type() . '_archive_readmore_text' ),
				) );
			}

			echo apply_filters( 'sl_archive_post', '' );

			echo '</div>'; // .details

			get_template_part( 'templates/parts/archive-booking' );

			?>

		</article>

	<?php
	endwhile;
	if ( ! sl_setting( get_post_type() . '_archive_priority' ) )
		peace_numeric_pagination( $query );
}
