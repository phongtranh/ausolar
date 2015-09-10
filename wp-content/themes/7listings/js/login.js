jQuery( function( $ )
{
	var $form = $( 'form' ),
		page = $form.attr( 'id' ).replace(/form/, ''),
		$message = $( '<div class="alert alert-error"></div>' ),
		$errorMessage = $( '.message' ).length ? $( '.message' ) : $( '#login_error' ),
		$a = $( '#nav a:last' );

	$( '#login' ).addClass( 'container' );
	$( '#backtoblog' ).remove();
	$a.attr( 'id', 'lost-pw' ).appendTo( $form );
	$( '#nav ' ).remove();
	$( '#wp-submit' ).remove();

	$form.addClass( 'form-signin' );

	// Logo
	if ( Sl.logo )
	{
		var $header = $( '#login h1 a' ),
			$img = $( '<img>' ).attr( 'src', Sl.logo );

		if ( Sl.display_site_title == 0 )
			$header.text( '' );

		$header.prepend( $img );
		if ( Sl.logo_width )
			$img.attr( 'width', Sl.logo_width );
		if ( Sl.logo_height )
			$img.attr( 'height', Sl.logo_height );
	}

	// Lost password page
	if ( 'lostpassword' == page )
	{
		var $userLogin = $( '#user_login' );

		$form.prepend( $userLogin )
			.prepend( '<h2 class="form-signin-heading">' + Sl.text.headerReset + '</h2>' )
			.append( '<button id="submit" class="button large login" type="submit">' + Sl.text.buttonReset + '</button>' )
			.find( 'p:not(:last)' ).remove();

		$loginError = $( '#login_error' ).addClass( 'alert alert-error' ).insertBefore( $form );
		$errorMessage.addClass( 'alert alert-info' );
	}

	// Login page
	else if ( 'login' == page )
	{
		var $rememberme = $( '#rememberme' ).parent(),
			$username = $( '#user_login' ),
			$password = $( '#user_pass' );

		$form.prepend( $rememberme )
			.prepend( $password )
			.prepend( $username )
			.prepend( '<h2 class="form-signin-heading">' + Sl.text.header + '</h2>' )
			.append( '<button id="submit" class="button large login" type="submit">' + Sl.text.button + '</button>' )
			.find( 'p:not(:last)' ).remove();

		if ( $errorMessage.length )
		{
			$message.html( $errorMessage.html() )
				.insertBefore( $form );
			$errorMessage.remove();

			// When enter correct username in Lost password page
			if ( /checkemail/.test( location.href ) )
			{
				$message.removeClass( 'alert-error' ).addClass( 'alert-success' );
			}
		}

		$rememberme.addClass( 'checkbox' );
		$username.addClass( 'input-block-level' ).attr( 'placeholder', Sl.text.username );
		$password.addClass( 'input-block-level' ).attr( 'placeholder', Sl.text.password );

		$a.text( Sl.text.lostPassword );
	}

	// Reset password page
	else
	{
		var $pass1 = $( '#pass1' ).addClass( 'input-block-level' ).attr( 'placeholder', Sl.text.resetPass1 ),
			$pass2 = $( '#pass2' ).addClass( 'input-block-level' ).attr( 'placeholder', Sl.text.resetPass2 );

		$pass1.parent().remove();
		$pass2.parent().remove();

		$form.prepend( $pass1 )
			.prepend( $pass2 )
			.prepend( '<h2 class="form-signin-heading">' + Sl.text.headerReset + '</h2>' )
			.append( '<button id="submit" class="button large login" type="submit">' + Sl.text.buttonReset + '</button>' );

		$( '#pass-strength-result' ).addClass( 'alert' );
		$( '.indicator-hint' ).addClass( 'alert' );
		$( '.reset-pass' ).addClass( 'alert alert-info' );
	}
} );
