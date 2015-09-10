jQuery( function( $ )
{
	var $message = $( '#ajax-message' ),
		$form = $( '#signup-form' );
	$form.submit( function( e )
	{
		e.preventDefault();

		$.post( SlCompany.ajaxUrl, {
			action: 'sl_company_signup',
			nonce : SlCompany.nonce,
			sl_nonce_save_company: SlCompany.nonceSave,
			data  : $( this ).serialize()
		}, function( r )
		{
			// Error when signup
			if ( !r.success )
			{
				$message.removeClass( 'alert-success' ).addClass( 'alert alert-error' ).html( r.data );
				return;
			}

			// Signup success, user paid for membership
			if ( parseInt( r.data.paid ) )
			{
				$message.removeClass( 'alert-error' ).addClass( 'alert alert-success' ).html( r.data.message );
				window.location = r.data.redirect;
				return;
			}

			// Signup success, user has not paid for membership, redirect to Paypal
			$( r.data.form ).appendTo( 'body' );
			$( '#checkout-form' ).modal();
			setTimeout( function ()
			{
				$( '#checkout-form' ).submit();
			}, 2000 );
		} );
	} );
} );