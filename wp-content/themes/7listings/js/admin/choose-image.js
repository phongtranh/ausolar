/* global jQuery, wp */

jQuery( function( $ )
{
	'use strict';

	var frame,
		$el,
		$body = $( 'body' );

	// Choose image
	$body.on( 'click', '.choose-image', function ( e )
	{
		e.preventDefault();

		$el = $( this );

		if ( !frame )
		{
			frame = wp.media( {
				library: {
					type: 'image'
				},
				// frame: 'post',
				title: wp.media.view.l10n.addMedia
			} );
		}
		frame.off( 'select' );
		frame.on( 'select', function ()
		{
			var image = frame.state().get( 'selection' ).first().toJSON(),
				$input = $el.siblings( 'input' ),
				url = image.url;

			// Use smallest thumbnail when possible for faster load
			if ( image.sizes.hasOwnProperty( 'sl_thumb_tiny' ) )
			{
				url = image.sizes.sl_thumb_tiny.url;
			}
			else if ( image.sizes.hasOwnProperty( 'thumbnail' ) )
			{
				url = image.sizes.thumbnail.url;
			}

			// Save image URL or ID depends on [data-type] attribute, default is ID
			$input.val( 'url' === $input.data( 'type' ) ? url : image.id )
				// Show image preview
				.siblings( 'img' ).attr( 'src', url ).removeClass( 'hidden' ).trigger( 'change' )
				// Show delete button
				.siblings( '.delete-image' ).removeClass( 'hidden' );

			//Change input
			angular.element( $input ).triggerHandler( 'input' );

		} );

		frame.open();
	} );

	// Delete image: empty the input and remove image preview
	$body.on( 'click', '.delete-image', function ( e )
	{
		e.preventDefault();

		$el = $( this );
		$el.siblings( 'img' ).attr( 'src', '' ).addClass( 'hidden' ).trigger( 'change' );
		$el.siblings( 'input' ).val( '' );
		$el.addClass( 'hidden' );
	} );
} );
