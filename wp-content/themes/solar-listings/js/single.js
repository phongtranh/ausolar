jQuery( function ( $ )
{
	// Send email to company
	$( '#email-company' ).on( 'submit', function ( e )
	{
		e.preventDefault();

		var $this = $( this ),
			$status = $( '#status' ),
			$ajaxLoading = $( '.ajax-loading' );

		$ajaxLoading.show();
		$status.removeClass( 'alert alert-error alert-success' );

		$.post( Sl.ajaxUrl, {
			action     : 'solar_email_company',
			id         : SolarSingle.id,
			_ajax_nonce: SolarSingle.nonce,
			name       : $this.find( 'input[name="name"]' ).val(),
			email      : $this.find( 'input[name="email"]' ).val(),
			phone      : $this.find( 'input[name="phone"]' ).val(),
			message    : $this.find( 'textarea[name="message"]' ).val()
		}, function ( r )
		{
			$ajaxLoading.hide();
			$status.text( r.data );
			if ( !r.success )
				$status.addClass( 'alert alert-error' );
			else
				$status.addClass( 'alert alert-success' );
		} );
	} );

	// Show more brands
	$( '#show-brands' ).on( 'click', function ( e )
	{
		e.preventDefault();
		$( this ).hide();
		$( '#other-brands' ).slideToggle();
		$( window ).trigger( 'scroll' );
	} );

	// show more product
	$( '#show-all-product' ).on( 'click', function ( e )
	{
		$('.other-products').css({display: 'block'});
		e.preventDefault();
		
		$( '#show-all-product' ).slideToggle();
		$( window ).trigger( 'scroll' );
		$( '#show-all-product' ).hide();
	} );

	// show more service
	$( '#show-all-service' ).on( 'click', function ( e )
	{
		$('.other-services').css({display: 'block'});
		e.preventDefault();
		
		$( '#show-all-service' ).slideToggle();
		$( window ).trigger( 'scroll' );
		$( '#show-all-service' ).hide();
	} );

	// Show more content
	$( '#show-content' ).on( 'click', function ( e )
	{
		e.preventDefault();
		$( this ).hide();
		$( '#more-content' ).fadeIn();
	} );

	// Remove (hide) all fields in comment form when reply
	$( '.commentlist' ).on( 'click', '.comment-reply-link', function ()
	{
		var $form = $( '#comment-form' ),
			selectors = ['.light', '.comment-form-location', '.comment-rates', '.comment-questions'];

		for ( var i = 0, l = selectors.length; i < l; i++ )
		{
			$form.find( selectors[i] ).hide(); // Hide elements, don't remove it because we need it for other comments
		}
	} );
} );
