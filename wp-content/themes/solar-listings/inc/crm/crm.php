<?php

namespace ASQ\Crm;

/**
 * Class Crm
 * 
 * @package ASQ\Crm
 */
class Crm
{
	public function __construct()
	{
		include __DIR__ . '/helper.php';

		add_action( 'sl_admin_menu', array( $this, 'add_page' ) );

		add_action( 'gform_after_submission_27', array( $this, 'create_from_27' ), 10, 1 );
		add_action( 'gform_after_submission_54', array( $this, 'create_from_54' ), 10, 1 );
		add_action( 'gform_after_submission_55', array( $this, 'create_from_55' ), 10, 1 );

		add_action( 'gform_after_submission_1', array( $this, 'update_received_status' ), 99, 1 );
		add_filter( 'gform_confirmation', array( $this, 'custom_confirmation'), 10, 4 );
		add_action( 'wp_ajax_update_lead_45', array ( $this, 'update_lead' ) );
		add_action( 'wp_ajax_get_notes_lead_45', array ( $this, 'get_notes' ) );
	}

	/**
	 * Add admin page
	 *
	 * @return void
	 */
	public function add_page()
	{
		$page = add_submenu_page( '7listings', __( 'Leads Management', '7listings' ), __( 'Leads Management',
			'7listings' ), 'edit_theme_options', 'leads-management', array( $this, 'show' ) );

		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	public function show()
	{
		include __DIR__ . '/leads.php';
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_register_script( 'angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.16/angular.min.js', '', '', true );
		wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/7admin.less' );
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );

		wp_enqueue_script( 'angular' );
		wp_enqueue_script( 'solar_crm', CHILD_URL . 'js/admin/crm.js', array( 'angular' ), '', true );
	}

	/**
	 * Update the active lead
	 */
	public function update_lead()
	{
		// Get the $_POST variable, it's a Lead returned as JSON
		$data = file_get_contents( 'php://input' );

		// Cast the JSON to Array
		$lead = json_decode( $data, true );

		\ASQ\Crm\Helper::update_lead( $lead );

		echo __( 'Lead was updated successfully!', '7listings' );

		exit( 0 );
	}

	public function get_notes()
	{
		$lead_id 	= $_GET['lead_id'];

		$notes 		= \ASQ\Crm\Helper::get_notes( $lead_id );

		echo json_encode( $notes );

		exit(0);
	}

	/**
	 * Todo: Merge 3 methods below to 1
	 */
	public function create_from_27( $lead )
	{
		\ASQ\Crm\Helper::create_lead_from_gravity_form( $lead, 27 );
	}

	public function create_from_54( $lead )
	{
		\ASQ\Crm\Helper::create_lead_from_gravity_form( $lead, 54 );
	}

	public function create_from_55( $lead )
	{
		\ASQ\Crm\Helper::create_lead_from_gravity_form( $lead, 55 );
	}

	/**
	 * Update status from #45 if the lead is transfered to #1 
	 * 
	 * @param  Mixed $lead Lead, auto passed by hook
	 * @return void
	 */
	public function update_received_status( $lead )
	{
		// Check Form #45 if has email exists on #1
		$exists = \GFAPI::get_entries( 45, array( 
			'field_filters' => array(
				'mode' => 'any',
				array( 'value' => $lead[11] ),
				array( 'value' => $lead[3] )
			)
		), null, array( 'offset' => 0, 'page_size' => 1 ) );

		// If so, update the is_starred of #45
		if ( $exists )
		{
			$crm_lead 				= $exists[0];
			$crm_lead['is_starred'] = 1;
			$crm_lead[4] 			= 'Interested';
			$crm_lead['user_agent'] = 'green';
			
			\GFAPI::update_entry( $crm_lead );
		}
	}

	/**
	 * Redirect the parent URL when processing iframe form complete
	 * 
	 * @return mixed
	 */
	public function custom_confirmation( $confirmation, $form, $lead, $ajax )
	{
		// Redirect the parent window if in iframe
		if ( str_contains( $lead['source_url'], array( 'australiansolarquotes.com.au/cta', 'postcode' ) ) )
			$confirmation = str_replace( 'document.location.href', 'window.top.location.href', $confirmation );

		return $confirmation;
	}
}

new Crm;