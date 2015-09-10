jQuery( function ( $ )
{
	'use strict';

	$( '.submitdelete' ).hide().click( function()
	{
		alert( 'You cannot delete any data!' );
		return false;
	} );

	/**
	 * Show company title when user enter company id
	 */
	$( '#move-comment-to' ).change( function()
	{
		var post_id = $( this ).val();

		$( '.spinner' ).show();

		$.get( ajaxurl, { 
			action: 'post_info',
			post_id: post_id
		}, function( r )
		{
			$( '.spinner' ).hide();

			if ( r.success )
			{
				var template = '<a href="/wp-admin/post.php?post='+ r.data.ID +'&action=edit">'+r.data.post_title+'</a>';
				$( '#move-comment-to-target span' ).append( template );
				$( '#move-comment-button' ).show();
			}
			else
			{
				$( '#move-comment-to-target span' ).html('');
				$( '#move-comment-button' ).hide();
			}
		}, 'json' )
	} );
	
	/**
	 * Move comment button click event handle
	 */
	$( '#move-comment-button' ).click( function()
	{
		// Prompt user one more time
		if ( ! confirm( 'This cannot be undone. Do you wish to continue?' ) )
			return;

		var start = $( this ).data( 'start' ),
			target= $( '#move-comment-to' ).val(),
			move_comment_nonce = $( '#move-comment-nonce' ).val();

		$( '.spinner' ).show();

		$.post( ajaxurl, { 
			action: 'move_comment',
			start: start,
			target: target,
			nonce: move_comment_nonce
		}, function( r )
		{
			$( '.spinner' ).hide();

			if ( r.success )
			{
				$( '#commentsdiv' ).hide();
				alert( 'Comments moved successfully. Total: ' + r.data.affected_rows + ' comments moved.' );
			}
		}, 'json' )
	} );

} );
