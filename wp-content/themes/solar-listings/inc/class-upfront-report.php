<?php

class Solar_Upfront_Report
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
		$page = add_submenu_page( '7listings', __( 'Upfront Report', '7listings' ), __( 'Upfront Report', '7listings' ), 'edit_theme_options', 'upfront-report', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	public function show()
	{
		// Get all companies which has purchased leads and have Upfront payment type
		$companies = get_posts( array(
			'post_type' 		=> 'company',
			'posts_per_page' 	=> -1,
			'meta_query'		=> array(
				'relation' => 'AND',
				array(
					'key' 	=> 'leads_payment_type',
					'value' => 'upfront'
				),
				array(
					'key' 		=> 'leads_count',
					'value' 	=> '',
					'compare' 	=> 'NOT LIKE'
				)
			)
		) );

		// Get all leads of this month
		$all_leads = \GFAPI::get_entries(1);

		include CHILD_DIR . 'inc/admin-pages/upfront-report.php';
	}
	
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue()
	{
		//wp_register_script( 'google', 'https://www.google.com/jsapi', '', '', true );
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
		wp_enqueue_style('select2');
		wp_enqueue_script('select2');
		
		//wp_enqueue_script( 'solar-leads-report', CHILD_URL . 'js/admin/leads-report.js', array( 'google', 'jquery' ), '', true );
	}
}

new Solar_Upfront_Report;