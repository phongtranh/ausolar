<?php

class Next_Match
{
	public function __construct()
	{
		add_action( 'wp', array( $this, 'setup_schedule' ) );

		add_action( 'next_match_daily_hook', array( $this, 'run' ) );
	}

	/**
	 * This module run daily. Right after deploy
	 */
	public function setup_schedule()
	{
		if ( ! wp_next_scheduled( 'next_match_daily_hook' ) )
			wp_schedule_event( time(), 'daily', 'next_match_daily_hook' );
	}

	/**
	 * Main functions
	 */
	public function run()
	{
		$today = new \DateTime();

		$next_match_offset = intval( sl_setting( 'solar_lead_next_match_offset' ) );

		$yesterday = $today->sub( new \DateInterval( 'P' . $next_match_offset . 'D' ) );

		$paging = array( 'offset' => 0, 'page_size' => 500 );

		$search_criteria = array(
			'start_date'    => $yesterday->format( 'Y-m-d H:i:s' )
		);

		$total_count = 0;
		$leads = GFAPI::get_entries( 1, $search_criteria, array( 'key' => '57', 'direction' => 'ASC' ), $paging, $total_count );
		$form = GFAPI::get_form( 1 );
		if ( $total_count > 0 ):

			$available_to_match = array();

			foreach ( $leads as $lead ) :
				// Only process leads already processed one time
				$processed = gform_get_meta( $lead['id'], 'processed' );

				if ( $processed < 1 || $processed > 7 )
					continue;

				$matched = gform_get_meta( $lead['id'], 'companies' );
				$matched = explode( ',', $matched );
				if ( count( $matched ) > 0 && count( $matched ) < 4 )
					$available_to_match[ $lead['id'] ] = array(
						'lead'    => $lead,
						'matched' => $matched
					);

			endforeach;

			if ( ! empty ( $available_to_match ) ):

				$dir = wp_upload_dir();

				$file = $dir['path'] . "/match-log.php";

				foreach ( $available_to_match as $lead_id => $data ) :

					Solar_Postcodes::after_submission( $data['lead'], $form, $data['matched'] );

					$matches = gform_get_meta( $lead_id, 'companies' );

					// Write Next Match Log After Complete
					$log = array(
						'created_at'    => date( 'Y-m-d H:i:s' ),
						'lead_id'       => $lead_id,
						'before'        => implode( ',', $data['matched'] ),
						'after'         => $matches
					);
					file_put_contents( $file, serialize( $log ) . '###', FILE_APPEND );
				endforeach;
			endif;
		endif;
	}
}

new Next_Match;