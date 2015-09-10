<?php
global $wpdb;

$leads = $wpdb->get_results("
	SELECT count(value) as total, value
	FROM asq_rg_lead_detail 
	WHERE form_id = 1 
	AND field_number = '17.5'
	GROUP BY value
	ORDER BY total DESC
	", ARRAY_N
);

$postcodes_latlng = get_option( 'postcodes_latlng' );

$markers = array();

foreach ( $leads as $lead )
{
	$latlng = explode( ',', $postcodes_latlng[$lead[1]] );

	$markers[] = array( $latlng[0], $latlng[1], $lead[0] );
}
?>
<h1>Leads Map</h1>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
<script>

function initialize() {
  var mapOptions = {
    zoom: 10,
    center: new google.maps.LatLng(-33.9, 151.2)
  }
  var map = new google.maps.Map(document.getElementById('map-canvas'),
                                mapOptions);

  setMarkers(map, beaches);
}

/**
 * Data for the markers consisting of a name, a LatLng and a zIndex for
 * the order in which these markers should display on top of each
 * other.
 */

var beaches = <?php echo json_encode( $markers, true ) ?>;

function setMarkers(map, locations) {
  for (var i = 0; i < locations.length; i++) {
    var beach = locations[i];
    var myLatLng = new google.maps.LatLng(beach[0], beach[1]);
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map
    });
  }
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<style>
	#map-canvas {
        height: 100%;
        min-height: 800px;
        margin: 0;
        padding: 0;
      }
</style>

<div id="map-canvas"></div>