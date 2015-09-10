<?php
/*
Plugin Name: 7 Compatibility
Plugin URI: http://www.7listings.net
Description: Maintain the compatibility for previous version of 7listings theme
Version: 2.0
Author: Rilwis
Author URI: http://www.deluxeblogtips.com
*/

define( 'SL_COMPATIBILITY_DIR', plugin_dir_path( __FILE__ ) );
if ( '7listings' == get_option( 'template' ) )
{
	if ( ! is_admin() )
	{
		require SL_COMPATIBILITY_DIR . 'shortcodes.php';
		new Sl_Compatibility_Shortcodes;
	}
	require SL_COMPATIBILITY_DIR . 'widgets.php';
}
