<?php
add_action( 'template_redirect', 'solar_check_paypal_email_notification', 20 ); // After main theme adds Paypal email notification

/**
 * Check whether or not show notification for paypal email
 *
 * @return void
 */
function solar_check_paypal_email_notification()
{
	if ( sl_setting( 'solar_notification_paypal' ) )
		return;

	Sl_Notification::remove( 'paypal_email' );
}

add_action( 'company_notifications', 'solar_notifications', 10, 2 );

/**
 * Add more notifications for company owner
 *
 * @param object $company
 * @param int    $user_id
 *
 * @return void
 */
function solar_notifications( $company, $user_id )
{
	$edit_page = get_permalink( sl_setting( 'company_page_edit' ) );
	$leads_page = get_permalink( sl_setting( 'company_page_leads' ) );

	if ( 'post' == get_post_meta( $company->ID, 'leads_payment_type', true ) )
	{
		$post_notification_message = "
			As of 1st of July 2015, Australian Solar Quotes will no longer offer the 'Post Payment Option'. 
			If you have not switched to 'Direct Debit' prior to the 1st of July 2015, then your account will be automatically suspended until we have received your application. 
			<a href=\"https://www.australiansolarquotes.com.au/my-account/support/billing/\">Click here to find out more</a>.
		";
		
		Sl_Notification::add( $post_notification_message );
	}

	if ( str_word_count( $company->post_content ) < 200 )
		Sl_Notification::add( sprintf( __( 'Optimise your page to improve your ranking and close ratio by simply adding a minimum of 200 words of unique <a href="%s">content</a> about your company', '7listings' ), $edit_page ) );

	// Company without logo notification
	$company_logo = asq_get_company_logo( $company->ID );
	
	if ( empty( $company_logo ) || ! $company_logo )
		Sl_Notification::add( sprintf( __( 'Improve your close ratio by adding your <a href="%s">logo</a>', '7listings' ), $edit_page ) );

	if ( ! get_post_meta( $company->ID, 'leads_enable', true ) )
	{
		Sl_Notification::add( sprintf( __( 'Do you want more business? <a href="%s">Start buying Leads</a> from Australian Solar Quotes!', '7listings' ), $leads_page ) );
		return;
	}

	// Leads page
	if ( !get_post_meta( $company->ID, 'leads', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Amount of Leads</a>', '7listings' ), $leads_page ) );
	if ( !get_post_meta( $company->ID, 'leads_payment_type', true ) )
		Sl_Notification::add( sprintf( __( 'Please choose: <a href="%s">Payment type for Leads</a>', '7listings' ), $leads_page ) );
	if ( !get_post_meta( $company->ID, 'trading_name', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Company Name</a>', '7listings' ), $edit_page ) );
	if ( !get_post_meta( $company->ID, 'company_abn', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">ABN</a>', '7listings' ), $edit_page ) );
	$type = get_post_meta( $company->ID, 'leads_type', true );
	if ( empty( $type ) )
		Sl_Notification::add( sprintf( __( 'Please select: <a href="%s">Type(s) of leads</a> you want to receive.', '7listings' ), $leads_page ) );
	$type = get_post_meta( $company->ID, 'service_type', true );
	if ( empty( $type ) )
		Sl_Notification::add( sprintf( __( 'Please select: <a href="%s">Service(s)</a> you want to receive leads for.', '7listings' ), $leads_page ) );
	$assessment = get_post_meta( $company->ID, 'assessment', true );
	if ( empty( $assessment ) )
		Sl_Notification::add( sprintf( __( 'Please select: <a href="%s">Type of Assessment</a> you offer for quotes.', '7listings' ), $leads_page ) );
	$service_radius = get_post_meta( $company->ID, 'service_radius', true );
	if ( !$service_radius )
		Sl_Notification::add( sprintf( __( 'Please select: <a href="%s">Service Area</a> type', '7listings' ), $leads_page ) );
	if ( 'radius' == $service_radius && !get_post_meta( $company->ID, 'leads_service_radius', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Radius</a> for your service area.', '7listings' ), $leads_page ) );
	if ( 'postcodes' == $service_radius && !get_post_meta( $company->ID, 'service_postcodes', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Postcodes</a> of service area.', '7listings' ), $leads_page ) );
	if ( !get_post_meta( $company->ID, 'leads_email', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Email(s) recipient</a> for leads.', '7listings' ), $leads_page ) );

	if ( 'direct' == get_post_meta( $company->ID, 'leads_payment_type', true ) && ! get_post_meta( $company->ID, 'leads_direct_debit_saved', true ) )
		Sl_Notification::add( sprintf( __( 'We have not yet received your direct debit application, <a href="%s">click here</a> to download now!', '7listings' ), 'http://bit.ly/ASQDDAPP' ) );

	if ( get_post_meta( $company->ID, 'leads_manually_suspend', true ) )
		Sl_Notification::add( __( 'Your leads has been suspended, please contact support on <a href="tel:1300 303 864">1300 303 864</a> immediately.', '7listings' ) );

	Sl_Notification::remove( 'address' );
	if ( ! get_post_meta( $company->ID, 'address2', true ) )
		Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Street Address</a>', '7listings' ), $leads_page ), 'address2' );

}

add_filter( 'gform_notification_1', 'solar_customer_notification', 10, 3 );

/**
 * Add number of matched companies and list of companies to email notification sent to customers
 *
 * @param array $notification
 * @param array $form
 * @param array $lead
 *
 * @return array
 */
function solar_customer_notification( $notification, $form, $lead )
{
	$companies = gform_get_meta( $lead['id'], 'companies' );
	$companies = array_filter( explode( ',', $companies . ',' ) );
	$num = count( $companies );

	$list = '<ol>';
	foreach ( $companies as $company )
	{
		$list .= '<li><a href="' . get_permalink( $company ) . '">' . get_the_title( $company ) . '</a></li>';
	}
	$list .= '</ol>';

	$notification['message'] = strtr( $notification['message'], array(
		'%%NUM_COMPANIES%%'  => $num,
		'%%LIST_COMPANIES%%' => $list,
	) );

	return $notification;
}
