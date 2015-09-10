/* global jQuery, Sl, rw_utils */

jQuery( function( $ )
{
	'use strict';

    // Hide original submit button
    $( '#respond' ).find( '.form-submit' ).hide();
    $( '.comment-body' ).find( '.form-submit' ).show();

    // Reply to comment
    $( '#comments' ).on( 'click', '.comment-reply-link', function( e )
    {
        e.preventDefault();
        $( '#comment_parent' ).val( $( this ).data( 'comment_id' ) );
        $( '#comment-form' ).modal( 'show' );
        removeSpan();
    } );

	// Don't add stars and event handler for product single page
	// Let WooCommerce do that
	if ( !Sl || !Sl.hasOwnProperty( 'post_type' ) || 'product' != Sl.post_type )
	{
		// Star ratings
		$( '.stars' ).each( function()
		{
			var $this = $( this ),
				$select = $this.next( 'select' );

			$this.on( 'click', 'a', function()
			{
				var $star = $( this );
				$select.val( $star.text() );
				$star.addClass( 'active' ).siblings().removeClass( 'active' );
				return false;
			} );
		} );
	}
	else
	{
		//removeSpan();
		$( '.span6' ).fitVids();
	}

	/**
	 * Remove wrapping span around stars for product
	 * @return void
	 */
	function removeSpan()
	{
		$( '.stars > span' ).each( function()
		{
			$( this ).replaceWith( this.innerHTML );
		} );
	}

	$( '#comment-form-submit' ).click( function()
	{
		// Validation
		var fields = ['author', 'email', 'comment', 'rating'],
			$field;
		for ( var i = 0, len = fields.length; i < len; i++ )
		{
			$field = $( '#' + fields[i] );
			if ( $field.length && !$field.val() )
			{
				$( '.error-' + fields[i] ).removeClass( 'hidden');
				return false;
			}
			else
			{
				$( '.error-' + fields[i] ).addClass( 'hidden');
			}
		}

		var $email = $( '#email' );
		if ( $email.length && !rw_utils.is_email( $email.val() ) )
		{
			alert( 'Invalid email address' );
			$email.focus();
			return false;
		}

		$( this ).closest( '.modal' ).modal( 'hide' );
		$( '#submit' ).trigger( 'click' );
		return false;
	} );
} );
