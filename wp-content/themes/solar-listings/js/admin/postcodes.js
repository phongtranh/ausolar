jQuery( function ( $ )
{
	var $button = $( '#company-postcodes' ),
		$spinner = $button.siblings( '.spinner' ),
		counter = 0;

	/**
	 * Send ajax request to update company postcodes
	 *
	 * @return void
	 */
	function send()
	{
		$.post( ajaxurl, {
			action: 'solar-update-postcodes',
			counter: counter
		}, function ( r )
		{
			// If success: repeat sending requests
			// If error: alert and stop. Message can be 'Successful' (when process is completed) or error messag

			if ( !r.success )
			{
				$spinner.hide();
				alert( r.data );
				return;
			}

			counter++;
			send();
		}, 'json' );
	}

	$button.click( function ( e )
	{
		e.preventDefault();

		$spinner.show();
		send();
	} );
} );