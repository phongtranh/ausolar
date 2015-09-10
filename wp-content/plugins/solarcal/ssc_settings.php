<?php
/**
 * Plugin Name: Solar Savings Calculator
 * Description: Realtime solar savings calculator plugin. Built with AngularJS.
 * Version: 2.0
 * Author: Tan Nguyen <tan@fitwp.com>
 * Author URI: http://www.australiansolarquotes.com.au
 */

add_action('plugins_loaded', 'ssc_init');

// Load the plugin's text domain
function ssc_init()
{ 
	load_plugin_textdomain( '7listings', false, dirname( plugin_basename( __FILE__ ) ));
}

// Added by Tan
function ssc_is_required_asset()
{
	return is_page( 18033 ) || is_page( 20064 ) || is_page( 23934 );
}

// Enqueues plugin scripts
function ssc_scripts()
{	
	if ( ssc_is_required_asset() )
		wp_enqueue_style( 'calculator_css', plugins_url( '/css/calculator.css', __FILE__ ), array(), '1.1.2' );
}

add_action( 'wp_enqueue_scripts', 'ssc_scripts', 9999 );

function wpb_adding_scripts() 
{
	if ( ssc_is_required_asset() )
	{
		wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js', array(), '1.3', true );
		wp_register_script( 'calculator', plugins_url( '/js/app.js', __FILE__ ), array( 'angular' ), '1.0', true );
		wp_enqueue_script( 'calculator' );
	}
}

add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' ); 

include 'calculator.view.php';