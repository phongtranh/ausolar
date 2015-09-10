/* global SlWidgets, ajaxurl */

/**
 * Handles all actions in admin 'widgets.php' page
 * Mostly Ajax requests to update widget elements
 */

jQuery( function ( $ )
{
	'use strict';

	/**
	 * Get all posts of a specific post type for Single widget
	 * Send Ajax request which is handled in Single widget file
	 *
	 * @see inc/widgets/single.php
	 *
	 * @return void
	 */
	function widgetSingleGetPosts()
	{
		$( '#widgets-right' ).on( 'click', '.toggle-choices label[for]', function ()
		{
			var $this = $( this ),
				labelFor = $this.attr( 'for' ),
				el = '.sl-widget-single-post-type#' + labelFor;

			$( '#widgets-right' ).on( 'change', el, function ( e )
			{
				/**
				 * Event bubbles is set only when we actually change the value of the select box
				 * This prevents sending ajax request when widget is updated
				 * because the 'change' event is triggered at that time (no bubbles if we just trigger the event)
				 *
				 * @see Sl.admin.toggleSelect
				 */

				var $this = $( this ),
					$listWrapper = $this.closest( '.sl-settings' ).next(),
					$spinner = $listWrapper.find( '.spinner' ),
					$list = $listWrapper.find( 'select' );

				$spinner.removeClass( 'sl-hidden' ).css( 'display', 'inline-block' );

				$.get( ajaxurl, {
					action     : 'sl_widget_single_get_posts',
					_ajax_nonce: SlWidgets.nonceGetPosts,
					post_type  : $this.val()
				}, function ( r )
				{
					$spinner.addClass( 'sl-hidden' );

					if ( !r.success )
						return;

					$list.empty().append( r.data );
				}, 'json' );
			} );
			$( '#widgets-right' ).find( el ).trigger( 'change' );
		});
	}

	widgetSingleGetPosts();
} );
