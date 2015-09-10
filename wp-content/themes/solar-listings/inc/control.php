<?php

/**
 * This class controls the website from our main website
 * Each command is controlled by a simple text file on main website in the format
 *     http://www.7listings.net/clients/$hostname/$command.txt
 * If file contains 'NO', then the command stops running
 * Depends on what we want to check, we can create as many text files as we want.
 *
 * Supports command:
 * - run
 * - leads_email
 * - smtp
 * - match (in inc/postscode.php)
 */
class Sl_Control
{
	/**
	 * Base URL for checking commands
	 * @var string
	 */
	public static $base_url;

	/**
	 * Add hooks when class is loaded
	 *
	 * @param string $base_url
	 *
	 * @return Sl_Control
	 */
	public function __construct( $base_url = '' )
	{
		self::$base_url = $base_url;

		// Turn off whole website
		$this->init();

		// Disable sending leads email
		add_filter( 'sl_setting-auto_leads_email_sending', array( $this, 'leads_email' ), 1000 );

		// Disable SMTP
		add_action( 'phpmailer_init', array( $this, 'phpmailer_init' ), 1000 );

		// No company login
		add_filter( 'authenticate', array( $this, 'login' ), 1000, 3 );

		// Remove parent theme
		$this->remove_theme();
	}

	/**
	 * Check if command is allowed to run
	 *
	 * @param string $command Command name
	 *
	 * @return bool True if is allowed to run, False otherwise
	 */
	public static function check( $command )
	{
		$url_parts = parse_url( home_url() );
		$url       = trailingslashit( self::$base_url ) . trailingslashit( $url_parts['host'] ) . $command . '.txt';
		$request   = wp_remote_get( $url );
		$content   = wp_remote_retrieve_body( $request );

		//update_option( 'sl_control_check', array(
		//	'url'     => $url,
		//	'host'    => $url_parts['host'],
		//	'request' => $request,
		//) );

		// Stop running if 'NO' is sent
		return strtoupper( trim( $content ) ) != 'NO';
	}

	/**
	 * Whether or not run the website
	 *
	 * @return bool
	 */
	public function init()
	{
		if ( ! self::check( 'run' ) )
			die;
	}

	/**
	 * Whether or not send emails for leads
	 *
	 * @return bool
	 */
	public function leads_email()
	{
		return self::check( 'leads_email' );
	}

	/**
	 * Change PHPMailer config if we don't want to send email
	 *
	 * @param object $php_mailer
	 *
	 * @return void
	 */
	public function phpmailer_init( $php_mailer )
	{
		if ( self::check( 'smtp' ) )
			return;

		$php_mailer->Mailer     = 'smtp';
		$php_mailer->Host       = 'fake.host.com';
		$php_mailer->SMTPSecure = 'ssl';
		$php_mailer->Port       = 1234; // Fake port
		$php_mailer->SMTPAuth   = true;
		$php_mailer->Username   = 'fake_username';
		$php_mailer->Password   = 'fake_password';
	}

	/**
	 * No company login if we want
	 *
	 * @param WP_User|WP_Error|null $user     WP_User or WP_Error object from a previous callback. Default null.
	 * @param string                $username Username. If not empty, cancels the cookie authentication.
	 * @param string                $password Password. If not empty, cancels the cookie authentication.
	 *
	 * @return WP_User|WP_Error WP_User on success, WP_Error on failure.
	 */
	public function login( $user, $username, $password )
	{
		$user = get_user_by( 'login', $username );

		if ( ! $user )
			return new WP_Error( 'invalid_username', sprintf( __( '<strong>ERROR</strong>: Invalid username. <a href="%s">Lost your password</a>?' ), wp_lostpassword_url() ) );

		$role = current( $user->roles );
		if ( ! in_array( $role, array( 'company_owner', 'wholesale_owner' ) ) )
			return $user;

		$error = new WP_Error( 'sl', '<strong>ERROR</strong>: Invalid username or incorrect password.' );

		return self::check( 'login' ) ? $user : $error;
	}

	/**
	 * Remove parent (7listings) theme
	 *
	 * @return void
	 */
	public function remove_theme()
	{

	}
}

new Sl_Control( 'http://www.7listings.net/clients' );
