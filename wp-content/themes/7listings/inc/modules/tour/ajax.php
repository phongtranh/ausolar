<?php

/**
 * This class will hold all things for rental ajax requests
 */
class Sl_Tour_Ajax extends SL_Core_Ajax
{
	/**
	 * Ajax callback for getting available allocation for booking resource on a day
	 * If no available allocations: show error message
	 * Else: show <select> tag
	 *
	 * @return void
	 */
	public function get_allocation()
	{
		$resource_title = $_POST['resource'];
		$post_id        = $_POST['post_id'];
		$date           = $_POST['date'];

		$max = Sl_Tour_Helper::get_max_allocation( $post_id, $resource_title, $date );
		if ( $max <= 0 )
			die( __( 'No space available', '7listings' ) );

		$find = sl_find_resource( $post_id, $resource_title, $this->post_type );
		if ( false === $find )
			die( __( 'No space available', '7listings' ) );

		$resource = $find[1];
		wp_send_json_success( array(
			'allocation' => $max,
			'html'       => SL_Tour_Helper::format_allocation_select( $max, $resource ),
		) );
	}

	/**
	 * Get scheduled prices for a tour
	 *
	 * @return void
	 */
	public function get_scheduled_price()
	{
		$resource_title = $_POST['resource'];
		$post_id        = $_POST['post_id'];
		$date           = $_POST['date'];

		$prices = Sl_Tour_Helper::get_prices( $post_id, $resource_title, $date );
		if ( empty( $prices ) )
			wp_send_json_error( __( 'Unable to get prices for selected date. Please try again.', '7listings' ) );

		wp_send_json_success( $prices );
	}

	/**
	 * Add custom booking data for current module
	 *
	 * @param array $data     Booking data, sent via $_POST
	 * @param array $resource Resource parameters
	 *
	 * @return array
	 */
	public function add_booking_data( $data, $resource )
	{
		// Get depart time
		$depart_time = '';
		$arrive_time = '';
		if ( ! empty( $data['custom_depart'] ) )
		{
			$depart_time = $data['custom_depart'];
		}
		else
		{
			// If daily depart
			if ( isset( $data['daily_depart'] ) )
			{
				$k           = $data['daily_depart'];
				$depart_time = $resource['depart'][$k];
				$arrive_time = $resource['arrive'][$k];
			}
			// If one-day depart
			else
			{
				$week_days = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
				foreach ( $week_days as $week_day )
				{
					if ( isset( $data["{$week_day}_depart"] ) )
					{
						$k           = $data["{$week_day}_depart"];
						$depart_time = $resource["{$week_day}_depart"][$k];
						$arrive_time = $resource["{$week_day}_arrive"][$k];

						break;
					}
				}
			}
		}
		$data['depart_time']  = $depart_time;
		$data['arrival_time'] = $arrive_time;

		// Upsells
		$this->add_booking_upsells( $data, $resource );

		return $data;
	}
}

new Sl_Tour_Ajax( 'tour', array( 'get_allocation', 'add_booking', 'get_scheduled_price' ) );
