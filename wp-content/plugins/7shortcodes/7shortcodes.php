<?php
/*
Plugin Name: 7 Shortcodes
Plugin URI: http://www.7listings.net
Description: Shortcodes for 7Listings theme.
Version: 0.1
Author: Rilwis
Author URI: http://www.deluxeblogtips.com
*/

define( 'SL_SHORTCODES_DIR', plugin_dir_path( __FILE__ ) );
define( 'SL_SHORTCODES_URL', plugin_dir_url( __FILE__ ) );
if ( ! is_admin() )
{
	require SL_SHORTCODES_DIR . 'inc/class-sl-shortcodes-frontend.php';
	new Sl_Shortcodes_Frontend;
}
elseif ( ! defined( 'DOING_AJAX' ) )
{
	require SL_SHORTCODES_DIR . 'inc/class-sl-shortcodes-admin.php';
	new Sl_Shortcodes_Admin;
}
