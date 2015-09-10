/**
 * This file handles all Javascript action in Appearance > Design page
 */

/* global jQuery, SlDesign */
jQuery( function ( $ )
{
	'use strict';
	// Slider
	$( '.slider' ).each( function ()
	{
		var $slider = $( this ),
			$input = $slider.parent().find( 'input' ),
			value = parseInt( $input.val() ),
			options = {
				min  : $slider.data( 'min' ),
				max  : $slider.data( 'max' ),
				slide: function ( event, ui )
				{
					$input.val( ui.value );
				}
			};

		if ( !value )
		{
			value = 0;
			$input.val( 0 );
		}

		// Assign field value and callback function when slide
		options.value = value;
		$slider.slider( options );

		$input.change( function ()
		{
			$slider.slider( 'value', $( this ).val() );
		} );
	} );

	// Import design
	$( '#import-design' ).click( function ( e )
	{
		e.preventDefault();

		var $spinner = $( this ).siblings( '.spinner' );
		$spinner.css( 'display', 'inline-block' );
		$.post( ajaxurl, {
			action  : 'sl_design_import',
			nonce   : SlDesign.nonceImport,
			settings: $( '#design-settings' ).val()
		}, function ( r )
		{
			$spinner.hide();
			if ( r.success )
				location.reload();
			else
				alert( r.data );
		}, 'json' );
	} );

	/**
	 * Hide mobile advanced settings when mobile nav is right
	 *
	 * @return void
	 */
	function checkMobileNavPosition()
	{
		var $settings = $( '.mobile-advanced-settings' );
		if ( $( '#design-layout-mobile-nav-right' ).is( ':checked' ) )
		{
			$settings.addClass( 'hidden' );
		}
		else
		{
			$settings.removeClass( 'hidden' );
		}
	}

	$( '.mobile-slideout' ).on( 'change', 'input', checkMobileNavPosition );
	checkMobileNavPosition();

	// Turn textarea for custom CSS code to beautiful code editor
	var editor = CodeMirror.fromTextArea( document.getElementById( 'custom-css' ), {
		lineNumbers   : true,
		extraKeys     : { 'Ctrl-Space': 'autocomplete' },
		viewportMargin: Infinity,
		matchBrackets : true
	} );

	setTextareaMode( 'css' );

	$( '.select-css-mode' ).on( 'change', function()
	{
		setTextareaMode( $( this).val() );
	} );

	function checkSVG()
	{
		var $svg = $('.svg_display input[type="checkbox"]' ),
			$image =$('#logo-options' ).find('.image-logo' );

		if ( $svg.is( ':checked' ) )
		{
			$image.addClass('hidden');
		}
		$svg.on( 'change', function()
		{
			var $this = $( this );

			if ( $this.is( ':checked' ) )
			{
				$image.addClass('hidden');
			}
			else
			{
				$image.removeClass('hidden');
			}
		} );
	}
	checkSVG();

	/**
	 * Set Textarea mode
	 *
	 * @param string $mode
	 *
	 * @return void
	 */
	function setTextareaMode( $mode )
	{
		editor.setOption( 'mode', $mode  );
	}
} );
