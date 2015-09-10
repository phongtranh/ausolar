<?php
add_action( 'wp_ajax_solar_fill_leads_info', 'solar_ajax_fill_leads_info' );

/**
 * Ajax fill leads info
 *
 * @return void
 */
function solar_ajax_fill_leads_info()
{
	//	if ( !isset( $_POST['_wpnonce'] ) || !wp_verify_nonce( $_POST['_wpnonce'], 'fill' ) )
	//		wp_send_json_error();
	if ( empty( $_POST['id'] ) )
		wp_send_json_error();

	$lead = GFFormsModel::get_lead( $_POST['id'] );

	$data = array(
		'name'        => $lead['1.3'] . ' ' . $lead['1.6'],
		'email'       => $lead['11'],
		'mobile'      => $lead['3'],
		'system_size' => $lead['29'],
		'city'        => $lead['17.3'],
		'state'       => $lead['17.4'],
	);

	wp_send_json_success( $data );
}

add_action( 'template_redirect', 'solar_verify_map' );

/**
 * Show a map to verify service area before company owner rejects a lead
 *
 * @return void
 */
function solar_verify_map()
{
	if ( ! empty( $_GET['company_id'] ) && !empty( $_GET['lead_id'] ) )
	{
		locate_template( 'templates/company/user-admin/reject-map.php', true );
		die;
	}
}

add_action( 'wp_ajax_solar_write_history', 'solar_ajax_solar_write_history' );

/**
 * Ajax log lead rejection
 *
 * @since 21/7/2014: No longer update 'leads_total_count' meta for post. That will be calculated to be more accurate
 *
 * @return void
 */
function solar_ajax_solar_write_history()
{
	if ( empty( $_POST['lead_id'] ) || empty( $_POST['company_id'] ) )
		wp_send_json_error();

	solar_log( array(
		'time'        => date( 'Y-m-d H:i:s' ),
		'type'        => __( 'Leads', 'sch' ),
		'action'      => __( 'Reject', 'sch' ),
		'description' => sprintf( __( '<span class="label">Leads:</span> <span class="detail">%s</span>', '7listings' ), $_POST['lead_id'] ),
		'object'      => $_POST['company_id'],
		'user'        => get_current_user_id(),
	) );

	wp_send_json_success();
}

add_action( 'gform_pre_submission_36', function()
{
	if ( 'The Prospect is outside your Elected Service Area' != $_POST['input_3'] || empty( $_POST['input_14'] ) )
		return;

	$lead_id 			= intval( $_POST['input_2'] );
	$company_name 		= trim( $_POST['input_14'] );
	$company 			= get_page_by_title( $_POST['input_14'], OBJECT, 'company' );
	$lead 				= \GFAPI::get_entry( $lead_id );
	$service_postcodes 	= get_post_meta( $company->ID, 'service_postcodes', true );
	$lead_postcode		= $lead['17.5'];
	
	if ( str_contains( $service_postcodes, $lead_postcode ) )
	{
		wp_redirect( '/my-account/leads/?message=1' );
		exit;
	}
} );