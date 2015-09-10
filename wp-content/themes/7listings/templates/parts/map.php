<?php
/**
 * Display google maps for listings in featured title area and inside post content
 *
 * @package 7listings
 */

// No map if settings for featured title area is off
if ( 'sl_featured_title_after' == current_filter() )
{
	if ( ! sl_setting( get_post_type() . '_single_featured_title_map' ) )
		return;
}
// No map if settings for post content is off
elseif ( ! sl_setting( get_post_type() . '_google_maps' ) )
{
	return;
}

$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
$longitude = get_post_meta( get_the_ID(), 'longitude', true );
$address   = get_post_meta( get_the_ID(), 'address', true );
$postcode  = get_post_meta( get_the_ID(), 'postcode', true );
$city      = get_post_meta( get_the_ID(), 'city', true );
$term      = get_term( $city, 'location' );
if ( ! empty( $term ) && ! is_wp_error( $term ) )
	$city = $term->name;

$map_address = array();
if ( $address )
	$map_address[] = $address;
if ( $city )
	$map_address[] = $city;
if ( $postcode )
	$map_address[] = $postcode;
$map_address = implode( ', ', $map_address );

if ( ( ! $latitude || ! $longitude ) && ! $map_address )
	return;

// Do not show heading and other tags in featured title area
if ( 'sl_featured_title_after' != current_filter() )
{
	echo '<section id="location-map">';
	echo '<h3 id="map-title" class="title">' . __( 'Location', '7listings' ) . '</h3>';
}

$args = array( 'height' => '350px' );

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
		'address' => $map_address,
		'class'   => 'map',
	) );
}

// Logo in info window
$info_window = '';
if ( $src = sl_broadcasted_image_src( sl_meta_key( 'logo', get_post_type() ), 'full' ) )
	$info_window = '<img src="' . $src . '" class="brand-logo" alt="' . the_title_attribute( 'echo=0' ) . '">';

// In featured title area
if ( 'sl_featured_title_after' == current_filter() )
{
	$args = array_merge( $args, array(
		'zoom' => sl_setting( get_post_type() . '_single_featured_title_map_zoom' ),
	) );
}

if ( 'sl_featured_title_after' == current_filter() )
{
	sl_map( $args, $info_window );
}
else
{
	$args = array_merge( $args, array( 'disable_dragging' => 0, ) );
	sl_map( $args );
}

// Do not show heading and other tags  in featured title area
if ( 'sl_featured_title_after' != current_filter() )
{
	echo '</section>';
}
