/* global jQuery, Sl */

/**
 * This file handles all actions in Accommodation Edit/Add New page
 */
jQuery( function ( $ )
{
	'use strict';

	// Add booking resource
	Sl.edit.addBookingResource( '.hotel_detail' );

	// Validate max occupancy based on occupancy
	$( 'body' ).delegate( '.hotel_detail_occupancy', 'change', function ()
	{
		var $t = $( this ),
			val = parseInt( $t.val() ),
			$max = $t.parents( '.col-occupancy' ).find( '.hotel_detail_max_occupancy' ),
			optionValue;

		$max.find( 'option' ).removeAttr( 'disabled' );

		$max.find( 'option' ).each( function ()
		{
			optionValue = parseInt( $( this ).html() );
			if ( optionValue < val )
				$( this ).attr( 'disabled', 'disabled' );
		} );
	} );
} );
