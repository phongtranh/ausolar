
jQuery( function( $ )
{
	var $body = $( 'body' );

	// Pay
	$body.on( 'click', '.membership-pay', function( e )
	{
		e.preventDefault();

		$.post( SlCompanyPayment.ajaxUrl, {
			action: 'sl_company_pay',
			nonce : SlCompanyPayment.noncePay
		}, function( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			// Redirect to Paypal
			$( r.data ).appendTo( 'body' );
			$( '#pay-form' ).modal();
			setTimeout( function ()
			{
				$( '#pay-form' ).submit();
			}, 2000 );
		} );
	} );

	// Renew
	$body.on( 'click', '.membership-renew', function( e )
	{
		e.preventDefault();

		$.post( SlCompanyPayment.ajaxUrl, {
			action: 'sl_company_renew',
			time: $( this ).data( 'time' ),
			nonce : SlCompanyPayment.nonceRenew
		}, function( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			// Redirect to Paypal
			$( r.data ).appendTo( 'body' );
			$( '#renew-form' ).modal();
			setTimeout( function ()
			{
				$( '#renew-form' ).submit();
			}, 2000 );
		} );
	} );

	// Renew
	$body.on( 'click', '.membership-upgrade', function( e )
	{
		e.preventDefault();

		$.post( SlCompanyPayment.ajaxUrl, {
			action: 'sl_company_upgrade',
			nonce : SlCompanyPayment.nonceUpgrade,
			type  : $( this ).data( 'type' ),
			time  : $( this ).data( 'time' )
		}, function( r )
		{
			if ( !r.success )
			{
				alert( r.data );
				return;
			}

			// Redirect to Paypal
			$( r.data ).appendTo( 'body' );
			$( '#upgrade-form' ).modal();
			setTimeout( function ()
			{
				$( '#upgrade-form' ).submit();
			}, 2000 );
		} );
	} );
} );