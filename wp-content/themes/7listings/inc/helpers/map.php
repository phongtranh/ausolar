<?php
/**
 * Show map shortcode
 *
 * @param array  $atts    Map config array
 * @param string $content Info window content
 * @param array  $markers List of markers, each marker is an array of (address, latitude, longitude, marker_title, content)
 *
 * @return string
 */
function sl_map( $atts, $content = '', $markers = array(), $data_customize_option = array() )
{
	$default_controls = sl_setting( 'design_map_controls' );
	$atts             = shortcode_atts( array(
		'type'             => 'address',
		'address'          => '',
		'latitude'         => '',
		'longitude'        => '',
		'map_type'         => sl_setting( 'design_map_type' ),
		'marker_title'     => '',
		'marker_animation' => sl_setting( 'design_map_marker_animation' ),
		'zoom'             => sl_setting( 'design_map_zoom' ),
		'width'            => '100%',
		'height'           => '400px',
		'align'            => 'none',
		'scrollwheel'      => in_array( 'scrollwheel', $default_controls ),
		'disable_dragging' => sl_setting( 'design_map_disable_dragging' ),
		'controls'         => implode( ',', $default_controls ),
		'marker_icon'      => 'custom' == sl_setting( 'design_map_marker_style' ) && sl_setting( 'design_map_marker' ) ? sl_setting( 'design_map_marker' ) : '',

		'js_callback'      => '', // JS callback
		'return_html'      => false,

		'class'            => '', // Custom CSS class
	), $atts );

	// Set correct map center type
	if ( $atts['address'] )
	{
		$atts['type'] = 'address';
	}
	elseif ( $atts['latitude'] && $atts['longitude'] )
	{
		$atts['type'] = 'latlng';
	}

	extract( $atts );

	$width  = intval( $width ) ? $width : '100%';
	$height = intval( $height ) ? $height : '400px';

	$class    = $class ? "google-map $class" : 'google-map';
	$controls = array_filter( explode( ',', $controls . ',' ) );

	$options = array_merge( $atts, array(
		'default_style'      => sl_setting( 'design_map_default_style' ),
		'mapTypeControl'     => in_array( 'map_type', $controls ) ? 'true' : 'false',
		'scaleControl'       => in_array( 'scale', $controls ) ? 'true' : 'false',
		'streetViewControl'  => in_array( 'street_view', $controls ) ? 'true' : 'false',
		'rotateControl'      => in_array( 'rotate', $controls ) ? 'true' : 'false',
		'overviewMapControl' => in_array( 'overview', $controls ) ? 'true' : 'false',
		'content'            => $content
	) );

	$markers = esc_attr( json_encode( $markers ) );
	$options = esc_attr( json_encode( $options ) );

	$data_customize_option = esc_attr( json_encode( $data_customize_option ) );

	$html    = sprintf( '<div style="width:%s;height:%s;float:%s" class="%s" data-map_options="%s" data-markers="%s" data-customize_option="%s"></div>', $width, $height, $align, $class, $options, $markers, $data_customize_option );

	if ( $return_html )
		return $html;

	echo $html;
}

/**
 * Display map for listings in a query
 *
 * @param WP_Query $query
 * @param array    $args Map configuration
 */
function sl_map_query( $query = null, $args = array() )
{
	global $wp_query;

	// If no query is passed, use the main query
	if ( empty( $query ) )
		$query = $wp_query;

	$query->rewind_posts();
	if ( ! $query->have_posts() )
		return;

	$default_controls = sl_setting( 'design_map_controls' );
	$args             = array_merge( array(
		'map_type'         => sl_setting( 'design_map_type' ),
		'zoom'             => sl_setting( 'design_map_zoom' ),
		'width'            => '100%',
		'height'           => '400px',
		'align'            => 'none',
		'scrollwheel'      => in_array( 'scrollwheel', $default_controls ),
		'disable_dragging' => sl_setting( 'design_map_disable_dragging' ),
		'controls'         => implode( ',', $default_controls ),
		'marker_animation' => sl_setting( 'design_map_marker_animation' ),
		'marker_icon'      => 'custom' == sl_setting( 'design_map_marker_style' ) && sl_setting( 'design_map_marker' ) ? sl_setting( 'design_map_marker' ) : '',
	), $args );

	$markers = array();
	while ( $query->have_posts() )
	{
		$query->the_post();

		$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
		$longitude = get_post_meta( get_the_ID(), 'longitude', true );
		$address   = get_post_meta( get_the_ID(), 'address', true );
		$postcode  = get_post_meta( get_the_ID(), 'postcode', true );
		$city      = get_post_meta( get_the_ID(), 'city', true );
		$term      = get_term( $city, 'location' );
		if ( ! empty( $term ) && ! is_wp_error( $term ) )
			$city = $term->name;

		if ( ( ! $latitude || ! $longitude ) && ( ! $address || ! $city || ! $postcode ) )
			continue;

		$title       = sl_listing_element( 'post_title' );
		$prefix      = get_post_type() . '_archive_map_';
		$description = sl_post_list_single( array(
			'thumbnail'      => sl_setting( "{$prefix}image" ),
			'image_size'     => sl_setting( "{$prefix}image_size" ),
			'rating'         => sl_setting( "{$prefix}rating" ),
			'price'          => sl_setting( "{$prefix}price" ),
			'booking'        => sl_setting( "{$prefix}booking" ),
			'excerpt'        => sl_setting( "{$prefix}excerpt" ),
			'excerpt_length' => sl_setting( "{$prefix}excerpt_length" ),
			'class'          => 'map-pop',
			'elements'       => array( 'post_title', 'rating', 'excerpt', 'price', 'booking' ),
		) );

		$title       = str_replace( array( "\n", "\r", "\t", "'", '&quot;' ), '', $title );
		$description = str_replace( array( "\n", "\r", "\t", "'", '&quot;' ), '', $description );

		$icon = '';
		$type = wp_get_post_terms( get_the_ID(), sl_meta_key( 'tax_type', get_post_type() ), array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $type ) && $type )
		{
			$type = current( $type );
			if ( $image = sl_get_term_meta( $type, 'icon_id' ) )
				$icon = wp_get_attachment_url( $image );
		}

		$markers[] = array(
			'address'      => $address . ',' . $city . ',' . $postcode,
			'latitude'     => $latitude,
			'longitude'    => $longitude,
			'icon'         => $icon,
			'marker_title' => strip_tags( $title ),
			'animation'    => '',
			'content'      => $description
		);


	}
	$controls = array_filter( explode( ',', $args['controls'] . ',' ) );

	$options = array_merge( $args, array(
		'address'            => $address . ',' . $city . ',' . $postcode,
		'latitude'           => $latitude,
		'longitude'          => $longitude,
		'default_style'      => sl_setting( 'design_map_default_style' ),
		'mapTypeControl'     => in_array( 'map_type', $controls ) ? 'true' : 'false',
		'scaleControl'       => in_array( 'scale', $controls ) ? 'true' : 'false',
		'streetViewControl'  => in_array( 'street_view', $controls ) ? 'true' : 'false',
		'rotateControl'      => in_array( 'rotate', $controls ) ? 'true' : 'false',
		'overviewMapControl' => in_array( 'overview', $controls ) ? 'true' : 'false',
	) );
	$options = esc_attr( json_encode( $options ) );
	$markers = esc_attr( json_encode( $markers ) );
	$html    = sprintf( '<div data-map_options="%s" data-markers="%s" id="option_map"></div>', $options, $markers );
	echo $html;
}