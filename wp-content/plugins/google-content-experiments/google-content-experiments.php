<?php
/* 
Plugin Name: Google Content Experiments
Version: 1.0.3
Description: This plugin enables you to use Google Content Experiments on your WordPress site.
Author: Glenn Mulleners
Author URI: http://wp-expert.nl
License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

if ( !defined('WPE_GCE_PATH') )
	define( 'WPE_GCE_PATH', plugin_dir_path( __FILE__ ) );

load_plugin_textdomain( 'gce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

if ( is_admin() && ( !defined('DOING_AJAX') || !DOING_AJAX ) && !class_exists( 'GCEAdmin' ) ) {
	include_once( WPE_GCE_PATH . 'admin/class-wpe-GCEAdmin.php' );
	add_action( 'load-post.php', 'load_GCEAdmin' );
	add_action( 'load-post-new.php', 'load_GCEAdmin' );
}

function load_GCEAdmin() 
{
	return new GCEAdmin;
}

if ( !class_exists( 'GCE' ) ) {
	include_once( WPE_GCE_PATH . 'class-wpe-GCE.php' );
	if( !is_admin() )
		$gce = new GCE();
}
	
?>