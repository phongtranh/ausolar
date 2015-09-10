jQuery( function( $ )
{
	// Hide original submit button
	$( '#respond .form-submit' ).hide();
    $( '.comment-body' ).find( '.form-submit' ).show();

	// Reply to comment
	$( '#comments' ).on( 'click', '.comment-reply-link', function()
	{
		$( '#comment_parent' ).val( $( this ).data( 'comment_id' ) );
		$( '#comment-form' ).modal( 'show' );
		return false;
	} );

	$( '#comment-form-submit' ).click( function()
	{
		$( this ).closest( '.modal' ).modal( 'hide' );
		$( '#submit' ).trigger( 'click' );
		return false;
	} );
} );
