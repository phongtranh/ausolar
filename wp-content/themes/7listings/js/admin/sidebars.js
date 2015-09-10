jQuery( function ( $ )
{
	$( '#sidebars' ).on( 'click', '.delete', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			$li = $this.parent(),
			data = {
				action  : 'sl_delete_sidebar',
				_wpnonce: SlSidebar.nonceDelete,
				sidebar : $this.attr( 'rel' )
			};

		$.post( ajaxurl, data, function ( r )
		{
			r.success ? $li.fadeOut( 'slow' ).remove() : alert( r.data );
		}, 'json' );
	} );
} );