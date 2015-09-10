// This file adds some LIVE to the Theme Customizer live preview.
( function( $ )
{
	// Site title
	var logo = $( '#site-title' ).find( 'img' )[0].outerHTML;
	listen( 'blogname', function( newval )
	{
		$( '#site-title' ).html( logo + newval );
	}, true );

	// Site description
	listen( 'blogname', function( newval )
	{
		$( '#site-description' ).html( newval );
	}, true );

	// Body background
	listen( 'design_body_background', function( newval )
	{
		$( 'body' ).css( 'backgroundColor', newval );
	} );

	/**
	 * A wrapper for callback listener of customizer
	 * @param  string   id             Settings ID
	 * @param  function callback       Callback function, take new value as an argument
	 * @param  bool     setByWordPress Is the settings set by WordPress, or belong to theme
	 * @return void
	 */
	function listen( id, callback, setByWordPress )
	{
		var setByWordPress = setByWordPress || false;
		if ( !setByWordPress )
			id = '7listings[' + id + ']';

		wp.customize( id, function( value )
		{
			value.bind( callback );
		} );
	}
} )( jQuery );