jQuery( function( $ )
{
	'use strict';

	// Change featured
	$( 'body' ).on( 'click', '.featured span', function()
	{
		var $this = $( this ),
			current   = parseInt( $this.data( 'featured' ), 10 ),
			data  = {
				action  : 'sl_change_featured',
				post_id : $this.data( 'post_id' ),
				current : current,
				_wpnonce: Sl_List.nonce_change_featured
			},
			featured = 2 === current ? 0 : current + 1,
			icon,
			cssClass;

		if ( 2 === featured )
		{
			cssClass = 'star dashicons dashicons-star-filled';
		}
		else if ( 1 === featured )
		{
			cssClass = 'featured dashicons dashicons-yes';
		}
		else
		{
			cssClass = 'dashicons dashicons-no';
		}

		$.post( ajaxurl, data, function( r )
		{
			if ( r.success )
			{
				$this.data( 'featured', featured )
					.html( icon )
					.removeClass( 'star dashicons-star-filled featured dashicons-yes dashicons-no' ).addClass( cssClass );
			}
		}, 'json' );

		return false;
	} );

	// Add class for table
	$( '.wp-list-table' ).addClass( Sl_List.post_type );

	// Unlink for edit broadcasted posts
	$( 'td.broadcasted' ).each( function()
	{
		var $this = $( this ),
			broadcasted = $this.find( '.yes-sm' ).length,
			$title = $this.siblings( '.post-title' ).find( 'strong:first' );

		if ( !broadcasted )
		{
			return;
		}

		$title.html( $title.text() );
	} );
} );
