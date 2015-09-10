jQuery( function ( $ )
{
	'use strict';

	$( '.social-media.buttons' ).each( function ()
	{
		var $this = $( this ),
			url = $this.data( 'url' );

		if ( ! url )
			return;

		// Get counter via ajax
		$.post( SlSocialButtons.ajaxUrl, {
			action     : 'sl_social_buttons_get_counter',
			_ajax_nonce: SlSocialButtons.nonce,
			url        : url
		}, function ( r )
		{
			if ( !r.success )
				return;

			var $span, counter, network;
			for ( network in r.data )
			{
				if ( !r.data.hasOwnProperty( network ) )
					continue;

				// Show counter only when it's > 0
				counter = parseInt( r.data[network] );
				if ( !counter )
					continue;

				$span = $( '<span class="counter"/>' ).text( counter );
				$this.find( '.' + network ).append( $span );
			}
		} );
	} );
} );
