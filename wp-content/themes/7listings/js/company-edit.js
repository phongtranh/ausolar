jQuery( function ( $ )
{
	var $postcodes = $( '.service-postcodes' ),
		$radius = $( '.service-radius' ),
		$map = $( '.map' );
	$( 'select[name="service_radius"]' ).change(function ()
	{
		if ( $( this ).val() == 'postcodes' )
		{
			$postcodes.slideDown();
			$radius.slideUp();
			$map.hide();
		}
		else
		{
			$postcodes.slideUp();
			$radius.slideDown();
			$map.show();
		}
	} ).trigger( 'change' );

	// Toggle checkbox
	$( 'body' ).on( 'change', '.checkbox-toggle input', function ()
	{
		var $this = $( this ),
			$parent = $this.closest( '.checkbox-toggle' ),
			$next = $parent.next(),
			effect = $parent.data( 'effect' );

		if ( !effect )
			effect = 'slide';

		if ( 'slide' == effect )
			$next[$this.is( ':checked' ) ? 'slideDown' : 'slideUp']();
		else if ( 'fade' == effect )
			$next[$this.is( ':checked' ) ? 'fadeIn' : 'fadeOut']();
	} );
	$( '.checkbox-toggle input' ).trigger( 'change' );

	$( '.timepicker' ).timepicker();
} );