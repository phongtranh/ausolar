<?php
namespace ASQ\Xero;

/**
 * Class Customer
 * Add customer when a company signs up
 * @package ASQ
 */
class Customer
{
	/**
	 * @var \XeroOAuth OAuth object
	 */
	public static $oath;

	/**
	 * Run when file is loaded
	 *
	 * @throws \Exception
	 * @return void
	 */
	public static function load()
	{
		self::$oath = new \XeroOAuth ( array(
			'application_type'    => XERO_APP_TYPE,
			'oauth_callback'      => XERO_OAUTH_CALLBACK,
			'user_agent'          => XERO_APP_NAME,

			'consumer_key'        => XERO_CONSUMER_KEY,
			'shared_secret'       => XERO_SHARE_SECRET,
			'core_version'        => XERO_CORE_VERSION,
			'payroll_version'     => XERO_PAYROLL_VERSION,
			'rsa_private_key'     => XERO_RSA_PRIVATE_KEY,
			'rsa_public_key'      => XERO_RSA_PUBLIC_KEY,

			'access_token'        => XERO_CONSUMER_KEY,
			'access_token_secret' => XERO_SHARE_SECRET,
		) );

		// Check configuration
		$check_config = self::$oath->diagnostics();
		if ( $check_config )
		{
			foreach ( $check_config as $check )
			{
				throw new \Exception( 'Xero Configuration error: ' . $check );
			}
		}

		Helper::set_session( array(
			'oauth_token'          => self::$oath->config['consumer_key'],
			'oauth_token_secret'   => self::$oath->config['shared_secret'],
			'oauth_session_handle' => '',
		) );
	}

	/**
	 * Add new customer
	 *
	 * @param array $info Customer info
	 *
	 * @return bool True if successful, false if error
	 */
	public static function add( $info = array() )
	{
		$xml = Helper::build_customer_xml( $info );
		self::$oath->request( 'POST', self::$oath->url( 'Contacts', 'core' ), array(), $xml, 'json' );

		return 200 == self::$oath->response['code'];
	}
}
