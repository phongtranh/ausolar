jQuery( function( $ )
{
	var $comment = $( '#comment_status' );
	$comment.change( function()
	{
		$( '#old-comments' )[$comment.is( ':checked' ) ? 'slideUp' : 'slideDown']();
	} ).trigger( 'change' );
} );
