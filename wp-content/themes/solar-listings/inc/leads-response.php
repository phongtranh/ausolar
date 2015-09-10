<?php
/// @@deprecated This was no longer needed
class Solar_Leads_Response
{
	/**
	 * Response for all lead page
	 * @return json $response
	 * @author Tan Nguyen <tannt.com@gmail.com>
	 */
	public static function all($args = array())
	{
		$lead = new Solar_Leads_Query;
		
		$response = array();
		

		$response['setting_asq'] = $lead->get_setting_asq();
		$response['setting_energysmart'] = $lead->get_setting_energysmart();
		$response['count'] 		= $lead->source_report(array('matched' => true));
		$response['income'] 	= $lead->income($response['count']['matched']);	
		$response['states']		= $lead->state_report();
		$response['companies']  = $lead->company_report();

		$response['rejection'] 	= $lead->rejection_report();
		$response['companies_rejections'] = $lead->company_rejection_report();

		$lead->set_lead_by_month(1, false);
		$response['total_leads'] = $lead->get_total_lead();

		if(isset($args['return']) && $args['return'] === true)
			return $response;

		// Tell app this is json response
		header('Content-Type: application/json');
		echo json_encode($response);
		exit(0);
	}

	/**
	 * Response for single page
	 * @return json $response
	 */
	public static function single( $company_id )
	{
		$lead = new Solar_Leads_Query;

		$response = $lead->single_company_report( $company_id );
		$response['setting_asq'] = $lead->get_setting_asq();
		$response['setting_energysmart'] = $lead->get_setting_energysmart();

		return $response;
	}
}