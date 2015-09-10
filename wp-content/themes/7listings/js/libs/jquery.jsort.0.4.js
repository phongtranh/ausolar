/*
 * jSort - jQury sorting plugin
 * http://do-web.com/jsort/overview
 *
 * Copyright 2011, Miriam Zusin
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://do-web.com/jsort/license
 */
(function ( $ )
{
	$.fn.jSort = function ( options )
	{
		options = $.extend( {
			item : 'div',
			order: 'asc', //desc
			data : ''
		}, options );

		return this.each( function ()
		{
			var hndl = this, titles = [], i = 0;

			$( this ).find( options.item ).each( function ()
			{
				var $this = $( this ),
					txt = $this.data( options.data );

				txt = txt.toString().toLowerCase();

				titles.push( [txt, i] );
				$this.attr( "rel", "sort" + i );
				i++;
			} );

			this.sortABC = function ( a, b )
			{
				return a[0] > b[0] ? 1 : -1;
			};

			titles.sort( hndl.sortABC );
			if ( options.order == "desc" )
				titles.reverse( hndl.sortABC );

			for ( var t = 0; t < titles.length; t++ )
			{
				var el = $( hndl ).find( options.item + "[rel='sort" + titles[t][1] + "']" );
				$( hndl ).append( el );
			}

		} );
	};
})( jQuery );