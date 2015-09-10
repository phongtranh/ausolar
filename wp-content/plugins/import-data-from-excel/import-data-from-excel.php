<?php
/*
Plugin Name: Import data companies for ASQ
Plugin URI: http://fitwp.com
Description: Update or add new information about company, this plugin just uses only for project ASQ
Version: 1.0.0
Author: FitWP
Author URI: http://fitwp.com
*/

// Plugin paths, for including files
define( 'EXCEL_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXCEL_INC_DIR', trailingslashit( EXCEL_DIR . 'inc' ) );

// Define plugin URLs, for fast enqueuing scripts and styles
define( 'EXCEL_URL', plugin_dir_url( __FILE__ ) );
define( 'EXCEL_CSS_URL', trailingslashit( EXCEL_URL . 'css' ) );

if ( is_admin() )
{
	require EXCEL_INC_DIR . 'simplexlsx.class.php';
	require EXCEL_INC_DIR . 'functions.php';
	require EXCEL_INC_DIR . 'settings.php';
	require EXCEL_INC_DIR . 'import-data.php';
}
