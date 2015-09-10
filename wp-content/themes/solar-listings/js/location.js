var parts = window.location.search.substr(1).split("&");
var $_GET = {};
for (var i = 0; i < parts.length; i++) {
    var temp = parts[i].split("=");
    $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
}

;jQuery( function( $ )
{
	var placeSearch, autocomplete;

	var formField = {
		address 	: 'input_1_17_1',
		suburb		: '#input_1_17_3',
		state		: '#input_1_17_4',
		postcode 	: '#input_1_17_5'
	};

	function initialize(formId) {

		if ( formId == 63 )
		{
			formField = {
				address 	: 'input_63_91_1',
				suburb		: '#input_63_91_3',
				state		: '#input_63_91_4',
				postcode 	: '#input_63_91_5'
			};
		}
				
	  	autocomplete = new google.maps.places.Autocomplete(
	      	document.getElementById(formField.address),
	      	{ types: ['address'], componentRestrictions: {country: 'au'} }
	    );
	  	
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place 				= autocomplete.getPlace(),
	    		hasPostcodeFilled 	= false;

	    	// $( formField.postcode ).parent().hide();

	    	for (var i = 0; i < place.address_components.length; i++) {
	    		var addressType = place.address_components[i].types[0],
	    			val 		= place.address_components[i]['short_name'];

	    		if (addressType === 'postal_code') {
	    			$(formField.postcode).val(val);
	    			
	    			hasPostcodeFilled = true;
	    		}

	    		if (addressType === 'administrative_area_level_1') {
	    			$(formField.state).val(val);
	    		}

	    		if (addressType === 'locality') {
	    			$(formField.suburb).val(val);
	    		}
	    	}

	    	if ( ! hasPostcodeFilled )
	    	{
	    		$( formField.postcode ).parent().css( {
	    			'position' 		: 'static',
	    			'visibility' 	: 'visible' 
	    		} );
	    	}
	  	} );
	}

	// [START region_geolocation]
	// Bias the autocomplete object to the user's geographical location,
	// as supplied by the browser's 'navigator.geolocation' object.
	function geolocate() {
	  if (navigator.geolocation) {
	    navigator.geolocation.getCurrentPosition(function(position) {
	      var geolocation = new google.maps.LatLng(
	          position.coords.latitude, position.coords.longitude);
	      var circle = new google.maps.Circle({
	        center: geolocation,
	        radius: position.coords.accuracy
	      });
	      autocomplete.setBounds(circle.getBounds());
	    });
	  }
	}

	$( document ).ready( function()
	{
		if ( $('#input_1_17_1').length )
			initialize(1);
		
		if ( $('#input_63_91_1').length )
			initialize(63);
	} );

	if ( $('#input_45_14_3').prop('tagName') != 'SELECT' )
	{
		$( '#input_45_14_3' ).replaceWith(
			$('<select></select>')
				.attr( 'id', 'input_45_14_3' )
				.attr('name', 'input_14.3' )
		);
	}

	if ( typeof ( $_GET['postcode'] ) != 'undefined' )
	{
		return location_auto_fill( {
			'postcode' 	: '#input_63_91_5',
			'state'		: '#input_63_91_4',
			'suburb'	: '#input_63_91_3'
		}, 'form_1' );
	}

	$( '#input_45_14_3_label' ).text( 'Suburb' );

	$( '#auto_fill_postcode' ).change( function()
	{
		return location_auto_fill( {
			'postcode' 	: '#auto_fill_postcode',
			'state'		: '#auto_fill_state',
			'city'		: '#auto_fill_city',
			'suburb'	: '#auto_fill_suburb'
		}, 'auto_fill' );
	} );


	$( '#input_45_14_5' ).change( function()
	{
		return location_auto_fill( {
			'postcode' 	: '#input_45_14_5',
			'state'		: '#input_45_14_4',
			'suburb'	: '#input_45_14_3'
		}, 'form_45' );
	} );

	/**
	 * Auto fill
	 *
	 * @param  Object targets Target for postcode, state, city, suburb
	 * @return String Json
	 *
	 * @author Tan
	 */
	function location_auto_fill( targets, form )
	{
		var endpoint = ( typeof ajaxurl != 'undefined' ) ? ajaxurl : Sl.ajaxUrl;

		// Get parents, retrieve as JSON
		$.post( endpoint, {
			action  : 'solar_fill_location',
			postcode: $( targets.postcode ).val()
		}, function ( r )
		{
			if ( r.success )
			{
				for ( var key in targets )
				{
					if ( typeof r.data[key] == 'undefined' )
						continue;

					if ( key == 'suburb' )
					{

						$target = $( targets.suburb );

						// Empty options
						$target.find( 'option' ).remove();

						// Bind related suburbs to select
						$.each( r.data.suburb, function( k, v )
						{
							$target.append(
								$( '<option></option>' )
									.attr( 'value', v )
									.text( v )
							);
						} );
					}
					else
					{
						$( targets[key] ).val( r.data[key]['name'] );
					}

					// That means we're in Gravity Form #1 or #45
					if ( form !== 'auto_fill' )
						$( targets.state ).val( r.data.state.desc );
				}
			}
			else
			{
				// Todo: Add error class to postcode
				alert( 'Can not find location. Please check your postcode' );
			}
		}, 'json' );
	}
} );

// Bubble Effect
jQuery( function( $ )
{
	var index = 0;

	var bubble = setInterval( function()
	{
		index++;

		var lastElement = $('.bubble p').length;

		if ( index === lastElement )
			index = 0;

		$( '.bubble' ).slideDown( 600 ).delay( 4000 ).slideUp( 600, function()
		{
			$('.bubble .bubble-content').html( $('.bubble > p').eq( index ).html() );
		} );

	}, 6000 );

	$( '.bubble .close' ).click( function( e )
	{
		e.preventDefault();

		$('.bubble').hide();

		clearInterval( bubble );

		bubble = 0;
	} );
} );

jQuery( '#agree-term' ).click( function()
{
	jQuery( '#submit-buying-leads' ).toggle( this.checked );
} );

jQuery( function( $ )
{
    // The Ajax URL which application should point to
    var endpoint = ( typeof ajaxurl != 'undefined' ) ? ajaxurl : Sl.ajaxUrl;

    // Cache object. This will save after each ajax look up.
    var cache = {};

    if ( $( '#search-location').length )
    {
	    $( "#search-location" ).autocomplete( {
	        minLength: 2,
	        source: function( request, response )
	        {
	            var term = request.term;

	            if ( term in cache ) {
	                response( cache[ term ] );
	                return;
	            }

	            $.getJSON( endpoint + '?action=autocomplete_location&location=' + term, function( r, status, xhr )
	            {
	                cache[ term ] = r.data;
	                response( r.data );
	            } );
	        },
	        select: function( event, ui )
	        {
	            document.location.href = '/solar-installers/?s2=' + ui.item.label;
	        }
	    } );
	}
} );

jQuery( function( $ )
{
	$( '.gw-go-btn-wrap-inner > a' ).click( function( e )
	{
		var href = $( this ).attr( 'href' );

		if ( typeof window.location.search != 'undefined' && window.location.search != '' )
		{
			var query_string = window.location.search.replace( '?', '&' );

			var win = window.open( href + query_string, '_blank' );
				win.focus();
			
			e.preventDefault();
		}
	} );

	if ( $('#input_1_3').length )
	{
		$('#input_1_3').attr('type', 'tel');
	}
	
	if ( $('#input_1_33').length )
	{
		$('#input_1_33').attr('type', 'tel');
	}
	
	if ( $('#input_63_3').length )
	{
		$('#input_63_3').attr('type', 'tel');
	}
	
	if ( $('.solar-quotes-split').length || $('.solar-quotes-5').length )
	{
		$(window).scroll(function(){
			var scroll = $(window).scrollTop();

			if ( scroll >= 10 )
				$('#content .image-scroll').addClass('image-scroll-fixed');
			else
			 	$('#content .image-scroll').removeClass('image-scroll-fixed');
		} );
	}

} );