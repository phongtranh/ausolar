/* global jQuery, rw_date, SlMetaBox */

/**
 * This file contains common JS code for add new / edit pages for all custom post types
 *
 * @package 7listings
 */


// Global Javascript object of the theme
var Sl = Sl || {};

/**
 * Edit module
 */
Sl.edit = (function ( $ )
{
	'use strict';

	/**
	 * Private variables
	 * $body stores the reference to $( 'body' )
	 * It is set in init() function, because only then the document is ready
	 */
	var $body = $( 'body' ),
		today = new Date();

	today.setHours( 0, 0, 0, 0 );

	/**
	 * Get row info when add schedule
	 *
	 * @see checkOverlappingDates()
	 *
	 * @param $tr jQuery object for the row that has the clicked "Add Schedule" button
	 * @return [] Array of following info
	 * - row type: 'price', 'upsell' or 'allocation'
	 * - jQuery selector for same rows, used to get the same row and used in checkOverlappingDates() function
	 * - resource
	 * - Additional info, can be: price type (if row type is 'price') or upsell index (if row type is 'upsell')
	 */
	function getRowInfo( $tr )
	{
		var resource = $tr.data( 'resource' ),
			selector = 'tr[data-resource="' + resource + '"]',
			rowType, info;

		// Price
		if ( typeof $tr.data( 'type' ) != 'undefined' )
		{
			rowType = 'price';
			info = $tr.data( 'type' );
			selector += '[data-type="' + info + '"]';
		}
		// Upsell
		else if ( typeof $tr.data( 'upsell' ) != 'undefined' )
		{
			rowType = 'upsell';
			info = $tr.data( 'upsell' );
			selector += '[data-upsell="' + info + '"]';
		}

		return [rowType, selector, resource, info];
	}

	/**
	 * Generate based name for inputs of cloned row when click "Add Schedule" button
	 * e.g., season[0][upsells][0][2]
	 *
	 * @param $tr jQuery object for the row that has the clicked "Add Schedule" button
	 * @return string Name for inputs
	 */
	function getCloneScheduleName( $tr )
	{
		var rowInfo = getRowInfo( $tr ),
			$same = $tr.siblings( rowInfo[1] ),
			name;

		switch ( rowInfo[0] )
		{
			case 'price':
				// season[resourceIndex][type][index]
				name = 'season[' + rowInfo[2] + '][' + rowInfo[3] + '][' + $same.length + ']';
				break;
			case 'upsell':
				// season[resourceIndex][upsells][upsellIndex][index]
				name = 'season[' + rowInfo[2] + '][upsells][' + rowInfo[3] + '][' + $same.length + ']';
				break;
			default:
				// allocations[resourceIndex][index]
				name = 'allocations[' + rowInfo[2] + '][' + $same.length + ']';
		}
		return name;
	}

	/**
	 * Check overlapping dates when add schedules for price, upsell, allocations, etc.
	 *
	 * @param $clone The cloned row
	 */
	function checkOverlappingDates( $clone )
	{
		var $from = $clone.find( '.from' ),
			$to = $clone.find( '.to' );

		var rowInfo = getRowInfo( $clone ),
			$same = $clone.siblings( rowInfo[1] );

		$from.removeClass( 'hasDatepicker' ).attr( 'id', '' ).datepicker( {
			dateFormat   : 'd/m/yy',
			beforeShowDay: function ( date )
			{
				var ok = date >= today,
					toValue = $to.val();

				// Compare to "to" day
				if ( toValue )
					ok = ok && date <= rw_date.get_obj( toValue );

				// Disable dates that have been selected
				$same.each( function ()
				{
					var $t = $( this ),
						f = rw_date.get_obj( $t.find( '.from' ).val() ),
						t = rw_date.get_obj( $t.find( '.to' ).val() );

					ok = ok && ( date < f || date > t );
				} );

				return [ok, ''];
			}
		} );

		$to.removeClass( 'hasDatepicker' ).attr( 'id', '' ).datepicker( {
			dateFormat   : 'd/m/yy',
			beforeShowDay: function ( date )
			{
				var ok = date >= today,
					fromValue = $from.val(),
					fromDateObject = rw_date.get_obj( fromValue );

				// Compare to "from" day
				if ( fromValue )
					ok = ok && date >= rw_date.get_obj( fromValue );

				// Disable dates that have been selected
				$same.each( function ()
				{
					var $t = $( this ),
						f = rw_date.get_obj( $t.find( '.from' ).val() ),
						t = rw_date.get_obj( $t.find( '.to' ).val() );

					ok = ok && ( date < f || date > t );

					if ( fromValue && fromDateObject < f )
						ok = ok && date < f;
				} );

				return [ok, ''];
			}
		} );
	}

	return {
		/**
		 * Limit number of words for excerpt
		 *
		 * @return void
		 */
		limitExcerpt           : function ()
		{
			var $excerpt = $( '#excerpt' ),
				$excerptStats = $( '#postexcerpt' ),
				html = '<table cellspacing="0" class="excerpt-words-limit"><tr><td class="excerpt-word-count">' + SlMetaBox.wordCount + ' <span>0</span></td><td class="excerpt-words-left">' + SlMetaBox.wordsLeft + ' <span>' + SlMetaBox.excerptLimit + '</span></td></tr></tbody></table>',
				$count, $left,
				settings = {
					strip: /<[a-zA-Z\/][^<>]*>/g, // Strip HTML tags
					clean: /[0-9.(),;:!?%#$'"_+=\\/-]+/g, // Regex to remove punctuation, etc.
					split: /\s+/g
				};

			if ( !$excerpt.length )
				return;

			// Replace excerpt info with word count and words left
			$excerpt.siblings( 'p' ).remove();
			$excerptStats.append( html );
			$count = $excerptStats.find( '.excerpt-word-count span' );
			$left = $excerptStats.find( '.excerpt-words-left span' );

			$excerpt.keyup( function ()
			{
				var count, text = $excerpt.val();

				text = text.replace( settings.strip, ' ' )
					.replace( /&nbsp;|&#160;/gi, ' ' )
					.replace( settings.clean, '' )
					.split( settings.split );

				count = text.length;
				if ( count <= SlMetaBox.excerptLimit )
				{
					$count.html( count );
					$left.html( SlMetaBox.excerptLimit - count );
				}
				else
				{
					text = text.slice( 0, SlMetaBox.excerptLimit );
					$excerpt.val( text.join( ' ' ) );
				}
			} );
		},
		/**
		 * Add booking resource
		 *
		 * @param selector Selector for booking resource. This has to be used because each post type uses different selector ('.tour_detail', '.detail', etc.).
		 *    It should be improved to use 1 selector only.
		 *    Each post
		 *
		 *
		 * @return void
		 */
		addBookingResource     : function ( selector )
		{
			// Add booking
			$( '.add-booking' ).click( function ( e )
			{
				e.preventDefault();

				var $this = $( this ),
					$detail = $this.siblings( selector + ':last' ).clone();

				$detail.insertBefore( $this );

				// Update inputs' name
				$detail.find( ':input' ).each( function ()
				{
					var $t = $( this ),
						n = $t.attr( 'name' );
					$t.val( '' );
					if ( n )
					{
						n = n.replace( /(.*_)(\d)(\[.*\])?$/, function ( match, p1, p2, p3 )
						{
							return p1 + ( parseInt( p2, 10 ) + 1 ) + ( p3 ? ( '[]' == p3 ? p3 : '[0]' ) : '' );
						} );
						$t.attr( 'name', n );
					}
				} );

				// Reset time picker
				$detail.find( '.timepicker' ).removeClass( 'hasDatepicker' ).attr( 'id', '' ).timepicker();

				// Photo
				$detail.find( 'ul' ).remove();
				$detail.find( '.upload:gt(0)' ).remove(); // Keep only 1 upload box
				$detail.find( '.upload img' ).attr( 'src', '' ).addClass( 'hidden' );
				$detail.find( '.upload .delete-image' ).addClass( 'hidden' );

				// Upsells
				$detail.find( '.upsell:not(:last)' ).remove();

				// Make checkboxes work and and reset their "checked" status to false
				$detail.slUpdateCheckboxes().find( '.checkbox input' ).prop( 'checked', false ).val( 1 ).trigger( 'change' );

				$detail.trigger( 'sl_clone_resource' );

				Sl.admin.checkRequired();
			} );
		},
		/**
		 * Delete booking resource
		 *
		 * @return void
		 */
		deleteBookingResource  : function ()
		{
			$body.on( 'click', '.delete-booking', function ( e )
			{
				e.preventDefault();
				$( this ).parent().remove();
			} );
		},
		/**
		 * Initialize date and time picker
		 *
		 * @return void
		 */
		initDateTimePicker     : function ()
		{
			// Date picker
			$( '.datepicker' ).datepicker( {
				dateFormat   : 'd/m/yy',
				beforeShowDay: function ( date )
				{
					return [date >= today, ''];
				}
			} );

			// Time picker
			$( '.timepicker' ).timepicker();
		},
		/**
		 * Add more schedule (seasonal prices + allocations)
		 *
		 * @return void
		 */
		addSchedule            : function ()
		{
			$body.on( 'click', '.add-schedule', function ( e )
			{
				e.preventDefault();

				var $tr = $( this ).parents( 'tr' ),
					$clone = $tr.clone();

				// Insert new row, add CSS class '.secondary' and make it active
				$clone.insertAfter( $tr ).addClass( 'secondary' ).removeClass( 'disabled' );

				// Show "Add" button and hide "Delete" button
				$clone.find( '.add-schedule' ).removeClass( 'hidden' );
				$clone.find( '.delete-schedule' ).addClass( 'hidden' );

				// Clear the 'Scheduled price'
				$clone.find( '.seas-new-price span' ).text( '' );

				// For current row, show "Delete" button and hide "Add" button
				$tr.find( '.add-schedule' ).addClass( 'hidden' );
				$tr.find( '.delete-schedule' ).removeClass( 'hidden' );

				/**
				 * Changing input names
				 * We need to look for same rows, calculate the index and create the new name for all inputs
				 */
				var name = getCloneScheduleName( $tr );
				$clone.find( 'input' ).each( function ()
				{
					var $t = $( this ),
						n = $t.attr( 'name' );

					n = n.replace( /.*(\[.*\])$/, function ( match, p1 )
					{
						return name + p1;
					} );

					$t.attr( 'name', n );
					$t.val( '' );
				} );

				$clone.find( '.checkbox input' ).val( '1' ).attr( 'id', name + '[enable]' ).attr( 'checked', 'checked' );
				$clone.find( '.checkbox label' ).attr( 'for', name + '[enable]' );

				checkOverlappingDates( $clone );
			} );
		},
		/**
		 * Delete schedule
		 *
		 * @return void
		 */
		deleteSchedule         : function ()
		{
			$body.on( 'click', '.delete-schedule', function ( e )
			{
				e.preventDefault();

				var $tr = $( this ).closest( 'tr' ),
					rowInfo = getRowInfo( $tr ),
					$same = $tr.siblings( rowInfo[1] );

				// Cannot remove current row if there's no other rows
				if ( !$same.length )
					return;

				$tr.remove();

				/**
				 * Make the first row primary and show its label
				 * Label will automatically show if we remove 'secondary' class
				 */
				$same.first().removeClass( 'secondary' );
			} );
		},
		/**
		 * Change CSS class when switching on/off for price scheduling
		 *
		 * @return void
		 */
		switchScheduleClass    : function ()
		{
			$( '.scheduling' ).delegate( '.checkbox input', 'change', function ()
			{
				var $this = $( this ),
					$tr = $this.closest( 'tr' );

				$tr[$this.is( ':checked' ) ? 'removeClass' : 'addClass']( 'disabled' );
			} );
		},
		/**
		 * Update price for seasons by PERCENTAGE
		 *
		 * @return void
		 */
		updatePriceByPercentage: function ()
		{
			$body.delegate( '.seas-percentage input', 'blur', function ()
			{
				var $this = $( this ),
					val = $this.val(),
					$tr = $this.parents( 'tr' ),
					price = $tr.find( '.seas-price' ).text(),
					$newPrice = $tr.find( '.seas-new-price' );

				if ( !val )
					return;

				price = price ? parseFloat( price ) : 0;

				// Relative price
				if ( /[-+]/.test( val ) )
					val = 1 + parseFloat( val ) / 100;
				else
					val = parseFloat( val ) / 100;

				val = val * price;
				val = val.toFixed( 2 );

				$newPrice.find( 'input' ).val( 'percentage' );
				$newPrice.find( 'span' ).text( val );

				// Calculate fixed price
				if ( val > price )
				{
					val -= price;
					$tr.find( '.seas-fixed input' ).val( '+' + val );
				}
				else
				{
					val = price - val;
					$tr.find( '.seas-fixed input' ).val( '-' + val );
				}
			} );
		},
		/**
		 * Update price for seasons by FIXED price
		 *
		 * @return void
		 */
		updatePricesByFixed    : function ()
		{
			$body.delegate( '.seas-fixed input', 'blur', function ()
			{
				var $this = $( this ),
					val = $this.val(),
					$tr = $this.parents( 'tr' ),
					price = $tr.find( '.seas-price' ).text(),
					$newPrice = $tr.find( '.seas-new-price' );

				if ( !val )
					return;

				price = price ? parseFloat( price ) : 0;

				// Relative price
				if ( /[-+]/.test( val ) )
					val = parseFloat( val ) + price;

				val = parseFloat( val );
				val = val.toFixed( 2 );

				$newPrice.find( 'input' ).val( 'fixed' );
				$newPrice.find( 'span' ).text( val );

				// Calculate percentage
				val = val * 100 / price;
				if ( val > 100 )
				{
					val -= 100;
					$tr.find( '.seas-percentage input' ).val( '+' + val );
				}
				else
				{
					val = 100 - val;
					$tr.find( '.seas-percentage input' ).val( '-' + val );
				}
			} );
		},
		/**
		 * Toggle schedule for upsells
		 *  When click on upsells title in scheduling tab
		 * - Change icon
		 * - Toggle rows
		 *
		 * @return void
		 */
		toggleUpsellSchedule   : function ()
		{
			$body.on( 'click', '.upsells-title', function ( e )
			{
				e.preventDefault();

				var $this = $( this ),
					$icon = $this.find( 'span' ),
					$rows = $this.parent().nextUntil( '.title' );

				$icon.toggleClass( 'dashicons-arrow-down dashicons-arrow-up' );
				$rows.fadeToggle();
			} );

			// Show upsells schedule if it has values
			$( '.scheduling table' ).each( function ()
			{
				var $this = $( this ),
					hasValue = false;
				$this.find( '[data-upsell]' ).each( function ()
				{
					if ( $( this ).find( ':checked' ).length )
					{
						hasValue = true;
					}
				} );
				if ( hasValue )
					$this.find( '.upsells-title' ).trigger( 'click' );
			} );
		},
		/**
		 * Remove uploaded movie
		 *
		 * @return void
		 */
		removeMovie            : function ()
		{
			$( '.delete-movie' ).on( 'click', function ( e )
			{
				e.preventDefault();
				$( this ).parent().remove();
			} );
		},
		/**
		 * Run when document ready
		 *
		 * @return void
		 */
		init                   : function ()
		{
			$body = $( 'body' );

			Sl.edit.limitExcerpt();
			Sl.edit.deleteBookingResource();
			Sl.edit.initDateTimePicker();
			Sl.edit.addSchedule();
			Sl.edit.deleteSchedule();
			Sl.edit.switchScheduleClass();
			Sl.edit.updatePriceByPercentage();
			Sl.edit.updatePricesByFixed();
			Sl.edit.toggleUpsellSchedule();
			Sl.edit.removeMovie();

			// Check overlapping dates even for non-clone fields
			$( '.from-to tr' ).each( function ()
			{
				checkOverlappingDates( $( this ) );
			} );
		}
	};
})( jQuery );

// Run when document ready
jQuery( Sl.edit.init );
