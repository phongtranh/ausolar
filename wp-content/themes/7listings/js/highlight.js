jQuery( function ( $ )
{
	'use strict';

	var $form = $( '#content form' );
	$form.on( 'change', ':input', function ()
	{
		var $this = $( this );
		if ( $this.hasClass( 'no-highlight' ) )
			return;

		if ( !$this.val() )
			$this.addClass( 'is-empty' );
		else
			$this.removeClass( 'is-empty' );
	} );
	$form.find( ':input' ).each( function ()
	{
		var $this = $( this );
		if ( $this.attr( 'type' ) != 'file' )
			$this.trigger( 'change' );
	} );
} );
