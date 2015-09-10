<?php
/**
 * Get companies that rejected a lead
 *
 * @param int $lead
 *
 * @return array Array (company title => reason)
 */
function solar_get_rejected_companies( $lead )
{
	global $wpdb;
	$sql      = "
		SELECT lead_id
		FROM {$wpdb->prefix}rg_lead_detail
		WHERE form_id = 36 AND field_number = 2 AND value = '$lead'
		GROUP BY lead_id
	";
	$lead_ids = $wpdb->get_col( $sql );
	if ( empty( $lead_ids ) )
		return array();
	$lead_ids  = '(' . implode( ',', $lead_ids ) . ')';
	$sql       = "
		SELECT t1.value AS post_title, t2.value AS reason
		FROM {$wpdb->prefix}rg_lead_detail AS t1
		JOIN {$wpdb->prefix}rg_lead_detail AS t2
		WHERE t1.lead_id IN $lead_ids AND t2.lead_id = t1.lead_id AND t1.field_number = 14 AND t2.field_number = 3
		GROUP BY post_title
	";
	$rejected  = $wpdb->get_results( $sql );
	$companies = array();
	$reasons   = array(
		'The lead is a duplicate lead'                      => 'duplicate',
		'You are not able to establish contact'             => 'no-contact',
		'The Prospect is outside your Elected Service Area' => 'service-area',
		'Any other reasonable grounds (describe below)'     => 'other',
	);
	foreach ( $rejected as $company )
	{
		$companies[$company->post_title] = $reasons[$company->reason];
	}

	return $companies;
}

/**
 * Get rejected leads of a company
 *
 * @param object $company Company object (WP Post)
 *
 * @return array
 */
function solar_get_rejected_leads( $company )
{
	global $wpdb;

	if ( ! is_object( $company ) )
		$company = get_post( $company );

	$post_title = isset( $company->post_title ) ? $company->post_title : '';
	// Somehow GF doesn't insert full company title with '&' inside
	if ( false !== strpos( $post_title, '&' ) )
		list( $post_title ) = explode( '&', $post_title );

	$sql = "
		SELECT DISTINCT lead_id
		FROM {$wpdb->prefix}rg_lead_detail
		WHERE form_id = 36 AND field_number = 14 AND value = %s
	";

	$lead_ids = $wpdb->get_col( $wpdb->prepare( $sql, $post_title ) );

	if ( empty( $lead_ids ) )
		return array();
	$lead_ids = '(' . implode( ',', $lead_ids ) . ')';
	$sql      = "
		SELECT t1.value AS lead_id, t2.value AS reason
		FROM {$wpdb->prefix}rg_lead_detail AS t1
		JOIN {$wpdb->prefix}rg_lead_detail AS t2
		WHERE t1.lead_id IN $lead_ids AND t2.lead_id = t1.lead_id AND t1.field_number = 2 AND t2.field_number = 3
		GROUP BY lead_id
	";
	$rejected = $wpdb->get_results( $sql );
	$leads    = array();
	$reasons  = array(
		'The lead is a duplicate lead'                      => 'duplicate',
		'You are not able to establish contact'             => 'no-contact',
		'The Prospect is outside your Elected Service Area' => 'service-area',
		'Any other reasonable grounds (describe below)'     => 'other',
	);
	foreach ( $rejected as $lead )
	{
		$leads[$lead->lead_id] = $reasons[$lead->reason];
	}

	return $leads;
}

/**
 * Send notification to company owners
 *
 * @param array  $leads Array of lead IDs
 * @param string $to    Comma separated email list
 *
 * @return void
 *
 * @see GFForms::resend_notifications()
 */
function solar_send_notification( $leads, $to )
{
	if ( DB_HOST != '192.168.3.7' )
		return;

	// Form settings, DON'T change
	$form_id         = 1;
	$notification_id = '533c9d2feb958';

	if ( ! is_array( $leads ) )
		$leads = array( $leads );

	// Improve performance if we send multiple notifications
	static $form = null;
	if ( null === $form )
		$form = RGFormsModel::get_form_meta( $form_id );

	if ( empty( $leads ) || empty( $form ) || ! isset( $form['notifications'] ) || ! isset( $form['notifications'][$notification_id] ) )
		return;

	$notification           = $form['notifications'][$notification_id];
	$notification['toType'] = 'email';

	$to = array_unique( multi_explode( $to ) );

	foreach ( $leads as $lead_id )
	{
		// Send email to single address
		foreach ( $to as $email )
		{
			$notification['to'] = $email;
			$lead               = RGFormsModel::get_lead( $lead_id );
			if ( false !== strpos( strtolower( $lead['51'] ), 'undecided' ) )
				$lead['51'] = __( 'Undecided', '7listings' );

			GFCommon::send_notification( $notification, $form, $lead );
		}

		\ASQ\Log::make( array( 'description' => 'Send ' . $lead_id . ' to:' .  serialize( $to ) ) );
	}
}

/**
 * Send notification to company owners
 *
 * @param array $lead Lead info
 *
 * @return void
 *
 * @see GFForms::resend_notifications()
 */
function solar_send_notification_customers( $lead )
{
	// Form settings, DON'T change
	$form_id         = 1;
	$notification_id = '53ccb7a74cf66';

	// Improve performance if we send multiple notifications
	static $form = null;
	if ( null === $form )
		$form = RGFormsModel::get_form_meta( $form_id );

	if ( empty( $lead ) || empty( $form ) || ! isset( $form['notifications'] ) || ! isset( $form['notifications'][$notification_id] ) )
		return;

	$notification           = $form['notifications'][$notification_id];
	$notification['toType'] = 'email';

	if ( false !== strpos( strtolower( $lead['51'] ), 'undecided' ) )
		$lead['51'] = __( 'Undecided', '7listings' );
	GFCommon::send_notification( $notification, $form, $lead );
}

/**
 * Get total leads of a company matched in a month
 *
 * @param int|WP_Post $company Company ID
 * @param string      $key     If month and year is not specified, then use $key, if not, use month and year
 * @param string      $month   Month, format date( 'm' )
 * @param string      $year    Year, format date( 'Y' )
 *
 * @return int
 *
 * @since 21/7/2014: No longer use 'leads_total_count' meta. This will be calculated to be more accurate
 */
function solar_leads_count_total( $company, $key = null, $month = null, $year = null )
{
	if ( !is_object( $company ) )
		$company = get_post( $company );
	if ( ! $key )
		$key = sprintf( '%02d', $month ) . '-' . $year;

	$all_leads = get_post_meta( $company->ID, 'leads_count', true );

	if ( empty( $all_leads ) )
		$all_leads = array();
	if ( empty( $all_leads[$key] ) )
		$all_leads[$key] = '';

	$all_leads = array_filter( explode( ',', $all_leads[$key] . ',' ) );
	$rejected  = solar_get_rejected_leads( $company );
	$accepted  = array_diff( $all_leads, array_keys( $rejected ) );

	return count( $accepted );
}

/**
 * @deprecated Log should not be cleared. Use solar_company_leads_logs instead
 * 
 * Get total leads of a company matched in a frequency period (day, week, month)
 *
 * @param int|WP_Post $company Company ID
 * @param int         $time    A timestamp of a point within the period
 * @param bool        $reset   Reset lead frequency log if period is passed
 *
 * @return int
 */
function solar_leads_frequency_total( $company, $time, $reset = true )
{
	if ( !is_object( $company ) )
		$company = get_post( $company );

	/**
	 * Lead frequency log has following format:
	 * time;lead1,lead2,...
	 *
	 * time is the a timestamp of a point within the period
	 * lead1, lead2, ... is the list of matched leads within the period
	 */
	$log = get_post_meta( $company->ID, 'lead_frequency_log', true );
	if ( ! $log )
		return 0;

	$frequency = get_post_meta( $company->ID, 'lead_frequency', true );
	list( $log_time, $leads ) = explode( ';', $log . ';' );

	// Check specified time is inside the period
	switch ( $frequency )
	{
		case 'day':
			$start = strtotime( date( 'Y-m-d', $log_time ) );
			$end = $start + 86400 - 1;
			break;
		case 'week':
			$start = strtotime( 'last monday', $log_time + 86400 );
			$end = $start + 7 * 86400 - 1;
			break;
		case 'month':
		default:
			$start = strtotime( date( 'Y-m-01', $log_time ) );
			$end = strtotime( date( 'Y-m-t', $log_time ) ) + 86400 - 1;
	}

	// If specified time is not in the period
	// - Reset the log if needed
	// - Return 0
	if ( $time > $end )
	{
		// Reset 'lead_frequency_log' if needed with $time and no leads
		if ( $reset )
		{
			$log = "$time;";
			update_post_meta( $company->ID, 'lead_frequency_log', $log );
		}
		return 0;
	}

	// Get all leads and remove rejected leads
	$leads = array_filter( explode( ',', $leads . ',' ) );

	//$rejected  = solar_get_rejected_leads( $company );
	
	//$accepted  = array_diff( $leads, array_keys( $rejected ) );
	
	return count( $leads );
}

/**
 * 
 * @return int number of lead matched with current company
 */
function solar_company_leads_logs( $company_id, $time = null, $freq = null )
{
	$logs 	   = get_post_meta( $company_id, 'leads_logs', true );
	
	$logs 	   = @unserialize( $logs );
	
	$frequency = ( $freq !== null ) ? $freq : get_post_meta( $company_id, 'lead_frequency', true );
	
	$now 	   = new DateTime( date( 'Y-m-d' ) );
	$now->format( 'Y-m-d' );

	switch ( $frequency )
	{
		case 'day':
			$start 	= $now;
		break;

		case 'week':
			$start = get_monday_of_week( $now );
		break;

		case 'month':
		default:
			$start = $now->modify( 'first day of this month' );
	}
	
	$total = 0;

	$payment_method = get_post_meta( $company_id, 'leads_payment_type', true );

	if ( ! empty( $logs ) )
	{
		if ( ! empty( $payment_method ) && $payment_method == 'upfront' )
		{
			$all_leads = solar_get_company_leads( $company_id );

			$rejected  = solar_get_rejected_leads( $company_id );

			$accepted  = array_diff( $all_leads, array_keys( $rejected ) );

			return count( $accepted );
		}
		else
		{	
			foreach ( $logs as $log )
			{
				$created_at = new DateTime( $log['created_at'] );
				
				if ( $created_at >= $start )
					$total++;
			}	
		}
	}

	return $total;
}

function get_monday_of_week($date = null)
{
    if ($date instanceof \DateTime) {
        $date = clone $date;
    } else if (!$date) {
        $date = new \DateTime();
    } else {
        $date = new \DateTime($date);
    }
    $date->setTime(0, 0, 0);
    
    if ($date->format('N') == 1) {
        // If the date is already a Monday, return it as-is
        return $date;
    } else {
        // Otherwise, return the date of the nearest Monday in the past
        // This includes Sunday in the previous week instead of it being the start of a new week
        return $date->modify('last monday');
    }
}

function solar_company_create_leads_log( $company_id, $lead_id, $time = null )
{
	$time_offset  	= sl_timezone_offset() * 3600;
	$t = ( $time !== null ) ? $time : date( 'Y-m-d H:i:s' );
	$created_at 	= strtotime( $t ) + $time_offset;
	$created_at		= date( 'Y-m-d', $created_at );

	$log = compact( 'created_at', 'lead_id' );

	$logs 	   = get_post_meta( $company_id, 'leads_logs', true );
	$logs 	   = unserialize( $logs );
	
	if ( ! is_array( $logs ) || empty( $logs ) )
		$logs = array();
	
	$logs[] = $log;

	$logs = serialize( $logs );

	update_post_meta( $company_id, 'leads_logs', $logs );
}

/**
 * Get company leads on the period time
 * @param  int $company_id Company ID to check
 * @param  string $mY   If is m-Y format, it will return all leads of selected month.
 *                      If is empty, it will return all leads of current month
 *                      If is false, it will return all leads from beginning of time
 * @return array Array of leads
 */
function solar_get_company_leads( $company_id, $mY = '' )
{
	if ( empty( $mY ) && $mY !== false )
		$mY = date( 'm-Y' );

	$all_leads = get_post_meta( $company_id, 'leads_count', true );

	if ( $mY === false )
		return $all_leads;

	if ( $mY === 'all' )
		return implode( ',', array_values( $all_leads ) );

	if ( ! empty( $all_leads[$mY] ) )
		return explode( ',', $all_leads[$mY] );

	return array();
}

function solar_get_upfront_leads( $company_id, $chunks = false )
{
	$upfront_leads = get_post_meta( $company_id, 'upfront_leads', true );

	$rejected_leads= solar_get_rejected_leads( $company_id );

	// To flexible. Split each chunk by |. For example 1,2,3..30|31,32,33..60
	$upfront_chunks = explode( '|', $upfront_leads );

	$leads = array();

	if ( $chunks )
	{
		$leads = explode( ',', end( $upfront_chunks ) );
	}
	else
	{
		foreach ( $upfront_chunks as $chunk )
		{
			$leads = array_merge( $leads, explode( ',', $chunk ) );
		}
	}

	$leads = array_diff( $leads, array_keys( $rejected_leads ) );
	
	return $leads;
}

function solar_get_lead_site( $lead )
{
	if ( ! is_object( $lead ) && is_numeric( $lead ) )
		$lead = \GFAPI::get_entry( $lead );

	$source_url = $lead['source_url'];

	if ( str_contains( $source_url, array( 'source', 'site' ) ) )
	{
		$query_string = parse_url( $source_url, PHP_URL_QUERY );
		if ( ! empty( $query_string ) )
		{
			parse_str( $query_string, $sources );

			if ( ! empty( $sources['site'] ) )
				return $sources['site'];
		}
	}

	return '';
}