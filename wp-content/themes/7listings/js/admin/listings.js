jQuery( function ( $ )
{
	// Switch between eway shared/hosted payment
	$( '#eway_shared' ).click( function ()
	{
		var checked = $( this ).is( ':checked' );

		if ( checked )
			$( '#eway_hosted' ).removeAttr( 'checked' );
		else
			$( '#eway_hosted' ).attr( 'checked', 'checked' );
	} );

	$( '#eway_hosted' ).click( function ()
	{
		var checked = $( this ).is( ':checked' );

		if ( checked )
			$( '#eway_shared' ).removeAttr( 'checked' );
		else
			$( '#eway_shared' ).attr( 'checked', 'checked' );
	} );
} );
