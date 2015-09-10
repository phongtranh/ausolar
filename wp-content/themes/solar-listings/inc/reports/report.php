<?php
/**
 * Report class after fine tuning
 */
class Solar_Report
{
    public static function general( $yyyy, $mm )
    {
        $time_offset    = sl_report_timezone_offset() * 3600;
        $start_date     = date('Y-m-d H:i:s', mktime( 0, 0, 0, $mm, 1, $yyyy ) - $time_offset);

        // This is the last day of selected month
        $end_date       = date('Y-m-d H:i:s', mktime( 0, 0, 0, $mm+1, 1, $yyyy ) - $time_offset);
        $key            = sprintf( '%02d', $mm ) . '-' . $yyyy;

        $total_leads    = 0;

        $search_criteria = compact( 'start_date', 'end_date' );
        
        if ( ! empty( $_GET['sources'] ) )
        {
            $search_criteria['field_filters'][] = array(
                'key' => '57',
                'operator' => 'in',
                'value' => $_GET['sources']
            );
        }

        $records = \GFAPI::get_entries( 1, $search_criteria, null, array( 'offset' => 0, 'page_size' => 9999 ), $total_leads );
        
        // Get leads source
        $sources = solar_get_sources();
        $sources_count = array();
        $state_count = array();

        // GF doesn't support assoc records
        $leads = array();
        foreach ( $records as $lead )
        {
            if ( empty( $lead[57] ) )
                $lead[57] = 'I';

            $leads[$lead['id']] = $lead;

            if ( ! isset( $sources_count[$sources[$lead[57]]] ) )
                $sources_count[$sources[$lead[57]]] = array();
            
            $sources_count[$sources[$lead[57]]][] = $lead['id'];

            if ( ! isset( $state_count[$lead['17.4']] ) )
                $state_count[$lead['17.4']] = array();

            $state_count[$lead['17.4']][] = $lead['id'];
        }
        
        // Companies report. Most important step
        $companies_leads            = array();
        $previous_month_leads       = array();
        $all_approved_leads         = array();
        $all_rejected_leads         = array();
        $total_rejected_reasons     = array();
        $companies_rejected_sources = array();
        $companies_approved_sources = array();
        $all_previous_month_leads   = array();

        // Get companies leads
        $company_meta_data = get_active_companies();
        
        foreach ( $company_meta_data as $row )
        {
            $company = get_post( $row->post_id );

            $company_leads = @unserialize( $row->meta_value );

            if ( empty( $company_leads[$key] ) ) continue;
            
            $all_leads                                  = explode( ',', $company_leads[$key] );
            $all_rejecteds                              = solar_get_rejected_leads( $company->ID );
            // Previous month exists because next match
            $previous_month_leads                       = array_diff( $all_leads, array_keys( $leads ) );    
           
            // Because we only get leads on this month, so we have to manual append 
            // leads information of previous month to calculate
            if ( ! empty( $previous_month_leads ) )
            {
                foreach( $previous_month_leads as $lead_id )
                {
                    if ( ! isset( $all_previous_month_leads[$lead_id] ) )
                    {
                        $lead = GFAPI::get_entry( $lead_id );
                        
                        if ( ! empty( $_GET['sources'] ) && in_array( $lead[57], $_GET['sources'] ) )
                        {
                            $sources_count[$sources[$lead[57]]][] = $lead['id'];
                            $state_count[$lead['17.4']][] = $lead['id'];
                        }
                    }

                    $all_previous_month_leads[$lead_id] = $lead_id;
                }
            }

            $rejected_leads = array();
            $approved_leads = array();

            foreach ( $all_leads as $lead )
            {
                if ( array_key_exists( $lead, $all_rejecteds ) )
                {
                    $rejected_leads[$lead] = $lead;

                    if ( ! isset( $total_rejected_reasons[$all_rejecteds[$lead]] ) )
                        $total_rejected_reasons[$all_rejecteds[$lead]] = 0;

                    $total_rejected_reasons[$all_rejecteds[$lead]]++;
                }
                else
                {
                    $approved_leads[$lead] = $lead;
                }

                if ( ! empty ( $rejected_leads ) )
                {
                    $companies_rejected_sources[$company->ID]['count']  = count( $rejected_leads );
                    $companies_rejected_sources[$company->ID]['count_distinct']  = count( array_unique( $rejected_leads ) );
                    $companies_rejected_sources[$company->ID]['sources'] = asq_get_leads_sources( $rejected_leads, $sources_count );
                    $companies_rejected_sources[$company->ID]['name']    = $company->post_title;
                    $companies_rejected_sources[$company->ID]['id']      = $company->ID;
                    $companies_rejected_sources[$company->ID]['percent'] = number_format( count( $rejected_leads ) / count(
                            $all_leads ) * 100, 2 );
                    $all_rejected_leads[$company->ID]                    = $rejected_leads;
                }

                if ( ! empty ( $approved_leads ) )
                {
                    $companies_leads_sources[$company->ID]['count']     = count( $approved_leads );
                    $companies_leads_sources[$company->ID]['count_distinct']     = count( array_unique( $approved_leads ) );
                    $companies_leads_sources[$company->ID]['sources']   = asq_get_leads_sources( $approved_leads, $sources_count );
                    $companies_leads_sources[$company->ID]['name']      = $company->post_title;
                    $companies_leads_sources[$company->ID]['id']        = $company->ID;
                    $all_approved_leads[$company->ID]                   = $approved_leads;
                }
            }
        }
        
        $approved_leads = array();
        foreach ( $all_approved_leads as $index => $leads )
        {
            foreach ( $leads as $lead )
            {
                if( ! empty( $lead ) )
                    $approved_leads[] = $lead;
            }
        }

        $total_approved = count( $approved_leads );

        $approved_leads_sources = asq_get_leads_sources( $approved_leads, $sources_count );

        $rejected_leads = array();
        foreach ( $all_rejected_leads as $index => $leads )
        {
            foreach ( $leads as $lead )
            {
                if ( ! empty ( $lead ) )
                    $rejected_leads[] = $lead;
            }
        }

        $state_data = array();
        foreach ( $approved_leads as $lead )
        {
            foreach ( $state_count as $state => $leads )
            {
                if ( ! in_array( $lead, $leads ) ) continue;

                if ( ! isset( $state_data[$state] ) )
                    $state_data[$state] = array();

                $state_data[$state][] = $lead;
            }
        }

        $state_bar = array();
        $state_pie = array();

        foreach ( $state_data as $state => $leads )
        {
            $state_bar[$state]['count']   = count( $leads );
            $state_bar[$state]['sources'] = asq_get_leads_sources( $leads, $sources_count );

            $total_matched  = count( $leads );
            $total_income   = $total_matched * 30;

            $state_pie[] = array(
                $state,
                $total_income,
                "$ {$total_income}, {$total_matched} leads"
            );
        }

        uasort( $companies_leads_sources, 'asq_source_sort' );
        uasort( $companies_rejected_sources, 'asq_source_sort' );
        uasort( $state_bar, 'asq_source_sort' );
        return compact( 'total_leads', 'companies_rejected_sources', 'companies_leads_sources', 'approved_leads_sources', 'state_bar', 'state_pie', 'total_approved', 'total_rejected_reasons', 'sources_count', 'all_previous_month_leads' );
    }


    /**
     * Report for archive page
     * @return array Report array
     */
    // public static function all()
    // {
    //     $setting_asq 				= get_setting_asq();
    //     $setting_energysmart 		= get_setting_energysmart();

    //     $all_leads 					= get_filtered_leads();

    //     $companies					= get_companies_leads_report( $all_leads );

    //     $reasons 					= $companies['total_rejected_reasons'];
    //     $approved_leads 			= $companies['approved_leads'];

    //     $total_leads 				= ( ! empty( $all_leads ) ) ? substr_count( $all_leads, ',' ) + 1 : 0;

    //     $sources 					= solar_get_leads_sources( $all_leads );

    //     $sources_approved 			= solar_get_approved_leads_sources( $all_leads, $approved_leads );

    //     $matched_incomes			= solar_get_income( $sources_approved );
    //     $states						= get_leads_states( $all_leads );

    //     return compact( 'total_leads', 'total_actives', 'sources', 'approved_leads', 'matched_incomes', 'reasons', 'states', 'companies', 'setting_asq', 'setting_energysmart' );
    // }

    /**
     * Report for singular page
     * @return array Report array
     */
    public static function single()
    {
        $company_id = $_GET['company_id'];

        // Turn $company_id to $company object
        $company       = get_post( $company_id );
        $company_leads = get_company_leads( $company_id, 'array' );

        $total_leads    = count( $company_leads );
        $all_rejected   = solar_get_rejected_leads( $company );

        $rejected_leads = array();
        $approved_leads = array();

        if ( ! empty( $company_leads ) )
        {
            foreach ( $company_leads as $lead )
            {
                if ( isset( $all_rejected[$lead] ) )
                    $rejected_leads[$lead] = $all_rejected[$lead];
                else
                    $approved_leads[$lead] = $lead;
            }
        }

        $sources = solar_get_leads_sources( $approved_leads );
        $incomes = solar_get_income( $sources );

        $reasons = array_count_values( $rejected_leads );

        return compact( 'company', 'company_leads', 'total_leads', 'rejected_leads', 'reasons', 'sources', 'incomes' );
    }

    public static function wholesale()
    {
        $all_leads = get_filtered_leads();

        // This report included reject and approved leads data
        $leads_data = get_companies_leads_report( $all_leads );

        // Get leads approved and rejected count
        $approved_leads = $leads_data['approved_leads'];
        $rejected_leads = $leads_data['rejected_leads'];
	    $reasons 		= $leads_data['total_rejected_reasons'];

	    $all_leads      = explode( ',', $all_leads );

        $approved_count = array_count_values( $approved_leads );
        $rejected_count = array_count_values( $rejected_leads );

        $raw = array();
        foreach ( $all_leads as $lead )
        {
            $raw[$lead]['approved'] = isset( $approved_count[$lead] ) ? $approved_count[$lead] : 0;
            $raw[$lead]['rejected'] = isset( $rejected_count[$lead] ) ? $rejected_count[$lead] : 0;
        }

	    $states						= get_leads_states( $all_leads );

	    // Reformat state pie chart
	    $states['state_pie_chart'] = array();
	    $states['state_pie_chart'] = array();
	    if ( !empty ( $states['states_data'] ) ) {
		    foreach ( $states['states_data'] as $state_code => $data ) {
			    $states['state_pie_chart'][] = array(
				    get_state_name( strtolower( $state_code ) ),
				    ( array_sum( $data ) / 2 ),
				    ( array_sum( $data ) / 2 ) . ' Matched'
			    );
		    }
	    }

        return compact( 'raw', 'approved_leads', 'rejected_leads', 'all_leads', 'reasons', 'states' );
    }
}

function asq_source_sort( $a, $b )
{
   return $b['count'] - $a['count'];
}

function asq_get_leads_sources( $leads_id, $sources_count )
{
    $sources            = array();

    foreach ( $leads_id as $lead_id )
    {
        foreach ( $sources_count as $source => $leads_id )
        {
            if ( ! in_array( $lead_id, $leads_id ) ) continue;
            
            if ( ! isset( $sources[$source] ) )
                $sources[$source] = 0;

            $sources[$source]++;      
        }
    }

    return $sources;
}

function solar_get_approved_leads_sources( $all_leads, $approved_leads = array() )
{
    //if ( empty ( $all_leads ) ) return array();
    $available_sources  = solar_get_sources();

    $sources_raw        = solar_get_leads_sources( $all_leads, false, true );
    $matched_sources    = array();

    foreach ( $sources_raw as $k => $v )
        $matched_sources[$v->value] = explode( ',', $v->leads_id );

    $sources = array();
    foreach ( $available_sources as $key => $value )
        $sources[$value] = 0;

    foreach ( $approved_leads as $lead_id )
    {
        foreach( $matched_sources as $source => $leads_id )
        {
            if ( in_array( $lead_id, $leads_id ) )
                $sources[$available_sources[$source]]++;
        }
    }

    $sources['internal'] += count( $approved_leads ) - array_sum( $sources );
	$sources['total']	= count( $approved_leads );

    return $sources;
}

function get_filtered_leads()
{
    $available_sources 	= solar_get_sources();
    $all_leads 	   		= get_leads_id( 1, 'comma', 'ORDER BY date_created DESC');

    //If user filtered by sources
    $sources_input = solar_get_sources_input();

    if( ! empty ( $sources_input ) )
    {
        $leads = array();

        foreach ( $available_sources as $key => $value )
            $leads[$key] = array();

        $sources 	= solar_get_leads_sources( $all_leads, false, true );
        $all_leads 	= explode( ',', $all_leads );

        $tmp_total_leads = array();

        foreach ( $sources as $source ){
            $leads[$source->value] 	= explode( ',', $source->leads_id );
            $tmp_total_leads 		= array_merge( $tmp_total_leads, $leads[$source->value] );
        }

        $empty_source_leads = array_diff( $all_leads, $tmp_total_leads );
		$leads['I'] = array_merge( $leads['I'], $empty_source_leads );

        foreach ( $leads as $source => $lead )
        {
            if ( ! in_array( $source, solar_get_sources_input() ) )
                unset( $leads[$source]);
        }

        $all_leads = '';
        foreach ( $leads as $key => $leads_id )
        {
            if ( ! empty ( $leads_id ) )
                $all_leads .= join( ',', $leads_id ) . ',';
        }

        $all_leads = rtrim( $all_leads, ',' );
    }

    return $all_leads;
}

function solar_get_sources()
{
    return array(
        'I' => 'internal',
	    'C' => 'callback',
        'P' => 'phone',
        'E' => 'energysmart',
        'Y'	=> 'your-solar-quotes',
        'F' => 'solar-lead-factory',
        'L' => 'exclusive-leads',
        'J' => 'jack-media',
        'K' => 'solar-leads',
	    'M' => 'ocere',
        'N' => 'green-utilities',
        'T' => 'solar-power-today',
        'U' => 'cleantechnia'
    );
}

function solar_get_source_with_title( $code = '' )
{
	$sources = array(
		''  => array( 'asq-website', 'Internal' ),
		'I' => array( 'asq-website', 'Internal' ),
		'C' => array( 'asq-internal', 'Call back' ),
		'P' => array( 'asq-phone', 'Phone in' ),
		'E' => array( 'energy-smart', 'Energy Smart' ),
		'Y' => array( 'your-solar-quotes', 'Your Solar Quotes' ),
		'F' => array( 'solar-lead-factory', 'Solar Lead Factory' ),
		'L' => array( 'exclusive-leads', 'Exclusive Leads' ),
		'J' => array( 'jack-media', 'Jack Media' ),
		'K' => array( 'solar-leads', 'Solar Leads' ),
        'M' => array( 'ocere', 'Ocere' ),
        'N' => array( 'green-utilities', 'Green Utilities' ),
		'T' => array( 'solar-power-today', 'Solar Power Today' ),
        'U' => array( 'cleantechnia', 'Cleantechnia' )
	);

	if ( ! empty ( $code ) )
		return $sources[$code];

	return $sources;
}

function solar_get_rejection_reasons()
{
    return array(
        'no-contact'    => 'No Contact',
        'service-area'  => 'Service Area',
        'duplicate'     => 'Duplicate',
        'other'         => 'Other'
    );
}

function solar_get_sources_input()
{
    return isset( $_GET['sources'] ) ? $_GET['sources'] : '';
}

/**
 * Put sources and retrieve income of that source
 * @param  array  $sources Ex: [website => total, energysmart => total, ...]
 * @return array  [website => income, energysmart => income, ..., total => income]
 */
function solar_get_income( $sources )
{
    if ( empty ( $sources ) ) return;

    $asq 			= get_setting_asq();
    $energysmart 	= get_setting_energysmart();

    $incomes = array();

    foreach ( $sources as $source => $count )
    {
        if ( $source === 'total' ) continue;

        $incomes[$source] = $count * $asq;

        if ( $source === 'energysmart' )
            $incomes[$source] = $count * $energysmart;
    }

    $incomes['total'] = array_sum( $incomes );

    return $incomes;
}

/**
 * We have to set max length to get longer length on GROUP_CONCAT function
 * @param integer $length max length
 * @return  void
 */
function solar_set_group_concat_max_len( $length = 99999 )
{
    global $wpdb;
    $wpdb->query( "SET SESSION group_concat_max_len = {$length}" );
}

/**
 * Get state name by code
 * @param string code Code of state
 * @return mixed State name
 */
function get_state_name( $code = null)
{
    $states = array(
        'act'   => 'Australian Capital Territory',
        'nsw'   => 'New South Wales',
        'nt'    => 'Northern Territory',
        'qld'   => 'Queensland',
        'sa'    => 'South Australia',
        'tas'   => 'Tasmania',
        'wa'    => 'Western Australia',
        'vic'   => 'Victoria'
    );

    if ( $code === null )
        return $states;

    $code = trim( strtolower( $code ) );

    return $states[$code];
}


/**
 * Get time offset query
 * @return SQL time offset SQL
 */
function get_queried_time()
{
    $time_offset = sl_report_timezone_offset() * 3600;

    $yyyy 	= date( 'Y' );
    $mm 	= date( 'm' );

    if ( ! empty( $_GET['year'] ) && ! empty( $_GET['month'] ) ){
        $yyyy 	= trim( $_GET['year'] );
        $mm 	= trim( $_GET['month'] );
    }

    $start_date = date('Y-m-d H:i:s', mktime( 0, 0, 0, $mm, 1, $yyyy ) - $time_offset);

    // This is the last day of selected month
    $end_date 	= date('Y-m-d H:i:s', mktime( 0, 0, 0, $mm+1, 1, $yyyy ) - $time_offset);

    $queried_time = " AND date_created BETWEEN '{$start_date}' AND '{$end_date}'";

    return $queried_time;
}


/**
 * Put leads id and get rejected lead id
 * @param  mixed  $leads array of leads id or separated by commas
 * @param  string $return Return type 'comma' or 'array'
 * @return array
 * @author Tan Nguyen
 */
function leads_to_rejecteds( $leads_id = array(), $return = 'comma' )
{
    global $wpdb;

    if ( is_array( $leads_id ) )
        $leads_id = join( ',', $leads_id );

    if ( empty ( $leads_id ) ) return;

    $query = "
        SELECT lead_id
        FROM `{$wpdb->prefix}rg_lead_detail`
        WHERE `form_id` = 36
        AND `field_number` = 2
        AND value IN({$leads_id})
    ";

    $rejecteds = $wpdb->get_col( $query );

    if ( $return === 'comma' )
        $rejecteds = join( ',', $rejecteds );

    return $rejecteds;
}


/**
 * Put leads id form 36 and retrieve leads id of form 1
 * @param  mixed  $leads array or list of leads separated by commas
 * @param  string $return Return type 'comma' or 'array'
 * @return array leads id of form 1
 * @author Tan Nguyen
 */
function rejecteds_to_leads( $leads_id = array(), $return = 'comma' )
{
    global $wpdb;

    if ( is_array( $leads_id ) )
        $leads_id = join( ',', $leads_id );

    if ( empty( $leads_id ) ) return;

    $query = "
		SELECT value
		FROM {$wpdb->prefix}rg_lead_detail
		WHERE form_id = 36
		AND field_number = 2
		AND lead_id IN({$leads_id})
	";

    $leads = $wpdb->get_col( $query );

    if( $return === 'comma' )
        $leads = join( ',', $leads );

    return $leads;
}

/**
 * Put leads id and retrieve not rejected
 * @param  array  $leads List of leads id
 * @param  string $return Return type 'comma' or 'array'
 * @return array  not rejected leads
 * @author Tan Nguyen
 */
function get_active_leads( $leads_id = array(), $return = 'comma' )
{
    global $wpdb;

    // If we not set, get everything
    if ( empty( $leads_id ) )
        $leads_id 	= get_leads_id( 1, 'array' );

    $active_leads 	= array();

    $rejecteds 		= get_rejected_leads( 'array' );

    if ( ! is_array( $leads_id ) )
        $leads_id = explode( ',', $leads_id );

    if ( empty( $leads_id ) ) return;

    $active_leads 	= array_diff( $leads_id, $rejecteds );

    if ( $return === 'comma' )
        $active_leads = join( ',', $active_leads );

    return $active_leads;
}

/**
 * Put leads id and retrieve matched
 * @param  array  $leads_id array of leads, separated by commas
 * @param  string $return Return type number / array / full
 * @return array matched leads
 * @author Tan Nguyen
 */
function get_matched_leads( $leads_id = array(), $return = 'array' )
{
    global $wpdb;

    //$leads_id = get_active_leads( $leads_id );

    if ( is_array( $leads_id ) )
        $leads_id = join( ',', $leads_id );

    if ( empty ( $leads_id ) ) return;

    $query = "
		SELECT lead_id, meta_value
		FROM `{$wpdb->prefix}rg_lead_meta`
		WHERE `meta_key` = 'companies'
		AND meta_value <> ''
		AND form_id = 1 AND lead_id IN({$leads_id})
	";

    $matched = $wpdb->get_results( $query );

    $total_matched = 0;

    // Now return lead => matched companies
    foreach ( $matched as $key => $match_data ){
        $matched[$match_data->lead_id] = $match_data->meta_value;
        $total_matched += substr_count( $match_data->meta_value, ',' ) + 1;
        unset ( $matched[$key] );
    }

    if( $return === 'number' )
        return $total_matched;

    if( $return === 'array' )
        return $matched;

    return compact( 'total_matched', 'matched' );
}


/**
 * Get all lead id of form from a specified time
 * @param  integer $form             Form ID, default 1
 * @param  string $return Return type 'comma' or 'array'
 * @return array leads id
 */
function get_leads_id( $form = 1, $return = 'comma', $append_query = '' )
{
    global $wpdb;

    $time_offset = get_queried_time();

    $query = "
		SELECT id
		FROM {$wpdb->prefix}rg_lead
		WHERE status = 'active'
		AND form_id = {$form} {$time_offset} {$append_query}
	";

    $leads_id = $wpdb->get_col( $query );

    if ( $return === 'comma' )
        $leads_id = join( ',', $leads_id );

    return $leads_id;
}

/**
 * Get 'real' lead id from rejected form
 * @param  string $return Return type 'comma' or 'array'
 */
function get_rejected_leads( $return = 'comma' )
{
    $rejecteds 	= get_leads_id( 36, 'comma' );

    $leads 		= rejecteds_to_leads( $rejecteds, $return );

    return $leads;
}

/**
 * Put leads id and get their reasons
 * @param  array  $leads_id Leads id to get report
 * @return array reasons and total lead
 * @author Tan Nguyen
 */
function get_rejected_leads_reasons( $leads_id = array() )
{
    global $wpdb;

    $titles = array(
        'The lead is a duplicate lead'                      => 'duplicate',
        'You are not able to establish contact'             => 'no-contact',
        'The Prospect is outside your Elected Service Area' => 'service-area',
        'Any other reasonable grounds (describe below)'     => 'other',
    );

    if ( empty( $leads_id ) )
        $leads_id = get_leads_id( 36 );
    else
        $leads_id = leads_to_rejecteds( $leads_id );

    $query = "
		SELECT value, count(0) as total
	    FROM `{$wpdb->prefix}rg_lead_detail`
	    WHERE form_id = 36 AND field_number = 3
	    AND lead_id IN({$leads_id})
	    GROUP BY value
    ";

    $reasons = $wpdb->get_results( $query );

    foreach ( $reasons as $key => $data )
    {
        $reasons[$titles[$data->value]] = $data->total;
        unset( $reasons[$key] );
    }

    return $reasons;
}

/**
 * Put leads id and get their states
 * @param  array  $leads_id lead id to get
 * @return array state and total leads
 */
function get_leads_states( $leads_id = array() )
{
    global $wpdb;

//    if ( empty( $leads_id ) )
//        $leads_id = get_matched_leads();

    if ( is_array( $leads_id ) )
        $leads_id = join( ',', $leads_id );

    if ( empty ( $leads_id ) ) return;

    $states 	= get_state_name();
    $state_keys = '';
    foreach ( $states as $state => $value )
        $state_keys .= "'{$state}',";

    $state_keys = rtrim( $state_keys, ',' );

    solar_set_group_concat_max_len();

    $query = "
		SELECT value, COUNT(0) as total, GROUP_CONCAT(lead_id) as leads_id
    	FROM `{$wpdb->prefix}rg_lead_detail`
    	WHERE form_id = 1 AND trim(field_number) = '17.4'
    	AND value IN({$state_keys})
    	AND lead_id IN( $leads_id )
    	GROUP BY value
    ";

    $states_data = $wpdb->get_results( $query );

    $states_income = array();
    $state_pie_chart = array();

    foreach ( $states_data as $index => $state )
    {
        $total_matched 					= get_matched_leads( $state->leads_id, 'number' );
        $states_data[$state->value]  	= solar_get_leads_sources( $state->leads_id, $total_matched );

        $states_income[$state->value] 	= solar_get_income( $states_data[$state->value] );

        $state_pie_chart[] = array(
            get_state_name( $state->value ),
            $states_income[$state->value]['total'],
            "$ {$states_income[$state->value]['total']}, $total_matched leads"
        );

        unset( $states_data[$index] );
    }

    uasort( $states_income, 'state_sort' );

    return compact('states_data', 'states_income', 'state_pie_chart');
}

/**
 * Put leads id and get their sources
 * @param mixed $leads_id array or commass
 * @param boolean $matched If set to false, we'll return source total matched
 * @param  boolean $raw Return raw data
 */
function solar_get_leads_sources( $leads_id = array(), $matched = false, $raw = false )
{
    global $wpdb;
    $titles = solar_get_sources();

    // If is array, convert it to string separated by commas
    if ( is_array( $leads_id ) )
        $leads_id = implode( ',', $leads_id );

    if ( empty( $leads_id ) ) return array();

    solar_set_group_concat_max_len();

    $query = "
		SELECT value, count(0) as total, GROUP_CONCAT(lead_id) as leads_id
        FROM `{$wpdb->prefix}rg_lead_detail`
        WHERE form_id = 1 AND field_number = 57
        AND lead_id IN({$leads_id})
        GROUP BY value
	";

    $sources = $wpdb->get_results( $query );

    if ( $raw )
        return $sources;

    foreach ( $sources as $index => $source )
    {
        $sources[$titles[$source->value]] = intval( $source->total );

        if ( $matched )
            $sources[$titles[$source->value]] = get_matched_leads( $source->leads_id, 'number' );

        unset ( $sources[$index] );
    }

    // If source not set, set it to zero. This is to make sure no warning
    foreach ( $titles as $k => $v )
    {
        if ( !isset( $sources[$v] ) )
            $sources[$v] = 0;
    }

    $sources['total'] = substr_count( $leads_id, ',' ) + 1;
    if ( $matched )
        $sources['total'] = get_matched_leads( $leads_id, 'number' );

    $empty_source_leads_count = $sources['total'] * 2 - array_sum( $sources );
	$sources['internal'] += $empty_source_leads_count;
    return $sources;
}

/**
 * Get company leads
 *
 * @param mixed  $company_id ID of company
 * @param string $return Return leads ID in CSV ('comma') or 'array' format
 * @param array  $leads_id
 *
 * @return array company,
 */
function get_company_leads( $company_id = null, $return = 'comma', $leads_id = null )
{
    $year  = date( 'Y' );
    $month = date( 'm' );

    if ( ! empty ( $_GET['year'] ) && ! empty ( $_GET['month'] ) )
    {
        $year  = trim( $_GET['year'] );
        $month = trim( $_GET['month'] );
    }

    // Generate meta key string
    $key = sprintf( '%02d', $month ) . '-' . $year;

    $companies = get_active_companies();

    foreach ( $companies as $k => $company )
    {
        $meta_value = unserialize( $company->meta_value );

        if ( ! isset( $meta_value[$key] ) )
            $meta_value[$key] = '';

        $company_leads = $meta_value[$key];

        if ( ! is_null ( $leads_id ) )
        //if( 1 )
        {
            if ( ! is_array( $leads_id ) )
                $leads_id = explode( ',', $leads_id );

            $company_leads    = array();
            $meta_value[$key] = explode( ',', $meta_value[$key] );

            foreach ( $meta_value[$key] as $lead_id )
            {
                if ( in_array( $lead_id, $leads_id ) )
                    $company_leads[] = $lead_id;
            }
            $company_leads = join( ',', $company_leads );
        }

        if ( ! empty ( $meta_value[$key] ) )
            $companies[$company->post_id] = $company_leads;

        if ( $return === 'array' && ! empty ( $meta_value[$key] ) )
            $companies[$company->post_id] = explode( ',', $company_leads );

        unset( $companies[$k] );
    }

    if ( $company_id !== null )
    {
        if ( isset ( $companies[$company_id] ) )
            return $companies[$company_id];

        return null;
    }

    return $companies;
}

/**
 * Get all bought leads companies
 * @return array Companies
 */
function get_active_companies()
{
    global $wpdb;

    $query = "
		SELECT *
		FROM `{$wpdb->prefix}postmeta`
		WHERE `meta_key` = 'leads_count'
		AND length(meta_value) > 0
		ORDER BY length(meta_value) DESC
	";

    return $wpdb->get_results( $query );
}

/**
 * Company lead report for archive page
 * @param  array  $companies Array of companies
 * @return array Sources and Rejected sources for archive page
 */
function get_companies_leads_report( $leads_id = array() )
{
    $companies_leads	= get_company_leads( null, 'array', $leads_id );

    $companies_rejected_sources = array();
    $companies_leads_sources 	= array();

    $total_approved         = array();
    $total_rejected         = array();
    $total_rejected_reasons = array();

    foreach ( $companies_leads as $company_id => $leads )
    {
        $company = get_post( $company_id );

        $all_rejecteds 		= solar_get_rejected_leads( $company );

        $rejected_leads = array();
        $approved_leads = array();

        foreach ( $leads as $lead )
        {
            if ( array_key_exists( $lead, $all_rejecteds ) )
            {
                $rejected_leads[$lead] = $lead;

	            if ( !isset( $total_rejected_reasons[$all_rejecteds[$lead]] ) )
		            $total_rejected_reasons[$all_rejecteds[$lead]] = 0;

                $total_rejected_reasons[$all_rejecteds[$lead]]++;
            }
            else
            {
                $approved_leads[$lead] = $lead;
            }
        }

        if ( ! empty ( $rejected_leads ) )
        {
            $companies_rejected_sources[$company_id]['incomes'] = solar_get_income( solar_get_leads_sources( $rejected_leads ) );
            $companies_rejected_sources[$company_id]['name'] 	= isset ( $company->post_title ) ?
                $company->post_title : '';
            $companies_rejected_sources[$company_id]['id'] 		= $company_id;
            $companies_rejected_sources[$company_id]['percent']	= number_format( count( $rejected_leads ) / count(
                    $leads ) * 100, 2);
            $total_rejected[]                                   = $rejected_leads;
        }

        if ( ! empty ( $approved_leads ) )
        {
            $companies_leads_sources[$company_id]['incomes'] 	= solar_get_income( solar_get_leads_sources( $approved_leads ) );
            $companies_leads_sources[$company_id]['name'] 		= isset ( $company->post_title ) ?
                $company->post_title : '';
            $companies_leads_sources[$company_id]['id'] 		= $company_id;
            $total_approved[]                                   = $approved_leads;
        }
    }

    $approved_leads = array();
    foreach ( $total_approved as $index => $leads )
    {
        foreach ( $leads as $lead )
        {
            if( ! empty( $lead ) )
                $approved_leads[] = $lead;
        }
    }

    $rejected_leads = array();
    foreach ( $total_rejected as $index => $leads )
    {
        foreach ( $leads as $lead )
        {
            if ( ! empty ( $lead ) )
                $rejected_leads[] = $lead;
        }
    }

    // Sort the report based on it total income
    uasort( $companies_leads_sources, 'company_income_sort' );
    uasort( $companies_rejected_sources, 'company_income_sort' );

    return compact( 'companies_leads_sources', 'companies_rejected_sources', 'approved_leads', 'rejected_leads', 'total_rejected_reasons' );
}

function company_income_sort( $a, $b )
{
    if ( $a['incomes']['total'] === $b['incomes']['total'] ) return 0;
    return ( $a['incomes']['total'] > $b['incomes']['total'] ) ? -1 : 1;
}

function state_sort( $a, $b )
{
    if ( $a['total'] === $b['total'] ) return 0;
    return ( $a['total'] > $b['total'] ) ? -1 : 1;
}

/** Todo: Remove this function **/
function sl_report_get_setting( $key, $default = '' )
{
    $settings = get_option( '7listings' );

    if( isset( $settings[$key] ) )
        return $settings[$key];

    return $default;
}

function get_setting_asq()
{
    return sl_setting( 'solar_website_lead_value' );
}

function get_setting_energysmart()
{
    return sl_setting( 'solar_es_lead_value' );
}

/** Todo: Remove this function **/
function sl_report_timezone_offset()
{
    $gmt_offset = get_option( 'gmt_offset' );
    $timezone_string = get_option( 'timezone_string' );
    if ( ! $gmt_offset && $timezone_string )
    {
        $timezone_selected = new DateTimeZone( $timezone_string );
        $gmt_offset = timezone_offset_get( $timezone_selected, date_create() ) / 3600;
    }

    return $gmt_offset;
}