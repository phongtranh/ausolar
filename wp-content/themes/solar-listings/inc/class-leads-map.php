<?php

class Leads_Map
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
		$page = add_submenu_page( '7listings', __( 'Leads Map', '7listings' ), __( 'Leads Map', '7listings' ), 'edit_theme_options', 'leads-map', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	public function show()
	{
		include __DIR__ . '/admin-pages/leads-map.php';
	}

	public function enqueue()
	{
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
	}
}

new Leads_Map;