/* global jQuery, Sl, rw_ultils */

/**
 * Retina detect
 * This runs immediately when file is loaded, no need to wait until document ready
 */
(function ()
{
	'use strict';

	if ( document.cookie.indexOf( 'retina' ) == -1 && 'devicePixelRatio' in window && window.devicePixelRatio == 2 )
	{
		var date = new Date();
		date.setTime( date.getTime() + 3600000 );

		document.cookie = 'retina=' + window.devicePixelRatio + ';' + ' expires=' + date.toUTCString() + '; path=/';

		// Reload the page
		if ( document.cookie.indexOf( 'retina' ) != -1 )
			window.location.reload();
	}
})();

jQuery( function ( $ )
{
	'use strict';

	var $body = $( 'body' );

	/**
	 * Helper function to get ie version
	 * User to add custom (.ie) CSS class to body for better styling
	 *
	 * @link https://gist.github.com/padolsey/527683#comment-955607
	 */
	function ie()
	{
		var v = 3,
			div = document.createElement( 'div' ),
			all = div.getElementsByTagName( 'i' );
		do
			div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->';
		while
			( all[0] );
		return v > 4 ? v : document.documentMode;
	}

	/**
	 * Helper function to get URL parameter
	 * @param name Parameter name
	 * @returns string
	 */
	function getURLParameter( name )
	{
		var match = new RegExp( '[?&]' + name + '=([^&]*)' );
		match = match.exec( location.search );
		return match && decodeURIComponent( match[1].replace( /\+/g, ' ' ) );
	}

	/**
	 * Fix for IE
	 * Add .ie to body class and add support for <main> tag
	 *
	 * @return void
	 */
	function ieFix()
	{
		var ieVersion = ie();
		if ( ieVersion )
		{
			$body.addClass( 'ie' );
			document.createElement( 'main' ); // IE does not support <main> tag
		}
	}

	/**
	 * Show ajax loading graphic whenever detect an ajax request
	 * Ajax request must be sent by theme, e.g. not from plugins (like WooCommerce), we check
	 * by the prefix in ajax action ("sl_")
	 * We also omit some requests that do not need to show ajax loading graphic, like show cart when
	 * page load or add company views
	 *
	 * @return void
	 */
	function showAjaxLoading()
	{
		/**
		 * Check if we need to show ajax loading graphic or not
		 *
		 * @param s Request object. Request string is stored in s.data
		 *
		 * @return bool
		 */
		function show( s )
		{
			var allowedActions = ['sl_cart_show', 'sl_company_views', 'sl_social_buttons_get_counter', 'sl_contact_current_time'],
				check = 'data' in s && -1 != s.data.indexOf( 'action=sl' );
			if ( check )
			{
				for ( var i = 0; i < allowedActions.length; i++ )
				{
					if ( -1 != s.data.indexOf( 'action=' + allowedActions[i] ) )
						check = false;
				}
			}
			return check;
		}

		var $ajaxLoading = $( '.ajax-loading' ),
			$doc = $( document );
		$doc.ajaxSend( function ( e, xhr, s )
		{
			if ( show( s ) )
				$ajaxLoading.show();
		} ).ajaxComplete( function ( e, xhr, s )
		{
			if ( show( s ) )
				$ajaxLoading.hide();
		} );
	}

	/**
	 * Code for shortcodes
	 *
	 * @return void
	 */
	function shortcodes()
	{
		// Toggle
		$body.on( 'click', '.toggle > a', function ()
		{
			$( this ).siblings().slideToggle().parent().toggleClass( 'active' );
			return false;
		} );

		// Accordions
		$body.on( 'click', '.accordion > a', function ( e )
		{
			e.preventDefault();

			var $this = $( this ),
				$parent = $this.parent(),
				$pane = $this.siblings(),
				$others = $parent.siblings();

			if ( $parent.hasClass( 'active' ) )
			{
				$pane.slideUp();
				$parent.removeClass( 'active' );
			}
			else
			{
				$others.find( '.content' ).slideUp();
				$pane.slideDown();
				$others.removeClass( 'active' );
				$parent.addClass( 'active' );
			}
		} );

		// Tabs
		$( '.tabs' ).each( function ()
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

		// Tooltip
		$( 'a[data-toggle="tooltip"]' ).tooltip();
	}

	/**
	 * Ajax actions for contact page
	 *
	 * @return void
	 */
	function contactPage()
	{
		if ( !Sl.hasOwnProperty( 'contact' ) )
			return;

		// Update current time on contact page
		var $currentTime = $( '#current-time' );
		if ( $currentTime.length )
		{
			$.get( Sl.ajaxUrl, { action: 'sl_contact_current_time' }, function ( r )
			{
				if ( !r.success )
					return;

				var $day = $currentTime.find( '.label' ),
					$time = $currentTime.find( '.detail' );

				$day.text( r.data.day );
				$time.text( r.data.time );
			}, 'json' );
		}

		// Send contact message
		var $contactForm = $( '.sl-form.contact' ),
			$statusError = $( '#status-error' ),
			$statusSuccess = $( '#status-success' );

		$contactForm.on( 'submit', function ( e )
		{
			e.preventDefault();

			var data = $contactForm.serialize();
			data += '&_ajax_nonce=' + Sl.nonceContactSend;

			$.post( Sl.ajaxUrl, data, function ( r )
			{
				if ( r.success )
				{
					$contactForm.hide();
					$statusSuccess.html( r.data ).addClass( 'success' ).slideDown();
				}
				else
				{
					$statusError.html( r.data ).addClass( 'error' ).slideDown();
				}
			}, 'json' );
		} );
	}

	/**
	 * Send ajax request for contact form
	 *
	 * @return void
	 */
	function contactForm()
	{
		$( '.contact_form' ).submit( function ( e )
		{
			e.preventDefault();
			var $form = $( this ),
				$status = $form.find( '.status' );
			$status.removeClass( 'alert-error alert-success' ).html( '' ).addClass( 'hidden' );

			$.post( Sl.ajaxUrl, {
				action       : 'sl_widget_cf_send',
				nonce        : Sl.nonceSendEmail,
				to           : $form.find( 'input[name="contact_to"]' ).val(),
				name         : $form.find( 'input[name="contact_name"]' ).val(),
				email        : $form.find( 'input[name="contact_email"]' ).val(),
				content      : $form.find( 'textarea[name="contact_content"]' ).val(),
				customMessage: $form.find( 'input[name="contact_custom_message"]' ).val(),
				message      : $form.find( 'input[name="contact_message"]' ).val(),
				url          : location.href
			}, function ( r )
			{
				if ( r.success )
					$form.find( ':input' ).hide();
				$status.addClass( r.success ? 'alert-success' : 'alert-error' ).html( r.data ).removeClass( 'hidden' );
			}, 'json' );
		} );
	}

	/**
	 * Code for listing search widget
	 *
	 * @return void
	 */
	function searchWidget()
	{
		var $searchWrapper = $( '.search-wrapper' );
		$searchWrapper.on( 'click', '.type-wrapper a', function ( e )
		{
			e.preventDefault();

			var $this = $( this ),
				postType = $this.data( 'post_type' );

			// Set post type
			$searchWrapper.find( 'input[name="post_type"]' ).val( postType );

			// Make tab active
			$this.addClass( 'active' ).siblings().removeClass( 'active' );

			// Show fields which have post type filter
			$searchWrapper.find( 'form [data-post_type]' ).hide().filter( '[data-post_type="' + postType + '"]' ).show();
		} );

		// Active the correct tab for search widget
		var postType = getURLParameter( 'post_type' );
		if ( postType )
		{
			$searchWrapper.find( '.type-wrapper a[data-post_type="' + postType + '"]' ).trigger( 'click' );
		}
		else
		{
			$searchWrapper.find( '.type-wrapper a:first' ).trigger( 'click' );
		}

		// Turn select dropdown in listings search widget (location) to beautiful dropdown using select2
		$( '.sl-location' ).select2( {
			width: 'resolve'
		} );
	}

	/**
	 * Show/hide navbar
	 *
	 * @return void
	 */
	function navbarToggle()
	{
		var $dropdown = $( '.nav li.dropdown' );

		// Double touch to go to menu location, used for touch devices
		$dropdown.doubleTouchToGo();

		// Enable hover to show sub-menu
		$dropdown.on(
			{
				mouseenter: function ()
				{
					$( this ).children( '.dropdown-menu' ).addClass( 'show' );
				},
				mouseleave: function ()
				{
					$( this ).children( '.dropdown-menu' ).removeClass( 'show' );
				}
			} );

		// Click on menu item redirects to correct page
		$( '.dropdown-toggle' ).click( function ()
		{
			location.href = $( this ).attr( 'href' );
		} );

		/**
		 * Show / Hide mobile nav menu
		 * Only remove CSS class, the effect of opening is done with CSS transition
		 */
		$( '#nav-open-btn' ).click( function ( e )
		{
			e.preventDefault();
			$body.toggleClass( 'nav-open' );
		} );
	}

	/**
	 * Add columns to dropdown main navigation
	 * if numbers li of .dropdown-menu > 10, start division columns
	 * 10 - 20 items = 2 columns, 20 - 30 items = 3 columns, ...., and max is 6 columns
	 *
	 * @return void
	 */
	function navbarColumns()
	{
		$( '.navbar .nav .dropdown-menu' ).each( function ()
		{
			var $this = $( this ),
				$children = $this.children( 'li' ).size();
			if ( $children > 10 && $children < 51 )
			{
				var columns = parseInt( $children / 10 ) + 1;
				$this.addClass( 'columns-' + columns );
			}
			else if ( $children > 50 )
			{
				$this.addClass( 'columns-6' );
			}
		} );
	}

	/**
	 * Display sticky header
	 * In mobile, menu will show only when you scroll up
	 *
	 * @return void
	 */
	function navbarSticky()
	{
		var $header = $( '#branding' ),
			prevScroll = 0,
			$nav = $header.find( 'nav' ),
			navOffset = $nav.length ? $nav.offset().top + 1 : 0,
			$window = $( window );

		// If show admin bar, re-calculate the offset
		if ( $( 'body' ).hasClass( 'admin-bar' ) )
			navOffset -= 28;

		// Add handler for scroll event
		$window.scroll( function ()
		{
			var currentScroll = $window.scrollTop();

			// If we scroll over the navbar, show it and add/remove "scroll-down" CSS class
			if ( currentScroll > navOffset )
			{
				// Add class "fixed" to show the navbar
				$header.addClass( 'fixed' );

				// Toggle "scroll-down" class according to scroll direction
				if ( currentScroll > prevScroll )
				{
					$header.addClass( 'scroll-down' );
				}
				else
				{
					$header.removeClass( 'scroll-down' );
				}
			}
			// If we don't scroll over the navbar, just remove all CSS classes
			else
			{
				$header.removeClass( 'fixed scroll-down' );
			}
			prevScroll = currentScroll;
		} );
	}

	// Custom code
	ieFix();

	navbarToggle();
	navbarColumns();
	navbarSticky();

	shortcodes();
	showAjaxLoading();
	contactPage();
	contactForm();
	searchWidget();
} );
