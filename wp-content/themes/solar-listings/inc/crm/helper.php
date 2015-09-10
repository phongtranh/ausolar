<?php
namespace ASQ\Crm;

class Helper
{
	public static $form_id = 45;

	/**
	 * Add 'notes' meta data, serialized to the lead.
	 * @param        $lead_id
	 * @param string $note
	 *
	 * @return array total notes
	 */
	public static function add_note( $lead_id, $outcome = '', $note = '' )
	{
		$notes = gform_get_meta( $lead_id, 'notes' );

		$notes = unserialize( $notes );

		$notes[] = array(
			'date'      => date('Y-m-d'),
			'user_id'   => get_current_user_id(),
			'outcome'   => $outcome,
			'note'      => $note
		);

		$notes_serialized = serialize( $notes );

		gform_update_meta( $lead_id, 'notes', $notes_serialized );

		return $notes;
	}

	/**
	 * Get all notes belongs to a lead
	 *
	 * @param $lead_id
	 *
	 * @return array Notes
	 */
	public static function get_notes( $lead_id )
	{
		$notes = gform_get_meta( $lead_id, 'notes' );

		return unserialize( $notes );
	}


	/**
	 * Update leads
	 * @param $lead Lead with new properties
	 *
	 * @return bool
	 */
	public static function update_lead( $lead )
	{
		$lead_db = \GFAPI::get_entry( $lead['id'] );

		// Only add custom note when it's different than old lead
		$lead[5] = ( $lead[5] != $lead_db[5] ) ? $lead[5] : '';

		$validate = self::validate( $lead );

		if ( is_wp_error( $validate ) )
		{
			echo $validate->get_error_message();
			exit;
		}

		$lead[11] = intval( $lead[11] ) + 1;

		if ( $lead[11] === 10 && $lead[4] !== 'Interested' )
		{
			$lead[4] = 'Unable to establish contact';
		}

		$outcome = $lead[4];

		$status_outcome = array(
			'Interested' 					=> 'green',
			'Not interested' 				=> 'red',
			'Requested call back'			=> 'blue',
			'Incorrect phone number'		=> 'red',
			'Incorrect information'			=> 'red',
			'Already been processed'		=> 'red',
			'No answer'						=> 'blue',
			'Unable to establish contact' 	=> 'red'
		);

		// Use user_agent field as CRM status for faster query
		$lead['user_agent'] = $status_outcome[$outcome];

		if ( $outcome == 'Requested call back' )
			$outcome .= " at {$lead[15]} {$lead[12]}";

		// Add Note to the Meta
		self::add_note( $lead['id'], $outcome, $lead[5]);

		\GFAPI::update_entry( $lead );

		echo "Lead was updated successfully!";

		exit;
	}


	/**
	 * Find CRM leads
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public static function find_lead_by_id( $id )
	{
		$lead = \GFAPI::get_entry( $id );

		if ( $lead['form_id'] == self::$form_id )
			return $lead;

		return false;
	}

	/**
	 * Find lead from self::$form_id by arguments
	 *
	 * @param array $args
	 *
	 * @return mixed Array of Leads
	 */
	public static function find_leads()
	{
		global $wpdb;

		$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : null;
		$end_date = isset( $_GET['end_date'] ) ? $_GET['end_date'] : null;

		$search_criteria = array(
			'start_date'    => $start_date,
			'end_date'      => $end_date
		);

		if ( !empty ( $_GET['search'] ) )
			$search_criteria["field_filters"][] = array( 'value' => trim( $_GET['search'] ) );

		if ( !empty ( $_GET['state'] ) )
			$search_criteria["field_filters"][] = array( 'key' => "14.4", 'value' => trim( $_GET['state'] ) );

		if ( !empty ( $_GET['source'] ) )
			$search_criteria["field_filters"][] = array( 'key' => "1", 'value' => trim( $_GET['source'] ) );

		$current_outcome = ( isset ( $_GET['outcome'] ) ) ? trim( $_GET['outcome'] ) : '';

		if ( $current_outcome !== 'active' && $current_outcome !== '' )
			$search_criteria["field_filters"][] = array( 'key' => "4", 'value' => $current_outcome );

		$order = array(
			'key'   => 'date_created',
			'value' => 'DESC'
		);

		$current_page = 1;

		if ( isset( $_GET['paged'] ) )
			$current_page = intval( $_GET['paged'] );

		$query_var_paged = get_query_var( 'paged' );
		if ( ! empty( $query_var_paged ) )
			$current_page = $query_var_paged;


		$paging = array( 'offset' => 0, 'page_size' => 999 );

		$data = \GFAPI::get_entries( self::$form_id, $search_criteria, $order, $paging );

		$entries = array();

		// Remove spam and trash. You have to go to #45 to see that
		foreach ( $data as $record )
		{
			if ( $record['status'] === 'spam' || $record['status'] === 'trash' ) continue;

			if ( strlen( $record['user_agent'] ) > 20 ) 
				$record['user_agent'] = 'active';

			if ( $current_outcome === 'active' )
			{
				if ( $record['4'] !== 'Requested call back' && $record['4'] !== 'No answer' ) continue;
			}

			$entries[] = $record;
		}

		$total_count = count( $entries );

		if ( $total_count > 20 )
		{
			$entries_chunks = array_chunk( $entries, 20 );
			
			$entries = array();
			
			if ( ! empty( $entries_chunks[$current_page-1] ) && is_array( $entries_chunks[$current_page-1] ) )
				$entries = $entries_chunks[$current_page-1];
		}

		return compact( 'entries', 'total_count' );
	}


	/**
	 * Remove lead by its id
	 *
	 * @param $lead_id
	 *
	 * @return mixed
	 */
	public static function destroy_lead( $lead_id )
	{
		$lead = self::find_lead_by_id( $lead_id );

		if ( $lead === false ) return;

		return \GFAPI::delete_entry( $lead_id );
	}

	/**
	 * Validate the current lead data and return a message to the frontend if error
	 *
	 * @param $lead
	 *
	 * @return bool|\WP_Error Error message
	 */
	public static function validate( $lead, $update = true )
	{
		if ( $lead['form_id'] != self::$form_id )
			return new \WP_Error( "denied", "Hacked? huh!" );

		if ( false === $update )
			$current_lead = $lead;
		else
			$current_lead = \GFAPI::get_entry( $lead['id'] );

		if ( !empty ( $lead[10] ) && $lead[10] != $current_lead[10] )
		{
			$email_check = array();

			$email_check["field_filters"][] = array( 'key' => '10', 'value' => $lead[10] );

			// Check if email is exists. If so, exit
			$emails = \GFAPI::get_entries( self::$form_id, $email_check );

			if ( count( $emails ) > 0 )
				return new \WP_Error( "duplicate", "The email you've entered has existed before!" );
		}

		if ( !empty ( $lead[9] ) && $lead[9] != $current_lead[9] )
		{
			$phone_check = array();

			$phone_check["field_filters"][] = array( 'key' => '9', 'value' => $lead[9] );

			$phones = \GFAPI::get_entries( self::$form_id, $phone_check );

			if ( count( $phones ) > 0 )
				return new \WP_Error( "duplicate", "The phone you've entered was existed before!" );
		}

		return true;
	}

	public static function get_statuses()
	{
		return array( 'red', 'green', 'blue' );
	}

	public static function get_outcomes()
	{
		return array(
			'Interested',
			'Not interested',
			'Requested call back',
			'Incorrect phone number',
			'Incorrect information',
			'Already been processed',
			'No answer',
			'Unable to establish contact'
		);
	}

	public static function get_callback_times()
	{
		return array(
			'8am', '9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm'
		);
	}

	public static function get_customer_services()
	{
		return array(
			'Has been contacted by 3 installers?',
			'Asked them to add a rating and review about their installer',
			'Gave them awesome customer service'
		);
	}

	public static function create_lead_from_gravity_form( $lead, $form_id ) 
	{
		$notes = array();

		$note = "Cloned From #{$form_id}: {$lead['id']} <br />";

		if ( ! empty( $lead[6] ) )
			$note .= "Method: {$lead[6]}";

		$new_note = array(
			'date'    => date( 'Y-m-d' ),
			'user_id' => get_current_user_id(),
			'note'    => $note
		);

		if ( ! empty( $lead[7] ) && $form_id == 27 ) 	
			$new_note['note'] .= "Callback time: {$lead[7]}";

		$notes[] = $new_note;

		$notes = serialize( $notes );

		$name       = explode( ' ', $lead[2] );
		$first_name = $name[0];
		$last_name  = '';
		if ( isset( $name[1] ) )
			$last_name = ltrim( $lead[2], $first_name );

		// Match form #45 with #27
		$match = array(
			'14.5' => 1,
			9      => 3,
			10     => 4
		);

		$unset = array( 'id', 1, 2, 3, 4, 6, 7, 8, 9 );

		$new_entry = $lead;
		foreach ( $unset as $key ) {
			unset( $new_entry[ $key ] );
		}

		foreach ( $match as $key => $value ) {
			$new_entry[ $key ] = $lead[ $value ];
		}
		
		$url = $lead['source_url'];
		if ( str_contains( $url, 'ref' ) )
		{
			$parsed_url = parse_url( $url, PHP_URL_QUERY );
			parse_str( $parsed_url, $parsed_query_string );
			if ( isset( $parsed_query_string['ref'] ) )
				$new_entry['transaction_id'] = $parsed_query_string['ref'];
		}

		$new_entry['form_id'] = 45;
		$new_entry['2.3']     = $first_name;
		$new_entry['2.6']     = $last_name;
		$new_entry[4]         = 'No answer';
		$new_entry[1]         = isset( $new_entry['transaction_id'] ) ? strtoupper( $new_entry['transaction_id'] ) : 'C';
		$new_entry[11]        = 0;
		$new_entry['source_url']    = "#{$form_id}:{$lead['id']}";
		$new_entry['date_created']  = date("Y-m-d H:i:s"); // Reset created at
		$new_entry['user_agent']  	= 'active';

		$validate = self::validate( $new_entry, false );
		
		if ( !is_wp_error( $validate ) )
		{
			$lead_id = \GFAPI::add_entry( $new_entry );

			gform_update_meta( $lead_id, 'notes', $notes );
		}

	}
}