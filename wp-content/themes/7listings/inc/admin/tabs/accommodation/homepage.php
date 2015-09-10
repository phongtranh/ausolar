<?php
$post_type = 'accommodation';
$prefix = "homepage_{$id}_";
if ( 'accommodation_featured' == $id )
{
	$title    = __( 'Accommodation Slider', '7listings' );
	$checkboxes = array(
		'post_title'  => __( 'Title', '7listings' ),
		'star_rating' => __( 'Star Rating', '7listings' ),
		'price'       => __( 'Price', '7listings' ),
		'booking'     => __( 'Booking Button', '7listings' ),
		'rating'      => __( 'Review Rating', '7listings' ),
	);
	include THEME_TABS . 'homepage/slider.php';
}
if ( 'accommodation_listings' == $id )
{
	$title    = __( 'Accommodations', '7listings' );
	$checkboxes = array(
		'star_rating' => __( 'Star Rating', '7listings' ),
		'rating'      => __( 'Rating', '7listings' ),
		'price'       => __( 'Price', '7listings' ),
		'booking'     => __( 'Booking Button', '7listings' ),
	);
	include THEME_TABS . 'homepage/atr-list.php';
}
if ( 'accommodation_types' == $id )
{
	$title = __( 'Accommodation Types', '7listings' );
	include THEME_TABS . 'homepage/types.php';
}
if ( 'accommodation_amenities' == $id )
{
	$title    = __( 'Accommodation Features', '7listings' );
	$taxonomy = 'amenities';
	include THEME_TABS . 'homepage/taxonomy.php';
}
