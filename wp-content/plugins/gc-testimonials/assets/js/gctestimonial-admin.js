jQuery( function( $ )
{
	var $tabs = $( '#tabs' );

	if ( $tabs.length && jQuery().tabs )
	{
		$tabs.tabs();
	}
} );
