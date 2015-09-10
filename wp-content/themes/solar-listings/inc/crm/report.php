<?php
namespace ASQ\Crm;

class Report
{
	public function __construct()
	{
		add_action( 'wp', array( $this, 'setup_schedule' ) );

		add_action( 'report_daily_hook', array( $this, 'email' ) );
	}

	public function setup_schedule()
	{
		if ( ! wp_next_scheduled( 'report_daily_hook' ) )
			wp_schedule_event( time() + 3600 * 19, 'daily', 'report_daily_hook' );
	}

	public function email()
	{
		global $wpdb;

		$yesterday 			= new \DateTime( 'yesterday' );
		$yesterday_string 	= $yesterday->format( 'Y-m-d' );
		
		$search_criteria 	= array(
			'start_date' => $yesterday_string
		);
		
		$total_count 	= 0;
		$leads 			= \GFAPI::get_entries( 45, $search_criteria, null, array( 'offset' => 0, 'page_size' => 999 ), $total_count );

		$yesterday_leads 	= array();
		$today_leads 		= array();
		
		$total_call_yesterday 	= 0;
		$total_call_today 		= 0;

		$lead_statuses = Helper::get_outcomes();
		
		foreach ( $lead_statuses as $outcome )
		{
			$yesterday_leads[$outcome] = array();
			$today_leads[$outcome]	 = array();
		}

		foreach ( $leads as $lead )
		{
			if ( str_contains( $lead['date_created'], $yesterday_string ) )
			{
				$yesterday_leads[$lead[4]][] = $lead;
				if ( intval( $lead[11] ) > 0 )
					$total_call_yesterday += intval( $lead[11] );
			}
			else
			{
				$today_leads[$lead[4]][] = $lead;
				if ( intval( $lead[11] ) > 0 )
					$total_call_today += intval( $lead[11] );
			}
		}

		$first_day_of_this_month = date( 'Y-m-01' );

		$total_converted_criteria = array(
			'start_date' => $first_day_of_this_month,
			'field_filters' => array(
				array(
					'key' 	=> 4,
					'value' => 'Interested'
				)
			)
		);

		$total_converted = \GFAPI::count_entries( 45, $total_converted_criteria );
		ob_start();
		?>
			<p>There were <?php echo $total_call_today ?> of outgoing calls made today and <?php echo count ( $today_leads['Interested'] ); ?> were converted.</p>
			<p>Outcome of today's call drive:</p>
			<ul>
				<?php foreach ( $lead_statuses as $outcome ) : ?>
				<li><?php echo count( $today_leads[$outcome] ) . ' ' . $outcome ?></li>
				<?php endforeach; ?>
			</ul>

			<p>Compared to yesterday</p>
			<ul>
				<?php 
				foreach ( $lead_statuses as $outcome ) :
					
					$division = ( count( $yesterday_leads[$outcome] ) === 0 || count( $today_leads[$outcome] ) === 0 ) ? 1 : count( $yesterday_leads[$outcome] );

					$percent = ( count( $today_leads[$outcome] ) - count( $yesterday_leads[$outcome] ) ) / $division * 100;
					
					$percent = round( $percent, 2 );

					$sign = ( $percent >= 0 ) ? '+' : '';

					echo "<li>{$sign}" . $percent . "% {$outcome}</li>";

				endforeach; 
				?>
			</ul>

			<p>This month:</p>
			<p>Total converted = <?php echo $total_converted ?></p>
		<?php

		$email_content = ob_get_clean();

		$to 		= 'darryn@ludovico.com.au';
    	$date 		= date( 'Y-m-d' );
    	$subject 	= "ASQ CRM Daily Report - {$date}";

		$headers 	= array();
    	$headers[] 	= 'Cc: tan@fitwp.com';
    	
    	wp_mail( $to, $subject, $email_content, $headers );
	}
}

new Report;