<?php

class Solar_Leads_Entries
{
	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'add_page' ) );
	}

	/**
	 * Add admin page
	 *
	 * @return void
	 */
	function add_page()
	{
		$page = add_menu_page( __( 'Leads', '7listings' ), __( 'Leads', '7listings' ), 'edit_posts', 'leads', array( $this, 'show' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	function show()
	{
		$file = 'leads-entries';
		if ( isset( $_GET['action'] ) && 'view_company_leads' == $_GET['action'] ){
			$file = 'company-leads';
			add_filter('gform_field_content', 'solar_fix_gf_field_markup', 10, 5);
			add_filter('gform_field_container', 'solar_fix_gf_container_markup', 10, 6);
		}

		echo '<div class="wrap">';
		include CHILD_DIR . "inc/admin-pages/$file.php";
		echo '</div>';
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
		//wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
		wp_enqueue_style( 'sl-admin', THEME_CSS . 'admin/admin.css' );
		wp_enqueue_style( 'sl-main' );
		//wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js', '', '', true );
		//wp_enqueue_script( 'solar-leads-report', CHILD_URL . 'js/admin/leads-report.js', array( 'angular', 'jquery' ), '', true );
		wp_enqueue_script( 'solar-company-leads', CHILD_URL . 'js/admin/company-leads.js', array( 'jquery' ), '', true );
		wp_localize_script( 'solar-company-leads', 'Solar', array(
			'nonceFill' => wp_create_nonce( 'fill' ),
			'nonceLog'  => wp_create_nonce( 'log' ),
			'homeUrl'   => HOME_URL,
		) );
	}
}

new Solar_Leads_Entries;
