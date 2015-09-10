// Prepend booking-validate.js
// Prepend booking.js

/* global jQuery, Sl, Resource, rw_utils, rw_date, SlBooking, MobileDetect */

/**
 * This file contains all Javascript code for booking tour page
 */
jQuery( function ( $ )
{
	'use strict';

	var $time = $( '.departure.time' ),
		$summary = $( '.summary' ),
		$guests = $( '.guests' ),
		$upsells = $( '.upsells' ),
		$upsellSelect = $upsells.find( 'select' ),
		$dailyDepart = $( '#daily-depart' );

	/**
	 * Select departure date
	 *
	 * @return void
	 */
	function selectDate()
	{
		$( '.datepicker' ).each( function ()
		{
			var $this = $( this ),
				$row = $summary.find( '.depart' );

			$this.datepicker( {
				dayNamesMin  : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
				beforeShowDay: function ( date )
				{
					var today = new Date(),
						dmy = rw_date.obj_to_euro( date ),
						allowedDays = $this.data( 'days' ),
						day = date.getDay(),
						show;

					today.setHours( 0, 0, 0, 0 );
					show = date >= today && Resource.allocation > 0; // Check for past days
					show = show && $.inArray( dmy, Resource.unbookable ) < 0; // Check for unbookable days
					if ( allowedDays )
					{
						show = show && -1 != allowedDays.toString().indexOf( day ); // Check for allowed days
					}

					return [show, ''];
				},
				onSelect     : function ( text )
				{
					showTime( text );

					// Show depart day in sidebar
					$row.removeClass( 'hidden' ).find( '.day' ).text( text );

					/**
					 * Get scheduled prices
					 * - Update when has scheduled prices
					 * - Revert to backup resource if no scheduled prices
					 */
					$.post( Sl.ajaxUrl, {
						action  : 'sl_tour_get_scheduled_price',
						resource: Resource.title,
						post_id : Resource.post_id,
						date    : text
					}, function ( r )
					{
						if ( r.success )
							$.extend( true, Resource, r.data );
						else
							alert( r.data );

						updateUpsellPrices();
						$upsellSelect.val( -1 ); // Clear selection to update total properly
					}, 'json' );

					// Show passengers
					$.post( Sl.ajaxUrl, {
						action  : 'sl_tour_get_allocation',
						resource: Resource.title,
						post_id : Resource.post_id,
						date    : text
					}, function ( r )
					{
						Resource.allocation = parseInt( r.data.allocation );
						$guests.find( '.sl-input' ).html( r.data.html );
						$( '.passenger-type select' ).trigger( 'change' );

						toggleGuests();
					}, 'json' );
				},
				beforeShow   : SlBooking.beforeDatetimePickerShow,
				onClose      : SlBooking.afterDatetimePickerShow
			} );
		} );
	}

	/**
	 * Toggle time inputs
	 *
	 * Hide time if:
	 * - There are > 1 specific day departures (not daily depart)
	 * - Or there is 1 specific day departures and on that day there's only 1 departure time
	 * - Or (daily depart and there's only 1 departure time)
	 * Else: show time
	 *
	 * Note: daily departure always have an empty value "-"
	 *
	 * @return void
	 */
	function toggleTime()
	{
		var $specificDay = $time.find( '.specific-day-depart' ),
			hide = $specificDay.length >= 2 ||
				( $specificDay.length == 1 && $specificDay.children().length < 3 ) ||
				( $dailyDepart.length && $dailyDepart.children().length < 3 );

		$time[hide ? 'addClass' : 'removeClass']( 'hidden' );
	}

	/**
	 * Show time input for users to choose from
	 * If there's only 1 option for time, just select it and keep it hidden
	 *
	 * @param date Selected date from date picker
	 *
	 * @return void
	 */
	function showTime( date )
	{
		var selectedOption;

		/**
		 * Show time picker if there are > 1 time slots
		 * If there's only 1 time slot then keep it hidden and trigger click
		 */

		// If daily departure
		if ( $dailyDepart.length > 0 )
		{
			// Show options if there are > 1 departures (note: there's an empty option "-"), else choose it
			if ( $dailyDepart.children().length > 2 )
			{
				$time.removeClass( 'hidden' );
			}
			// If there is only one departure, set value
			else
			{
				selectedOption = $dailyDepart.children( ':last' ).val();
				$dailyDepart.val( selectedOption ).trigger( 'change' );
			}
			return;
		}

		// Specific day departures
		var day = rw_date.get_obj( date ).getDay(),
			$specificDay = $( '[data-day="' + day + '"]' );

		if ( $specificDay.length > 0 )
		{
			// Show options if there are > 1 departures (note: there's an empty option "-"), else choose it
			if ( $specificDay.children().length > 2 )
			{
				$( '.specific-day-depart' ).addClass( 'hidden' );
				$specificDay.removeClass( 'hidden' );
				$time.removeClass( 'hidden' );
			}
			// If there is only one departure, set value
			else
			{
				selectedOption = $specificDay.children( ':last' ).val();
				$specificDay.val( selectedOption ).trigger( 'change' );
			}
		}
	}

	/**
	 * Select departure time
	 *
	 * @return void
	 */
	function selectTime()
	{
		// Select time from select dropdown for specific day departure and daily departure
		$time.on( 'change', '.time-select', function ()
		{
			var $this = $( this ),
				time = $this.val() ? $this.children( ':selected' ).text() : '';

			afterSelectTime( time );
		} );

		/**
		 * For custom depart we use 2 selects for hour and minute
		 * We need to update selected value to a hidden input when these selects change
		 */
		$time.on( 'change', '.time-input', function ()
		{
			var hour = $( '#hour-input' ).val(),
				minute = $( '#minute-input' ).val(),
				time = hour && minute ? (hour + ':' + minute) : '';

			$( '[name="custom_depart"]' ).val( time );
			afterSelectTime( time );
		} );
	}

	/**
	 * After select time:
	 * - Display time in sidebar
	 * - Toggle guest select
	 * - Toggle Next button
	 *
	 * @param time Time in text format
	 */
	function afterSelectTime( time )
	{
		if ( time )
		{
			$summary.find( '.depart' ).removeClass( 'hidden' ).find( '.time' ).text( time );
		}
		toggleGuests();
		toggleNextButton();
	}

	/**
	 * Select guest
	 *
	 * @return void
	 */
	function selectGuest()
	{
		$guests.on( 'change', '.passenger-type select', function ()
		{
			var $this = $( this ),
				type = $this.attr( 'name' ),
				amount = $this.val(),
				$passenger = $summary.find( '.passengers' ),
				$row = $summary.find( '.' + type ),
				$label = $row.find( '.left' ),
				$price = $row.find( '.right' ),
				hasValue = false,
				total = 0,
				priceLabel,
				price,
				$passengerType = $( '.passenger-type select' );

			if ( -1 == amount )
			{
				$label.text( '0 ' + type );
				$price.text( SlBooking.formatCurrency( 0 ) );
				$row.addClass( 'hidden' );

				$passengerType.each( function ()
				{
					if ( -1 != $( this ).val() )
						hasValue = true;
				} );
				if ( !hasValue )
				{
					$passenger.addClass( 'hidden' );
				}
			}
			else
			{
				$label.text( amount + ' ' + type );
				$row.removeClass( 'hidden' );
				priceLabel = 'adult';
				switch ( type )
				{
					case 'children':
						priceLabel = 'child';
						break;
					case 'seniors':
						priceLabel = 'senior';
						break;
					case 'families':
						priceLabel = 'family';
						break;
					case 'infants':
						priceLabel = 'infant';
						break;
					default:
				}
				priceLabel = 'price_' + priceLabel;
				price = Resource[priceLabel] ? parseFloat( Resource[priceLabel] ) : 0;
				$price.text( SlBooking.formatCurrency( amount * price ) );

				$passenger.removeClass( 'hidden' );
			}

			// Add/delete passengers if needed
			$passengerType.each( function ()
			{
				if ( -1 != $( this ).val() )
					total += parseInt( $( this ).val() );
			} );

			// Add more guests
			SlBooking.addMoreGuests( total, 'tour' );

			// Check if total guests exceed resource allocation
			if ( countGuests() > Resource.allocation )
			{
				var message = Sl.tour.exceedAllocation.replace( '%d', Resource.allocation );
				if ( $guests.find( '.error.exceed-allocation' ).length )
				{
					$guests.find( '.error.exceed-allocation' ).html( message ).removeClass( 'hidden' );
				}
				else
				{
					$guests.find( '.sl-input' ).append( '<div class="error exceed-allocation">' + message + '</div>' );
				}
			}
			else
			{
				$guests.find( '.error.exceed-allocation' ).addClass( 'hidden' );
			}

			// Trigger change for upsells, in case of 'multiply'
			$upsellSelect.trigger( 'change' );

			updateTotal();
			toggleUpsells();
			toggleNextButton();
		} );
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
				action  : 'sl_tour_add_booking',
				_wpnonce: Resource.nonceAddBooking,
				post_id : Resource.post_id,
				resource: Resource.title,
				data    : $( '.booking-form' ).serialize()
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
	 * Check if date and time are entered
	 *
	 * @return bool
	 */
	function isTimeSelected()
	{
		var $date = $( '[name="day"]' ),
			date = $date.val();

		/**
		 * Check date input
		 */
		if ( $date.length && !date )
			return false;

		/**
		 * Check time input
		 */
		var $time = $( '.departure.time' ),
			hasTime = true;

		/**
		 * Check specific day departure
		 * Specific day departure has multiple select, we need to check it separately
		 * Note: for specific day departure, we always have 'date' selected
		 */
		if ( $time.find( '[data-day]' ).length )
		{
			date = rw_date.get_obj( date );

			var day = date.getDay(),
				$specificDay = $( '[data-day="' + day + '"]' );

			if ( $specificDay.length && !$specificDay.val() )
				hasTime = false;
		}
		/**
		 * For other type of departure time, we need to check inputs (input or select tag) which has 'name' attribute
		 * All inputs need to have value
		 */
		else
		{
			$time.find( ':input[name]' ).each( function ()
			{
				if ( !$( this ).val() )
					hasTime = false;
			} );
		}

		return hasTime;
	}

	/**
	 * Check if guest is selected
	 *
	 * @return bool
	 */
	function isGuestSelected()
	{
		var selected = false;

		$( '.passenger-type select' ).each( function ()
		{
			if ( -1 != $( this ).val() )
				selected = true;
		} );

		return selected;
	}

	/**
	 * Count total number of guests
	 *
	 * @return int
	 */
	function countGuests()
	{
		var total = 0;

		$( '.passenger-type select' ).each( function ()
		{
			var $this = $( this ),
				val = $this.val(),
				type = $this.attr( 'name' );

			if ( -1 == val )
				return;

			total += val * ( 'families' == type ? 4 : 1 );
		} );

		return total;
	}

	/**
	 * Check if date and time entered, then show guests select, else hide it
	 *
	 * @return void
	 */
	function toggleGuests()
	{
		$guests[isTimeSelected() ? 'removeClass' : 'addClass']( 'hidden' );
		toggleUpsells();
	}

	/**
	 * Show upsells only after guests are selected
	 *
	 * @return void
	 */
	function toggleUpsells()
	{
		if ( isGuestSelected() && !$guests.hasClass( 'hidden' ) )
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
	 * Update upsell prices
	 * When a date is selected, upsell prices are updated from ajax request
	 * We need to update to the frontend
	 *
	 * @return void
	 */
	function updateUpsellPrices()
	{
		for ( var i = Resource.upsell_prices.length; i--; )
		{
			$( '[name=upsell_' + i + ']' ).parent().find( '.amount' ).text( Resource.upsell_prices[i] );
		}
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
		 * Use jQuery trigger to allow plugins to change [data-hide] attribute of the next button
		 * This attribute will be used to show/hide the button
		 */
		$next.trigger( 'sl-next-toggle' );

		/**
		 * Show next button only when:
		 * - Guest is selected
		 * - Total guests is not greater than resource allocation
		 * - Time is selected
		 * - [data-hide] attribute is false. This attribute is empty by default, but plugins can use it to toggle next
		 *   button with more conditions (like 7 Tour - Min Guest plugin)
		 */
		var show = isGuestSelected() && countGuests() <= Resource.allocation && isTimeSelected() && !$next.data( 'hide' );

		$next.closest( '.sl-field' )[show ? 'removeClass' : 'addClass']( 'hidden' );
	}

	/**
	 * Update total
	 *
	 * @return void
	 */
	function updateTotal()
	{
		var totalGuests = 0,
			totalUpsells = 0,
			numberOfGuests = 0,
			value,
			types = ['adult', 'child', 'senior', 'family', 'infant'],
			$selector,
			price;

		for ( var i = types.length; i--; )
		{
			$selector = $( '#guest-' + types[i] );
			if ( !$selector.length )
				continue;

			value = parseInt( $selector.val() );
			price = Resource['price_' + types[i]] ? parseFloat( Resource['price_' + types[i]] ) : 0;
			if ( -1 != value && price )
			{
				totalGuests += value * price;
				numberOfGuests += value;
			}
		}

		$upsellSelect.each( function ()
		{
			var $this = $( this ),
				index = $this.attr( 'name' ).split( '_' )[1],
				amount = $this.val(),
				price;

			if ( -1 != amount && Resource.upsell_prices[index] )
			{
				price = parseFloat( Resource.upsell_prices[index] );
				if ( Resource.upsell_multipliers[index] )
					price *= numberOfGuests;
				totalUpsells += amount * price;
			}
		} );

		var total = totalGuests + totalUpsells;

		// Update hidden field for amount and verify
		$( '[name=amount]' ).val( total );
		$( '[name=verify]' ).val( (total * 3 - 10) + ',' + Resource.nonceAddBooking );

		// Show text on right sidebar
		$( '.total_guests' ).removeClass( 'hidden' )
			.find( '.right' ).text( SlBooking.formatCurrency( totalGuests ) );
		$( '.total_upsells' ).removeClass( 'hidden' )
			.find( '.right' ).text( SlBooking.formatCurrency( totalUpsells ) );
		$( '.total .right' ).text( SlBooking.formatCurrency( total ) );

		SlBooking.checkFreeBooking( total );
	}

	selectDate();
	toggleTime();
	selectTime();

	selectGuest();
	selectUpsells();

	formSubmit();
} );
