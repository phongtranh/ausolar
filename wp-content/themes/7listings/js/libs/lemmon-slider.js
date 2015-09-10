/*
 * Modified from Lemmon Slider - Simple and lightweight slider/carousel supporting variable elements/images widths.
 * http://jquery.lemmonjuice.com/plugins/slider-variable-widths.php
 * Copyright (c) 2011 Jakub Pelák <jpelak@gmail.com>
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 */
(function ( $ )
{
	var methods = {
		init   : function ( options )
		{
			options = $.extend( {}, $.fn.lemmonSlider.defaults, options );

			return this.each( function ()
			{
				var $slider = $( this ),
					data = $slider.data( 'slider' );

				if ( data )
					return;

				var $sliderContainer = $slider.find( options.slider ),
					$items = $sliderContainer.find( options.items ),
					originalWidth = 1;

				$items.each( function ()
				{
					originalWidth += $( this ).outerWidth( true )
				} );
				$sliderContainer.width( originalWidth );

				// slide to last item
				if ( options.slideToLast ) $sliderContainer.css( 'padding-right', $slider.width() );

				// infinite carousel
				if ( options.infinite )
				{
					$slider.attr( 'data-slider-infinite', true );

					originalWidth = originalWidth * 3;
					$sliderContainer.width( originalWidth );

					$items.clone().addClass( '-after' ).insertAfter( $items.filter( ':last' ) );
					$items.filter( ':first' ).before( $items.clone().addClass( '-before' ) );

					$items = $sliderContainer.find( options.items );
				}

				$slider.items = $items;
				$slider.options = options;

				// attach events
				$slider.bind( 'nextSlide', function ()
				{
					var scroll = $slider.scrollLeft(),
						x = 0,
						slide = 0;

					$items.each( function ( i )
					{
						var left = $( this ).position().left;
						if ( x == 0 && left > 1 )
						{
							x = left;
							slide = i;
						}
					} );

					if ( x > 0 && $sliderContainer.outerWidth() - scroll - $slider.width() - 1 > 0 )
					{
						slideTo( $slider, scroll + x, slide, options.transitionSpeed );
					}
					else if ( options.loop )
					{
						// Return to first
						slideTo( $slider, 0, 0, options.transitionSpeed );
					}
				} );

				$slider.data( 'slider', 1 );
			} );
		}
	};

	function slideTo( $slider, x, i, t )
	{
		$slider.items.filter( 'li:eq(' + i + ')' ).addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
		$slider.animate( { 'scrollLeft': x }, t );
	}

	$.fn.lemmonSlider = function ( method, options )
	{
		if ( methods[method] )
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );

		if ( typeof method === 'object' || !method )
			return methods.init.apply( this, arguments );

		$.error( 'Method ' + method + ' does not exist on jQuery.lemmonSlider' );
	};

	$.fn.lemmonSlider.defaults = {
		items          : '> *',
		loop           : true,
		slideToLast    : false,
		slider         : '> *:first',
		infinite       : false,
		transitionSpeed: 600
	};
})( jQuery );
