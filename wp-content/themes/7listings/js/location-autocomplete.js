jQuery( function ( $ )
{
	'use strict';

	/**
	 * Extend autocomplete library to allow to use HTML in item label
	 * We'll use it to highlight item name
	 */
	$.extend( $.ui.autocomplete.prototype, {
		_renderItem: function ( ul, item )
		{
			return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( $( "<a></a>" ).html( item.label ) )
				.appendTo( ul );
		}
	} );

	$( '.location-autocomplete' ).autocomplete( {
		delay : 500,
		source: function ( request, response )
		{
			$.post( SlLocation.ajaxUrl, {
				action: 'sl_location_autocomplete',
				term  : request.term,
				nonce : SlLocation.nonce,
				level : $( this.element ).data( 'level' )
			}, function ( r )
			{
				if ( r.success )
					response( r.data );
			}, 'json' );
		}
	} );
} );
