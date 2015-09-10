/* global jQuery, SlPhoto, ajaxurl */

jQuery( function ( $ )
{
	'use strict';

	var $body = $( 'body' ),
		$photos = $( '.reorder' );

	// Delete photo
	$photos.on( 'click', '.delete-file', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			data = {
				action       : 'sl_photo_delete',
				_wpnonce     : SlPhoto.nonce_delete,
				post_id      : $( '#post_ID' ).val(),
				attachment_id: $this.data( 'attachment_id' ),
				resource_id  : $this.data( 'resource_id' )
			};

		$.post( ajaxurl, data, function ( r )
		{
			if ( !r.success )
				return;

			$this.closest( '.sl-settings' ).remove();
			$this.closest( '.uploaded' ).remove();
		}, 'json' );
	} );

	// Update photo description
	$photos.on( 'blur', 'input', function ()
	{
		var $this = $( this );
		$.post( ajaxurl, {
			action       : 'sl_photo_update_description',
			_wpnonce     : SlPhoto.nonce_update_description,
			attachment_id: $this.data( 'attachment_id' ),
			description  : $this.val()
		} );
	} );

	// Reorder photos
	$photos.each( function ()
	{
		var $this = $( this ),
			data = {
				action     : 'sl_photos_reorder',
				_wpnonce   : SlPhoto.nonce_reorder,
				post_id    : $( '#post_ID' ).val(),
				resource_id: $this.data( 'resource_id' ) // null if sort photos, int if sort booking resource photos
			};
		$this.sortable( {
			placeholder: 'ui-state-highlight',
			update     : function ()
			{
				data.order = $this.sortable( 'serialize' );
				$.post( ajaxurl, data );
			}
		} );
	} );

	// Set featured image
	var $featuredImageBox = $( '#postimagediv' ).find( '.inside' );
	$photos.on( 'click', '.star', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			data = {
				action      : 'set-post-thumbnail',
				_wpnonce    : SlPhoto.nonce_set_featured,
				post_id     : $( '#post_ID' ).val(),
				thumbnail_id: $this.data( 'attachment_id' )
			};

		$.post( ajaxurl, data, function ( r )
		{
			// Set current item 'featured'
			$( '.star' ).removeClass( 'dashicons-star-filled' );
			$this.removeClass( 'dashicons-star-empty' )
				.addClass( 'dashicons-star-filled' );

			// Show the featured image in the featured box
			$featuredImageBox.html( r );
		} );
	} );

	// Add more photo
	$body.delegate( '.add-file', 'click', function ( e )
	{
		e.preventDefault();
		var $this = $( this ),
			$last = $this.closest( '.sl-settings' ).prev(),
			$upload = $last.clone();

		$upload.insertAfter( $last )
			.removeClass( 'hidden' )
			.find( 'input' ).val( '' ).end()
			.find( 'img' ).attr( 'src', '' ).addClass( 'hidden' ).end()
			.find( '.delete-image' ).addClass( 'hidden' ).end()
			.find( '.choose-image' ).trigger( 'click' );
	} );
} );
