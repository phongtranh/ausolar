/* global jQuery */

/**
 * This file handles actions in Pages > Tours/Accommodations/Rental ... settings page
 */
jQuery( function ( $ )
{
	'use strict';

	/**
	 * When layout is list: show booking resources option
	 * When layout is grid: hide booking resources option and show price tag, booking button options
	 */
	var $bookingResources = $( '.booking-resources' ),
		$bookingInput = $bookingResources.find( 'input[type=checkbox]' ),
		$layout = $( '.listing-type.layout' ),
		$priceBook = $( '.price-book' );
	$layout.on( 'change', 'input', function ()
	{
		if ( 'list' === $( this ).val() )
		{
			$bookingResources.slideDown();
			$bookingInput.trigger( 'change' );
		}
		else
		{
			$bookingResources.slideUp();
			$priceBook.slideDown();
		}
	} );

	/**
	 * If show booking resources, then don't show price tag and booking button
	 * And vise-versa
	 */
	$bookingInput.change( function ()
	{
		$priceBook[$bookingInput.is( ':checked' ) ? 'slideUp' : 'slideDown']();
	} ).trigger( 'change' );

	/**
	 * Trigger layout change AFTER $bookingInput is triggered because it affect $priceBook to be shown/hid
	 */
	$layout.find( 'input:checked' ).trigger( 'change' );

	/**
	 * When map is shown, turn off image
	 */
	$( 'input[name*="_archive_map]"]' ).change( function ()
	{
		var $image = $( 'input[name*="_archive_cat_image]"]' );
		if ( $( this ).is( ':checked' ) )
		{
			$image.prop( 'checked', false );
			$image.trigger( 'change' );
		}
	} );

	/**
	 * When image is shown, turn off map
	 */
	$( 'input[name*="_archive_cat_image]"]' ).change( function ()
	{
		var $map = $( 'input[name*="_archive_map]"]' );
		if ( $( this ).is( ':checked' ) )
		{
			$map.prop( 'checked', false );
			$map.trigger( 'change' );
		}
	} );
} );
