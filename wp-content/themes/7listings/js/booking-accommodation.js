// Prepend booking-validate.js
// Prepend booking.js

/* global jQuery, Sl, Resource, rw_utils, rw_date, SlBooking, MobileDetect */

/**
 * This file contains all Javascript code for booking accommodation page
 */
jQuery( function ( $ )
{
	'use strict';

	var params = Sl.accommodation, // JS params which sent by PHP
		$summary = $( '.summary' ),
		$from = $( '#checkin' ),
		$to = $( '#checkout' ),
		today = new Date(),
		$guests = $( '#guests' );

	/**
	 * Select from (check-in) date
	 *
	 * @return void
	 */
	function selectFromDate()
	{
		$from.datepicker( {
			minDate      : today,
			beforeShowDay: function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect     : function ( text )
			{
				$summary.find( '.checkin-time' ).removeClass( 'hidden' ).find( '.right' ).text( text );

				// Add ".disable" to "to" datepicker
				$to.prop( 'disabled', true );

				$.post( Sl.ajaxUrl, {
					action  : 'sl_accommodation_get_max_date',
					post_id : Resource.post_id,
					resource: Resource.title,
					date    : text
				}, function ( r )
				{
					$to.prop( 'disabled', false )
						.datepicker( 'option', 'minDate', rw_date.get_obj( text ) );

					if ( r )
						$to.datepicker( 'option', 'maxDate', r );
				} );

				updateTotal();
				toggleNextButton();
			},
			beforeShow   : SlBooking.beforeDatetimePickerShow,
			onClose      : SlBooking.afterDatetimePickerShow
		} );
	}

	/**
	 * Select to (check-out) date
	 *
	 * @return void
	 */
	function selectToDate()
	{
		$to.datepicker( {
			minDate      : today,
			beforeShowDay: function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect     : function ( text )
			{
				$summary.find( '.checkout-time' ).removeClass( 'hidden' ).find( '.right' ).text( text );

				updateTotal();
				toggleNextButton();
			},
			beforeShow   : SlBooking.beforeDatetimePickerShow,
			onClose      : SlBooking.afterDatetimePickerShow
		} );
	}

	/**
	 * Select guest
	 *
	 * @return void
	 */
	function selectGuest()
	{
		// Guests
		$guests.change( function ()
		{
			var $this = $( this ),
				guests = $this.val(),
				$row1 = $summary.find( '.guests' ),
				$row2 = $summary.find( '.extra-guests' );

			if ( -1 == guests )
			{
				$row1.addClass( 'hidden' );
				$row2.addClass( 'hidden' );
			}
			else
			{
				$row1.removeClass( 'hidden' );

				if ( guests <= Resource.occupancy )
				{
					$row2.addClass( 'hidden' );
					$row1.find( '.left' ).text( guests + ' ' + params.guests );
				}
				else
				{
					$row2.removeClass( 'hidden' );
					var extra = guests - Resource.occupancy;

					$row1.find( '.left' ).text( Resource.occupancy + ' ' + params.guests );
					$row2.find( '.left' ).text( '+ ' + extra + ' x ' + params.extra );
				}
			}

			// Add more guests
			SlBooking.addMoreGuests( guests, 'accommodation' );

			updateTotal();
			toggleNextButton();
		} );
	}

	/**
	 * Submit form via ajax
	 * Check for:
	 * - passenger
	 * - agree to terms and conditions
	 * - payment gateway
	 * - credit card info
	 *
	 * @returns void
	 */
	function formSubmit()
	{
		$( '.booking-form' ).submit( function ( e )
		{
			e.preventDefault();

			/**
			 * Validate everything before submit form
			 * Note: we want to validate everything, so all the validation function must run
			 * To do that, we put result at the end of the expression (result = XXX && result)
			 */
			var validate = SlBooking.validate,
				result;

			// Validate everything before submit form
			result = validate.passenger();
			result = validate.term() && result;
			result = validate.paymentGateway() && result;
			result = validate.creditCard() && result;
			if ( !result )
				return;

			$.post( Sl.ajaxUrl, {
				action  : 'sl_accommodation_add_booking',
				_wpnonce: Resource.nonceAddBooking,
				post_id : Resource.post_id,
				resource: Resource.title,
				data    : $( this ).serialize()
			}, function ( r )
			{
				/**
				 * Return of the request has 3 forms:
				 * - an URL: for redirection
				 * - a form (with ID = checkout_form): payment form of Paypal or eWay, just submit that form to redirect user to payment page
				 * - simple string: error message
				 */
				if ( /^http/.test( r ) )
				{
					location.href = r;
					return;
				}
				else if ( -1 === r.indexOf( 'checkout_form' ) )
				{
					alert( r );
					return;
				}

				$( r ).appendTo( 'body' );
				$( '#checkout_form' ).modal();

				setTimeout( function ()
				{
					$( '#checkout_form' ).submit();
				}, 2000 );
			} );
			return false;
		} );
	}

	/**
	 * Toggle next button
	 *
	 * @return void
	 */
	function toggleNextButton()
	{
		var $next = $( '#to-contact' );

		/**
		 * Show next button only when:
		 * - Guest is selected
		 * - Check in/out dates are selected
		 */
		var show = -1 != $guests.val() && $from.val() && $to.val();

		$next.closest( '.sl-field' )[show ? 'removeClass' : 'addClass']( 'hidden' );
	}

	/**
	 * Update total
	 *
	 * @return void
	 */
	function updateTotal()
	{
		var total = 0,
			guests, nights, extra,
			from = $from.val(),
			to = $to.val();

		if ( !from || !to )
			return;

		// Update #nights
		from = rw_date.toUSA( from );
		to = rw_date.toUSA( to );

		nights = rw_date.days_diff( from, to );

		if ( 0 === nights )
			nights = 1;

		if ( nights < 0 )
		{
			alert( params.invalidDates );
			return;
		}

		$( '.nights' ).removeClass( 'hidden' ).find( '.right' ).text( nights );

		$( '.panel .rate' ).addClass( 'hidden' );
		$( 'input[name=amount]' ).val( '' );
		$( '.total .right' ).text( SlBooking.formatCurrency( 0 ) );

		// Get total
		$.post( Sl.ajaxUrl, {
			action  : 'sl_accommodation_get_total_price',
			resource: Resource.title,
			post_id : Resource.post_id,
			from    : from,
			to      : to
		}, function ( r )
		{
			var rate,
				total = parseFloat( r.total ),
				prices = r.prices,
				$right = $summary.find( '.guests' ),
				html = [];

			if ( $guests.length )
			{
				guests = $guests.val();
				if ( -1 != guests && guests > Resource.occupancy )
				{
					extra = Resource.price_extra * (guests - Resource.occupancy);
					total += parseFloat( extra ) * nights;
				}
			}

			// Update hidden field for amount and verify
			$( '[name=amount]' ).val( total );
			$( '[name=verify]' ).val( (total * 3 - 10) + ',' + Resource.nonceAddBooking );

			// Show text on right sidebar
			$( '.total .right' ).text( SlBooking.formatCurrency( total ) );

			// Update Rate
			rate = total / nights;
			$( '.panel .rate' ).removeClass( 'hidden' );
			$( '.panel .rate strong' ).text( parseFloat( rate.toFixed( 2 ) ) );

			for ( var price in prices )
			{
				if ( prices.hasOwnProperty( price ) )
					html.push( SlBooking.formatCurrency( price ) + ' x ' + prices[price] );
			}
			$right.find( '.right' ).html( html.join( '<br>' ) );

			// Rate for extra guests
			if ( -1 != guests && guests > Resource.occupancy )
			{
				$summary.find( '.extra-guests .right' ).html( SlBooking.formatCurrency( extra ) + ' x ' + nights );
			}

			SlBooking.checkFreeBooking( total );
		}, 'json' );
	}

	// Our custom code
	today.setHours( 0, 0, 0, 0 );
	Resource.occupancy = parseInt( Resource.occupancy );
	Resource.price = parseFloat( Resource.price );
	Resource.price_extra = parseFloat( Resource.price_extra );
	Resource.unbookable = Resource.unbookable || [];

	selectFromDate();
	selectToDate();
	selectGuest();

	formSubmit();
} );

