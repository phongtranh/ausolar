jQuery( function ( $ )
{
	'use strict';

	// Move tooltip to the meta box title
	var $homepageEnable = $( '.homepage-enable' );
	$homepageEnable.find( '.sl-tooltip' ).appendTo( '.hndle' );
	$homepageEnable.on( 'change', 'input', function ()
	{
		var checked = $( this ).is( ':checked' ),
			$widgets = $homepageEnable.next(),
			$info = $widgets.next();

		if ( checked )
		{
			$widgets.slideDown();
			$info.slideUp();
		}
		else
		{
			$widgets.slideUp();
			$info.slideDown();
		}
	} );
	$homepageEnable.find( 'input' ).trigger( 'change' );

	// Toggle boxes
	$( 'body' ).on( 'click', '.box .toggle', function ()
	{
		var $this = $( this ),
			$settings = $this.siblings( '.widget-settings' ),
			open = $this.hasClass( 'add-md' );

		if ( open )
		{
			$this.removeClass( 'add-md' ).addClass( 'delete-md' );
			$settings.slideDown();

			$settings.find( '.listing-type :checked' ).trigger( 'click' );
			$settings.find( '.sidebar.layout :checked' ).trigger( 'click' );
		}
		else
		{
			$this.removeClass( 'delete-md' ).addClass( 'add-md' );
			$settings.slideUp();
		}
	} );

	// Sortable boxes
	$( '#homepage-widgets' ).sortable( {
		handle     : '.heading',
		placeholder: 'ui-state-highlight',
		connectWith: '.box'
	} );

	// Custom Html
	var editor = CodeMirror.fromTextArea( document.getElementById( 'custom-html' ), {
		lineNumbers     : true,
		mode            : "text/html",
		extraKeys       : { 'Ctrl-Space': 'autocomplete' },
		viewportMargin  : Infinity,
		matchBrackets   : true
	} );
} );
