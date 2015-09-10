/* global jQuery, Sl, Resource, SlBooking */

jQuery( function ( $ )
{
	'use strict';

	// Trigger guest select change to update total
	$( '.passenger-type select' ).trigger( 'change' );

	$( '.done' ).click( function ( e )
	{
		e.preventDefault();

		if ( !SlBooking.validate.passenger() || !SlBooking.validate.term() || !SlBooking.validate.creditCard() )
			return;

		$.post( Sl.ajaxUrl, {
			action  : 'sl_cart_edit',
			_wpnonce: Resource.nonceAddBooking,
			type    : $( this ).data( 'type' ),
			post_id : Resource.post_id,
			resource: Resource.title,
			data    : $( this ).closest( 'form' ).serialize()
		}, function ( r )
		{
			if ( !r.success )
				alert( r.data );
			else
				location.href = r.data;
		}, 'json' );
	} );
} );
