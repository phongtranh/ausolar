/* global jQuery, SlPriceSlide */

jQuery( function ( $ )
{
	'use strict';

	// Get markup ready for slider
	$( '#min_price, #max_price' ).hide();
	$( '.price_slider, .price_label' ).show();

	// Price slider uses jquery ui
	var minPrice = $( '#min_price' ).data( 'min' ),
		maxPrice = $( '#max_price' ).data( 'max' ),
		currentMinPrice = parseInt( minPrice, 10 ),
		currentMaxPrice = parseInt( maxPrice, 10 );

	if ( SlPriceSlide.min_price && SlPriceSlide.max_price )
	{
		currentMinPrice = parseInt( SlPriceSlide.min_price, 10 );
		currentMaxPrice = parseInt( SlPriceSlide.max_price, 10 );
	}

	$( 'body' ).bind( 'price_slider_create price_slider_slide', function ( event, min, max )
	{
		var $from = $( '.price_slider_amount span.from' ),
			$to = $( '.price_slider_amount span.to' );
		if ( 'left' == SlPriceSlide.position )
		{
			$from.html( SlPriceSlide.symbol + min );
			$to.html( SlPriceSlide.symbol + max );
		}
		else if ( 'right' == SlPriceSlide.position )
		{
			$from.html( min + SlPriceSlide.symbol );
			$to.html( max + SlPriceSlide.symbol );
		}
		else if ( 'left_space' == SlPriceSlide.position )
		{
			$from.html( SlPriceSlide.symbol + '&nbsp;' + min );
			$to.html( SlPriceSlide.symbol + '&nbsp;' + max );
		}
		else if ( 'right_space' == SlPriceSlide.position )
		{
			$from.html( min + '&nbsp;' + SlPriceSlide.symbol );
			$to.html( max + '&nbsp;' + SlPriceSlide.symbol );
		}

		$( 'body' ).trigger( 'price_slider_updated', min, max );
	} );
	$( '.price_slider' ).slider( {
		range  : true,
		animate: true,
		min    : minPrice,
		max    : maxPrice,
		values : [currentMinPrice, currentMaxPrice],
		create : function ( event, ui )
		{
			$( '#min_price' ).val( currentMinPrice );
			$( '#max_price' ).val( currentMaxPrice );

			$( 'body' ).trigger( 'price_slider_create', [currentMinPrice, currentMaxPrice] );
		},
		slide  : function ( event, ui )
		{
			$( '#min_price' ).val( ui.values[0] );
			$( '#max_price' ).val( ui.values[1] );

			$( 'body' ).trigger( 'price_slider_slide', [ui.values[0], ui.values[1]] );
		},
		change : function ( event, ui )
		{
			$( 'body' ).trigger( 'price_slider_change', [ui.values[0], ui.values[1]] );
		}
	} );
} );
