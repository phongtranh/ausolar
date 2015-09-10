/* global jQuery, Sl */

/**
 * This file handles all actions in Tour Edit/Add New page
 */
jQuery( function ( $ )
{
	'use strict';

	var $box = $( '#tour-booking' ),
		$body = $( 'body' );

	/**
	 * Update lead in rate price dropdown
	 * Show only active prices
	 *
	 * @return void
	 */
	function updateLeadInRate()
	{
		$box.on( 'change', '.price-box input[type="checkbox"]', function ()
		{
			var $this = $( this ),
				value = $this.attr( 'id' ).replace( 'tour_detail_price_', '' ).replace( /_[0-9]*$/, '' ),
				label = $this.closest( '.price-box' ).find( '.price-type' ).text(),
				$select = $this.closest( '.sl-row' ).find( 'select' );

			if ( $this.is( ':checked' ) )
			{
				if ( !$select.find( 'option[value="' + value + '"]' ).length )
				{
					$select.append( '<option value="' + value + '">' + label + '</option>' );
				}
			}
			else
			{
				$select.find( 'option[value="' + value + '"]' ).remove();
			}
		} ).find( '.price-box input[type="checkbox"]' ).trigger( 'change' );
	}

	$( '.select2' ).attr( 'id', '' ).select2();

	updateLeadInRate();

	// Add departure
	$box.on( 'click', '.add-departure', function ( e )
	{
		e.preventDefault();
		var $col = $( this ).siblings( '.col:last' ),
			$clone = $col.clone();

		$clone.insertBefore( $col );
		$clone.find( 'input' ).removeClass( 'hasDatepicker' ).attr( 'id', '' ).timepicker();
		$clone.find( '.delete-departure' ).removeClass( 'hidden' );

		$col.find( 'input' ).val( '' );
	} );

	// Remove departure
	$box.on( 'click', '.delete-departure', function ( e )
	{
		e.preventDefault();
		$( this ).parents( '.col' ).remove();
	} );

	// Add upsell item
	$box.on( 'click', '.add-upsell-item', function ( e )
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
		$upsell.slUpdateCheckboxes().find( '.checkbox input' ).prop( 'checked', false ).trigger( 'change' );

		Sl.admin.checkRequired();
	} );

	// Delete upsell item
	$box.delegate( '.delete-upsell-item', 'click', function ( e )
	{
		e.preventDefault();
		$( this ).closest( '.sl-settings' ).remove();
	} );

	// Add booking resource
	Sl.edit.addBookingResource( '.tour_detail' );

	// Update departure type when add booking resource
	$body.on( 'sl_clone_resource', '.tour_detail', function ()
	{
		var $detail = $( this ),
			$departureType = $detail.find( '.departure-type' ),
			departureTypeName = $departureType.attr( 'name' );

		// Daily departures
		$detail.find( '.daily-departure' ).attr( 'data-name', departureTypeName )
			.find( '.col:gt(0)' ).remove();

		// Departures by days
		$detail.find( '.departures-by-days' ).attr( 'data-name', departureTypeName )
			.find( '.departures-by-days-content' ).each( function ()
			{
				$( this ).find( 'col:gt(0)' ).remove();
			} );

		// Departure type, must be done AFTER daily departure and departures by day are done because of 'change' event
		$departureType.val( 'daily' ).trigger( 'change' );

		// Update lead in rate dropdown
		$detail.find( '.tour_detail_lead_in_rate option' ).remove().end()
			.find( '.select2-container' ).remove().end()
			.find( 'select.select2' ).show().removeClass( 'select2-hidden-accessible' ).select2();
	} );
} );
