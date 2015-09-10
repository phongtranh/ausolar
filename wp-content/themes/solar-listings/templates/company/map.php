<?php
$membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true );
if ( ! $membership )
	$membership = 'none';

// Setup the address info
$address = [];
$address_fields = ['address2', 'address', 'area', 'postcode', 'state'];
foreach ( $address_fields as $address_field )
{
	if ( $field = get_post_meta( get_the_ID(), $address_field, true ) )
		$address[] = $field;
}

$address = implode( ',', $address );

$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
$longitude = get_post_meta( get_the_ID(), 'longtitude', true );

if ( ( $latitude && $longitude ) || $address )
{
	// Do not show heading and other tags in featured title area
	if ( 'sl_featured_title_after' != current_filter() )
	{
		echo '<section id="location-map" class="company-meta">';
		//echo '<h3 id="map-title" class="title">' . __( 'Location', '7listings' ) . '</h3>';
	}

	$args = array(
		'height'    => '280px',
		'output_js' => true,
	);

	if ( $latitude && $longitude )
	{
		$args = array_merge( $args, array(
			'type'       => 'latlng',
			'latitude'   => $latitude,
			'longtitude' => $longitude,
			'class'      => 'map clearfix',
		) );
	}
	else
	{
		$args = array_merge( $args, array(
			'type'    => 'address',
			'address' => "$address, Australia",
			'class'   => 'map',
		) );
	}

	$logo = asq_get_company_logo( get_the_ID() );
	
	// Logo in info window
	$info_window = '';
    if ( ! empty( $logo ) && in_array( $membership, array( 'gold', 'silver' ) ) )
    	$info_window = '<img style="width:100%;height:100%;" src="' . $logo . '" class="brand-logo" alt=" post_type :  ' . get_post_type() . '  src: ' . $logo . '  tiltle '  . the_title_attribute( 'echo=0' ) . '">';

    //marker visible options
    $marker_visible = true;
    if( $info_window !== '' )
    	$marker_visible = false;

	// For single company content, i.e. not in featured title area
	if ( 'sl_featured_title_after' != current_filter() )
	{
		// Radius
		if ( sl_setting( "company_service_area_$membership" ) && get_post_meta( get_the_ID(), 'leads_service_radius', true ) )
			$args['js_callback'] = 'SlCompanyRadiusCallback';
	}
	// In featured title area: no controls
	else
	{
		$args = array_merge( $args, array(
			'scrollwheel'      => false,
			'disable_dragging' => true,
			'controls'         => '',
			'zoom'             => sl_setting( get_post_type() . '_single_featured_title_map_zoom' ),
		) );
	}

	sl_map( $args, $info_window, array(), array('event' => false, 'marker_visible' => $marker_visible) );

	// Do not show heading and other tags in featured title area
	if ( 'sl_featured_title_after' != current_filter() )
	{
		echo '</section>';
	}
}
