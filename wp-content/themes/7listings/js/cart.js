/* global jQuery, Sl */

/**
 * This file handles all cart actions, including:
 * - Get cart content via ajax
 * - Add item to cart
 * - Remove item from cart
 * - Checkout
 *
 * It doesn't contains code for processing cart booking page, which is stored in 'cart-edit.js' file
 *
 * Can re-use $( '#cart' ) because it will be replaced by ajax content
 * Same as $count and $items
 */
jQuery( function ( $ )
{
	'use strict';

	// Check if cart is active, if not - do nothing
	if ( !Sl.hasOwnProperty( 'cart' ) || !parseInt( Sl.cart ) )
		return;

	// Show payment area or not
	paymentArea();

	// Update cart content via ajax
	$.post( Sl.ajaxUrl, {
		action: 'sl_cart_show'
	}, function ( r )
	{
		if ( !r.success )
			return;

		$( '#cart' ).replaceWith( r.data );
		toggleCart();
	} );

	// Add to cart
	$( '.add-to-cart' ).click( function ()
	{
		var $this = $( this );

		$this.closest( '.modal' ).modal( 'hide' );

		$.post( Sl.ajaxUrl, {
			action  : 'sl_cart_add',
			post    : $this.data( 'post' ),
			resource: $this.data( 'resource' )
		}, function ( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			var $cart = $( '#cart' ),
				$count = $cart.find( '.count' ),
				$items = $cart.find( 'ul' ),
				$title = $cart.find( '.title' ),
				$button = $cart.find( '.booking' ),
				num = parseInt( $count.text() );

			$title.attr( 'href', r.data.url );
			$button.attr( 'href', r.data.url );

			$count.text( num + 1 );
			$items.append( r.data.item );

			toggleCart();
		}, 'json' );

		return false;
	} );

	// Remove item from cart
	$( '.remove-from-cart' ).click( function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			$tr = $this.closest( 'tr' );

		toggleCart();

		$.post( Sl.ajaxUrl, {
			action: 'sl_cart_remove',
			index : $this.data( 'index' )
		}, function ( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			var $cart = $( '#cart' ),
				$count = $cart.find( '.count' ),
				$items = $cart.find( 'ul' ),
				num = parseInt( $count.text() ),
				index = $( '.bookings tbody tr' ).index( $tr );

			// Remove item from booking table and cart
			$items.find( 'li' ).eq( index ).remove();
			$tr.remove();

			// Update cart quantity
			$count.text( num - 1 );

			// Show payment area or not
			paymentArea();

			toggleCart();
		}, 'json' );
	} );

	$( 'body.cart' ).on( 'change', 'input[name=payment_gateway]', function ( e )
	{
		e.preventDefault();

		var show = true;

		if ( !$( 'input[name=payment_gateway]:checked' ).length || !validateBookings() )
			show = false;

		$( '.pay' ).closest( '.sl-field' )[show ? 'removeClass' : 'addClass']( 'hidden' );
	} );

	// Checkout for only cart page
	$( 'body.cart .pay' ).click( function ( e )
	{
		e.preventDefault();

		if ( !validateBookings() )
			return;

		$.post( Sl.ajaxUrl, {
			action         : 'sl_cart_book',
			payment_gateway: $( 'input[name=payment_gateway]:checked' ).val()
		}, function ( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			r = r.data;

			// If returned text is an URL, just redirect to that page
			// Used in eway hosted payment
			if ( /^http/.test( r ) )
			{
				location.href = r;
				return;
			}

			$( r ).appendTo( 'body' );
			$( '#checkout_form' ).modal();

			setTimeout( function ()
			{
				$( '#checkout_form' ).submit();
			}, 2000 );

		}, 'json' );
	} );

	var $gateways = $( 'input[name=payment_gateway]' );
	$gateways.change( function ()
	{
		$gateways.parent().removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
	} );

	/**
	 * Check booking information
	 *
	 * @return bool
	 */
	function validateBookings()
	{
		return $( '.bookings tbody tr:not(.has-details)' ).length ? false : true;
	}

	/**
	 * Total items in cart
	 *
	 * @return bool
	 */
	function total()
	{
		return $( '.bookings tbody tr' ).length;
	}

	/**
	 * Display or hide payment area
	 */
	function paymentArea()
	{
		var show = true;

		if ( 0 == total() || !validateBookings() )
			show = false;

		$( '.payment-area' )[show ? 'removeClass' : 'addClass']( 'hidden' );
	}

	/**
	 * Display or hide cart
	 *
	 * @return void
	 */
	function toggleCart()
	{
		var $cart = $( '#cart' );
		$cart[parseInt( $cart.find( '.count' ).text() ) ? 'show' : 'hide']();
	}
} );
