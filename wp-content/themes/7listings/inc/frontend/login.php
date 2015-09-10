<?php

add_action( 'login_enqueue_scripts', 'sl_login_enqueue' );
add_filter( 'style_loader_tag', 'sl_login_remove_wp_styles', 10, 2 );

add_filter( 'login_headerurl', 'sl_login_headerurl' );
add_filter( 'login_headertitle', 'get_bloginfo' );

remove_filter( 'lostpassword_url', 'woocommerce_lostpassword_url' );

/**
 * Enqueue scripts and styles for login page
 *
 * @return void
 */
function sl_login_enqueue()
{
	wp_enqueue_style( 'sl-main' );
	wp_enqueue_style( 'sl-login', THEME_LESS . 'login.less' );
	wp_print_styles( array( 'sl-main', 'sl-login' ) );

	wp_enqueue_script( 'sl-login', THEME_JS . 'login.js', array( 'jquery' ), '', true );
	$params = array(
		'text' => array(
			'header'       => __( 'Login', '7listings' ),
			'headerReset'  => __( 'Reset Password', '7listings' ),
			'button'       => __( 'Sign in', '7listings' ),
			'buttonReset'  => __( 'Reset', '7listings' ),
			'lostPassword' => __( 'Lost Your Password?', '7listings' ),
			'username'     => __( 'Username', '7listings' ),
			'password'     => __( 'Password', '7listings' ),
			'resetPass1'   => __( 'New Password', '7listings' ),
			'resetPass2'   => __( 'Confirm New Password', '7listings' ),
		),
	);
	if ( sl_setting( 'logo_display' ) && sl_setting( 'logo' ) )
	{
		$params['logo']               = wp_get_attachment_url( sl_setting( 'logo' ) );
		$params['logo_width']         = sl_setting( 'logo_width' );
		$params['logo_height']        = sl_setting( 'logo_height' );
		$params['display_site_title'] = sl_setting( 'display_site_title' );
	}
	wp_localize_script( 'sl-login', 'Sl', $params );
}

/**
 * Remove WP styles for login page
 *
 * @param $tag
 * @param $handle
 *
 * @return string
 */
function sl_login_remove_wp_styles( $tag, $handle )
{
	if ( in_array( $handle, array( 'login' ) ) )
		$tag = '';

	return $tag;
}

/**
 * Change login header URL
 *
 * @return string
 */
function sl_login_headerurl()
{
	return HOME_URL;
}
