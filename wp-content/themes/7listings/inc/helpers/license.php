<?php

class Sl_License
{
	/**
	 * @var array License types
	 */
	static $types = array(
		'7Pro'           => '7PRO-',
		'7Tours'         => '7T-',
		'7Attraction'    => '7Att-',
		'7Accommodation' => '7A-',
		'7Rental'        => '7R-',
		'7Products'      => '7P-',
		'7Comp'          => '7C-',
		'Basic'          => '7B-',
		'7Network'       => '7N-',
	);

	/**
	 * Set default settings for license
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	static function default_settings( $settings )
	{
		$settings = array_merge( array(
			'license_email' => '',
			'license_key'   => '',
			'license_valid' => false,
		), $settings );

		return $settings;
	}

	/**
	 * Get theme license type, e.g. product id
	 *
	 * @return string
	 */
	static function license_type()
	{
		if ( false == self::is_activated() )
			return false;

		$key = sl_setting( 'license_key' );
		foreach ( self::$types as $product_id => $prefix )
		{
			if ( 0 === strpos( $key, $prefix ) )
				return $product_id;
		}

		return false;
	}

	/**
	 * Check if a module is activated
	 *
	 * @param string $module Module name
	 *
	 * @return bool
	 */
	static function is_module_activated( $module )
	{
		$product_id = self::license_type();
		if ( false === $product_id )
			return false;

		switch ( $product_id )
		{
			case '7Network':
			case '7Pro':
				return true;
			case '7Tours':
				return in_array( $module, array( 'post', 'tour' ) );
			case '7Attraction':
				return in_array( $module, array( 'post', 'attraction' ) );
			case '7Accommodation':
				return in_array( $module, array( 'post', 'accommodation' ) );
			case '7Rental':
				return in_array( $module, array( 'post', 'rental' ) );
			case '7Products':
				return in_array( $module, array( 'post', 'product' ) );
			case '7Comp':
				return in_array( $module, array( 'post', 'company' ) );
			case 'Basic':
			default:
				return 'post' == $module;
		}
	}

	/**
	 * Check if module is activated and enabled
	 *
	 * @param string $module Module name
	 * @param bool   $check_activated
	 *
	 * @return boolean
	 */
	static function is_module_enabled( $module, $check_activated = true )
	{
		if ( $check_activated && ! self::is_module_activated( $module ) )
			return false;

		$enabled = true;
		$types   = sl_setting( 'listing_types' );
		switch ( $module )
		{
			case 'accommodation':
			case 'tour':
			case 'rental':
			case 'product':
			case 'company':
			case 'attraction':
				if (
					empty( $types )
					|| ! is_array( $types )
					|| ! in_array( $module, $types )
				)
					$enabled = false;
				break;
			case 'post':
				$enabled = true; // Always enable Post module
				break;
			case 'cart':
				$enabled = sl_setting( 'cart' ) && in_array( Sl_License::license_type(), array( '7Accommodation', '7Tours', '7Attraction', '7Rental', '7Pro', '7Network' ) );
				break;
		}

		return $enabled;
	}

	/**
	 * Check if license is activated
	 *
	 * @return bool
	 */
	static function is_activated()
	{
		return sl_setting( 'license_valid' );
	}

	/**
	 * Activate theme with given email and key
	 *
	 * @param string $email
	 * @param string $key
	 *
	 * @return bool Are email & key valid?
	 */
	static function activate( $email = '', $key = '' )
	{
		if ( ! $email || ! $key || ! is_email( $email ) )
			return false;

		// If license hasn't been checked yet, send request to server to check
		$found = false;
		foreach ( self::$types as $product_id => $prefix )
		{
			if ( 0 === strpos( $key, $prefix ) )
			{
				$found = true;
				break;
			}
		}
		if ( ! $found )
			return false;

		$base_url = 'http://7listings.net/';

		// Check license
		$args    = array(
			'wc-api'      => 'software-api',
			'request'     => 'check',
			'email'       => $email,
			'licence_key' => $key,
			'product_id'  => $product_id,
		);
		$url     = add_query_arg( $args, $base_url );
		$request = wp_remote_get( $url );

		if ( is_wp_error( $request ) )
			return false;

		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		if ( empty( $response ) || ! is_array( $response ) || ! isset( $response['success'] ) )
			return false;

		// If license is valid
		// - Always return true
		// - Send activation request to server to activate current license
		// - We don't need to check activation success or not (because it might be activated before)
		$args = array(
			'wc-api'      => 'software-api',
			'request'     => 'activation',
			'email'       => $email,
			'licence_key' => $key,
			'product_id'  => $product_id,
			'instance'    => parse_url( HOME_URL, PHP_URL_HOST ),
		);
		$url  = add_query_arg( $args, $base_url );
		wp_remote_get( $url );

		return true;
	}
}

add_filter( 'sl_default_settings', array( 'Sl_License', 'default_settings' ) );
