<?php
add_action( 'admin_init', 'sl_email_preview' );

/**
 * Preview email
 *
 * @return void
 */
function sl_email_preview()
{
	if ( empty( $_GET['sl-email-preview'] ) || empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'preview-email' ) )
		return;

	$id     = $_GET['sl-email-preview'];
	$option = isset( $_GET['sl-option'] ) ? $_GET['sl-option'] : '';
	echo sl_email_content( $option, $id, array(), array(), true );
	die;
}

add_action( 'phpmailer_init', 'sl_email_filter' );

/**
 * Use SMTP to send email, config from name, from email
 *
 * @param object $php_mailer
 *
 * @return void
 */
function sl_email_filter( $php_mailer )
{
	// Content type
	$php_mailer->ContentType = 'text/html';

	// From name and email
	if ( $name = sl_setting( 'emails_from_name' ) )
		$php_mailer->FromName = sl_email_replace( $name );
	if ( $email = sl_setting( 'emails_from_email' ) )
		$php_mailer->From = sl_email_replace( $email );

	// Append header and footer
	global $sl_email_use_template;
	if ( ! empty( $sl_email_use_template ) || sl_setting( 'emails_use_template' ) )
	{
		ob_start();
		get_template_part( 'templates/email/header' );
		$header = ob_get_clean();
		$header = sl_email_replace( $header );
		ob_start();
		get_template_part( 'templates/email/footer' );
		$footer = ob_get_clean();
		$footer = sl_email_replace( $footer );

		// Make sure HTML is not encoded
		$php_mailer->Body = html_entity_decode( $php_mailer->Body );

		// This is default email sent by WordPress, wrap it into paragraphs
		if ( empty( $sl_email_use_template ) )
			$php_mailer->Body = wpautop( $php_mailer->Body );

		// Fix weird problem with reset URL
		$php_mailer->Body = preg_replace( '!<(http[^>]*?)>!', '$1', $php_mailer->Body );
		$php_mailer->Body = str_replace( '</http>', '', $php_mailer->Body );

		$php_mailer->Body = make_clickable( $php_mailer->Body );
		$php_mailer->Body = $header . $php_mailer->Body . $footer;
	}

	// SMTP config
	if ( sl_setting( 'emails_smtp_enable' ) )
	{
		$php_mailer->Mailer = 'smtp';
		$php_mailer->Host   = sl_setting( 'emails_smtp_host' );
		$secure             = sl_setting( 'emails_smtp_secure' );
		if ( $secure && 'none' != $secure )
			$php_mailer->SMTPSecure = $secure;
		$php_mailer->Port = sl_setting( 'emails_smtp_port' );
		$auth             = sl_setting( 'emails_smtp_auth' );
		if ( $auth )
		{
			$php_mailer->SMTPAuth = true;
			$php_mailer->Username = sl_setting( 'emails_smtp_username' );
			$php_mailer->Password = sl_setting( 'emails_smtp_password' );
		}
	}
}

// WooCommerce
add_action( 'woocommerce_email_header', 'sl_email_header', 1 );

/**
 * Remove WooCommerce email header template
 *
 * @return void
 */
function sl_email_header()
{
	// Use email template
	global $sl_email_use_template;
	$sl_email_use_template = true;

	global $wp_filter;
	foreach ( $wp_filter['woocommerce_email_header'] as $k => $v )
	{
		if ( ! isset( $v['sl_email_header'] ) )
			unset( $wp_filter['woocommerce_email_header'][$k] );
	}
}

add_action( 'woocommerce_email_footer', 'sl_email_footer', 1 );

/**
 * Remove WooCommerce email footer template
 *
 * @return void
 */
function sl_email_footer()
{
	global $wp_filter;
	foreach ( $wp_filter['woocommerce_email_footer'] as $k => $v )
	{
		if ( ! isset( $v['sl_email_footer'] ) )
			unset( $wp_filter['woocommerce_email_footer'][$k] );
	}
}
