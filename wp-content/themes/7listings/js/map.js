/* global google, jQuery */

jQuery( function ( $ )
{
	'use strict';

	/**
	 * Callback function for Google Maps Lazy Load library to display map
	 *
	 * @param container jQuery container where map is displayed
	 * @param map       Google Maps object
	 */
	function displayMap( container, map )
	{
		var customize_option = $(container).data( 'customize_option' );
		var cus_event = 'click';
		if(typeof customize_option.event !== 'undefined')
		{
			cus_event = customize_option.event;
		} 
		console.log(customize_option.event);



		var $container = $( container ),
			mapOptions = $container.data( 'map_options' ),
			markers = $container.data( 'markers' ) ? $container.data( 'markers' ) : [],
			latLng = new google.maps.LatLng( -34.397, 150.644 );

		/**
		 * By default, each div wrapper of the map has [data-map_option] attribute to store map options
		 * But sometimes we can't display map options in the same div (like in archive page where the options
		 * are set after the div is displayed and set only after the query runs)
		 * So we create another div which store map options and connect current map to that div
		 */
		var connectedMapOptions = $container.data( 'connect_to' );
		if ( connectedMapOptions )
		{
			mapOptions = $( connectedMapOptions ).data( 'map_options' );
			markers = $( connectedMapOptions ).data( 'markers' );
		}

		// Set Google Maps type
		var googleMapType = google.maps.MapTypeId.ROADMAP;
		switch ( mapOptions.map_type )
		{
			case 'satellite':
				googleMapType = google.maps.MapTypeId.SATELLITE;
				break;
			case 'hybrid':
				googleMapType = google.maps.MapTypeId.HYBRID;
				break;
			case 'terrain':
				googleMapType = google.maps.MapTypeId.TERRAIN;
				break;
		}

		/**
		 * map variable is already initialized by the Google Maps Lazy Load library
		 * We only need to set its options
		 */
		map.setOptions( {
			zoom              : parseInt( mapOptions.zoom ),
			center            : latLng,
			mapTypeId         : googleMapType,
			panControl        : mapOptions.panControl,
			panControlOptions : {
				position: google.maps.ControlPosition.RIGHT_TOP
			},
			zoomControl       : mapOptions.zoomControl,
			zoomControlOptions: {
				style   : google.maps.ZoomControlStyle.LARGE,
				position: google.maps.ControlPosition.RIGHT_TOP
			},
			mapTypeControl    : mapOptions.mapTypeControl,
			scaleControl      : mapOptions.scaleControl,
			streetViewControl : mapOptions.streetViewControl,
			rotateControl     : mapOptions.rotateControl,
			overviewMapControl: mapOptions.overviewMapControl,
			scrollwheel       : mapOptions.scrollwheel ? 'false' : 'true',
			draggable         : mapOptions.disable_dragging ? 'false' : 'true'
		} );

		var marker = new google.maps.Marker( {
			position : latLng,
			map      : null,
			draggable: true // Not used. Just to make sure marker is displayed
		} );
		var geocoder = new google.maps.Geocoder(); // Defined as global to be used widely below to find location by address of markers

		// Set map style
		if ( 'default' === mapOptions.map_type && mapOptions.default_style )
		{
			map.set( 'styles', $.parseJSON( mapOptions.default_style ) );
		}

		// Set marker animation
		var animation = null;
		switch ( mapOptions.marker_animation )
		{
			case 'drop':
				animation = google.maps.Animation.DROP;
				break;
			case 'bounce':
				animation = google.maps.Animation.BOUNCE;
				break;
		}
		marker.setAnimation( animation );

		// Set marker icon
		if ( mapOptions.marker_icon )
		{
			marker.setIcon( mapOptions.marker_icon );
		}

		// Set marker title
		if ( mapOptions.marker_title )
		{
			marker.setTitle( mapOptions.marker_title );
		}
		// Set info window only when there's no markers
		if ( mapOptions.content && $.isEmptyObject( markers ) )
		{
			var infoWindow = new google.maps.InfoWindow( {
				content : mapOptions.content,
				minWidth: 200
			} );

			if(cus_event == false)
			{
				infoWindow.open( map, marker );
			} else {
				google.maps.event.addListener( marker, cus_event, function ()
				{
					infoWindow.open( map, marker );
				} );
			}
		}

		/**
		 * Set map center by coordinates or address
		 * And optional set map marker (if no markers are passed)
		 */
		if ( 'latlng' === mapOptions.type && mapOptions.latitude && mapOptions.longitude )
		{
			latLng = new google.maps.LatLng( mapOptions.latitude, mapOptions.longitude );
			map.setCenter( latLng );
			marker.setPosition( latLng );
		}
		else if ( mapOptions.address )
		{
			geocoder.geocode( { address: mapOptions.address }, function ( results, status )
			{
				if ( status !== google.maps.GeocoderStatus.OK )
				{
					return;
				}

				var loc = results[0].geometry.location;
				latLng = new google.maps.LatLng( loc.lat(), loc.lng() );
				map.setCenter( latLng );
				marker.setPosition( latLng );
			} );
		}

		/**
		 * Set map markers
		 * If there's no markers passed to the map, then set a marker at map center
		 */
		if ( $.isEmptyObject( markers ) )
		{
			marker.setMap( map );
		}
		$.each( markers, function ( i )
		{
			var location = markers[i],
				marker = new google.maps.Marker( {
					position : latLng,
					map      : map,
					draggable: true // Not used. Just to make sure marker is displayed
				} );

			// Set marker icon
			if ( location.icon )
			{
				marker.setIcon( location.icon );
			}
			else
			{
				marker.setIcon( mapOptions.marker_icon );
			}

			// Set marker animation
			marker.setAnimation( animation );

			// Set marker title
			if ( location.marker_title )
			{
				marker.setTitle( location.marker_title );
			}

			// Set info window
			var infoWindow = new google.maps.InfoWindow( {
				content : location.content,
				minWidth: 200
			} );

			if(cus_event == false)
			{
				infoWindow.open( map, marker );
			} else {
				google.maps.event.addListener( marker, cus_event, function ()
				{
					infoWindow.open( map, marker );
				} );
			}
			

			// Set marker position by coordinates or address
			if ( location.latitude && location.longitude )
			{
				latLng = new google.maps.LatLng( location.latitude, location.longitude );
				marker.setPosition( latLng );
			}
			else if ( location.address )
			{
				geocoder.geocode( { address: location.address }, function ( results, status )
				{
					if ( status !== google.maps.GeocoderStatus.OK )
					{
						return;
					}

					var loc = results[0].geometry.location;
					latLng = new google.maps.LatLng( loc.lat(), loc.lng() );
					marker.setPosition( latLng );
				} );
			}
		} ); // End $.each()
	}

	/**
	 * Lazy load Google Maps and display maps
	 */
	$( '.google-map' ).lazyLoadGoogleMaps( {
		callback: displayMap
	} );
} );