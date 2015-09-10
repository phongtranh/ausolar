<?php
/*
Plugin Name: 7 History
Plugin URI: http://7listings.net
Description: Log all activities for users in 7listings theme
Version: 1.1
Author: Rilwis
Author URI: http://fitwp.com
*/

class SH
{
	/**
	 * Run when the plugin is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		self::constants();
		register_activation_hook( __FILE__, array( __CLASS__, 'create_table' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'update_db_check' ) );

		self::load_files();
	}

	/**
	 * Define plugin constants
	 *
	 * @return void
	 */
	public static function constants()
	{
		global $wpdb;

		define( 'SH_VERSION', '1.0' );
		define( 'SH_SETTINGS', '7history' );
		define( 'SH_TABLE', $wpdb->prefix . '7history' );
		define( 'SH_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Create table for plugin
	 *
	 * @return void
	 */
	public static function create_table()
	{
		$table_name = SH_TABLE;

		$sql = "CREATE TABLE $table_name (
			id int UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE PRIMARY KEY,
			time datetime NOT NULL,
			type tinytext NOT NULL,
			action tinytext NOT NULL,
			description text NOT NULL,
			object int NOT NULL,
			user int NOT NULL
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		self::update_option( 'version', SH_VERSION );
	}

	/**
	 * Check plugin version and update database structure if needed
	 *
	 * @return void
	 */
	public static function update_db_check()
	{
		$version = self::get_option( 'version' );
		if ( version_compare( SH_VERSION, $version, '>' ) )
			self::create_table();
	}

	/**
	 * Get plugin option
	 *
	 * @param string $name Option name
	 *
	 * @return mixed
	 */
	public static function get_option( $name )
	{
		static $settings = null;
		if ( null === $settings )
			$settings = get_option( SH_SETTINGS );
		return isset( $settings[$name] ) ? $settings[$name] : false;
	}

	/**
	 * Update plugin option
	 *
	 * @param string $name  Option name
	 * @param mixed  $value Option value
	 *
	 * @return mixed
	 */
	public static function update_option( $name, $value )
	{
		$settings = get_option( SH_SETTINGS );
		$settings[$name] = $value;
		update_option( SH_SETTINGS, $settings );
	}

	/**
	 * Log data to database
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public static function log( $data )
	{
		global $wpdb;
		$wpdb->insert( SH_TABLE, $data );
	}

	/**
	 * Load plugin files
	 *
	 * @return void
	 */
	public static function load_files()
	{
		require 'company.php';
		require 'display.php';
		require 'account.php';
		require 'user.php';
	}
}

SH::load();