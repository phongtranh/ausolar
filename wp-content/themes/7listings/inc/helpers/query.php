<?php
/**
 * Query posts with priority: star > featured > normal
 *
 * @param array    $args       Common arguments
 * @param string   $callback   Callback function to display posts
 * @param string   $before     HTML before showing queries' content
 * @param string   $after      HTML before showing queries' content
 * @param bool     $normal     Query normal posts or not?
 * @param int      $limit      Number of posts
 * @param bool|int $pagination false if no pagination, a number for posts per page if pagination
 *
 * @return int Number of queried posts
 */
function sl_query_with_priority( $args, $callback, $before = '', $after = '', $normal = true, $limit = 0, $pagination = false )
{
	global $sl_not_duplicated, $sl_archive_posts;

	if ( empty( $args['posts_per_page'] ) )
		$args['posts_per_page'] = get_option( 'posts_per_page' );
	$total                  = 0 < $limit ? $limit : $args['posts_per_page'];
	$args['posts_per_page'] = $total;

	$not_duplicated = ! empty( $args['post__not_in'] ) ? $args['post__not_in'] : array();

	// Priority query
	$meta_query     = empty( $args['meta_query'] ) ? array() : $args['meta_query'];
	$meta_query[]   = array(
		'key'   => 'featured',
		'value' => 2,
	);
	$priority_args  = array(
		'meta_query'     => $meta_query,
		'posts_per_page' => - 1, // Take all priority listings first
	);
	$priority_args  = array_merge( $args, $priority_args );
	$priority_query = new WP_Query( $priority_args );
	while ( $priority_query->have_posts() )
	{
		$priority_query->the_post();
		$not_duplicated[] = get_the_ID();
	}
	$priority_query->rewind_posts();

	// Featured query
	$meta_query     = empty( $args['meta_query'] ) ? array() : $args['meta_query'];
	$meta_query[]   = array(
		'key'   => 'featured',
		'value' => 1,
	);
	$featured_args  = array(
		'meta_query'     => $meta_query,
		'post__not_in'   => $not_duplicated,
		'posts_per_page' => - 1, // Take all featured listings first
	);
	$featured_args  = array_merge( $args, $featured_args );
	$featured_query = new WP_Query( $featured_args );

	// Normal query
	if ( $normal )
	{
		while ( $featured_query->have_posts() )
		{
			$featured_query->the_post();
			$not_duplicated[] = get_the_ID();
		}
		$featured_query->rewind_posts();

		$normal_args  = array(
			'post__not_in' => $not_duplicated,
		);
		$normal_args  = array_merge( $args, $normal_args );
		$normal_query = new WP_Query( $normal_args );

		while ( $normal_query->have_posts() )
		{
			$normal_query->the_post();
			$not_duplicated[] = get_the_ID();
		}
		$normal_query->rewind_posts();
	}

	// Log duplicated posts for modules to rebuild query args
	$sl_not_duplicated = $not_duplicated;

	// Merge queries' results
	$result        = new WP_Query();
	$result->posts = array_merge( $priority_query->posts, $featured_query->posts );

	if ( $normal )
		$result->posts = array_merge( $result->posts, $normal_query->posts );
	$num_posts             = $result->post_count = count( $result->posts );
	$result->max_num_pages = 1;

	// Store all posts for future use (filter in company module or map)
	$sl_archive_posts = $result->posts;

	if ( $total == - 1 )
		$total = $result->post_count;

	if ( $pagination )
	{
		$paged      = absint( max( 1, get_query_var( 'paged' ) ) ) - 1;
		$pagination = absint( $pagination );
		$pagination = $pagination ? $pagination : get_option( 'posts_per_page' );

		$result->max_num_pages = ceil( $num_posts / $pagination );
		$total                 = $pagination;

		$result->posts      = array_slice( $result->posts, $paged * $pagination, $pagination );
		$result->post_count = count( $result->posts );
	}

	// If have posts
	if ( $num_posts )
	{
		echo $before;

		call_user_func( $callback, $result, $total );
		// call_user_func_array( $callback, array( $result, $total ) );
		// call_user_func_array( $callback, array( $result, $total ) );
		// call_user_func_array( $callback, array( $featured_query, $total - $priority_query->post_count ) );
		// if ( $normal )
		// 	call_user_func_array( $callback, array( $normal_query, $total - $priority_query->post_count - $featured_query->post_count ) );

		// Pagination
		peace_numeric_pagination( $result );

		wp_reset_postdata();

		echo $after;

		return $result->post_count;
	}

	return 0;
}

/**
 * Callback for `sl_query_with_priority()` which displays list of posts (or any custom post types)
 * It simply uses `sl_post_list_single()` to display single post
 * Argument for `sl_post_list_single()` is passed via a global variable `$sl_list_args`
 * We have to use global variable to make this arguments can be passed to this callback before calling `sl_query_with_priority()`
 *
 * @see   sl_query_with_priority()
 * @see   sl_post_list_single()
 *
 * @param WP_Query $query
 * @param int      $limit
 *
 * @return void
 *
 * @since 5.1.5
 */
function sl_list_callback( $query, $limit = 5 )
{
	$args = $GLOBALS['sl_list_args'];

	while ( 0 < $limit && $query->have_posts() )
	{
		$query->the_post();
		$limit --;
		echo sl_post_list_single( $args );
	}
}

/**
 * Build query args from array of inputs
 * This is used widely in all widgets (slider, list) and shortcodes
 * Handle following params:
 * - post_status
 * - ignore_sticky_posts
 * - posts_per_page (number)
 * - location
 * - display (for priority sorting)
 * - orderby
 *
 * @param array $query_args Query args which will be modified
 * @param array $args       Array of inputs
 *
 * @return array
 */
function sl_build_query_args( &$query_args, $args )
{
	// Always show display publish posts and ignore sticky ones
	$query_args['post_status']         = 'publish';
	$query_args['ignore_sticky_posts'] = true;

	// Hidden post
	if ( isset( $args['post__not_in'] ) )
		$query_args['post__not_in'] = $args['post__not_in'];

	// Posts per page
	if ( isset( $args['number'] ) )
		$query_args['posts_per_page'] = intval( $args['number'] );

	// Show free list
	if ( isset( $args['show_free'] ) && 0 == intval( $args['show_free'] ) )
	{
		if ( isset( $args['post_types'] ) )
		{
			foreach ( $args['post_types'] as $post_type )
			{
				if ( in_array( $post_type, array( 'accommodation', 'rental', 'tour' ) ) )
				{
					$query_args['meta_query'][] = array(
						'key'     => 'price_from',
						'value'   => 0,
						'compare' => '>',
						'type'    => 'DECIMAL',
					);
				}
			}
		}
		else
		{
			if ( 'product' == $args['post_type'] )
			{
				$query_args['meta_query'][] = array(
					'key'     => '_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL',
				);
			}
			else if ( in_array( $args['post_type'], array( 'accommodation', 'rental', 'tour' ) ) )
			{
				$query_args['meta_query'][] = array(
					'key'     => 'price_from',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL',
				);
			}
		}
	}

	// Initialize taxonomy and meta query
	if ( ! isset( $query_args['tax_query'] ) )
		$query_args['tax_query'] = array();
	if ( ! isset( $query_args['meta_query'] ) )
		$query_args['meta_query'] = array();

	// Location
	if ( ! empty( $args['location'] ) )
	{
		$query_args['tax_query'][] = array(
			'taxonomy' => 'location',
			'field'    => 'id',
			'terms'    => $args['location'],
		);
	}

	// Type
	if ( $args['type'] && 'product' != $args['post_type'] )
	{
		$query_args['tax_query'][] = array(
			'taxonomy' => sl_meta_key( 'tax_type', $args['post_type'] ),
			'field'    => 'id',
			'terms'    => $args['type'],
		);
	}

	// Feature
	if ( ! empty( $args['feature'] ) )
	{
		$query_args['tax_query'][] = array(
			'taxonomy' => sl_meta_key( 'tax_feature', $args['post_type'] ),
			'field'    => 'id',
			'terms'    => $args['feature'],
		);
	}

	/**
	 * Priority sorting (display)
	 * The param 'display' might be confusing (like display as a list or grid)
	 * So we have to check for each value we need and no 'default'
	 * Also in list widgets, the param name is 'display_order'
	 */
	$display = isset( $args['display_order'] ) ? $args['display_order'] : ( isset( $args['display'] ) ? $args['display'] : '' );
	if ( $display && 'all' != $display )
	{
		switch ( $display )
		{
			case 'star':
				$query_args['meta_query'][] = array(
					'key'   => 'featured',
					'value' => 2,
				);
				break;
			case 'star-featured':
				$query_args['meta_query'][] = array(
					'key'     => 'featured',
					'value'   => 1,
					'compare' => '>=',
				);
				break;
			case 'featured':
				$query_args['meta_query'][] = array(
					'key'   => 'featured',
					'value' => 1,
				);
				break;
			case 'featured-normal':
				global $wpdb;
				$post_ids = $wpdb->get_col( "
						SELECT post_id FROM {$wpdb->postmeta}
						WHERE meta_key = 'featured' AND meta_value = '2'
					" );
				if ( $post_ids )
					$query_args['post__not_in'] = $post_ids;
				break;
		}
	}

	// Order
	if ( ! empty( $args['orderby'] ) )
	{
		switch ( $args['orderby'] )
		{
			case 'rand':
				$query_args['orderby'] = 'rand';
				break;
			case 'views':
				$query_args['orderby']  = 'meta_value_num';
				$query_args['meta_key'] = 'views';
				break;
			case 'price-asc':
				$query_args['meta_key'] = 'price_from';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'ASC';
				break;
			case 'price-desc':
				$query_args['meta_key'] = 'price_from';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;
		}
	}

	return $query_args;
}
