<?php

class Company_Search
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
		$page = add_submenu_page( '7listings', __( 'Company Search', '7listings' ), __( 'Company Search', '7listings' ), 'edit_theme_options', 'company-search', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	public function show()
	{
		include __DIR__ . '/admin-pages/company-search.php';
	}

	public function enqueue()
	{
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
	}
}

new Company_Search;