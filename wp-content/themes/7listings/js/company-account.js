jQuery( function ( $ )
{
	var $form = $( '#modal-change-account' ),
		$type = $form.find( 'select[name="membership"]' ),
		$time = $form.find( 'select[name="time"]' ),
		$price = $( '#membership-price' );
	$form.on( 'change', 'select', function ()
	{
		var name = 'price_' + $type.val() + '_' + $time.val();
		if ( name in SlCompanyAccount && SlCompanyAccount[name] )
			$price.text( SlCompanyAccount[name] + ' ' + SlCompanyAccount.currency );
		else
			$price.text( SlCompanyAccount.free );
	} );
	$type.trigger( 'change' );
} );