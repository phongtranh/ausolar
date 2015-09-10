/* global jQuery, rw_utils, Sl */

/**
 * This file contains booking validation module for booking page
 * It has to be prepended into 'booking-xxx.js' files
 */

// Global Javascript object of the theme
var SlBooking = SlBooking || {};

/**
 * Booking validation module
 * This module validates passenger, credit cart, term, payment gateways
 */
SlBooking.validate = (function ( $ )
{
	'use strict';

	// Store validation error messages for quick reference
	var messages = Sl.bookingErrors;

	/**
	 * Wrapper checking function that calls the individual check (callback).
	 * It passes the $error div to that callback for adding error message.
	 * This function uses the result from the callback function and show / hide the error wrapper div.
	 *
	 * @param   errorSelector CSS selector for error message
	 * @param   callback      Callback function that performs individual check
	 * @returns bool          True if individual check is valid, false otherwise
	 */
	function check( errorSelector, callback )
	{
		var $error = $( errorSelector ),
			$errorWrapper = $error.closest( '.error-box' ),
			ok;

		$error.html( '' );
		ok = callback( $error );
		$errorWrapper[ok ? 'addClass' : 'removeClass']( 'hidden' );
		return ok;
	}

	/**
	 * Make sure first passenger enters his name and email
	 *
	 * @return bool
	 */
	function checkPassenger( $error )
	{
		var $firstPassenger = $( '.passenger:first' ),
			ok = true,
			name = true;

		// Check both first name and last name
		$firstPassenger.find( '.name' ).each( function ()
		{
			if ( !$( this ).val() )
				name = false;
		} );
		if ( !name )
		{
			$error.append( '<p>' + messages.name + '</p>' );
			ok = false;
		}

		var email = $firstPassenger.find( '.email' ).val();
		if ( !email )
		{
			$error.append( '<p>' + messages.email + '</p>' );
			ok = false;
		}
		else if ( email && !rw_utils.is_email( email ) )
		{
			$error.append( '<p>' + messages.invalidEmail + '</p>' );
			ok = false;
		}
		return ok;
	}

	/**
	 * Make sure first passenger enters his name and email
	 *
	 * @return bool
	 */
	function validPassenger()
	{
		var $firstPassenger = $( '.passenger:first' ),
			email = $firstPassenger.find( '.email' ).val();

		return $firstPassenger.find( '.name.first' ).val() && $firstPassenger.find( '.name.last' ).val() && email && rw_utils.is_email( email );
	}

	/**
	 * Make sure user agrees with terms and conditions
	 *
	 * @return bool
	 */
	function checkTerm( $error )
	{
		var $agree = $( '[name=agree]' );
		if ( $agree.length && !$agree.is( ':checked' ) )
		{
			$error.html( '<p>' + messages.term + '</p>' );
			return false;
		}
		return true;
	}

	/**
	 * Make sure user agrees with terms and conditions
	 *
	 * @return bool
	 */
	function validTerm()
	{
		var $agree = $( '[name=agree]' );
		return !$agree.length || $agree.is( ':checked' );
	}

	/**
	 * Make sure passenger enter credit card information
	 *
	 * @return bool
	 */
	function checkCreditCard( $error )
	{
		var ok = true;

		// No billing panel, return true
		if ( !$( '.payment' ).length )
			return ok;

		if ( !$( '[name=card_holders_name]' ).val() )
		{
			$error.append( '<p>' + messages.carName + '</p>' );
			ok = false;
		}
		if ( !$( '[name=card_number]' ).val() )
		{
			$error.append( '<p>' + messages.cardNumber + '</p>' );
			ok = false;
		}
		if ( !$( '[name=card_expiry_month]' ).val() )
		{
			$error.append( '<p>' + messages.cardExpiryMonth + '</p>' );
			ok = false;
		}
		if ( !$( '[name=card_expiry_year]' ).val() )
		{
			$error.append( '<p>' + messages.cardExpiryYear + '</p>' );
			ok = false;
		}
		if ( !$( '[name=card_cvn]' ).val() )
		{
			$error.append( '<p>' + messages.cardCvn + '</p>' );
			ok = false;
		}
		return ok;
	}

	/**
	 * Make sure passenger enter credit card information
	 *
	 * @return bool
	 */
	function validCreditCard()
	{
		// No billing panel, return true
		if ( !$( '.payment' ).length )
			return true;

		return $( '[name=card_holders_name]' ).val() && $( '[name=card_number]' ).val() && $( '[name=card_expiry_month]' ).val() && $( '[name=card_expiry_year]' ).val() && $( '[name=card_cvn]' ).val();
	}

	/**
	 * Make sure user select payment gateway
	 *
	 * @return bool
	 */
	function checkPaymentGateway( $error )
	{
		var $gateways = $( 'input[name=payment_gateway]' );
		if ( $gateways.length && !$gateways.filter( ':checked' ).length )
		{
			$error.html( '<p>' + messages.payment + '</p>' );
			return false;
		}
		return true;
	}

	/**
	 * Make sure user select payment gateway
	 *
	 * @return bool
	 */
	function validPaymentGateway()
	{
		var $gateways = $( 'input[name=payment_gateway]' );
		return !$gateways.length || $gateways.filter( ':checked' ).length;
	}

	// Return the module object with public functions
	return {
		passenger      : function ()
		{
			return check( '.error-passenger', checkPassenger );
		},
		term           : function ()
		{
			return check( '.error-terms', checkTerm );
		},
		creditCard     : function ()
		{
			return check( '.error-cc', checkCreditCard );
		},
		paymentGateway : function ()
		{
			return check( '.error-gateways', checkPaymentGateway );
		},
		validToCheckout: function ()
		{
			return validPassenger() && validTerm();
		},
		validCreditCart: validCreditCard,
		validToPay     : function ()
		{
			return validPassenger() && validTerm() && validPaymentGateway() && validCreditCard();
		}
	};
})( jQuery );
