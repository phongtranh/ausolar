jQuery( function ( $ )
{
	var $span = $( '#terms-cond-update' );
	$( '#terms-cond-display' ).click( function ( e )
	{
		e.preventDefault();

		$.post( ajaxurl, {
			action: 'solar_leads_update',
			nonce : Solar.nonceUpdate
		}, function ( r )
		{
			if ( r.success )
				$span.text( r.data );
			else
				alert( r.data );
		}, 'json' );
	} );
} );