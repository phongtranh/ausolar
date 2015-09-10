<?php
if ( empty( $_GET['company_id'] ) || empty( $_GET['lead_id'] ) )
	die;
$company_id = intval( $_GET['company_id'] );
$lead_id = intval( $_GET['lead_id'] );

$lead = GFFormsModel::get_lead( $lead_id );
$db = new wpdb( POSTCODE_DB_USER, POSTCODE_DB_PASSWORD, POSTCODE_DB_NAME, POSTCODE_DB_HOST );

$query = $db->prepare( "SELECT `postcode`, `lat`, `lon` FROM `postcode_db` WHERE postcode = %s LIMIT 1", $lead['17.5'] );
$base = $db->get_row( $query );
?>
<!DOCTYPE html>
<html>
<head>
	<title>Company Map</title>
	<script>
		function SlCompanyRadiusCallback( map, latLng, marker )
		{
			var circleOptions = {
				strokeColor  : '<?php echo sl_setting( 'design_map_stroke_color' ); ?>',
				strokeOpacity: 1,
				strokeWeight : 2,
				fillColor    : '<?php echo sl_setting( 'design_map_fill_color' ); ?>',
				fillOpacity  : 0.8,
				map          : map,
				center       : latLng,
				radius       : <?php echo 1000 * floatval( get_post_meta( $company_id, 'leads_service_radius', true ) ); ?>
			};
			var circle = new google.maps.Circle( circleOptions );

			var leadLatLng = new google.maps.LatLng( <?php echo $base->lat; ?>, <?php echo $base->lon; ?> );
			marker = new google.maps.Marker( {
				position: leadLatLng,
				map     : map,
				title   : 'Lead address'
			} );
		}
	</script>
</head>
<body>
<?php
$address = get_post_meta( $company_id, 'address', true );
$city = get_post_meta( $company_id, 'city', true );
$postcode = get_post_meta( $company_id, 'postcode', true );
$latitude = get_post_meta( $company_id, 'latitude', true );
$longtitude = get_post_meta( $company_id, 'longtitude', true );
if (
	( $latitude && $longtitude )
	|| ( $address && !empty( $city ) && $postcode )
)
{
	echo '<h1>' . __( 'Please verify the service area in this map before reject the lead', '7listings' ) . '</h4>';
	echo '<p><strong>Lead ID: ' . $lead_id . '</strong></p>';

	$args = array(
		'height'           => '500px',
		'output_js'        => true,
		'controls'         => 'pan,zoom',
		'disable_dragging' => 0,
		'marker_title'     => 'Your company',
		'scrollwheel'      => 1,
	);

	if ( $latitude && $longtitude )
	{
		$args = array_merge( $args, array(
			'type'       => 'latlng',
			'latitude'   => $latitude,
			'longtitude' => $longtitude,
			'class'      => 'map clearfix',
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

	if ( get_post_meta( $company_id, 'leads_service_radius', true ) )
		$args['js_callback'] = 'SlCompanyRadiusCallback';

	sl_map( $args );
}
?>
</body>
</html>