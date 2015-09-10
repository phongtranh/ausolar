<?php
/**
 * Plugin Name: Solar Saving Calculator Plugin
 * Description: Solar Saving Calculator.
 * Version: 1.0
 * Author: Kazi Rabiul & Fuad Bin
 * Author URI: http://www.newsquote.com.au
 * Text Domain: Solar Saving Calculator
 */

add_action('plugins_loaded', 'ssc_init');

// Load the plugin's text domain
function ssc_init()
{ 
	load_plugin_textdomain( 'verysimple', false, dirname( plugin_basename( __FILE__ ) ));
}

// Added by Tan
function ssc_is_required_asset()
{
	return is_page( 18033 ) || is_page( 20064 );
}

// Enqueues plugin scripts
function ssc_scripts()
{	
	
	if ( ssc_is_required_asset() )
	{
		wp_enqueue_style( 'ssc-custom-script', plugins_url('/css/ssc_style.css',__FILE__), array(), '1.1.2' );
		//wp_register_script( 'ssc-custom-script', plugins_url( '/js/jquery-1.10.2.min.js', __FILE__ ) );		
		//wp_enqueue_script( 'ssc-custom-script' );
	}
}

add_action( 'wp_enqueue_scripts', 'ssc_scripts' );

function wpb_adding_scripts() 
{
	if ( ssc_is_required_asset() )
	{
		wp_register_script('my_amazing_script', plugins_url('/js/solarcalc.js', __FILE__), array('jquery'), '1.1', true );
		wp_enqueue_script('my_amazing_script');
	}
}

add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' ); 

include 'ssc_index.php';