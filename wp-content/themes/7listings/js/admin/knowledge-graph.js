/* global jQuery */

jQuery( function ( $ )
{
	'use strict';
	
	/**
	 * Allow to select areas (countries) better with select2 library
	 *
	 * @return void
	 */
	function updateSelect2()
	{
		$( '.knowledge-graph-areas' ).select2( {
			width: 'resolve'
		} );
	}
	
	/**
	 * Delete phone settings in Knowledge Graph settings page
	 *
	 * @return void
	 */
	function deleteContactPoint()
	{
		$( '.delete-contact-point' ).click( function ( e )
		{
			e.preventDefault();
			
			var $thisContactPoint = $( this ).closest( '.contact-point' ),
				$contactLength = $( 'div.contact-point' ).size();
				
			if( $contactLength < 2 )
				return;
			
			$thisContactPoint.remove();
		} );
	}
	
	updateSelect2();
	deleteContactPoint();
	
	/**
	 * Add more phone settings in Knowledge Graph settings page
	 *
	 * @return void
	 */
	$( '#add-contact-point' ).click( function ( e )
	{
		e.preventDefault();

		// Reset all select2 to normal select dropdown
		$( '.knowledge-graph-areas' ).select2( 'destroy' );

		var $this = $( this ).closest( '.sl-settings' ),
			$contactPoint = $( '.contact-point:last' ).clone();
		
		$contactPoint.insertBefore( $this );
		deleteContactPoint();
		
		// Update inputs' name
		$contactPoint.find( ':input' ).each( function ()
		{
			var $input = $( this ),
				name = $input.attr( 'name' );
			$input.val( '' );
			if ( name )
			{
				/**
				 * Name has format 7listings[knowledge_graph_contact_points][index][param]([])
				 * Last brackets are for areas (countries) only
				 * We need to increase index
				 */
				name = name.replace( /(.*)\[(\d)\](\[.*\])(\[\])?$/, function ( match, p1, p2, p3, p4 )
				{
					return p1 + '[' + ( parseInt( p2, 10 ) + 1 ) + ']' + p3 + ( p4 ? '[]' : '' );
				} );
				$input.attr( 'name', name );
			}
		} );
		
		// Update new checkboxes
		$contactPoint.slUpdateCheckboxes();
		updateSelect2();
	} );
	
	/**
	 * Function check when knowledge graph type select changed
	 * If it has value 'Person', we will hide logo input
	 */
	function grapTypeEvent()
	{
		var type = $( '.knowledge_graph_type' ).val(),
			logoField = $( '.knowledge_graph_type' ).closest( '.sl-settings' ).next();
		
		if ( type === 'person' )
		{
			logoField.slideUp();
		} else {
			logoField.slideDown();
		}
	}
	
	grapTypeEvent();
	
	$( '.knowledge_graph_type' ).change( function ()
	{
		grapTypeEvent();
	} );
	
} );