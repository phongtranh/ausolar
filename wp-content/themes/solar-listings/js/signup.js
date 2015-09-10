jQuery( function ( $ )
{
	'use strict';

	// Toggle checkbox
	$( 'body' ).on( 'change', '.checkbox-toggle input', function ()
	{
		var $this = $( this ),
			$parent = $this.closest( '.checkbox-toggle' ),
			effect = $parent.data( 'effect' ),
			checked = $this.is( ':checked' ),
			$next = $parent.next(),
			inverse = $parent.data( 'inverse' );

		if ( !effect )
			effect = 'slide';

		if ( inverse )
		{
			if ( 'slide' == effect )
				$next[checked ? 'slideUp' : 'slideDown']();
			else if ( 'fade' == effect )
				$next[checked ? 'fadeOut' : 'fadeIn']();
		}
		else
		{
			if ( 'slide' == effect )
				$next[checked ? 'slideDown' : 'slideUp']();
			else if ( 'fade' == effect )
				$next[checked ? 'fadeIn' : 'fadeOut']();
		}
	} );
	$( '.checkbox-toggle input' ).trigger( 'change' );

	// Auto populate city based on value of suburb
	$( 'input[name="area"]' ).on( 'autocompletechange', function ( e, ui )
	{
		if ( !ui.hasOwnProperty( 'item' ) || !ui.item.hasOwnProperty( 'label' ) )
			return;

		var parts = (ui.item.label + ',').split( ',' ),
			city = parts[1];

		city = city.replace(/^\s+|\s+$/g, '');

		if ( ! city )
			return;

		$( 'input[name="city"]' ).val( city );
	} );
} );
