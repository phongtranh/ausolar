jQuery( function ( $ )
{
	'use strict';

	// Show tooltip
	$( 'a[data-toggle="tooltip"]' ).tooltip();

	// Refresh tooltip when ajax actions finish so we have tooltip on new added element
	$( document ).ajaxComplete( function ()
	{
		$( 'a[data-toggle="tooltip"]' ).tooltip();
	} );
} );
