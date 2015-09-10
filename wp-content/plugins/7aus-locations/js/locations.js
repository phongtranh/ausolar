jQuery( function ( $ )
{
	'use strict';

	var $status = $( '#status' ),
		$button = $( '#process ' ),
		$ajaxLoader = $button.siblings( '.spinner' ),
		restart;

	$button.on( 'click', function ( e )
	{
		e.preventDefault();
		$ajaxLoader.css( {
			display: 'inline-block',
			float  : 'none'
		} );

		// Set global variable true to restart again
		restart = 1;
		importLocations();
	} );

	/**
	 * Import locations
	 * Keep sending ajax requests for the action until done
	 *
	 * @return void
	 */
	function importLocations()
	{
		$.post( ajaxurl, {
			action  : 'sl_aus_locations_import',
			restart : restart,
			_wpnonce: $button.data( 'nonce' )
		}, function ( r )
		{
			restart = 0; // Set this global variable = false to make sure all other calls continue properly
			callback( r, importLocations );
		} );
	}

	/**
	 * Callback function to display messages
	 *
	 * @param r JSON object returned from WordPress
	 * @param func Callback function
	 *
	 * @return void
	 */
	function callback( r, func )
	{
		var html = $status.html(),
			message;

		$status.removeClass( 'updated error' );

		if ( !r.success )
		{
			$status.addClass( 'error' );

			message = '<p>' + r.data + '</p>';
			html = html ? html + message : message;
			$status.html( html );
			return;
		}

		$status.addClass( 'updated' );
		if ( r.data.message )
		{
			message = r.data.message ? '<p>' + r.data.message + '</p>' : '';
			html = html ? html + message : message;
			$status.html( html );
		}

		// Submit form again
		if ( r.data.type == 'continue' )
		{
			func();
		}
		else
		{
			$ajaxLoader.hide();
			alert( $button.data( 'done_text' ) );
		}
	}
} );
