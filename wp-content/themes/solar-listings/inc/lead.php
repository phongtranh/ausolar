<?php
/**
 * Lead and it own relationship tables query class
 */
class Solar_Leads_Query{

	public $db;
	public $table_rg_lead;

	public $where_lead_ids = '';
	public $total_lead = 0;

	// Time to get report
	private $month = 0;
	private $year = 0;
	private $where_date_created = '';

	// Sort variable
	public $sort_flag = '';

	/**
	 * Set default properties
	 */
	public function __construct(){
		global $wpdb;
		$this->db = $wpdb;
        $this->table_rg_lead = $wpdb->prefix . 'rg_lead';
		
		// Set time and append time query to each query
		$this->set_time();

		// Only get stats with these leads
		$this->set_lead_by_month();
	}

	/**
	 * Set lead to be get on current month and form
	 * @param integer $form_id Form ID
	 */
	public function set_lead_by_month($form_id = 1, $remove_rejected = true)
	{
		if( $this->where_date_created === '' ){
			$this->total_lead = RGFormsModel::get_lead_count( $form_id, null );
			return; 
		}

		// Because we use GROUP_CONCAT to retrieve leads so we have to set longer length
		$this->set_group_concat_max_len();

		// Exclude rejected lead from result if $form_id <> 1
		$rejected_leads = '';
		if($form_id === 1 && $remove_rejected)
			$rejected_leads = " AND id NOT IN({$this->get_rejection_lead_id()})";

		$query = "SELECT GROUP_CONCAT(id) as lead_ids, count(0) as total 
					FROM {$this->table_rg_lead} 
					WHERE form_id = {$form_id} {$rejected_leads} {$this->where_date_created}";

		$leads_total = $this->db->get_row($query);

		$where_lead_ids = " AND lead_id IN({$leads_total->lead_ids})";
		$total_lead = $leads_total->total;
		
		$this->where_lead_ids = $where_lead_ids;
		$this->total_lead = $total_lead;
	}

	public function get_total_matched($lead_ids = '')
	{
		$this->set_group_concat_max_len();

		$where_lead_ids = ($lead_ids === '' || $lead_ids === null) 
                        ? $this->get_where_lead_id() 
                        : " AND lead_id IN({$lead_ids})";
		
		$query = "SELECT GROUP_CONCAT(meta_value) 
					FROM `{$this->table_rg_lead}_meta` 
					WHERE `meta_key` LIKE 'companies' 
					AND meta_value <> '' 
					AND form_id = 1 {$where_lead_ids}";

		$companies = $this->db->get_var($query);
		
		$companies_count = substr_count( $companies, ',' );

		if($companies_count > 0)
			$companies_count++;

		return $companies_count;
	}

	/**
	 * By default GROUP_CONCAT column is not long, so we have to set longer string
	 * for this field
	 */
	public function set_group_concat_max_len()
	{
		$this->db->query("SET SESSION group_concat_max_len = 999999");
	}

	public function get_where_lead_id()
	{
		return $this->where_lead_ids;
	}

	public function get_total_lead()
	{
		return $this->total_lead;
	}

	/**
	 * Remove rejected lead id from a list
	 * @param  string $lead_ids lead ids to check
	 * @return string           lead ids which not removed
	 */
	public function remove_rejected_lead_ids($lead_ids = '', $array = true)
	{
		$rejected_lead_ids = $this->get_rejection_lead_id();
		$rejecteds = explode(',', $rejected_lead_ids);
		$check = explode(',', $lead_ids);

		$return_array = array_diff($check, $rejecteds);

		if(!$array)
			return join(',', $return_array);

		return $return_array;
	}

	/**
	 * Get report by source to get array following format source => total_lead
	 * @param  array  $args Currently supports $args['lead_ids']
	 * @return array  Array source => total_lead
	 * @author Tan Nguyen <tannt.com@gmail.com>
	 */
    public function source_report( $args = array() )
    {
    	// Convert from database to variable friendly
        $title = array( 'W' => 'website', 'I' => 'internal', 'P' => 'phone', 'E' => 'energysmart' );

    	$total_lead = 0;

    	// If $args['lead_id'] is set, add following where string
    	$where_lead_ids = '';
    	if( isset( $args['lead_ids'] ) ){
    		$where_lead_ids = " AND lead_id IN({$args['lead_ids']})";
    		$total_lead = substr_count( $args['lead_ids'], ',' ) + 1;
    	}else{
            $this->set_lead_by_month(1, false);
    		$where_lead_ids = $this->get_where_lead_id();
    		$total_lead = $this->get_total_lead();
    	}

    	// By defaults, all sources is set to 0
        //$sources = array();
        $this->set_group_concat_max_len();
        $query = "SELECT value, count(0) as total, GROUP_CONCAT(lead_id) as lead_ids 
                FROM `{$this->table_rg_lead}_detail`
                WHERE form_id = 1 AND field_number = 57 
                {$where_lead_ids} 
                GROUP BY value";

        // Generate array format as Source => Total
        $sources = $this->db->get_results( $query );
        
        //p($sources);
        $matched = array();

        foreach( $sources as $index => $value ){
            $sources[$title[$value->value]] = intval( $value->total );
          	
            if(isset($args['matched']))
            $matched[$title[$value->value]] = $this->get_total_matched($value->lead_ids);
            unset( $sources[$index] );
        }
        
        foreach( $title as $k => $v ){
            if(!isset($sources[$v]))
            	$sources[$v] = 0;

            if(!isset($matched[$v]))
            	$matched[$v] = 0;
        }

        $sources['total'] = $total_lead;
        $sources['website'] = $sources['total'] - $sources['phone']
                               - $sources['internal'] - $sources['energysmart'];

        if(isset($args['matched'])){
        	$matched['total'] = $this->get_total_matched($args['lead_ids']);
        	$matched['website'] = $matched['total'] - $matched['phone']
                               - $matched['internal'] - $matched['energysmart'];
        }

        if(isset($args['income'])){
        	return $this->income($sources);
        }
        
        return compact('sources', 'matched');
    }

    /**
     * Shortcut to source_report without any parameter
     * @return array
     */
    public function general_source_report(){
    	return $this->source_report();
    }

    /**
     * Report for rejection field
     * @return array reason => lead_count
     */
    public function rejection_report( $lead_ids = '' )
    {
    	// We have to match title with variable friendly field
        $title = array(
        	'Any other reasonable grounds (describe below)' 			=> 'other',
        	'The lead is a duplicate lead'								=> 'duplicate',
        	'You have already received the same Lead' 					=> 'duplicate',
        	'The Prospect is outside your Elected Service Area' 		=> 'service_area',
        	'The Prospect is outside your Elected Service Area or' 		=> 'service_area',
        	'You are not able to establish contact'						=> 'no_contact',
        	'You are not able to establish contact with the Prospect' 	=> 'no_contact'	
        );

        if( $lead_ids === '' ){
            // Only get lead on form id = 36 // rejection form
            $this->set_lead_by_month( 36 );
            $where_lead_ids = $this->get_where_lead_id();
        }else{
            $where_lead_ids = " AND lead_id IN ({$lead_ids}) ";
        }

        //$total_rejection = RGFormsModel::get_lead_count( $form_id, null );

        $query = "SELECT value, count(0) as total
                FROM `{$this->table_rg_lead}_detail`
                WHERE form_id = 36 AND field_number = 3
                {$where_lead_ids}
                GROUP BY value";
        // Generate array format as Source => Total
        $rejections = $this->db->get_results( $query );

        $total_rejection = 0;
        foreach( $rejections as $index => $value ){
            $rejections[$title[$value->value]] += intval( $value->total );
            $total_rejection += $value->total;
            unset( $rejections[$index] );
        }

        $rejections['total'] = $total_rejection;

        return $rejections;
    }

    public function state_report()
    {
    	// Set all state will be available in report here
    	$report_states = array('act'    => 'Australian Capital Territory',
    						 	'nsw'   => 'New South Wales', 
    						 	'nt'    => 'Northern Territory', 
    						 	'qld'   => 'Queensland', 
    						 	'sa'    => 'South Australia', 
    						 	'tas'   => 'Tasmania', 
    						 	'wa'    => 'Western Australia', 
    						 	'vic'   => 'Victoria');

    	$report_state_key = '';
    	foreach( $report_states as $k => $v ){
    		$report_state_key .= "'{$k}',";
    	}

    	$report_state_key = rtrim( $report_state_key, ',' );

    	$states = array();
    	$states['others'] = 0;

    	//$this->set_lead_by_month(1);
    	$this->set_group_concat_max_len();

    	$query = "SELECT value, COUNT(0) as total, GROUP_CONCAT(lead_id) as lead_ids 
    				FROM `{$this->table_rg_lead}_detail` 
    				WHERE form_id = 1 AND field_number LIKE '%17.4%' 
    				AND value IN({$report_state_key}) 
    				{$this->get_where_lead_id()}
    				GROUP BY value";
    	
    	$states =  $this->db->get_results($query);
    	$state_total = 0;

    	// Print friendly output to report
    	$pie_chart_report = array();
    	$bar_report = array();
    	foreach($states as $key => $data){
    		$pie_chart_report[] = array($report_states[strtolower($data->value)], intval($data->total), '$# - ' . $data->total . ' leads');
    		$bar_report[$report_states[strtolower($data->value)]]['raw'] = $this->source_report(array('lead_ids' => $data->lead_ids, 'matched' => true));
    		$bar_report[$report_states[strtolower($data->value)]]['income'] = $this->income($bar_report[$report_states[strtolower($data->value)]]['raw']['matched']);
    	}
    		
    	// Recalculate income before output
    	foreach($pie_chart_report as $k => $v){
    		foreach($bar_report as $state => $source){
    			if($v[0] === $state){
    				$state_total += $source['income']['total'];
    				$pie_chart_report[$k][1] = $source['income']['total'];
    				$pie_chart_report[$k][2] = str_replace('#', $source['income']['total'], $pie_chart_report[$k][2]);
    			}
    		}	
    	}

    	// Sorting the bar report
    	$this->sort_flag = 'income.total';
    	uasort( $bar_report, array($this, 'sort'));

    	$bar_report_array_key = array_values($bar_report);
    	$bar_report_max = $bar_report_array_key[0]['income']['total'];

    	$i = 0;
    	foreach($bar_report as $k => $v){
    		
    		if($v['income']['total'] == 0){
    			unset($bar_report[$k]);
    			continue;
    		}

    		$i++;
    		$bar_report[$i . '_' . $k] = $v;
    		unset($bar_report[$k]);
    	}

    	// Sorting the pie chart report
    	$this->sort_flag = 1;
    	usort($pie_chart_report, array($this, 'sort'));

    	return compact('pie_chart_report', 'bar_report', 'state_total', 'bar_report_max');
    }


    public function company_report()
    {
    	// Select top 10 companies which have longest length in meta_value field 
	    $query = "SELECT * FROM `{$this->db->prefix}postmeta` 
					WHERE `meta_key` LIKE 'leads_count'
					ORDER BY length(meta_value) DESC";

    	$companies = $this->db->get_results( $query );

    	// This step to convert companies leads to post_id => leads
    	// where leads is separated by commas
    	$companies_leads = array();
    	foreach( $companies as $company ){
    		$companies_leads[$company->post_id] = '';
    		$meta_value = unserialize( $company->meta_value );

    		// If user get values by month
    		if( $this->where_date_created !== '' ){
    			$param_year_month = sprintf( '%02d', intval( $this->month ) ) . '-' . intval( $this->year );
    			if( array_key_exists( $param_year_month, $meta_value ) )
    				$companies_leads[$company->post_id] .= $meta_value[$param_year_month];
    		}else{
    			foreach( $meta_value as $month ){
    				$companies_leads[$company->post_id] .= ',' . $month;
    			}
    		}

    		// Remove empty items
    		if(empty($companies_leads[$company->post_id])){
    			unset($companies_leads[$company->post_id]);
    			continue;
    		}

    		$companies_leads[$company->post_id] = ltrim( $companies_leads[$company->post_id], ',' );
    	}

    	$company_bar_report = array();

    	$company_bar_report_max_income = 0;

    	foreach( $companies_leads as $company_id => $company_leads ){
    		$company_name = get_the_title($company_id);
    		$company_lead_not_rejected = $this->remove_rejected_lead_ids($company_leads, false);
    		//p($company_lead_not_rejected);
			$company_bar_report[$company_name]['sources'] = $this->source_report( array( 'lead_ids' => $company_lead_not_rejected, 'income' => true) ); 
    		$company_bar_report[$company_name]['id'] = $company_id;
    		//$company_bar_report_max_income += $company_bar_report[$company_name]['sources']['total'];
    	}

    	$this->sort_flag = 'sources.total';
    	uasort($company_bar_report, array($this, 'sort'));
    	
    	// Add prefix to keep json sort
    	$company_bar_report            = $this->add_prefix($company_bar_report);
    	$company_bar_report_num_key    = array_values($company_bar_report);
    	$company_bar_report_max_income = $company_bar_report_num_key[0]['sources']['total'];
    	//return $company_bar_report;
    	return compact('company_bar_report', 'company_bar_report_max_income');
    }

    public function single_company_report($id)
    {
        if( $id <= 0 ) return;

        $company_name = get_the_title( $id );

        $query = "
            SELECT * FROM `{$this->db->prefix}postmeta` 
            WHERE `meta_key` LIKE 'leads_count'
            AND `post_id` = {$id}
        ";

        $company = $this->db->get_row( $query );
        $meta_value = unserialize( $company->meta_value );
        $param_year_month = sprintf( '%02d', intval( $this->month ) ) . '-' . intval( $this->year );

        if( !array_key_exists( $param_year_month, $meta_value ) )
            return "This company currently have no lead";

        $company_leads = $meta_value[$param_year_month];
        $total_leads = substr_count($company_leads, ',') + 1;

        // Get company rejected leads report
        //
        $this->set_group_concat_max_len();

        $query = "
            SELECT GROUP_CONCAT(lead_id) as leads_id
            FROM `{$this->table_rg_lead}_detail` 
            WHERE `form_id` = 36 
            AND `field_number` = 2
            AND value IN($company_leads)
        ";

        $rejected_lead_ids = $this->db->get_var($query);

        $this->set_group_concat_max_len();
        $query = "
            SELECT GROUP_CONCAT(lead_id) as leads_id
            FROM `{$this->table_rg_lead}_detail` 
            WHERE `form_id` = 36 
            AND `field_number` = 14 
            AND value = '{$company_name}'
            AND lead_id IN({$rejected_lead_ids})
        ";

        // Keep the value for other report
        $rejected_lead_raw = $this->db->get_var($query);
        $real_leads_id = $this->get_rejection_lead_id( $rejected_lead_raw );

        $real_leads_id_array = array_flip(explode(',', $real_leads_id));
        $company_leads = array_flip(explode(',', $company_leads));
        
        $not_rejected_leads = array();
        foreach($company_leads as $key => $value){
            if(!array_key_exists($key, $real_leads_id_array))
                $not_rejected_leads[] = $key;
        }

        $not_rejected_leads = join(',', $not_rejected_leads);
        $rejection_report = $this->rejection_report($rejected_lead_raw);
        $company_bar_report = $this->source_report( array( 'lead_ids' => $not_rejected_leads, 'income' => true) );

        return compact('rejection_report', 'company_bar_report', 'total_leads');
    }

    public function get_rejected_lead_by_lead( $leads_id = '' )
    {
        $this->set_group_concat_max_len();

        $query = "
            SELECT GROUP_CONCAT(lead_id) as leads_id
            FROM `{$this->table_rg_lead}_detail` 
            WHERE `form_id` = 36 
            AND `field_number` = 2
            AND value IN($leads_id)
        ";

        $rejected_leads = $this->db->get_var( $query );

        return $rejected_leads;
    }

    public function company_rejection_report()
    {
    	// We have to retrieve lead_ids of form 36
    	$this->set_lead_by_month( 36 );
    	
    	//Todo: Review this line
    	$where_lead_ids = $this->get_where_lead_id();

    	$this->set_group_concat_max_len();

    	$query = "SELECT value, count(0) as total, GROUP_CONCAT(lead_id) as lead_ids
    				FROM `{$this->table_rg_lead}_detail` 
    				WHERE `form_id` = 36 
    				AND `field_number` = 14 
    				{$where_lead_ids}
    				GROUP BY value 
    				ORDER BY total DESC";

    	$companies = $this->db->get_results($query);
    	$rejection_bar_report = array();
    	$rejection_bar_report['total'] = 0;

    	$titles = '';
    	foreach( $companies as $key => $data ){
    		$titles .= "'{$data->value}',";
    		//$rejection_bar_report['total'] += $data->total;
    		// Current lead id is leads of form 36, we must convert to leads of form 1
    		$real_lead_ids = $this->get_rejection_lead_id( $data->lead_ids );
    		$rejection_bar_report['detail'][$data->value]['sources'] = $this->source_report( array( 'lead_ids' => $real_lead_ids, 'income' => true ) );
    		$rejection_bar_report['detail'][$data->value]['id'] = $data->value;
    	}
    	$titles = rtrim($titles, ',');

    	foreach( $rejection_bar_report['detail'] as $bar ){
    		$rejection_bar_report['total'] += $bar['sources']['total'];
    	}

    	// We have to get ID :-(
    	$query = "SELECT ID, post_title FROM {$this->db->prefix}posts WHERE post_type = 'company' AND post_title IN({$titles})";
    	$titles_ids = $this->db->get_results($query);
    	foreach($titles_ids as $k => $v){
    		$titles_ids[$v->post_title] = $v->ID;
    		unset($titles_ids[$k]);
    	}

    	$this->sort_flag = 'sources.total';
    	uasort( $rejection_bar_report['detail'], array($this, 'sort') );
    	$rejection_bar_report['detail'] = $this->add_prefix( $rejection_bar_report['detail'] );

    	// Add post id to this array
    	foreach($rejection_bar_report['detail'] as $company_name => $data){
    		$rejection_bar_report['detail'][$company_name]['id'] = $titles_ids[$data['id']];
    	}

    	$rejection_bar_report_array_key = array_values($rejection_bar_report['detail']);
    	$rejection_bar_report['max'] = $rejection_bar_report_array_key[0]['sources']['total'];

    	return $rejection_bar_report;
    }

    /**
     * Get all lead id of form 1 based on lead id of form 36
     * @param  string $lead_ids lead ids of form 36, separated by commas
     * @return string lead_ids of form 1
     */
    private function get_rejection_lead_id( $lead_ids = '' )
    {	
    	$where_lead_ids = '';
    	if($lead_ids !== '')
    		$where_lead_ids .= " AND lead_id IN ({$lead_ids})";

    	$this->set_group_concat_max_len();

    	return $this->db->get_var( "SELECT GROUP_CONCAT(value) 
    								FROM {$this->table_rg_lead}_detail 
    								WHERE form_id = 36 
    								AND field_number = 2 
    								{$where_lead_ids}" );
    }

	/**
	 * Calculate income by lead group
	 * @param  array  $leads lead
	 * @return array  Lead group and it income
	 */
	public function income( $leads = array() ){

		$setting_asq 			= $this->get_setting_asq();
		$setting_energysmart 	= $this->get_setting_energysmart();
		$approved = 1;

		if( empty( $leads ) || !is_array( $leads ) )
			return array();

		$incomes = array();

		$incomes['total'] = 0;

		foreach( $leads as $key => $value ){

			if($key === 'total') continue;

			$related_setting = ($key !== 'energysmart') ? $setting_asq : $setting_energysmart;

			$incomes[$key] = $value * $approved * $related_setting;
			$incomes['total'] += $incomes[$key];
		}
		return $incomes;
	}

	/**
	 * Set time based on query string, if is not set, use current time
	 * Also set $year, $month, $where_date_created
	 */
	private function set_time()
	{
		if( isset( $_GET['month'] ) && isset( $_GET['year'] ) 
		   && $_GET['month'] != '' && $_GET['year'] != '' ){
			$mm 	= trim( $_GET['month'] );
			$yyyy 	= trim( $_GET['year'] );
		}else{
			$mm 	= date('m');
			$yyyy	= date('Y');
		}
		
		$this->month = $mm;
		$this->year  = $yyyy;

		$this->where_date_created = " AND year(date_created) = {$yyyy} AND month(date_created) = {$mm}";
	}


	public function __toString(){
		return $this;
	}

	/**
	 * Todo: Remove it and use 7listings method
	 */
	public function get_setting($key, $default){
		$settings = get_option( '7listings' );
		
		if( isset( $settings[$key] ) )
			return $settings[$key];
		
		return $default;
	}

	public function get_setting_asq()
	{
		return $this->get_setting('solar_website_lead_value');
	}

	public function get_setting_energysmart()
	{
		return $this->get_setting('solar_es_lead_value');
	}

	public function sort($a, $b){
		$sort_flag = $this->sort_flag;

		if(array_get($a, $sort_flag) == array_get($b, $sort_flag)) return 0;
			return (array_get($a, $sort_flag) > array_get($b, $sort_flag)) ? -1 : 1;
	}

	public function add_prefix($array)
	{
		$i = 0;
		foreach($array as $k => $v){
			$i++;
			$array[sprintf( '%02d', $i ) . '_' . $k] = $v;
			unset($array[$k]);
		}
		return $array;
	}
}
