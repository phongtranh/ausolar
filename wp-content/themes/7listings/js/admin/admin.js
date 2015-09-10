/* global jQuery */

/**
 * This file contains common Javascript code for all admin pages
 */

/**
 * jQuery update checkbox plugin
 * This plugin will updates all IDs and [for] attribute of <label> tag to make sure
 * they're unique, thus CSS works
 */
(function ( $ )
{
	'use strict';

	/**
	 * Generate unique ID
	 * It doesn't generate "real" unique ID, but acceptable in concept of theme
	 * It generates 8-characters-length string
	 *
	 * @see http://stackoverflow.com/q/105034/371240 For more implementations of GUID
	 *
	 * @return string
	 */
	function uniqueId()
	{
		return Math.floor( Math.random() * 0x100000000 ).toString( 16 );
	}

	$.fn.slUpdateCheckboxes = function ()
	{
		this.find( '.checkbox' ).each( function ()
		{
			var id = uniqueId(),
				$el = $( this );
			$el.find( 'label' ).attr( 'for', id ).end()
				.find( 'input' ).attr( 'id', id );
		} );

		return this;
	};
}( jQuery ));

/**
 * jQuery modal plugin
 * This plugin mimics the WordPress media modal
 */
(function ( $ )
{
	'use strict';

	/**
	 * slModal plugin
	 * @param options Plugin option, can be:
	 * - callback function when the modal opens
	 * - 'open' to open the modal
	 * - 'close' to close the modal
	 *
	 * Usage:
	 *
	 * $( 'a.modal' ).slModal();        // Use [data-trigger] or [href] attribute to point to the modal
	 * $( '#modal' ).slModal( 'open' ); // Just open the modal itself
	 */
	$.fn.slModal = function ( options )
	{
		return this.each( function ()
		{
			var $this = $( this ),
				// Get targeted modal by [data-target] or [href] attribute
				selector = $this.data( 'target' ) || $this.attr( 'href' ),
				$modal = $( selector );

			// If no modal target: we know that it this is the call to open or close the modal itself
			if ( !$modal.length )
			{
				$modal = $this;
				$modal['open' === options ? 'removeClass' : 'addClass']( 'hidden' );
			}
			// Otherwise add event listener to open modal
			else
			{
				// Click to open modal
				$this.on( 'click', 'button', function ( e )
				{
					e.preventDefault();
					$modal.removeClass( 'hidden' );

					// Run callback if necessary
					if ( typeof options.open === 'function' )
					{
						options.open();
					}
				} );
			}

			// Always add event listener to close modal
			$modal.on( 'click', '.media-modal-close', function ( e )
			{
				e.preventDefault();
				$modal.addClass( 'hidden' );
			} );
		} );
	};
}( jQuery ));

// Global Javascript object of the theme
var Sl = Sl || {};

/**
 * Admin module
 */
Sl.admin = (function ( $ )
{
	'use strict';

	/**
	 * Store the reference to $( 'body' )
	 * It is set in init() function, because only then the document is ready
	 */
	var $body;

	/**
	 * Where we update all methods, used internally inside methods.
	 * If it's null or not set, that means updating whole document and its value is set to $body.
	 * It is set in init() function, because only then the document is ready.
	 */
	var $range;

	/**
	 * Check whether need to add event handlers.
	 * We only need to add event handler only once. It will be set to false in init() method.
	 */
	var addHandler = true;

	return {
		/**
		 * Show / hide sidebar dropdown when change sidebar layout
		 *
		 * @return void
		 */
		toggleSidebar   : function ()
		{
			if ( addHandler )
			{
				$body.on( 'change', '.sidebar.layout input', function ()
				{
					var $this = $( this );
					$this.closest( '.layout' ).next()['none' == $this.val() ? 'slideUp' : 'slideDown']();
				} );
			}

			var $sidebar = $range.find( '.sidebar.layout' );
			$sidebar.find( ':checked' ).trigger( 'change' );

			/**
			 * Hide sidebar selection if sidebar layout is hidden
			 * But if sidebar layout is visible (has class '.visible'), do nothing
			 */
			$sidebar.each( function ()
			{
				var $this = $( this );

				if ( $this.hasClass( 'visible' ) )
					return;

				if ( $this.is( ':hidden' ) )
					$this.next().hide();
			} );
		},
		/**
		 * Show / hide listing columns
		 *
		 * @return void
		 */
		toggleColumns   : function ()
		{
			if ( addHandler )
			{
				$body.on( 'change', '.listing-type input', function ()
				{
					var $this = $( this );
					$this.closest( '.listing-type' ).next()['grid' == $this.val() ? 'slideDown' : 'slideUp']();
				} );
			}

			$range.find( '.listing-type :checked' ).trigger( 'change' );
		},
		/**
		 * Show / hide next item when checkbox is on / off
		 *
		 * @return void
		 */
		toggleCheckbox  : function ()
		{
			if ( addHandler )
			{
				$body.on( 'change', '.checkbox-toggle input', function ()
				{
					var $this = $( this ),
						$parent = $this.closest( '.checkbox-toggle' ),
						effect = $parent.data( 'effect' ) || 'slide',
						$next = $parent.next(),
						reverse = $parent.data( 'reverse' ),
						show = $this.is( ':checked' ) ? !reverse : reverse;

					if ( 'slide' == effect )
						$next[show ? 'slideDown' : 'slideUp']();
					else if ( 'fade' == effect )
						$next[show ? 'fadeIn' : 'fadeOut']();
				} );
			}
			$range.find( '.checkbox-toggle input' ).trigger( 'change' );
		},
		/**
		 * Switch 2 checkboxes
		 *
		 * @return void
		 */
		switchCheckboxes: function ()
		{
			if ( !addHandler )
				return;
			$body.on( 'change', '.checkbox-switch input', function ()
			{
				var $this = $( this ),
					$parent = $this.closest( '.checkbox-switch' ).parent(),
					$input = $parent.find( '.checkbox-switch input' ).not( this );

				if ( $this.is( ':checked' ) && $input.is( ':checked' ) )
					$input.prop( 'checked', false ).trigger( 'change' );
			} );
		},
		/**
		 * Toggle choices based on value of select box
		 *
		 * @return void
		 */
		toggleSelect    : function ()
		{
			if ( addHandler )
			{
				$body.on( 'change', '.toggle-choices select', function ()
				{
					var $this = $( this ),
						name = $this.attr( 'name' ),
						value = $this.val(),
						$el = $( '[data-name="' + name + '"]' ),
						effect = $this.closest( '.toggle-choices' ).data( 'effect' );

					if ( !effect )
						effect = 'slide';

					$el.each( function ()
					{
						/**
						 * Show only when field (has same value AND not reverse) OR (has another value AND reverse)
						 * e.g has same value XOR reverse
						 */
						var $t = $( this ),
							hasSameValue = value && -1 != $t.data( 'value' ).indexOf( value ),
							reverse = 1 == $t.data( 'reverse' ),
							show = hasSameValue ? !reverse : reverse;

						if ( 'slide' == effect )
						{
							$t[show ? 'slideDown' : 'slideUp']();
						}
						else if ( 'fade' == effect )
						{
							$t[show ? 'fadeIn' : 'fadeOut']();
						}
					} );
				} );
			}
			$range.find( '.toggle-choices select' ).trigger( 'change' );
		},
		/**
		 * Toggle choices based on value of radio box
		 *
		 * @return void
		 */
		toggleRadio: function ()
		{
			if ( addHandler )
			{
				$body.on( 'click', '.toggle-choices label[for]', function ()
				{
					var $this = $( this ),
						parent = $this.parents( '.post-type-input' ),
						labelFor = $this.attr( 'for' ),
						labelID = '#' + labelFor,
						effect = $this.closest( '.toggle-choices' ).data( 'effect' );

					$( 'input[type=radio]', parent ).attr( 'checked', false );

					$( labelID, parent ).attr( 'checked', true );

					var name = $( labelID, parent ).attr( 'name' ),
						$el = $( '#widgets-right div[data-name="' + name + '"]' );

					if ( !effect )
						effect = 'slide';

					$el.each( function ()
					{
						var $this = $( this ),
							show = labelFor && -1 != $this.data( 'value' ).indexOf( labelFor );

						if ( 'slide' == effect )
						{
							$this[show ? 'slideDown' : 'slideUp']();
						}
						else if ( 'fade' == effect )
						{
							$this[show ? 'fadeIn' : 'fadeOut']();
						}
					} );

				} );
				$body.on( 'change', '.toggle-choices input[type=radio]:checked', function ()
				{
					var $this = $( this ),
						name = $this.attr( 'name' ),
						value = $this.val(),
						$el = $( '[data-name="' + name + '"]' ),
						effect = $this.closest( '.toggle-choices' ).data( 'effect' );

					if ( !effect )
						effect = 'slide';

					$el.each( function ()
					{
						var $t = $( this ),
							show = value && -1 != $t.data( 'value' ).indexOf( value );

						if ( 'slide' == effect )
						{
							$t[show ? 'slideDown' : 'slideUp']();
						}
						else if ( 'fade' == effect )
						{
							$t[show ? 'fadeIn' : 'fadeOut']();
						}
					} );
				} );
			}
			$range.find( '.toggle-choices input[type=radio]	' ).trigger( 'change' );
		},
		/**
		 * Toggle choices based on value of check box
		 *
		 * @return void
		 */
		toggleCheckboxWidget : function ()
		{
			if ( addHandler )
			{
				$body.on( 'click', '.post-types label[for]', function ()
				{
					var $this = $( this ),
						$parent = $this.parents( '.post-type-input' ),
						labelFor = $this.attr( 'for' ),
						labelID = '#' + labelFor,
						$input = $parent.find( 'input[type=checkbox]' ).not( this );

					// If choose all post, post other type have not checked
					if( '-1' === labelFor )
					{
						$input.attr( 'checked', false );
					}
					// If post other type have checked, all post have not checked
					else
					{
						$( '#-1', $parent ).attr( 'checked', false );
					}

					if( $( labelID, $parent ).is(':checked') )
					{
						$( labelID, $parent ).attr( 'checked', false );
					}
					else
					{
						$( labelID, $parent ).attr( 'checked', true );
					}


				} );
			}
		},
		/**
		 * Enable color picker
		 *
		 * @return void
		 */
		colorPicker     : function ()
		{
			$range.find( '.color' ).wpColorPicker();
		},
		/**
		 * Add '.active' to <label> when the inside <input> is checked
		 *
		 * @return void
		 */
		activeLabel     : function ()
		{
			if ( !addHandler )
				return;
			$body.on( 'change', 'label input', function ()
			{
				$( this ).parent().addClass( 'active' ).siblings().removeClass( 'active' );
			} );
		},
		/**
		 * Check and show / hide required labels
		 * Assign it as a global function so we can use it when add elements dynamically
		 *
		 * @param $el jQuery element where we check required fields. If missed, use global $range param
		 *            This param is used to make JS run faster in a smaller scope instead of whole document
		 *
		 * @return void
		 */
		checkRequired   : function ( $el )
		{
			$el = $el || $range;

			$el.find( 'label .required' ).each( function ()
			{
				var $this = $( this ),
					$container = $this.closest( '.sl-settings' ),
					$input = $container.find( 'input[type=text], input[type=number], input[type=email], input[type=tel]' ),
					$textarea = $container.find( 'textarea' ),
					$select = $container.find( 'select' );

				if ( $input.length )
				{
					$input.focus( function ()
					{
						$this.fadeOut();
					} ).blur( function ()
					{
						// If multiple inputs like price list, upsells: check them all
						var hasValue = false;
						$input.each( function ()
						{
							if ( $( this ).val() )
								hasValue = true;
						} );
						if ( hasValue )
							$this.fadeOut();
						else
							$this.removeClass( 'hidden' ).fadeIn();
					} ).trigger( 'blur' );
				}

				if ( $textarea.length )
				{
					$textarea.focus( function ()
					{
						$this.fadeOut();
					} ).blur( function ()
					{
						if ( $textarea.val() )
							$this.fadeOut();
						else
							$this.removeClass( 'hidden' ).fadeIn();
					} ).trigger( 'blur' );
				}

				if ( $select.length )
				{
					$select.change( function ()
					{
						if ( -1 == $select.val() )
							$this.removeClass( 'hidden' ).fadeIn();
						else
							$this.fadeOut();
					} ).trigger( 'change' );
				}
			} );
		},
		/**
		 * Make tabs work in admin
		 *
		 * @return void
		 */
		tab             : function ()
		{
			$range.find( '.tabs' ).each( function ()
			{
				var $this = $( this ),
					$ul = $this.children( 'ul' ),
					$lis = $ul.children(),
					$divs = $this.children( 'div' );

				$lis.filter( ':first' ).addClass( 'active' );
				$divs.hide().filter( ':first' ).show();

				$ul.on( 'click', 'li', function ( e )
				{
					e.preventDefault();

					var i = $lis.index( this ),
						$div = $divs.filter( ':eq(' + i + ')' );

					$lis.removeClass( 'active' );
					$( this ).addClass( 'active' );

					$div.fadeIn( 'slow' );
					$div.siblings( 'div' ).hide();

					$( window ).trigger( 'resize' ); // For showing Google Maps
				} );
			} );
		},
		/**
		 * Make tabs default of wordpress working in admin
		 * This is new function for tabs, it will replace for tab function when update done
		 *
		 * @return void
		 */
		newTab          : function ()
		{
			var $hash = window.location.hash;

			$range.find( '.sl-tabs' ).each( function ()
			{
				var $this = $( this ),
					$a = $this.children( 'a' ),
					$parentTab = $this.next(),
					$tabs = $parentTab.children( 'div' );

				$a.filter( ':first' ).addClass( 'nav-tab-active' );
				$tabs.hide().filter( ':first' ).show();

				$a.each( function ()
				{
					var $aa = $( this ),
						$anchor = $aa.attr( 'href' );

					if ( ( $hash ) && ( $anchor === $hash ) )
					{
						$a.removeClass( 'nav-tab-active' );
						$aa.addClass( 'nav-tab-active' );
						var j = $a.index( $aa ),
							$divShow = $tabs.filter( ':eq(' + j + ')' );

						$tabs.hide();
						$divShow.show();
					}

				} );

				$a.click( function ()
				{
					var i = $a.index( this ),
						$div = $tabs.filter( ':eq(' + i + ')' );

					$a.removeClass( 'nav-tab-active' );
					$( this ).addClass( 'nav-tab-active' );

					$div.fadeIn( 'slow' );
					$div.siblings( 'div' ).hide();

					$( window ).trigger( 'resize' ); // For showing Google Maps
				} );

			} );
		},
		/**
		 * Only check a category for stars taxonomy
		 * Apply for edit screen and quick edit a post
		 *
		 * @return void
		 */
		starsCheckOne   : function ()
		{
			/**
			 * For single post screen
			 */
			$range.find( '#starschecklist-pop li input[type="checkbox"], #starschecklist li input[type="checkbox"]' ).click( function ()
			{
				$( '#starschecklist-pop li input[type="checkbox"], #starschecklist li input[type="checkbox"]' ).prop( 'checked', false );
				$( this ).prop( 'checked', true );
			} );

			/**
			 * For Quick edit
			 */
			$range.find( 'ul.stars-checklist li input[type="checkbox"]' ).click( function ()
			{
				$( 'ul.stars-checklist li input[type="checkbox"]' ).prop( 'checked', false );
				$( this ).prop( 'checked', true );
			} );
		},
		/**
		 * Add event handlers when add, update widgets
		 *
		 * @return void
		 */
		addEventHandlers: function ()
		{
			/**
			 * Make checkbox toggle, layout toggle, etc. work when update or add widgets
			 * Also make them work with FitWP Widget Shortcode plugin (when the modal window is loaded)
			 */
			$( document ).on( 'widget-updated widget-added', Sl.admin.init );
			$( document ).on( 'fitws-form-load', Sl.admin.init );
			/**
			 * Update ID get after when add widgets
			 */
			$( document ).on( 'widget-updated widget-added', function ( event, $widget )
			{
				$widget.slUpdateCheckboxes();
			} );
			/**
			 * When toggle widget, just run the code to show / hide elements again
			 * Because in this code we check for ':visible' to show / hide element
			 *
			 * TODO: still buggy, need to check why
			 */
			//$body.on( 'click.widgets-toggle', Sl.admin.toggleSidebar );
			//$body.on( 'click.widgets-toggle', Sl.admin.toggleColumns );
		},
		/**
		 * Initialize function, which calls all functions above
		 * It's called when document ready, and only until then we can set the $body = $( 'body' )
		 * The function also can update only the active widget to narrow the area where we should update all methods
		 * If it's not presented, then we refresh whole document
		 *
		 * @param event The widget-update, widget-added, widgets-toggle event
		 * @param $widget Where we update all methods. Optional. Default is null (update whole document)
		 * @return void
		 */
		init            : function ( event, $widget )
		{
			$body = $( 'body' );

			// If we're on Widgets page, we run all methods for #widgets-right only (where we view widgets)
			if ( -1 != location.href.indexOf( 'widgets.php' ) )
				$body = $( '#widgets-right' );

			$range = $widget || $body;

			Sl.admin.toggleSidebar();
			Sl.admin.toggleColumns();
			Sl.admin.toggleCheckbox();
			Sl.admin.toggleCheckboxWidget();
			Sl.admin.toggleSelect();
			Sl.admin.toggleRadio();
			Sl.admin.toggleCheckbox();
			Sl.admin.colorPicker();
			Sl.admin.activeLabel();
			Sl.admin.checkRequired();
			Sl.admin.tab();
			Sl.admin.newTab();
			Sl.admin.starsCheckOne();

			// Never add event handlers anymore
			addHandler = false;
		}
	};
})( jQuery );

// Run code when document is ready
jQuery( function ()
{
	'use strict';

	Sl.admin.init();
	Sl.admin.addEventHandlers();
} );
