<?php
add_filter( 'body_class', 'sl_body_class' );

/**
 * Retrieve the classes for the body element as an array.
 *
 * @param string|array $classes
 *
 * @return array Array of classes.
 */
function sl_body_class( $classes )
{
	global $post;

	if ( is_front_page() )
	{
		$classes[] = 'home';
	}
	elseif ( is_404() )
	{
		$classes[] = 'page';
		$classes[] = 'not-found';
	}
	elseif ( is_home() )
	{
		$classes[] = 'archive';
		$classes[] = 'posts';
	}
	elseif ( get_query_var( 'book' ) )
	{
		$classes[] = 'book';
		$classes[] = $post->post_type;
	}
	elseif ( is_page_template( 'templates/contact.php' ) )
	{
		$classes[] = 'page';
		$classes[] = 'contact';
	}
	elseif ( is_archive() || is_search() )
	{
		$classes[] = 'archive';

		if ( is_search() )
			$classes[] = 'search';
		elseif ( is_object( $post ) )
			$classes[] = "{$post->post_type}s";
	}
	elseif ( is_single() )
	{
		$classes[] = 'single';
		$classes[] = $post->post_type;
		if ( in_array( $post->post_type, sl_setting( 'listing_types' ) ) )
			$classes[] = $post->post_name;
	}
	elseif ( is_page() )
	{
		$classes[] = 'page';
		$classes[] = $post->post_name;
	}

	if ( sl_setting( 'design_header_phone' ) && sl_setting( 'phone' ) )
		$classes[] = 'phone';
	if ( sl_setting( 'weather_active' ) && sl_setting( 'woeid' ) )
	{
		$info = sl_get_weather_info( sl_setting( 'woeid' ), sl_setting( 'weather_unit' ) );
		if ( ! empty( $info ) )
		{
			$classes[] = 'weather';
			$classes[] = 'weather-' . sl_setting( 'design_weather_style' );
		}
	}

	// Layout position
	$position = sl_setting( 'layout_position' );
	if ( $position && 'center' != $position )
		$classes[] = "layout-$position";

	// Mobile NAV position
	$mobile_nav_position = sl_setting( 'design_layout_mobile_nav' );
	if ( $mobile_nav_position && 'left' != $mobile_nav_position )
		$classes[] = "mobile-menu-$mobile_nav_position";

	return $classes;
}

add_filter( 'post_class', 'sl_post_class' );

/**
 * Add 'featured' and 'star' to post class
 *
 * @param array $classes
 *
 * @return array
 */
function sl_post_class( $classes )
{
	if ( $featured = intval( get_post_meta( get_the_ID(), 'featured', true ) ) )
		$classes[] = 1 == $featured ? 'featured' : 'star';

	return $classes;
}
