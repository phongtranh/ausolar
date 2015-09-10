(function ( $ )
{
	tinymce.PluginManager.add( 'SlShortcodes', function ( editor )
	{
		// Helper functions

		/**
		 * Wrap selected text in a predefined pair of texts
		 *
		 * @param before Text before
		 * @param end    Text after
		 */
		function html( before, end )
		{
			var s = editor.selection;
			s.setContent( before + s.getContent() + end );
		}

		/**
		 * Wrap selected text in a <tag>
		 *
		 * @param tagName Tag name
		 */
		function tag( tagName )
		{
			editor.execCommand( 'formatblock', false, tagName );
		}

		/**
		 * Execute a command
		 *
		 * @param name Command that will be run
		 */
		function command( name )
		{
			editor.execCommand( name, false );
		}


		/**
		 * Show popup for a menu
		 *
		 * @param id Menu item ID
		 */
		function popup( id )
		{
			$( '#sls-popup-' + id ).modal();
		}

		/**
		 * Add layout
		 */
		function layout()
		{
			var html = '',
				text = 'some text, some text, some text, some text, some text, some text, some text, some text, some text, some text';
			for ( var i = 0; i < arguments.length; i++ )
			{
				html += '<div class="span' + arguments[i] + '">' + text + '</div>';
			}
			tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, '<div class="row-fluid">' + html + '</div>&nbsp;' );
		}

		// Button
		editor.addButton( 'sls_button', {
			title  : 'Insert Button',
			onclick: function ()
			{
				$( '#sls-popup-button' ).modal();
			}
		} );

		// Icon
		editor.addButton( 'sls_icon', {
			title  : 'Insert icon',
			onclick: function ()
			{
				$( '#sls-popup-icon' ).modal();
			}
		} );

		// Map
		editor.addButton( 'sls_map', {
			title  : 'Insert Map',
			onclick: function ()
			{
				$( '#sls-popup-map' ).modal();
			}
		} );

		// Text styling
		editor.addButton( 'sls_text', {
			title: 'Text Styling',
			icon : false,
			type : 'menubutton',
			menu : [
				{
					text   : 'Dropcap',
					onclick: function ()
					{
						html( '<span class="dropcap">', '</span>' );
					}
				},
				//{
				//	text   : 'Blockquote',
				//	onclick: function ()
				//	{
				//		tag( 'blockquote' );
				//	}
				//},
				//{
				//	text   : 'Underline',
				//	onclick: function ()
				//	{
				//		command( 'underline' );
				//	}
				//},
				//{
				//	text   : 'Strikethrough',
				//	onclick: function ()
				//	{
				//		command( 'strikethrough' );
				//	}
				//},
				{
					text   : 'Highlight',
					onclick: function ()
					{
						html( '<mark class="hightlight">', '</mark>' );
					}
				},
				{
					text   : 'Superscript',
					onclick: function ()
					{
						command( 'superscript' );
					}
				},
				{
					text   : 'Subscript',
					onclick: function ()
					{
						command( 'subscript' );
					}
				}
			]
		} );

		// Custom list
		editor.addButton( 'sls_custom_list', {
			title: 'List',
			icon : false,
			type : 'menubutton',
			menu : [
				//{
				//	text   : 'Unordered list',
				//	onclick: function ()
				//	{
				//		editor.execCommand( 'insertunorderedlist', false );
				//		editor.selection.getNode().className = 'sl-list';
				//	}
				//},
				{
					text   : 'Custom List',
					onclick: function ()
					{
						$( '#sls-popup-custom_list' ).modal();
					}
				}
			]
		} );

		// Normal
		editor.addButton( 'sls_normal', {
			title: 'Insert Layout Element',
			icon : false,
			type : 'menubutton',
			menu : [
				{
					text   : 'Slideshow',
					onclick: function ()
					{
						popup( 'slideshow' );
					}
				},
				{
					text   : 'Styled Boxes',
					onclick: function ()
					{
						popup( 'styled_boxes' );
					}
				},
				{
					text   : 'Framed Image',
					onclick: function ()
					{
						popup( 'framed_image' );
					}
				},
				{
					text   : 'Divider',
					onclick: function ()
					{
						editor.execCommand( 'mceInsertContent', false, '<hr>' );
					}
				},
				{
					text: 'Layout',
					menu: [
						{
							text   : '1/2 : 1/2',
							onclick: function ()
							{
								layout( 6, 6 );
							}
						},
						{
							text   : '1/3 : 1/3 : 1/3',
							onclick: function ()
							{
								layout( 4, 4, 4 );
							}
						},
						{
							text   : '1/3 : 2/3',
							onclick: function ()
							{
								layout( 4, 8 );
							}
						},
						{
							text   : '2/3 : 1/3',
							onclick: function ()
							{
								layout( 8, 4 );
							}
						},
						{
							text   : '1/4 : 1/4 : 1/4 : 1/4',
							onclick: function ()
							{
								layout( 3, 3, 3, 3 );
							}
						},
						{
							text   : '1/4 : 3/4',
							onclick: function ()
							{
								layout( 3, 9 );
							}
						},
						{
							text   : '1/4 : 1/4 : 2/4',
							onclick: function ()
							{
								layout( 3, 3, 6 );
							}
						},
						{
							text   : '1/4 : 2/4 : 1/4',
							onclick: function ()
							{
								layout( 3, 6, 3 );
							}
						},
						{
							text   : '2/4 : 1/4 : 1/4',
							onclick: function ()
							{
								layout( 6, 3, 3 );
							}
						},
						{
							text   : '3/4 : 1/4',
							onclick: function ()
							{
								layout( 9, 3 );
							}
						}
					]
				},
				{
					text   : 'Widget Area',
					onclick: function ()
					{
						popup( 'widget_area' );
					}
				},
				{
					text   : 'Tabs',
					onclick: function ()
					{
						popup( 'tabs' );
					}
				},
				{
					text   : 'Toggle',
					onclick: function ()
					{
						popup( 'toggle' );
					}
				},
				{
					text   : 'Accordions',
					onclick: function ()
					{
						popup( 'accordions' );
					}
				},
				{
					text   : 'Tooltip',
					onclick: function ()
					{
						popup( 'tooltip' );
					}
				},
				{
					text   : 'Social Links',
					onclick: function ()
					{
						popup( 'social_links' );
					}
				}
			]
		} );

		// Other elements
		//editor.addButton( 'sls_listings', {
		//	title: 'Elements',
		//	icon : false,
		//	type : 'menubutton',
		//	menu : [
		//		{
		//			text   : 'Table',
		//			onclick: function ()
		//			{
		//				popup( 'table' );
		//			}
		//		},
		//		{
		//			text   : 'Pricing Tables',
		//			onclick: function ()
		//			{
		//				popup( 'pricing_tables' );
		//			}
		//		},
		//		{
		//			text   : 'Chart',
		//			onclick: function ()
		//			{
		//				popup( 'chart' );
		//			}
		//		},
		//		{
		//			text   : 'Lightbox',
		//			onclick: function ()
		//			{
		//				popup( 'lightbox' );
		//			}
		//		},
		//		{
		//			text   : 'Alert Message',
		//			onclick: function ()
		//			{
		//				popup( 'alert' );
		//			}
		//		},
		//		{
		//			text   : 'Author Info',
		//			onclick: function ()
		//			{
		//				popup( 'author_info' );
		//			}
		//		},
		//		{
		//			text   : 'Sitemap',
		//			onclick: function ()
		//			{
		//				popup( 'sitemap' );
		//			}
		//		},
		//		{
		//			text   : 'Contact Form',
		//			onclick: function ()
		//			{
		//				popup( 'contact_form' );
		//			}
		//		},
		//		{
		//			text   : 'Twitter',
		//			onclick: function ()
		//			{
		//				popup( 'twitter' );
		//			}
		//		}
		//	]
		//} );
	} );
})( jQuery );
