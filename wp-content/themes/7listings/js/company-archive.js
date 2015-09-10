jQuery( function ( $ )
{
	'use strict';

	//	$( '#companies' ).tablesorter();
	$( "#asc_btn_desc" ).click( function ()
	{
		$( "#demo-divs" ).jSort( {
			sort_by: 'p.txt span',
			item   : 'div',
			order  : 'asc'
		} );
	} );
	$( '.sorter' ).on( 'click', 'div', function ()
	{
		var $this = $( this ),
			by = $this.data( 'sort_by' ),
			order = $this.data( 'order' );

		if ( !order || order == 'desc' )
			order = 'asc';
		else
			order = 'desc';

		$this.siblings().removeClass( 'asc desc' );
		$this.removeClass( 'asc desc' ).addClass( order ).data( 'order', order );

		$( '.companies' ).jSort( {
			item : '.company',
			data : by,
			order: order
		} );
	} );
} );
