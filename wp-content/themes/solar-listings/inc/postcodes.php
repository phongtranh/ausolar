<?php
//include_once 'http://www.7listings.net/clients/asq-postcodes.source';
/**
 * This class matches a request with companies based on some criteria:
 * - postcode (or within a service radius of a company)
 * - leads type
 * - service type
 * - assessment
 * - age
 *
 * Companies are sorted by membership type
 */
class Solar_Postcodes
{
	/**
	 * List of form fields info
	 *
	 * @var array
	 */
	public static $fields = array(
		'postcode'     => array(
			'id' => '17.5',
		),
		'leads_type'   => array(
			'id'     => '30',
			'values' => array(
				'Home'     => 'residential',
				'Business' => 'commercial',
			)
		),
		'service_type' => array(
			'id'     => '56',
			'values' => array(),
		),
		'assessment'   => array(
			'id'     => '47',
			'values' => array(
				'I prefer the installers to visit my property and give a firm price' => 'onsite',
				'No need for an installer to visit, an estimate via email is fine'   => 'phone_email',
				'I have no preference'                                               => 'any',
				'I_have_no_preference'                                               => 'any',
			),
		),
		'age'          => array(
			'id'     => '18',
			'values' => array(
				'Under 20 years' => 'retrofit',
				'Over 20 years'  => 'retrofit',
				'U/C'            => 'under_construction',
			),
		)
	);

	/**
	 * Run when class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		// Find companies match a lead when it's submitted
		add_action( 'gform_entry_created', array( __CLASS__, 'after_submission' ), 10, 2 );
	}

	/**
	 * Find companies match a lead when it's submitted
	 *
	 * @param array $entry
	 */
	public static function after_submission( $entry, $form, $already_matched_companies = array() )
	{
		if ( $entry['form_id'] != 1 )
			return;

		// Get matched companies
		$matches = self::match_companies( $entry, 4, $already_matched_companies );
		$companies_id = gform_get_meta( $entry['id'], 'companies' );

		if ( empty( $companies_id ) )
			return;
		
		$companies_id = explode( ',', $companies_id );

		$entry[88] = count( $companies_id );
		\GFAPI::update_entry( $entry );

		// $gfa = GFMailChimp::get_instance();
		// $gfa->maybe_process_feed( $entry, $form );

		// Skip all already matched companies, because we just have to send notification
		// to newly matched companies
		foreach ( $companies_id as $index => $company_id )
		{
			if ( in_array( $company_id, $already_matched_companies ) )
				unset ( $companies_id[$index] );
		}

		if ( ! sl_setting( 'auto_leads_email_sending' ) )
			return;

		$emails = array();
		foreach ( $companies_id as $company_id )
		{
			$emails[] = get_post_meta( $company_id, 'leads_email', true );
		}

		$emails = implode( ',', $emails );

		solar_send_notification( $entry['id'], $emails ); // Send notification to company owners
		solar_send_notification_customers( $entry );      // Send notification to customers
	}

	/**
	 * Search for postcodes and save in post meta
	 *
	 * @param int $post_id
	 *
	 * @return void
	 */
	public static function save_postcodes( $post_id )
	{
		$postcodes = self::find( $post_id );
		if ( empty( $postcodes ) )
		{
			delete_post_meta( $post_id, 'served_postcodes' );
			update_post_meta( $post_id, 'served_postcodes_not_found', 1 );
		}
		else
		{
			update_post_meta( $post_id, 'served_postcodes', implode( ',', $postcodes ) );
			delete_post_meta( $post_id, 'served_postcodes_not_found' );
		}
	}

	/**
	 * Find postcodes within company's service area
	 *
	 * @param int $company_id
	 *
	 * @return array
	 */
	public static function find( $company_id )
	{
		// Not enable service area
		$enable = get_post_meta( $company_id, 'service_area', true );
		if ( ! $enable )
			return array();

		$type = get_post_meta( $company_id, 'service_radius', true );

		// ALWAYS use entered postcodes. Will enable search by radius later

		// By post codes
		//		if ( 'postcodes' == $type )
		//		{
		$postcodes = get_post_meta( $company_id, 'service_postcodes', true );
		$postcodes = preg_split( '/(\s|,)+/', $postcodes );
		$postcodes = array_unique( array_filter( $postcodes ) );

		return (array) $postcodes;
	}

	/**
	 * Get latitude, longtitude of a company
	 *
	 * @param int $company_id
	 *
	 * @return array
	 */
	public static function get_coordinates( $company_id )
	{
		$lat = get_post_meta( $company_id, 'latitude', true );
		$lon = get_post_meta( $company_id, 'longtitude', true );
		if ( $lat && $lon )
			return array( $lat, $lon );

		$address  = get_post_meta( $company_id, 'address', true );
		$postcode = get_post_meta( $company_id, 'postcode', true );
		$city     = get_post_meta( $company_id, 'city', true );

		$address      = "$address, $city, $postcode";
		$address_hash = md5( $address );
		$coordinates  = get_transient( $address_hash );

		if ( false !== $coordinates )
			return $coordinates;

		$url      = add_query_arg( array( 'address' => urlencode( $address ), 'sensor' => 'false' ), 'http://maps.googleapis.com/maps/api/geocode/json' );
		$response = wp_remote_get( $url );
		$data     = wp_remote_retrieve_body( $response );

		if ( empty( $data ) )
			return array();

		$data = json_decode( $data );

		if ( 'OK' !== $data->status )
			return array();

		$coordinates = $data->results[0]->geometry->location;

		$cache_value['lat'] = $coordinates->lat;
		$cache_value['lng'] = $coordinates->lng;

		// Cache coordinates for 3 months
		set_transient( $address_hash, $cache_value, 3600 * 24 * 30 * 3 );

		return $cache_value;
	}

	/**
	 * Find postcodes near a given postcode, within a radius
	 *
	 * @param int|array $postcode or array (latitude, longtitude)
	 * @param float     $radius
	 *
	 * @return array
	 */
	public static function find_postcodes( $postcode, $radius )
	{
		$db = new wpdb( POSTCODE_DB_USER, POSTCODE_DB_PASSWORD, POSTCODE_DB_NAME, POSTCODE_DB_HOST );

		// Get info for based point
		if ( is_numeric( $postcode ) )
		{
			$base = $db->get_row( "SELECT `postcode`, `lat`, `lon` FROM `postcode_db` WHERE postcode = $postcode LIMIT 1" );
			if ( empty( $base ) )
				return array();
			$lat = $base->lat;
			$lon = $base->lon;
		}
		else
		{
			$lat = $postcode['lat'];
			$lon = $postcode['lon'];
		}

		$all = $db->get_results( "SELECT `postcode`, `lat`, `lon` FROM `postcode_db` WHERE postcode != $postcode" );
		if ( empty( $all ) )
			return array();
		$postcodes = array();
		foreach ( $all as $point )
		{
			if ( $radius >= self::distance( $lat, $lon, $point->lat, $point->lon ) )
				$postcodes[] = $point->postcode;
		}

		return array_unique( $postcodes );
	}

	/**
	 * Calculate distance between 2 locations in KM
	 *
	 * @param $latitude1
	 * @param $longitude1
	 * @param $latitude2
	 * @param $longitude2
	 *
	 * @return float KM
	 */
	public static function distance( $latitude1, $longitude1, $latitude2, $longitude2 )
	{
		$thet     = $longitude1 - $longitude2;
		$dist     = sin( deg2rad( $latitude1 ) ) * sin( deg2rad( $latitude2 ) ) + cos( deg2rad( $latitude1 ) ) * cos( deg2rad( $latitude2 ) ) * cos( deg2rad( $thet ) );
		$dist     = acos( $dist );
		$dist     = rad2deg( $dist );
		$kmperlat = 111.325; // Kilometers per degree latitude constant
		$dist     = $dist * $kmperlat;

		return round( $dist );
	}

	/**
	 * Calculates distance in KM between postcodes
	 *
	 * @param int $postcode1
	 * @param int $postcode2
	 *
	 * @return float
	 */
	public static function postcode_distance( $postcode1, $postcode2 )
	{
		$db = new wpdb( POSTCODE_DB_USER, POSTCODE_DB_PASSWORD, POSTCODE_DB_NAME, POSTCODE_DB_HOST );

		$point1 = $db->get_row( "SELECT `lat`, `lon` FROM `postcode_db` WHERE postcode = $postcode1 LIMIT 1" );
		if ( empty( $point1 ) )
			return 9999;

		$point2 = $db->get_row( "SELECT `lat`, `lon` FROM `postcode_db` WHERE postcode = $postcode2 LIMIT 1" );
		if ( empty( $point2 ) )
			return 9999;

		return self::distance( $point1->lat, $point1->lon, $point2->lat, $point2->lon );
	}

	/**
	 * Show button in Tools page to regenerate served postcodes
	 *
	 * @return void
	 */
	public static function tools_button()
	{
		echo '
<p>
	<a href="#" class="button button-primary" id="company-postcodes" style="float: left">' . __( 'Update company postcodes' ) . '</a>
	<span class="spinner" style="display: none; float: left"></span>
	<span style="clear: both; display: block"></span>
</p>';
	}

	/**
	 * Enqueue script for ajax action in tools page
	 *
	 * @return void
	 */
	public static function enqueue_admin_scripts()
	{
		wp_enqueue_script( 'solar-find-postcodes', CHILD_URL . 'js/admin/postcodes.js', array( 'jquery' ), '', true );
	}

	/**
	 * Update company postcodes
	 *
	 * @return void
	 */
	public static function ajax_update_postcodes()
	{
		// Number of company run in 1 request
		$amount    = 10;
		$companies = get_posts( array(
			'post_type'      => 'company',
			'posts_per_page' => - 1,
		) );

		// Clear served postcodes first time
		$first = (int) $_POST['counter'];
		if ( 0 === $first )
		{
			foreach ( $companies as $company )
			{
				delete_post_meta( $company->ID, 'served_postcodes' );
				delete_post_meta( $company->ID, 'served_postcodes_not_found' );
				//				delete_post_meta( $company->ID, 'leads_count' );
			}
			wp_send_json_success();
		}

		$count = 0;
		foreach ( $companies as $company )
		{
			// Ignore company which has postcodes
			if ( get_post_meta( $company->ID, 'served_postcodes', true ) )
				continue;

			// Ignore company that doesn't have any served postcodes
			if ( get_post_meta( $company->ID, 'served_postcodes_not_found', true ) )
				continue;

			$count ++;

			// If
			if ( $count > $amount )
				wp_send_json_success();

			$postcodes = self::find( $company->ID );
			if ( empty( $postcodes ) )
			{
				delete_post_meta( $company->ID, 'served_postcodes' );
				update_post_meta( $company->ID, 'served_postcodes_not_found', 1 );
			}
			else
			{
				update_post_meta( $company->ID, 'served_postcodes', implode( ',', $postcodes ) );
				delete_post_meta( $company->ID, 'served_postcodes_not_found' );
			}
		}

		wp_send_json_error( __( 'Completed' ) );
	}

	/**
	 * Find companies that match request
	 *
	 * @param array $request GForm entry
	 * @param int   $num     Number of companies returned
	 * @param $matched_companies Companies that have already matched. Use this for skip
	 *
	 * @return array
	 */
	public static function match_companies( array $request, $num = 4, $already_matched_companies = array() )
	{

		// If the third argument is set. Then process the next match action
		// Changes:
		// - Skip all matched companies.
		// - Just match with max 4 companies. For example, if this lead was matched with 2 companies,
		//   then this time, match with another two companies
		// - Update the lead meta, set processed = 2
		if ( empty ( $already_matched_companies ) )
		{
			// If leads already has matched companies, just get it from meta data
			$matches = gform_get_meta( $request['id'], 'companies' );
			if ( ! empty( $matches ) )
			{
				$matches = array_filter( explode( ',', $matches . ',' ) );
				$matches = get_posts( array(
					'post_type'      => 'company',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'post__in'       => $matches,
				) );
				return $matches;
			}
		}

		$time_offset  = sl_timezone_offset() * 3600;
		$request_date = time() + $time_offset;
		$key          = date( 'm', $request_date ) . '-' . date( 'Y', $request_date );
		// Allow leads can process two times.
		$processed = gform_get_meta( $request['id'], 'processed' );

		if ( empty ( $processed ) )
			$processed = 0;

		$next_match_offset = intval( sl_setting( 'solar_lead_next_match_offset' ) );

		// Check if lead is processed before:
		// If processed, then no matched companies (because match companies are get above) => return array()
		// Also add an additional check for lead older than 4 day, just for sure
		if ( ( empty ( $already_matched_companies ) && $processed > 6 ) || $request_date + 86400 * $next_match_offset < time() )
			return array();

		// Use static variable to not have to run get_posts() many times for each request
		static $companies = null;
		if ( null === $companies )
		{
			$companies = get_posts( array(
				'post_type'      => 'company',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					// Company must enable buying leads
					array(
						'key'   => 'leads_enable',
						'value' => 1,
					),

					// Company hasn't stop buying leads
					array(
						'key'     => 'cancel_reason',
						'value'   => 1,
						'compare' => 'NOT EXISTS',
					),

					// Company is not manually suspended
					array(
						'key'     => 'leads_manually_suspend',
						'value'   => 1,
						'compare' => 'NOT EXISTS',
					),
				),
			) );
		}

		$matches = array();
		foreach ( $companies as $company )
		{
			// If this lead belongs to current company before, skip it.
			if ( in_array( $company->ID, $already_matched_companies ) )
				continue;

			$company_owner_id = get_post_meta( $company->ID, 'user', true );
			if ( empty( $company_owner_id ) ) 
				continue;

			$membership = get_user_meta( $company_owner_id, 'membership', true );
			if ( ! $membership || empty( $membership ) )
				continue;

			$payment_type = get_post_meta( $company->ID, 'leads_payment_type', true );
			if ( 'direct' == $payment_type && ! get_post_meta( $company->ID, 'leads_direct_debit_saved', true ) )
				continue;

			// Check for buying leads
			$amount = intval( get_post_meta( $company->ID, 'leads', true ) );
			if ( ! $amount )
			{
				if ( $payment_type == 'upfront' )
					$amount = 30;
				else
					continue;
			}

			// Check buying lead date, i.e company has to turn on buying leads BEFORE this request
			$buying_date = intval( get_post_meta( $company->ID, 'leads_paid', true ) );
			if ( empty( $buying_date ) || $request_date < $buying_date )
				continue;

			// Get saved leads count
			$leads_count = get_post_meta( $company->ID, 'leads_count', true );
			if ( empty( $leads_count ) )
				$leads_count = array();
			if ( empty( $leads_count[$key] ) )
				$leads_count[$key] = '';
			$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );

			// If we have checked this company, just get it
			if ( in_array( $request['id'], $values ) )
			{
				$matches[] = $company;
				continue;
			}

			if ( $payment_type != 'upfront' )
			{
				// If reach limit, skip it
				if ( $amount <= solar_company_leads_logs( $company->ID, $request_date ) )
					continue;
			}
			else
			{
				// For upfront
				$last_upfront_leads = solar_get_upfront_leads( $company->ID, true );

				if ( count( $last_upfront_leads ) >= 30 || ! get_post_meta( $company->ID, 'leads_upfront_admin_active', true ) )
					continue;
			}

			// Check postcode
			$field     = self::$fields['postcode'];
			$postcodes = get_post_meta( $company->ID, 'service_postcodes', true );

			if ( ! $postcodes )
				continue;

			$postcodes = array_unique( array_filter( explode( ',', $postcodes . ',' ) ) );

			if ( ! in_array( intval( $request[$field['id']] ), $postcodes ) )
				continue;
			// Check leads type
			$field      = self::$fields['leads_type'];
			
			$leads_type = get_post_meta( $company->ID, 'leads_type', true );
			if ( ! is_array( $leads_type ) )
				$leads_type = ( array ) $leads_type;
			$request_value = $request[$field['id']];
			$request_value = $field['values'][$request_value];

			if ( ! in_array( $request_value, $leads_type ) )
				continue;

			// Check service type
			$service_type = get_post_meta( $company->ID, 'service_type', true );
			if ( ! is_array( $service_type ) )
				$service_type = ( array ) $service_type;
			$request_value = array( 'solar_pv' );
			if ( 'Yes' == $request[56] )
				$request_value[] = 'solar_hot_water';
			$common = array_intersect( $request_value, $service_type );

			if ( empty( $common ) )
				continue;

			// Check assessment
			$field      = self::$fields['assessment'];
			$assessment = get_post_meta( $company->ID, 'assessment', true );
			if ( ! is_array( $assessment ) )
				$assessment = ( array ) $assessment;
			$request_value = $request[$field['id']];
			$request_value = $field['values'][$request_value];

			if ( 'any' != $request_value && ! in_array( $request_value, $assessment ) )
				continue;

			// Check age
			$field = self::$fields['age'];
			$age   = get_post_meta( $company->ID, 'age', true );
			if ( ! is_array( $age ) )
				$age = ( array ) $age;
			$request_value = $request[$field['id']];
			$request_value = $field['values'][$request_value];

			if ( 'any' != $request_value && ! in_array( $request_value, $age ) )
				continue;

			$matches[] = $company;
		}
		
		@usort( $matches, array( __CLASS__, 'sort_companies' ) );
		// Tan: If already matched, then just match with 4 - already matched companies
		// For example: if matched with 3 companies before, we have to make sure that just match
		// with 1 company this time.
		$remaining_match = count( $already_matched_companies );
		if ( empty ( $already_matched_companies[0] ) )
			$remaining_match--;

		// No-way but I have to make sure
		if ( $remaining_match < 0 )
			$remaining_match = 0;

		if ( - 1 != $num )
			$matches = array_slice( $matches, 0, $num - $remaining_match );

		// Save request ID into leads count
		// Update frequency log
		$matched_companies = array();

		foreach ( $matches as $company )
		{
			// Save company ID
			$matched_companies[] = $company->ID;

			solar_company_create_leads_log( $company->ID, $request['id'] );

			// Update leads_count
			$leads_count = get_post_meta( $company->ID, 'leads_count', true );
			if ( empty( $leads_count ) )
				$leads_count = array();
			if ( empty( $leads_count[$key] ) )
				$leads_count[$key] = '';

			$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
			
			if ( ! in_array( $request['id'], $values ) )
			{
				$values[]          = $request['id'];
				$values            = array_unique( $values );
				$leads_count[$key] = implode( ',', $values );

				// If company payment type is Upfront and reached limit. 
				// Untick the Admin active check box and send email notification
				// For upfront
				$leads_payment_type = get_post_meta( $company->ID, 'leads_payment_type', true );

				if ( $leads_payment_type == 'upfront' )
				{
					$upfront_leads = get_post_meta( $company->ID, 'upfront_leads', true );
					// Do not append ',' if the current lead is the first element of the last chunk
					if ( ! ends_with( $upfront_leads, '|' ) )
					{
						$upfront_leads .= ',';
					}
					
					$upfront_leads .= $request['id'];

					update_post_meta( $company->ID, 'upfront_leads', $upfront_leads );
					
					$last_upfront_leads = solar_get_upfront_leads( $company->ID, true );

					if ( count( $last_upfront_leads ) == 22 )
					{
						asq_send_purchase_another_lead_pack_email( $company->ID, 75 );
					}

					if ( count( $last_upfront_leads ) >= 30 )
					{
						// Stop the current chunk
						update_post_meta( $company->ID, 'upfront_leads', $upfront_leads . '|' );
						// Untick the admin active check box
						update_post_meta( $company->ID, 'leads_upfront_admin_active', false );
						// Todo: Log this. For future use
					 	
					 	asq_send_purchase_another_lead_pack_email( $company->ID );
					}
				}
				else
				{
					asq_send_reached_notification( $company->ID );
				}

				update_post_meta( $company->ID, 'leads_count', $leads_count );
			}
		}

		// Tan: Save companies to lead meta
		// If this is next match, then merge already matched companies with newly matched companies
		if ( ! empty ( $already_matched_companies ) && !empty( $already_matched_companies[0] ) )
		{
			$matched_companies = array_merge( $matched_companies, $already_matched_companies );
		}

		$matched_companies = implode( ',', $matched_companies );

		// Tan: If we insert null value, then gform_update_meta will create duplicated entry, to handle it
		// we'll pretend that the 'companies' meta was cached
		// Remember, do it only when we process next match.
		if ( $processed >= 1 )
		{
			//$processed = 1;
			global $_gform_lead_meta;
			$_gform_lead_meta[$request['id'].'_companies'] = $matched_companies;
		}

		gform_update_meta( $request['id'], 'companies', $matched_companies );

		gform_update_meta( $request['id'], 'processed', $processed + 1 );

		return $matches;
	}

	/**
	 * Sort companies
	 * - By membership
	 * - By sign up date
	 *
	 * @param WP_Post $a First company
	 * @param WP_Post $b Second company
	 *
	 * @return int
	 */
	public static function sort_companies( WP_Post $a, WP_Post $b )
	{
		$a_user = get_post_meta( $a->ID, 'user', true );
		$b_user = get_post_meta( $b->ID, 'user', true );

		if ( ! $a_user || ! $b_user )
			return - 1;

		$enable_compare_membership = ( null !== sl_setting( 'enable_compare_membership' ) ) ? sl_setting( 'enable_compare_membership' ) : false;

		if ( $enable_compare_membership )
		{
			// Compare membership
			$a_membership = get_user_meta( $a_user, 'membership', true );
			if ( ! $a_membership )
				$a_membership = 'none';
			$b_membership = get_user_meta( $b_user, 'membership', true );
			if ( ! $b_membership )
				$b_membership = 'none';
			$levels  = array(
				'gold'   => 0,
				'silver' => 1,
				'bronze' => 2,
				'none'   => 3,
			);
			$compare = $levels[$a_membership] - $levels[$b_membership];
			if ( $compare )
				return $compare;
		}

		// Compare buying leads date
		$a_date = get_post_meta( $a->ID, 'leads_paid', true );
		$b_date = get_post_meta( $b->ID, 'leads_paid', true );

		return $a_date - $b_date;
	}

	/**
	 * Find requests that match company service
	 *
	 * @param int $company_id
	 *
	 * @return array
	 */
	public static function match_requests( $company_id )
	{
		$matches = array();

		$postcodes = get_post_meta( $company_id, 'served_postcodes', true );
		if ( ! $postcodes )
			return $matches;

		$postcodes = array_filter( explode( ',', $postcodes . ',' ) );

		$form_id  = 26;
		$requests = GFFormsModel::get_leads( $form_id, 0, 'DESC', '', 0, 9999 );
		$matches  = array();
		foreach ( $requests as $request )
		{
			if ( ! in_array( $request[self::$fields['postcode']['id']], $postcodes ) )
				continue;

			$matches[] = $request;
		}

		return $matches;
	}
}
Solar_Postcodes::load();