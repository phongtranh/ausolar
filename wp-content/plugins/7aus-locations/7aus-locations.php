<?php
/*
Plugin Name: 7Listings - Australia Locations
Plugin URI: http://7listings.net
Description: Import location database for Australia
Version: 0.0.1
Author: 7 Listings
Author URI: http://7listings.net
*/

namespace Sl\Locations\Aus;

defined( 'ABSPATH' ) || exit;

// Autoload
if ( ! class_exists( '\Sl\Autoloader' ) )
	require_once plugin_dir_path( __FILE__ ) . 'lib/autoloader.php';

$loader = new \Sl\Autoloader;
$loader->register();
$loader->add_namespace( 'Sl', plugin_dir_path( __FILE__ ) . 'lib' );
$loader->add_namespace( 'Sl\Locations\Aus', plugin_dir_path( __FILE__ ) . 'inc' );

if ( is_admin() )
{
	require_once plugin_dir_path( __FILE__ ) . 'inc/admin.php';
	Admin::load();

	if ( defined( 'DOING_AJAX' ) )
	{
		require_once plugin_dir_path( __FILE__ ) . 'lib/simplexlsx.php';
		require_once plugin_dir_path( __FILE__ ) . 'inc/ajax.php';
		Ajax::load();
	}
}
