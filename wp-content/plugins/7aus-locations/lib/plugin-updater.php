<?php
namespace Sl;

/**
 * Plugin updater class
 *
 * @author Tran Ngoc Tuan Anh <anh@fitwp.com>
 * @link   http://www.deluxeblogtips.com
 */
class Plugin_Updater
{
	// API URL where plugin info, download package are get
	var $api_url;

	// API key, for checking license
	// TODO: Implement checking
	var $api_key;

	// Plugin slug (plugin_directory/plugin_file.php)
	var $plugin_slug;

	// Plugin file name
	var $slug;

	/**
	 * Check plugin for update
	 *
	 * @param string $plugin_slug Plugin slug
	 * @param string $api_url     API URL
	 * @param string $api_key     API key
	 */
	function __construct( $plugin_slug = '', $api_url = '', $api_key = '' )
	{
		if ( ! current_user_can( 'update_core' ) || empty( $plugin_slug ) || empty( $api_url ) )
			return;

		if ( false === strpos( $plugin_slug, '/' ) )
		{
			$slug        = $plugin_slug;
			$plugin_slug = "{$slug}/{$slug}.php";
		}
		else
		{
			$slug = end( explode( '/', $plugin_slug ) );
			$slug = str_replace( '.php', '', $slug );
		}

		$this->plugin_slug = $plugin_slug;
		$this->slug        = $slug;
		$this->api_url     = $api_url;
		$this->api_key     = $api_key;

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'get_info' ), 10, 3 );
	}

	/**
	 * Check plugin for updates
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
		$args['action'] = 'get_version';

		if (
			false != ( $version = $this->request( $args ) )
			&& version_compare( $data->checked[$this->plugin_slug], $version, '<' )
		)
		{
			$obj                                = new \stdClass();
			$obj->slug                          = $this->slug;
			$obj->new_version                   = $version;
			$obj->url                           = $this->api_url;
			$obj->package                       = add_query_arg( 'plugin', $this->slug, $this->api_url );
			$data->response[$this->plugin_slug] = $obj;
		}

		return $data;
	}

	/**
	 * Get plugin information
	 *
	 * @param bool   $false
	 * @param string $action
	 * @param object $args
	 *
	 * @return mixed
	 */
	function get_info( $false, $action, $args )
	{
		if ( ! isset( $args->slug ) || $args->slug !== $this->slug )
			return $false;

		$params           = $this->request_args();
		$params['action'] = 'get_info';

		if ( false != ( $plugin_data = $this->request( $params, true ) ) )
			return $plugin_data;

		return $false;
	}

	/**
	 * Prepare request args to send to remote host
	 *
	 * @return array
	 */
	function request_args()
	{
		$args = array(
			'slug'    => $this->slug,
			'api-key' => $this->api_key,
		);

		return $args;
	}

	/**
	 * Send request to remote host
	 *
	 * @param array $args       Query arguments
	 * @param bool  $serialized Is data serialized?
	 *
	 * @return bool|mixed
	 */
	function request( $args, $serialized = false )
	{
		$request = wp_remote_post( $this->api_url, array(
			'body' => $args,
		) );

		if ( ! is_wp_error( $request ) && ( 200 == $request['response']['code'] ) )
		{
			$response = wp_remote_retrieve_body( $request );

			if ( ! empty( $response ) )
			{
				if ( $serialized )
					$response = @unserialize( $response );

				return $response;
			}
		}

		return false;
	}
}
