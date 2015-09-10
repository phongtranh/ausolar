jQuery( function( $ )
{
	// Hide Up & Cross Sells if upsells and crosssells checkbox are not checked
	$( '.sells-checkbox input' ).on( 'change', function()
	{
		var $settings = $( '.sells-sub-settings' );

		if ( 0 == $( '.sells-checkbox input[type="checkbox"]:checked' ).length )
			$settings.addClass( 'hidden' );
		else
			$settings.removeClass( 'hidden' );
	} );

	/*
	$( '#product_upsells, #product_related' ).change( function()
	{
		var checked = $( '#product_upsells' ).is( ':checked' ) || $( '#product_related' ).is( ':checked' ),
			$p = $( this ).closest( 'p' ).siblings( '.single.columns' );

		checked ? $p.slideDown() : $p.slideUp();
	} );
	*/
} );
