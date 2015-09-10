/* global jQuery, rw_utils, Sl, MobileDetect */

/**
 * This file contains common Javascript code for booking page
 * It has to be prepended into 'booking-xxx.js' files
 */

// Global Javascript object of the theme
var SlBooking = SlBooking || {};

/**
 * Booking module
 * This module contains common actions for booking page
 */
SlBooking = $.extend( SlBooking, (function ( $ )
{
	'use strict';

	var mobileDetect = new MobileDetect( window.navigator.userAgent );

	return {
		/**
		 * Show / hide list of available booking resources
		 * Hide list of available booking resources when click anywhere outside
		 *
		 * @return void
		 */
		toggleResources          : function ()
		{
			$( 'body' ).on( 'click', function ( e )
			{
				var $list = $( '.change-resource' ).siblings( 'ul' );

				// When click the link, show/hide list of resources
				if ( $( e.target ).is( '.change-resource' ) )
				{
					e.preventDefault();
					$list.toggleClass( 'hidden' );
				}
				else
				{
					$list.addClass( 'hidden' );
				}
			} );
		},
		/**
		 * Show / hide more guests' info fields
		 *
		 * @return void
		 */
		toggleMoreGuests         : function ()
		{
			$( '.more-guests' ).on( 'click', 'a', function ( e )
			{
				e.preventDefault();
				$( this ).addClass( 'hidden' ).siblings().removeClass( 'hidden' );
				$( '.other-passengers' ).toggleClass( 'hidden' );
			} );
		},
		/**
		 * Add credit card icon
		 *
		 * @return void
		 */
		addCreditCardIcon        : function ()
		{
			$( '[name=card_number]' ).keyup( function ()
			{
				var $this = $( this ),
					$icon = $this.siblings( '.icon' ),
					type = rw_utils.get_cc_type( $this.val() );

				$icon.removeClass( 'visa mastercard amex' );
				if ( 'unknown' != type )
					$icon.addClass( type );
			} );
		},
		/**
		 * Handle actions for agree to terms and conditions checkbox
		 *
		 * @return void
		 */
		agree                    : function ()
		{
			// Check "I agree terms" in modal
			$( '.modal-footer .agree' ).on( 'click', function ( e )
			{
				e.preventDefault();
				$( '[name=agree]' ).prop( 'checked', true ).trigger( 'change' );
			} );
		},
		/**
		 * Toggle pay button
		 * Show pay button only when all info are entered
		 *
		 * @return void
		 */
		togglePayButton          : function ()
		{
			var $contact = $( '.contact' ),
				$payment = $( '.payment' ),
				$pay = $( '.pay' ).closest( '.sl-field' );

			// If we don't have Billing Info panel
			if ( !$payment.length )
			{
				$contact.on( 'change', 'input', function ()
				{
					$pay[SlBooking.validate.validToPay() ? 'removeClass' : 'addClass']( 'hidden' );
				} );
			}
			// If we have Billing Info panel when eWay hosted is activated
			else
			{
				// Toggle next button to Billing Info panel
				$contact.on( 'change', 'input', function ()
				{
					var $next = $contact.find( '.next' ).closest( '.sl-field' );
					$next[SlBooking.validate.validToCheckout() ? 'removeClass' : 'addClass']( 'hidden' );
				} );

				// Toggle pay button in Billing Info panel
				$payment.on( 'change', 'input', function ()
				{
					$pay[SlBooking.validate.validCreditCart() ? 'removeClass' : 'addClass']( 'hidden' );
				} );
			}
		},
		/**
		 * Format currency in booking page in sidebar
		 * @param amount
		 * @param showFloat Show float value or integer?
		 * @return string
		 */
		formatCurrency           : function ( amount, showFloat )
		{
			// Make sure amount is a number
			var output;
			amount = parseFloat( amount );
			if ( isNaN( amount ) )
				amount = 0;
			if ( typeof showFloat == 'undefined' )
				showFloat = true;
			if ( showFloat )
				amount = amount.toFixed( 2 );
			if ( 'left' == Sl.currencyPosition )
				output = Sl.currency + amount;
			else if ( 'right' == Sl.currencyPosition )
				output = amount + Sl.currency;
			else if ( 'left_space' == Sl.currencyPosition )
				output = Sl.currency + ' ' + amount;
			else if ( 'right_space' == Sl.currencyPosition )
				output = amount + ' ' + Sl.currency;

			return output;
		},
		/**
		 * Add CSS classes (.phone-device, .tablet-device) to body tag for customization
		 * Also set the device name to hidden input for statistics analysis
		 *
		 * @return void
		 */
		mobileClass              : function ()
		{
			var device = mobileDetect.phone() ? 'phone' : ( mobileDetect.tablet() ? 'tablet' : '' );

			if ( device )
			{
				$( 'body' ).addClass( device + '-device' );
				$( '[name="device"]' ).val( device );
			}
		},
		/**
		 * Add more guests
		 * @param total    Total guests
		 * @param postType Post type
		 * @return void
		 */
		addMoreGuests            : function ( total, postType )
		{
			// Remove redundant passengers
			$( '.passenger:gt(' + (total - 1) + ')' ).remove();

			// Add more passengers if needed
			var $container = $( '.other-passengers' ),
				$first = $( '.passenger:first' ),
				i = $( '.passenger' ).length,
				$clone;
			for ( ; i < total; i++ )
			{
				$clone = $first.clone();
				$clone.appendTo( $container )
					.prepend( '<h4 class="guest-title">' + Sl[postType].guestTitle + ' ' + ( i + 1 ) + '</h4>' )
					.find( 'input' ).val( '' ).end()
					.find( '.required' ).removeClass( 'required' ).end()
					.find( '.more-guests' ).remove().end()
					.find( '.sl-input-warning' ).remove().end()
					.find( '.name' ).unwrap();
			}

			// Hide them all by default
			$container.addClass( 'hidden' );

			// Update show/hide links
			$( '.guests-hide' ).addClass( 'hidden' );
			$( '.guests-show' )[total < 2 ? 'addClass' : 'removeClass']( 'hidden' );

			/**
			 * Tab index
			 * Start from 400 to make sure it does not coincident with other fields of the form
			 * Note:
			 * - 1st panel (booking details) has tab index 1xx
			 * - 2nd panel (contact info) - 2xx
			 * - 3rd panel (eway hosted) - 3xx
			 * @type int
			 */
			var tabIndex = 400;
			$container.find( '[tabindex]' ).each( function ()
			{
				$( this ).attr( 'tabindex', ++tabIndex );
			} );
		},
		/**
		 * Set jQuery UI date time picker defaults
		 * - Disable the input before show and enable it after
		 * - Add CSS classes for custom styling
		 *
		 * Note: this will set defaults to all date time picker on booking page
		 *
		 * @return void
		 */
		setDatetimePickerDefaults: function ()
		{
			$.datepicker.setDefaults( {
				dateFormat    : 'd/m/yy',
				numberOfMonths: mobileDetect.phone() ? 1 : ( mobileDetect.tablet() ? 2 : 3 )
			} );
		},
		/**
		 * Callback to do before showing date time picker popup:
		 * - Add CSS classes
		 * - Disable the input
		 */
		beforeDatetimePickerShow : function ()
		{
			$( this ).prop( 'disabled', true );

			// Add CSS class to date picker popup for easy styling
			var classes = 'sl-datepicker sl-button';

			// For mobile devices
			if ( mobileDetect.phone() )
			{
				classes += ' phone';
			}
			else if ( mobileDetect.tablet() )
			{
				classes += ' tablet';
			}

			// If we use time picker (slider for now)
			if ( $.fn.datetimepicker )
			{
				classes += ' sl-timepicker-slider';
			}

			$( '#ui-datepicker-div' ).addClass( classes );
		},
		/**
		 * Callback to do after showing date time picker popup:
		 * - Enable the input
		 */
		afterDatetimePickerShow  : function ()
		{
			$( this ).prop( 'disabled', false );
		},
		/**
		 * Toggle payment gateways and change pay button when total amount is 0
		 * (free booking like reservation)
		 */
		checkFreeBooking         : function ( total )
		{
			var $gateway = $( '.payment-gateway' ),
				$payButton = $( '.button.pay' );

			if ( total )
			{
				$gateway.removeClass( 'hidden' );
				$payButton.val( Sl.bookingText );
			}
			else
			{
				/**
				 * Hide payment gateways and set the first payment option checked
				 * to make sure validation work
				 */
				$gateway.addClass( 'hidden' )
					.find( 'input:first' ).prop( 'checked', true );

				$payButton.val( Sl.bookingTextFree );
			}
		}
	};
})( jQuery ) );

/**
 * Panel sub module
 * This module handles all actions like expand / collapse panels, show panel content
 */
SlBooking.panel = (function ( $ )
{
	'use strict';

	return {
		/**
		 * Show active panel content
		 *
		 * @return void
		 */
		showActive: function ()
		{
			$( '.panel.active .panel-content' ).addClass( 'show' );
		},
		/**
		 * Show panel when click 'Next' button
		 *
		 * @return void
		 */
		next      : function ()
		{
			$( '.next' ).click( function ( e )
			{
				e.preventDefault();

				var $panel = $( this ).closest( '.panel' ),
					$nextPanel = $panel.next();

				// Validate passenger name and agreement with terms and conditions for 'Contact' panel
				if ( $panel.hasClass( 'contact' ) )
				{
					if ( !SlBooking.validate.passenger() || !SlBooking.validate.term() || !SlBooking.validate.paymentGateway() )
						return;
				}

				$panel.removeClass( 'active' ).addClass( 'collapse' )
					.find( '.panel-content' ).removeClass( 'show' );

				$nextPanel.addClass( 'active' ).removeClass( 'collapse' )
					.find( '.panel-content' ).addClass( 'show' );
			} );
		},
		/**
		 * Expand panel when click its title
		 *
		 * @return void
		 */
		expand    : function ()
		{
			$( '.booking-form' ).on( 'click', '.collapse > h2', function ( e )
			{
				e.preventDefault();

				var $panel = $( this ).closest( '.panel' );

				$( '.panel' ).each( function()
				{
					var $this = $( this );
					if ( ! $this.find( 'nav' ).hasClass( 'hidden' ) )
					{
						$this.addClass( 'collapse' );
						$this.next().addClass( 'collapse' );
					}
					$this.filter( '.active' ).removeClass( 'active' ).find( '.panel-content' ).removeClass( 'show' );
				} );

				$panel.addClass( 'active' ).removeClass( 'collapse' )
					.find( '.panel-content' ).addClass( 'show' );
			} );
		}
	};
})( jQuery );

/**
 * This code runs when document is ready
 * It calls some methods of booking module
 */
jQuery( function ()
{
	'use strict';

	SlBooking.panel.showActive();

	// Call functions to show/hide panels
	SlBooking.panel.expand();
	SlBooking.panel.next();

	SlBooking.mobileClass();
	SlBooking.setDatetimePickerDefaults();
	SlBooking.toggleResources();
	SlBooking.toggleMoreGuests();
	SlBooking.togglePayButton();
	SlBooking.addCreditCardIcon();
	SlBooking.agree();
} );
