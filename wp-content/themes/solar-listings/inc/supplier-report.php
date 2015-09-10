<?php

class Solar_Supplier_Report
{
	
	public function __construct()
	{
		add_action( 'sl_admin_menu', array( $this, 'add_page' ) );
	}

	/**
	 * Add admin page
	 *
	 * @return void
	 */
	public function add_page()
	{
		$page = add_submenu_page( '7listings', __( 'Supplier Report', '7listings' ), __( 'Supplier Report', '7listings' ), 'edit_theme_options', 'supplier-report', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	public function show()
	{
		include CHILD_DIR . 'inc/admin-pages/supplier-report.php';
	}
	
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_register_script( 'google', 'https://www.google.com/jsapi', '', '', true );
		//wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js', '', '', true );
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
		wp_enqueue_style('select2');
		wp_enqueue_script('select2');
		
		wp_enqueue_script( 'solar-wholesale-report', CHILD_URL . 'js/wholesale-leads-report.js', array( 'google', 'jquery' ), '', true );
	}
}

new Solar_Supplier_Report;