<?php

/**
 * This class will hold all things for rental ajax requests
 */
class Sl_Rental_Ajax extends Sl_Core_Ajax
{
	/**
	 * Ajax callback for getting max date (checkout) for current day
	 *
	 * @return void
	 */
	public function get_max_date()
	{
		$resource_title = $_POST['resource'];
		$post_id        = $_POST['post_id'];
		$date           = array_shift( explode( ' ', $_POST['date'] . ' ' ) );
		$date           = strtotime( str_replace( '/', '-', $date ) );

		$find = sl_find_resource( $post_id, $resource_title, $this->post_type );
		if ( empty( $find ) )
			die;

		$unbookable = Sl_Rental_Helper::get_unbookable_dates( $post_id, $find[1], $find[0] );
		foreach ( $unbookable as $k => $book_date )
		{
			$unbookable[$k] = str_replace( '/', '-', $book_date );
		}
		$unbookable = array_map( 'strtotime', $unbookable );

		$sortable_dates = array_filter( $unbookable, create_function( '$element', 'return $element >= ' . $date . ';' ) );
		$now            = time();
		$sortable_dates = array_filter( $sortable_dates, create_function( '$element', 'return $element >= ' . $now . ';' ) );
		sort( $sortable_dates, SORT_NUMERIC );

		if ( count( $sortable_dates ) > 0 )
			echo date( 'd/m/Y', $sortable_dates[0] );

		die;
	}

	/**
	 * Ajax callback for getting total price
	 *
	 * @return void
	 */
	public function get_total_price()
	{
		$post_id        = $_POST['post_id'];
		$resource_title = $_POST['resource'];
		$from           = strtotime( $_POST['from'] );
		$to             = strtotime( $_POST['to'] );

		// Store number of days for each price
		$prices = array();
		$total  = 0;

		// Check if resource exist
		// And get its index ($index)
		$find = sl_find_resource( $post_id, $resource_title, $this->post_type );
		if ( false == $find )
			die( json_encode( array( 'total' => $total, 'prices' => $prices ) ) );

		$index    = $find[0];
		$resource = $find[1];

		$schedule = get_post_meta( $post_id, 'schedule', true );
		$season   = get_post_meta( $post_id, 'season', true );

		$days = ceil( ( $to - $from ) / 86400 );
		$days = $days > 7 ? 7 : $days;

		// Just a trick that make the calculation for 1 day if book at the same time
		if ( $from == $to )
			$to = $from + 1;

		// Calculate price for each day
		while ( $from < $to )
		{
			// Get correct price for the day
			$price = 0;
			if ( ! empty( $resource['price'] ) && is_array( $resource['price'] ) )
			{
				for ( $i = $days; $i > 0; $i -- )
				{
					if ( ! empty( $resource['price'][$i] ) )
					{
						$price = $resource['price'][$i];
						break;
					}
				}
			}

			// Permanent price
			if ( ! empty( $schedule[$index] ) )
			{
				$value = array_merge( array(
					'date'   => '',
					'price'  => '',
					'enable' => 0,
				), $schedule[$index] );

				$value['date'] = strtotime( str_replace( '/', '-', $value['date'] ) );

				if ( $value['enable'] && $value['date'] >= $value['date'] && '' !== $value['price'] )
					$price = $value['price'];
			}

			// Calculate by seasonal prices
			if ( ! empty( $season[$index] ) )
			{
				$values = $season[$index];

				foreach ( $values as $value )
				{
					$value = array_merge( array(
						'from'       => '',
						'to'         => '',
						'fixed'      => '',
						'percentage' => '',
						'source'     => '',
						'enable'     => 0,
					), $value );

					$value['from'] = strtotime( str_replace( '/', '-', $value['from'] ) );
					$value['to']   = strtotime( str_replace( '/', '-', $value['to'] ) );

					if ( ! $value['enable'] || $from < $value['from'] || $from > $value['to'] )
						continue;

					$found = false;

					if ( 'percentage' == $value['source'] && '' !== $value['percentage'] )
					{
						$new = (float) $value['percentage'] / 100;
						if ( false !== strpos( $value['percentage'], '-' ) || false !== strpos( $value['percentage'], '+' ) )
							$new += 1;
						$price *= $new;

						$found = true;
					}
					elseif ( 'fixed' == $value['source'] && '' !== $value['fixed'] )
					{
						$new = (float) $value['fixed'];
						if ( false !== strpos( $value['fixed'], '-' ) || false !== strpos( $value['fixed'], '+' ) )
							$price += $new;

						$found = true;
					}

					if ( $found )
						break;
				}
			}

			$total += $price;

			$price = (string) $price;
			if ( ! isset( $prices[$price] ) )
				$prices[$price] = 1;
			else
				$prices[$price] ++;

			$from += 86400;
		}

		die( json_encode( array( 'total' => $total, 'prices' => $prices ) ) );
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
		$this->add_booking_upsells( $data, $resource );
		return $data;
	}
}

new Sl_Rental_Ajax( 'rental', array( 'get_max_date', 'get_total_price', 'add_booking' ) );
