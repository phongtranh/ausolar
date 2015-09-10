(function ()
{
	'use strict';

	/**
	 * Show rating widget in a div container
	 * @param div Dom Node
	 */
	function showWidget( div )
	{
		var id = div.getAttribute( 'data-id' ),
			width = div.getAttribute( 'data-width' ),
			height = div.getAttribute( 'data-height' ),
			theme = div.getAttribute( 'data-theme' ),
			style,
			url = 'https://www.australiansolarquotes.com.au/company-rating-widget/?',
			iframe = document.createElement( 'iframe' ),
			link = document.createElement( 'a' ),
			text = document.createTextNode( 'AustralianSolarQuotes.com.au' );

		width = width || '100%';
		height = height || '165px';
		style = 'width:' + width + ';height:' + height;
		theme = theme || 'white';
		url += 'id=' + id;
		url += '&theme=' + theme;

		iframe.setAttribute( 'src', url );
		iframe.setAttribute( 'style', style );
		iframe.setAttribute( 'frameborder', 'no' );
		iframe.setAttribute( 'scrolling', 'no' );

		div.appendChild( iframe );

		link.setAttribute( 'href', 'https://australiansolarquotes.com.au' );
		if ( 'white' == theme )
		{
			link.setAttribute( 'style', 'color:#f0f0f0;display:block' );
		}
		else
		{
			link.setAttribute( 'style', 'color:#333;display:block' );
		}
		link.appendChild( text );
		div.appendChild( link );

		div.setAttribute( 'style', 'text-align:center' );
	}

	var widgets = document.querySelectorAll( '.asq-review' );
	for ( var i = 0, l = widgets.length; i < l; i++ )
	{
		showWidget( widgets.item( i ) );
	}
})();
