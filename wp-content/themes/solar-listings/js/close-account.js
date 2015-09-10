jQuery( function ( $ )
{
	// Toggle choices based on value of select box
	$( '.toggle-choices select' ).change( function ()
	{
		var $this = $( this ),
			name = $this.attr( 'name' ),
			value = $this.val(),
			$el = $( '[data-name="' + name + '"]' );

		$el.slideUp().filter( '[data-value="' + value + '"]' ).slideDown();
	} ).trigger( 'change' );

	$( '#stop' ).click( function ( e )
	{
		e.preventDefault();

		$( '#modal-close-account' ).modal( 'hide' );
		$( $( this ).attr( 'href' ) ).modal( 'show' );
	} );

	// Validation for cancel
	$( '#modal-stop' ).submit( function ()
	{
		var value = $( 'select[name="cancel_reason"]' ).val(),
			$suspendDays = $( 'input[name="suspend_days"]' ),
			$otherReason = $( 'textarea[name="other_reason"]' );

		if ( 'other' == value && !$otherReason.val() )
		{
			$otherReason.addClass( 'is-empty' );
			alert( 'Please enter your reason' );
			return false;
		}
		if ( 'too_many_temp' == value && !$suspendDays.val() )
		{
			$suspendDays.addClass( 'is-empty' );
			alert( 'Please enter how many days you want to suspend your service for' );
			return false;
		}

		return true;
	} );


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

} );
