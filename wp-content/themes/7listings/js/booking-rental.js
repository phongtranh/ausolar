// Prepend booking-validate.js
// Prepend booking.js

/* global jQuery, Sl, Resource, rw_utils, rw_date, SlBooking, MobileDetect */

/**
 * This file contains all Javascript code for booking rental page
 */
jQuery( function ( $ )
{
	'use strict';

	var params = Sl.rental, // JS params which sent by PHP
		$summary = $( '.summary' ),
		$from = $( '#checkin' ),
		$to = $( '#checkout' ),
		today = new Date(),
		$upsells = $( '.upsells' ),
		$upsellSelect = $upsells.find( 'select' );

	/**
	 * Select from (check-in) date
	 *
	 * @return void
	 */
	function selectFromDate()
	{
		$from.datetimepicker( {
			minDate      : today,
			stepMinute   : 5,
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
					action  : 'sl_rental_get_max_date',
					post_id : Resource.post_id,
					resource: Resource.title,
					date    : text
				}, function ( r )
				{
					$to.prop( 'disabled', false )
						.datepicker( 'option', 'minDate', rw_date.get_obj( text.split(' ')[0] ) );

					if ( r )
						$to.datepicker( 'option', 'maxDate', r );

					toggleUpsells();
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
		$to.datetimepicker( {
			minDate      : today,
			stepMinute   : 5,
			beforeShowDay: function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect     : function ( text )
			{
				$summary.find( '.checkout-time' ).removeClass( 'hidden' ).find( '.right' ).text( text );

				toggleUpsells();
				updateTotal();
				toggleNextButton();
			},
			beforeShow   : SlBooking.beforeDatetimePickerShow,
			onClose      : SlBooking.afterDatetimePickerShow
		} );
	}

	/**
	 * Show upsells only after dates are selected
	 *
	 * @return void
	 */
	function toggleUpsells()
	{
		if ( $from.val() && $to.val() )
		{
			$upsells.removeClass( 'hidden' );
		}
		else
		{
			$upsells.addClass( 'hidden' );
			$upsellSelect.val( -1 ); // Clear selection to update total properly
		}
	}

	/**
	 * Callback function when select upsells
	 *
	 * @return void
	 */
	function selectUpsells()
	{
		$upsellSelect.change( function ()
		{
			var $this = $( this ),
				name = $this.attr( 'name' ),
				index = name.split( '_' )[1],
				amount = $this.val(),
				$row = $summary.find( '.' + name ),
				$label = $row.find( '.left' ),
				$selector = $row.find( '.right' ),
				hasValue = false,
				$options = $summary.find( '.options' ),
				price = Resource.upsell_prices[index] ? parseFloat( Resource.upsell_prices[index] ) : 0,
				noGuests = 0;

			if ( -1 == amount )
			{
				$selector.text( SlBooking.formatCurrency( 0 ) );
				$label.text( Resource.upsell_items[index] );
				$row.addClass( 'hidden' );

				$upsellSelect.each( function ()
				{
					if ( -1 != $( this ).val() )
						hasValue = true;
				} );
				if ( !hasValue )
					$options.addClass( 'hidden' );
			}
			else
			{
				if ( Resource.upsell_multipliers && Resource.upsell_multipliers[index] )
				{
					$( '.guests select' ).each( function ()
					{
						var val = parseInt( $( this ).val() );
						if ( -1 != val )
							noGuests += val;
					} );
					price *= noGuests;
				}
				$selector.text( SlBooking.formatCurrency( amount * price ) );
				$label.text( amount + ' x ' + Resource.upsell_items[index] );

				$row.removeClass( 'hidden' );

				$options.removeClass( 'hidden' );
			}

			updateTotal();
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
				action  : 'sl_rental_add_booking',
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
		 * - Dates are selected
		 */
		var show = $from.val() && $to.val();

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
			nights,
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

		$( 'input[name=amount]' ).val( '' );
		$( '.total .right' ).text( SlBooking.formatCurrency( 0 ) );

		var totalUpsells = 0;
		$( '.upsells select' ).each( function ()
		{
			var $this = $( this ),
				index = $this.attr( 'name' ).split( '_' )[1],
				amount = $this.val(),
				price;

			if ( -1 != amount && Resource.upsell_prices[index] )
			{
				price = Resource.upsell_prices[index];
				totalUpsells += parseFloat( amount * price );
			}
		} );

		// Get total
		$.post( Sl.ajaxUrl, {
			action  : 'sl_rental_get_total_price',
			resource: Resource.title,
			post_id : Resource.post_id,
			from    : from,
			to      : to
		}, function ( r )
		{
			var total = parseFloat( r.total ),
				prices = r.prices,
				html = [];

			total += totalUpsells;

			// Update hidden field for amount and verify
			$( '[name=amount]' ).val( total );
			$( '[name=verify]' ).val( (total * 3 - 10) + ',' + Resource.nonceAddBooking );

			// Show text on right sidebar
			$( '.total .right' ).text( SlBooking.formatCurrency( total ) );

			for ( var price in prices )
			{
				if ( prices.hasOwnProperty( price ) )
					html.push( SlBooking.formatCurrency( price ) + ' x ' + prices[price] );
			}
			$summary.find( '.nights .right' ).html( html.join( '<br>' ) );

			SlBooking.checkFreeBooking( total );
		}, 'json' );
	}

	// Our custom code
	today.setHours( 0, 0, 0, 0 );

	selectFromDate();
	selectToDate();
	selectUpsells();

	formSubmit();
} );
