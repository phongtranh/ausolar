<?php
$post_type = 'tour';
$prefix = "homepage_{$id}_";
if ( 'tour_featured' == $id )
{
	$title = __( 'Tour Slider', '7listings' );
	$checkboxes = array(
		'post_title' => __( 'Title', '7listings' ),
		'rating'     => __( 'Review Rating', '7listings' ),
		'price'      => __( 'Price', '7listings' ),
		'booking'    => __( 'Booking Button', '7listings' ),
	);
	include THEME_TABS . 'homepage/slider.php';
}
if ( 'tour_listings' == $id )
{
	$title = __( 'Tours', '7listings' );
	$checkboxes = array(
		'rating'  => __( 'Rating', '7listings' ),
		'price'   => __( 'Price', '7listings' ),
		'booking' => __( 'Booking Button', '7listings' ),
	);
	include THEME_TABS . 'homepage/atr-list.php';
}
if ( 'tour_types' == $id )
{
	$title = __( 'Tour Types', '7listings' );
	include THEME_TABS . 'homepage/types.php';
}
if ( 'tour_features' == $id )
{
	$title    = __( 'Tour Features', '7listings' );
	$taxonomy = 'features';
	include THEME_TABS . 'homepage/taxonomy.php';
}
