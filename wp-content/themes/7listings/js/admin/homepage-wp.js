jQuery( function ( $ )
{
	'use strict';

	// Move tooltip to the meta box title
	var $info = $( '<tr><th colspan="2"><div class="settings-error updated"><p>' + SlHome.text + '</p></div></th></tr>' );

	$info.insertAfter( '.form-table tr:first' );
} );
