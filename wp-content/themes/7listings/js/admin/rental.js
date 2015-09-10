/* global jQuery, Sl */

/**
 * This file handles all actions in Rental Edit/Add New page
 */
jQuery( function ( $ )
{
	'use strict';

	var $body = $( 'body' );

	// Add booking resource
	Sl.edit.addBookingResource( '.detail' );

	// Validate max occupancy based on occupancy
	$body.delegate( '.detail_occupancy', 'change', function ()
	{
		var $t = $( this ),
			val = parseInt( $t.val(), 10 ),
			$max = $t.parents( '.col-occupancy' ).find( '.detail_max_occupancy' ),
			optionValue;

		$max.find( 'option' ).removeAttr( 'disabled' );

		$max.find( 'option' ).each( function ()
		{
			optionValue = parseInt( $( this ).html(), 10 );
			if ( optionValue < val )
			{
				$( this ).attr( 'disabled', 'disabled' );
			}
		} );
	} );

	// Add upsell item
	$body.on( 'click', '.add-upsell-item', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			$upsell = $this.closest( '.sl-settings' ),
			$clone = $upsell.clone();

		$clone.insertBefore( $upsell )
			.find( '.add-upsell-item' ).remove().end()
			.find( '.delete-upsell-item' ).removeClass( 'hidden' ).end()
			.find( '.required' ).attr( 'style', '' );

		$upsell.find( ':input' ).each( function ()
		{
			var $input = $( this ),
				name = $input.attr( 'name' );
			name = name.replace( /(.*)\[(.*)\]$/, function ( match, p1, p2 )
			{
				return p1 + '[' + ( parseInt( p2, 10 ) + 1 ) + ']';
			} );
			$input.attr( 'name', name ).val( '' );
		} );

		// Make checkboxes work and and reset their "checked" status to false
		$upsell.slUpdateCheckboxes().find( '.checkbox input' ).prop( 'checked', false ).val( 1 ).trigger( 'change' );

		Sl.admin.checkRequired();
	} );

	// Delete upsell item
	$body.on( 'click', '.delete-upsell-item', function ( e )
	{
		e.preventDefault();
		$( this ).closest( '.sl-settings' ).remove();
	} );
} );
