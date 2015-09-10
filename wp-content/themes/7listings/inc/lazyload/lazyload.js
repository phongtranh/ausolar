jQuery( function ( $ )
{
	'use strict';

	// Lazyload
	$( 'img' ).each( function ()
	{
		var $this = $( this ),
			elements = ['.slider', '#slider', '#company-logos', '.logo'];
		for ( var i = 0, l = elements.length; i < l; i++ )
		{
			if ( $this.closest( elements[i] ).length )
				return;
		}
		$this.attr( 'data-original', $this.attr( 'src' ) ).attr( 'src', Sl.lazyLoader ).addClass( 'lazy' );
	} );
	$( '.lazy' ).lazyload( {
		effect        : 'fadeIn',
		threshold     : 200,
		failure_limit : 100,
		skip_invisible: false
	} );
});
