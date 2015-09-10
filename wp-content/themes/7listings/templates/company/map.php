<?php
$membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true );
if ( ! $membership )
	$membership = 'none';

$address   = get_post_meta( get_the_ID(), 'address', true );
$city      = get_post_meta( get_the_ID(), 'city', true );
$postcode  = get_post_meta( get_the_ID(), 'postcode', true );
$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
$longitude = get_post_meta( get_the_ID(), 'longitude', true );

if ( ( ! $latitude || ! $longitude ) && ( ! $address || ! $city || ! $postcode ) )
	return;

// Do not show heading and other tags in featured title area
if ( 'sl_featured_title_after' != current_filter() )
{
	echo '<section id="location-map" class="company-meta">';
	//echo '<h3 id="map-title" class="title">' . __( 'Location', '7listings' ) . '</h3>';
}

$args = array(
	'height' => '280px'
);

if ( $latitude && $longitude )
{
	$args = array_merge( $args, array(
		'type'      => 'latlng',
		'latitude'  => $latitude,
		'longitude' => $longitude,
		'class'     => 'map clearfix',
	) );
}
else
{
	$args = array_merge( $args, array(
		'type'    => 'address',
		'address' => "$address, $city, $postcode",
		'class'   => 'map',
	) );
}

// Logo in info window
$info_window = '';
if ( $src = sl_broadcasted_image_src( sl_meta_key( 'logo', get_post_type() ), 'full' ) )
	$info_window = '<img src="' . $src . '" class="brand-logo" alt="' . the_title_attribute( 'echo=0' ) . '">';

// Not in featured title area
if ( 'sl_featured_title_after' != current_filter() )
{
	// Radius
	if ( sl_setting( "company_service_area_$membership" ) && get_post_meta( get_the_ID(), 'leads_service_radius', true ) )
		$args['js_callback'] = 'SlCompanyRadiusCallback';
}
// In featured title area
else
{
	$args = array_merge( $args, array(
		'zoom' => sl_setting( get_post_type() . '_single_featured_title_map_zoom' ),
	) );
}

if ( 'sl_featured_title_after' == current_filter() )
	sl_map( $args, $info_window );
else
	sl_map( $args );

// Do not show heading and other tags in featured title area
if ( 'sl_featured_title_after' != current_filter() )
{
	echo '</section>';
}
