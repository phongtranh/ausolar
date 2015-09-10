<?php

class Wholesale_Report
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
		$page = add_submenu_page( '7listings', __( 'Wholesale Leads', '7listings' ), __( 'Wholesale Leads', '7listings' ), 'edit_theme_options', 'wholesale-report', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
		add_action( "load-$page", array( $this, 'csv_export' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	public function show()
	{
		include CHILD_DIR . 'inc/admin-pages/wholesale-report.php';
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue()
	{
		//wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js', '', '', true );
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );
	}

	/**
	 * Export report to CSV
	 * Must use inside "load-$page" hook to make sure parent theme is loaded, all functions like sl_setting() are available
	 */
	public function csv_export()
	{
		if( empty( $_GET['csv_export'] ) || $_GET['csv_export'] != 1 )
			return;

		include 'leads-csv.php';
		die;
	}
}

new Wholesale_Report;
