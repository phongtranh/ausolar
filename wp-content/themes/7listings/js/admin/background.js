jQuery( function ( $ )
{
	'use strict';

	var $layoutOption = $( '.layout-option' );

	// Layout option
	$layoutOption.on( 'click', 'input', function ()
	{
		var $this = $( this ),
			$parent = $this.parents( '.layout-option' );

		$parent.find( 'label' ).removeClass( 'active' );
		$this.siblings( 'label' ).addClass( 'active' );
	} );
	$layoutOption.find( 'input:checked' ).trigger( 'click' );

	// Background color picker
	var $bgColor = $( 'input[name*="design_body_background"]' ),
		val = $bgColor.val();

	// Make sure the value is displayed
	if ( val )
	{
		$layoutOption.find( 'label' ).css( 'background-color', val );
	}

	$bgColor.wpColorPicker( {
		change: function ( event, ui )
		{
			$layoutOption.find( 'label' ).css( 'background-color', ui.color.toString() );
		}
	} );
} );
