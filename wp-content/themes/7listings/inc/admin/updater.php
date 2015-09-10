<?php

/**
 * Theme updater class
 */
class Sl_Theme_Updater
{
	/**
	 * API URL where theme info, download package are get
	 */
	const URL = 'http://downloads.7listings.net/themes.php';

	/**
	 * Theme slug
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Add hooks when initialize class
	 *
	 * @return Sl_Theme_Updater
	 */
	function __construct()
	{
		// Invalid license? No update!
		if ( ! Sl_License::is_activated() )
			return;

		$this->slug = get_template();

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_update' ) );
		add_filter( 'themes_api', array( $this, 'get_info' ), 10, 3 );
	}

	/**
	 * Check theme for updates
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	function check_update( $data )
	{
		if ( empty( $data->checked ) )
			return $data;

		$args           = $this->request_args();
		$args['action'] = 'theme_update';

		if ( false != ( $theme_data = $this->request( $args ) ) && version_compare( $data->checked[$this->slug], $theme_data['version'], '<' ) )
			$data->response[$this->slug] = $theme_data;

		return $data;
	}

	/**
	 * Get theme information
	 *
	 * @param bool   $false
	 * @param string $action
	 * @param object $args
	 *
	 * @return mixed
	 */
	function get_info( $false, $action, $args )
	{
		if ( isset( $args->slug ) && $args->slug != $this->slug && 'theme_information' == $action )
			return $false;

		$params           = $this->request_args();
		$params['action'] = 'theme_info';

		if ( false != ( $theme_data = $this->request( $params ) ) )
			return $theme_data;

		return $false;
	}

	/**
	 * Prepare request args to send to remote host
	 *
	 * @return array
	 */
	function request_args()
	{
		$theme = wp_get_theme( $this->slug );
		$args  = array(
			'slug'    => $this->slug,
			'email'   => sl_setting( 'license_email' ),
			'key'     => sl_setting( 'license_key' ),
			'version' => $theme->version,
		);

		return $args;
	}

	/**
	 * Send request to remote host
	 *
	 * @param array $args
	 *
	 * @return bool|mixed
	 */
	function request( $args )
	{
		$request  = wp_remote_post( self::URL, array(
			'body' => $args,
		) );
		$response = wp_remote_retrieve_body( $request );

		return $response ? maybe_unserialize( $response ) : false;
	}
}

add_action( 'admin_init', 'sl_check_update' );

/**
 * Check theme update
 *
 * @return void
 */
function sl_check_update()
{
	new Sl_Theme_Updater;
}
