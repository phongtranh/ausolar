<?php
/*
Plugin Name: Inspector
Plugin URI: http://www.deluxeblogtips.com/inspector/
Description: Inpect hidden information of your WordPress websites for debugging
Version: 1.2.9
Author: Rilwis
Author URI: http://www.deluxeblogtips.com
*/

// Define plugin constants
define( 'RWI_URL', plugin_dir_url( __FILE__ ) );
define( 'RWI_JS', trailingslashit( RWI_URL . 'js' ) );
define( 'RWI_CSS', trailingslashit( RWI_URL . 'css' ) );
define( 'RWI_DIR', plugin_dir_path( __FILE__ ) );

if ( is_admin() )
	include_once RWI_DIR . 'inc/backend.php';
