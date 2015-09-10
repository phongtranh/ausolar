<?php
$post_type = 'rental';
$prefix = "homepage_{$id}_";
if ( 'rental_featured' == $id )
{
	$title = __( 'Rental Slider', '7listings' );
	$checkboxes = array(
		'post_title' => __( 'Title', '7listings' ),
		'rating'     => __( 'Review Rating', '7listings' ),
		'price'      => __( 'Price', '7listings' ),
		'booking'    => __( 'Booking Button', '7listings' ),
	);
	include THEME_TABS . 'homepage/slider.php';
}
if ( 'rental_listings' == $id )
{
	$title      = __( 'Rentals', '7listings' );
	$checkboxes = array(
		'rating'  => __( 'Rating', '7listings' ),
		'price'   => __( 'Price', '7listings' ),
		'booking' => __( 'Booking Button', '7listings' ),
	);
	include THEME_TABS . 'homepage/atr-list.php';
}
if ( 'rental_types' == $id )
{
	$title = __( 'Rental Types', '7listings' );
	include THEME_TABS . 'homepage/types.php';
}
if ( 'rental_features' == $id )
{
	$title    = __( 'Rental Features', '7listings' );
	$taxonomy = 'features';
	include THEME_TABS . 'homepage/taxonomy.php';
}
