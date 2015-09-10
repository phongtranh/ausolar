<?php

class Next_Match_Report
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
		$page = add_submenu_page( '7listings', __( 'Next Match Report', '7listings' ), __( 'Next Match Report', '7listings' ), 'edit_theme_options', 'next-match-report', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	public function show()
	{
		include __DIR__ . '/admin-pages/next-match-report.php';
	}

	public function enqueue()
	{
		//wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js', '', '', true );
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
	}
}

new Next_Match_Report;