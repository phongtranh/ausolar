/* global jQuery, ajaxurl */

/**
 * This file contains all Javascript actions for edit booking details in admin
 */
jQuery( function ( $ )
{
	'use strict';

	// Global variables
	var $body   = $( 'body' );
	var today   = new Date();
	var obj     = {
		post_type       : $( '.sl-post-type:checked' ).val(),
		post_id         : 0,
		resource_index  : 0
	};
	var Resource = {};

	// When sl-post-type changed
	$( '.sl-post-type' ).on( 'click', function ( e )
	{
		clearFormInputs();

		obj.post_type = $( this ).val();

		showSpinner( '.post-type', true );
		showSpinner( '.select2', true );

		getListPosts();
	} );

	// When sl-post changed
	$( '.sl-post' ).on( 'change', function ( e )
	{
		clearFormInputs();

		obj.post_id = $( this ).val();

		showSpinner( '.select2', true );

		getListResources();
	} );

	// When sl-resource changed
	$( '.sl-resource' ).on( 'change', function ( e )
	{
		var val = $( this ).val();

		clearFormInputs();

		if ( '-1' != val )
		{
			obj.resource_index = val;
			showSpinner( '.sl-resource', true );
			getResourceInfo();
			toggleControl( 'step2', false );
			displayBookingResourceForm();
		}
	} );

	/**
	 * Get posts of a specific post type to sl-post
	 *
	 * @return void
	 */
	function getListPosts()
	{
		var $posts = $( '.sl-post' );

		$.get( ajaxurl, {
			action      : 'get_list_posts',
			_ajax_nonce : SlBookings.nonceGetListPosts,
			obj         : JSON.stringify( obj )
		}, function ( r )
		{
			if ( !r.success )
				return;

			$posts.empty().append( r.data.items );

			$posts.select2( 'val', $posts.val() );

			obj.post_id = $posts.val();

			showSpinner( '.post-type', false );

			getListResources();

		}, 'json' );

		$( '.total-prices' ).html( 0 );
	}

	getListPosts();

	/**
	 * Get resources of a specific post to sl-resource
	 *
	 * @return void
	 */
	function getListResources()
	{
		var $resources = $( '.sl-resource');

		$.get( ajaxurl, {
			action      : 'get_list_resources',
			_ajax_nonce : SlBookings.nonceGetListResources,
			obj         : JSON.stringify( obj )
		}, function ( r )
		{
			if ( !r.success )
				return;

			$resources.empty().append( r.data );

			showSpinner( '.select2', false );

		}, 'json' );
	}

	/**
	 * Display CPT booking resource form
	 *
	 * @return void
	 */
	function displayBookingResourceForm()
	{
		$( '.sl-post-type' ).each( function( i )
		{
			toggleControl( this.value + '-resources', true );
		} );

		toggleControl( $( '.sl-post-type:checked' ).val() + '-resources', false );
	}

	displayBookingResourceForm();

	/**
	 * Select rental from date
	 *
	 * @return void
	 */
	function selectRentalFromDate()
	{
		var $from = $( '#rental-checkin' );

		$from.datetimepicker( {
			minDate         : today,
			stepMinute      : 5,
			dateFormat      : 'd/m/yy',
			beforeShowDay   : function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect        : function( text )
			{
				totalPrice();
			}
		} );
	}

	/**
	 * Select rental to date
	 *
	 * @return void
	 */
	function selectRentalToDate()
	{
		var $from   = $( '#rental-checkin' ),
			$to     = $( '#rental-checkout' );

		$to.datetimepicker( {
			minDate         : today,
			stepMinute      : 5,
			dateFormat      : 'd/m/yy',
			beforeShowDay   : function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect        : function( text )
			{
				if ( $from.val() && $to.val() )
				{
					totalPrice();
					selectUpsells();
					$( '#rental-upsells' ).removeClass( 'hidden' );
					toggleControl( 'step3', false );
				}
			}
		} );
	}

	selectRentalFromDate();
	selectRentalToDate();

	/**
	 * Select accommodation from date
	 *
	 * @return void
	 */
	function selectAccommodationFromDate()
	{
		var $from = $( '#accommodation-checkin' );

		$from.datepicker( {
			minDate         : today,
			dateFormat      : 'd/m/yy',
			beforeShowDay   : function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect        : function( text )
			{
				totalPrice();
			}
		} );
	}

	/**
	 * Select accommodation to date
	 *
	 * @return void
	 */
	function selectAccommodationToDate()
	{
		var $from   = $( '#accommodation-checkin' ),
			$to     = $( '#accommodation-checkout' );

		$to.datepicker( {
			minDate         : today,
			dateFormat      : 'd/m/yy',
			beforeShowDay   : function ( date )
			{
				return [$.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0, ''];
			},
			onSelect        : function( text )
			{
				totalPrice();
				if ( $from.val() && $to.val() )
				{
					toggleControl( 'step3', false );
				}
			}
		} );
	}

	selectAccommodationFromDate();
	selectAccommodationToDate();

	/**
	 * Select tour depart date
	 *
	 * @return void
	 */
	function selectTourDepart()
	{
		var $depart = $( '#tour-depart' );

		$depart.datepicker( {
			minDate         : today,
			dateFormat      : 'd/m/yy',
			beforeShowDay   : function ( date )
			{
				var day = date.getDay(),
					show;

				today.setHours( 0, 0, 0, 0 );
				show = date >= today && Resource.allocation > 0; // Check for past days
				show = show && $.inArray( rw_date.obj_to_euro( date ), Resource.unbookable ) < 0; // Check for unbookable days
				if ( Resource.allowedDays )
				{
					show = show && -1 != Resource.allowedDays.toString().indexOf( day ); // Check for allowed days
				}

				return [show, ''];
			},
			onSelect    : function( text )
			{
				if ( $depart.val() )
				{
					getTourAllocations();
					$( '#tour-upsells' ).removeClass( 'hidden' );
					toggleControl( 'step3', false );
				}
			}
		} );
	}

	selectTourDepart();

	/**
	 * Get allocation of a specific resource, use only on tour
	 *
	 * @return void
	 */
	function getTourAllocations()
	{
		$.get( ajaxurl, {
			action      : 'get_tour_allocations',
			_ajax_nonce : SlBookings.nonceGetTourAllocations,
			obj         : JSON.stringify( obj ),
			depart      : $( '#tour-depart').val()
		}, function ( r ) {
			if ( !r.success )
				return;

			var $tourUpsells = $( '#tour-upsells' );

			$tourUpsells.empty().append( r.data );
			$tourUpsells.append( Resource.upsells );

			selectGuests();
			selectUpsells();
			totalPrice();

		}, 'json' );
	}

	/**
	 * Clear all inputs in form
	 *
	 * @return void
	 */
	function clearFormInputs()
	{
		var $step2 = $( '.step2' ),
			$step3 = $( '.step3' );

		$step2.find( 'input[type=text]' ).val( '' );
		$step2.find( 'select' ).empty();
		$step3.find( 'input[type=text], textarea' ).val( '' );

		$( '.total-prices' ).html( 0 );

		$( '.sl-post-type' ).each( function( i )
		{
			$( '#' + this.value + '-upsells' ).empty().addClass( 'hidden' );
		} );

		toggleControl( 'step2', true );
		toggleControl( 'step3', true );
	}

	$( '#update').on( 'click', function()
	{
		$( this ).addClass( 'hidden' );

		showSpinner( '#update', true );

		$.post( ajaxurl, {
			action      : 'add_booking',
			_ajax_nonce : SlBookings.nonceAddBooking,
			data        : $( '#booking-form input, select, textarea').serialize()
		}, function ( r ) {
			if ( !r.success )
				return;

			window.location.href = r.data;
		}, 'json' );
	} );

	$( '#accommodation-guests' ).on( 'change', function()
	{
		totalPrice();
	} );

	/**
	 * Show spinner when doing ajax
	 *
	 * @param $id   string
	 * @param $show boolean
	 *
	 * @return void
	 */
	function showSpinner( $id, $show )
	{
		var $spinner = $( $id ).next( '.spinner' );

		if ( $show )
			$spinner.css( { 'display': 'inline-block', 'visibility': 'inherit' } );
		else
			$spinner.css( 'display', 'none' );
	}

	/**
	 * Get resource's information and fill to inputs
	 *
	 * @return void
	 */
	function getResourceInfo()
	{
		$.get( ajaxurl, {
			action      : 'get_resource_info',
			_ajax_nonce : SlBookings.nonceGetResourceInfo,
			obj         : JSON.stringify( obj )
		}, function ( r ) {
			if ( !r.success )
				return;

			Resource = r.data;

			switch ( obj.post_type )
			{
				case 'accommodation':
					$( '#accommodation-guests' ).empty().append( Resource.numberOfGuests );
					$( '.occupancy' ).html( Resource.prices.occupancy );
					$( '.max-occupancy' ).html( Resource.prices.maxOccupancy );
					break;
				case 'rental':
					$( '#rental-upsells' ).empty().append( Resource.upsells );
					break;
				case 'tour':
					if ( Resource.tourDailyTime )
					{
						$( '#tour-time' ).empty().append( Resource.tourDailyTime );
						toggleControl( 'tour-daily-time', false );
					}
					else
						toggleControl( 'tour-daily-time', true );

					break;
			}

			showSpinner( '.sl-resource', false );

		}, 'json' );
	}

	/**
	 * Show total prices
	 *
	 * @return void
	 */
	function totalPrice()
	{
		var nights = 0,
			total = 0,
			from,
			to;

		switch ( obj.post_type )
		{
			case 'accommodation':
				var $from   = $( '#accommodation-checkin' ),
					$to     = $( '#accommodation-checkout' ),
					guests,
					$upsellItems,
					upsellLength,
					i;

				if ( '' == $from.val() || '' == $to.val() )
					return;

				from    = rw_date.toUSA( $from.val() );
				to      = rw_date.toUSA( $to.val() );
				guests  = parseInt( $( '#accommodation-guests' ).val() );

				if ( ! from || ! to )
					return;

				nights = rw_date.days_diff( from, to );

				if ( 0 === nights )
					nights = 1;

				Resource.prices.occupancy   = parseInt( Resource.prices.occupancy );
				Resource.prices.price       = parseFloat( Resource.prices.price );
				Resource.prices.priceExtra  = parseFloat( Resource.prices.priceExtra );

				total = nights * Resource.prices.price;

				if ( guests > Resource.prices.occupancy )
					total += ( guests - Resource.prices.occupancy ) * Resource.prices.priceExtra;

				break;

			case 'rental':
				var $from   = $( '#rental-checkin' ),
					$to     = $( '#rental-checkout' ),
					maxDays,
					dayPrices;

				if ( '' == $from.val() || '' == $to.val() )
					return;

				from    = rw_date.toUSA( $from.val() ),
				to      = rw_date.toUSA( $to.val() );

				if ( ! from || ! to )
					return;

				nights = rw_date.days_diff( from, to );

				if ( 0 === nights )
					nights = 1;

				maxDays     = Object.keys( Resource.prices.price ).length;
				dayPrices   = ( nights > maxDays ) ? Resource.prices.price[maxDays] : Resource.prices.price[nights];
				total       = parseFloat( dayPrices ) * nights;

				break;

			case 'tour':
				$( '.guest-type' ).each( function()
				{
					var $this   = $( this ),
						type    = $this.data( 'type' ),
						number  = $this.val();

					number = '-1' === number ? 0 : number;
					if ( Resource.prices.hasOwnProperty( type ) )
						total += parseInt( number ) * parseFloat( Resource.prices[type] );
				} );

				break;
		}

		if ( 'accommodation' != obj.post_type )
		{
			var upsellsNumber   = 0;
			$upsellItems        = $( '.upsells-item' );
			upsellLength        = $upsellItems.length;

			for( i = 0; i < upsellLength; i++ )
			{
				upsellsNumber   = ( '-1' == $upsellItems.eq( i ).val() ) ? 0 : $upsellItems.eq( i ).val();
				total           += parseInt( upsellsNumber ) * parseFloat( Resource.prices.upsellPrices[i] );
			}
		}

		$( '.total-prices' ).html( total );
		$( '#total-prices' ).val( total );
	}

	/**
	 * When Upsell selects change
	 *
	 * @return void
	 */
	function selectUpsells()
	{
		$( '.upsells-item' ).each( function()
		{
			$( this ).on( 'change', function()
			{
				totalPrice();
			} );
		} );
	}

	/**
	 * When select number of guests in tour
	 *
	 * @return void
	 */
	function selectGuests()
	{
		$( '.guest-type' ).each( function()
		{
			$( this ).on( 'change', function()
			{
				totalPrice();
			} );
		} );
	}

	/**
	 * Check guest information to show Update button
	 *
	 * @return void
	 */
	function displayUpdateButton()
	{
		var info = [ '#customer-first-name', '#customer-last-name', '#customer-email', '#customer-phone' ];
		var $update = $( '#update' );

		$.each( info, function( k, v )
		{
			$( v ).on( 'input', function()
			{
				$update.removeClass( 'hidden' );
				$.each( info, function( k2, v2 )
				{
					if ( ! $( v2 ).val().trim() )
						$update.addClass( 'hidden' );
				} );
			} );
		} );

	}

	displayUpdateButton();

	/**
	 * Set select2 to select option
	 */
	function setSelect2()
	{
		$( '.sl-post' ).select2();
	}

	setSelect2();

	/**
	 * Toggle a control
	 * @param name  string
	 * @param hide  boolean
	 */
	function toggleControl( name, hide )
	{
		$( '.' + name )[hide ? 'addClass' : 'removeClass']( 'hidden' );
	}

	/**
	 * Send booking email to admin or customers when edit booking in admin
	 *
	 * @return void
	 */
	function sendEmail()
	{
		$( '#sl-booking-apply' ).on( 'click', function ( e )
		{
			e.preventDefault();
			var booking_id = $( '#post_ID' ).val(),
				type = $( '#sl-booking-action' ).val(),
				$spinner = $( '#actions' ).find( '.spinner' ),
				$success = $( '.success' ),
				$error = $( '.error' );
			$success.addClass( 'hidden' );
			$error.addClass( 'hidden' );

			if ( !type )
				return;

			$success.addClass( 'hidden' );
			$error.addClass( 'hidden' );
			$spinner.css( 'display', 'inline-block' );

			$.post( ajaxurl, {
				action    : 'sl_booking_send_email',
				booking_id: booking_id,
				type      : type
			}, function ( r )
			{
				$spinner.hide();
				if ( r.success )
				{
					$success.removeClass( 'hidden' );
				}
				else
				{
					$error.removeClass( 'hidden' );
				}
			} );
		} );
	}

	/**
	 * Click edit icon will show inputs
	 *
	 * @return void
	 */
	function enableEdit()
	{
		$body.on( 'click', '.sl-edit', function ( e )
		{
			e.preventDefault();

			var $this = $( this ),
				$tr = $this.closest( 'tr' );

			$tr.find( '.view' ).addClass( 'hidden' ).end()
				.find( '.edit' ).removeClass( 'hidden' );
		} );
	}

	/**
	 * Click edit icon will show inputs customer message
	 *
	 * @return void
	 */
	function editCustomerMessage()
	{
		$body.on( 'click', '.edit-customer-message', function ( e )
		{
			e.preventDefault();

			$( '#notes' ).find( '.view' ).addClass( 'hidden' ).end()
				.find( '.edit' ).removeClass( 'hidden' );
		} );
	}

	sendEmail();
	enableEdit();
	editCustomerMessage();
} );
