jQuery( function ( $ )
{
	'use strict';

	var url = '?post_type=booking&export=csv',
		button = '<p><a href="' + url + '" class="button-primary export">Export To CSV</a></p>';

	$( '#posts-filter' ).append( button );

	// Add class for table
	$( '.wp-list-table' ).addClass( 'booking' );

	// Move total overview to under heading
	$( '#total-overview' ).appendTo( '.wrap > h2:first' );

	var $body = $( 'body' );

	// Modal window for sending emails
	$body.on( 'click', '.email', function ()
	{
		var $this = $( this ),
			booking_id = $this.data( 'booking_id' ),
			$modal = $( '#' + booking_id + '-email-form' ),
			$spinner = $modal.find( '.spinner' ),
			$email = $modal.find( 'input' );

		$modal.removeClass( 'hidden' );
		$email.focus();

		$modal.on( 'click', 'button', function ( e )
		{
			e.preventDefault();

			var email = $email.val();
			if ( !email )
			{
				alert( SlBookingList.noEmail );
				$email.focus();
				return;
			}
			if ( !rw_utils.is_email( email ) )
			{
				alert( SlBookingList.invalidEmail );
				$email.focus();
				return;
			}

			$spinner.css( 'display', 'inline-block' );
			$.post( ajaxurl, {
				action    : 'sl_booking_send_email',
				booking_id: booking_id,
				type      : 'custom',
				email     : email
			}, function ()
			{
				$spinner.hide();
				$modal.addClass( 'hidden' );
			} );
		} );
	} );

	// Close modal window
	$body.on( 'click', '.media-modal-close', function ()
	{
		$( this ).parent().addClass( 'hidden' );
	} );

	// Toggle payment status
	$body.on( 'click', '.toggle-paid', function ( e )
	{
		e.preventDefault();
		if ( !confirm( SlBookingList.confirm ) )
			return;

		var $this = $( this ),
			paid = parseInt( $this.data( 'paid' ) );
		$.post( ajaxurl, {
			action: 'sl_toggle_paid',
			id    : $this.data( 'id' ),
			paid  : paid
		}, function ( r )
		{
			if ( !r.success )
				return;
			paid = 1 - paid;
			$this.data( 'paid', paid ).removeClass( 'yes-sm cross-sm' ).addClass( paid ? 'yes-sm' : 'cross-sm' );
		}, 'json' );
	} );
} );
