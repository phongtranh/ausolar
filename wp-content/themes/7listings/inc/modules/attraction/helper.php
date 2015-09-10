<?php

/**
 * This class will hold all helper functions
 */
class Sl_Attraction_Helper
{
	/**
	 * @var string Post type
	 */
	public static $post_type = 'attraction';

	/**
	 * Show allocation select box for attraction
	 *
	 * @param int   $max
	 * @param array $resource
	 *
	 * @return string
	 */
	public static function format_allocation_select( $max, $resource )
	{
		$types = array(
			'adults'   => array( __( 'Adults', '7listings' ), 'adult' ),
			'children' => array( __( 'Children', '7listings' ), 'child' ),
			'seniors'  => array( __( 'Seniors', '7listings' ), 'senior' ),
			'families' => array( __( 'Families', '7listings' ), 'family' ),
			'infants'  => array( __( 'Infants', '7listings' ), 'infant' ),
		);

		$k        = 0;
		$html     = '';
		$not_free = self::get_resource_price( $resource );

		$lead_in_rate   = isset( $resource['lead_in_rate'] ) ? $resource['lead_in_rate'] : 'adult';
		$family_warning = '';
		foreach ( $types as $name => $type )
		{
			if ( $not_free && empty( $resource["price_{$type[1]}"] ) )
				continue;

			// Each family takes 4 allocations, other guest type takes 1 allocation
			$max_allocation = 'families' == $name ? floor( $max / 4 ) : $max;

			// If no allocation for families
			if ( ! $max_allocation )
			{
				// Add warning if lead in rate is family
				if ( 'family' == $lead_in_rate )
				{
					$family_warning = __( 'This attraction does not have enough availability for a family (4).<br>Please select a different day or attraction.', '7listings' );
				}
				continue;
			}

			$k ++;
			$html .= "
				<span class='passenger-type'>
					<select name='$name'>
						<option value='-1'>-</option>
			";
			for ( $i = 1; $i <= $max_allocation; $i ++ )
			{
				$html .= sprintf(
					'<option value="%s"%s>%s</option>',
					$i,
					selected( $lead_in_rate == $type[1] && 1 == $i, true, false ),
					$i
				);
			}
			$html .= "</select> {$type[0]}</span> ";
			if ( 3 == $k )
				$html .= '<br>';
		}

		// Add warning if not enough spots for family and lead in rate is family
		if ( $family_warning )
			$html .= "<div class='error error-family'>$family_warning</div>";

		return $html;
	}

	/**
	 * This function gets all dates are 'unbookable' for a resource
	 *
	 * @param int    $post_id     Listing ID in current blog, NOT the original listing
	 * @param array  $resource
	 * @param int    $index       Resource index
	 * @param string $date_format The format of dates to return
	 *
	 * @return array|string
	 */
	public static function get_unbookable_dates( $post_id = 0, $resource, $index = null, $date_format = 'd/m/Y' )
	{
		// Get all blogs which has this listing shared
		$all_blogs = sl_get_broadcasted_listings( $post_id );

		// Get booked dates in all blogs in the network
		$booked_dates = array();
		$types        = array(
			'adults',
			'children',
			'seniors',
			'families',
			'infants',
		);
		$now          = current_time( 'timestamp' );

		// Number of blogs
		$original_blog_id = $GLOBALS['blog_id'];
		foreach ( $all_blogs as $share_blog_id => $share_post_id )
		{
			// Only switch if shared blog is different than current blog
			if ( $share_blog_id != $original_blog_id )
				switch_to_blog( $share_blog_id );

			// Get all normal booking dates
			$bookings = get_posts( array(
				'post_type'      => 'booking',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'   => 'post_id',
						'value' => $share_post_id,
					),
					array(
						'key'   => 'resource',
						'value' => $resource['title'],
					),
					array(
						'key'   => 'paid',
						'value' => 1,
					),
				),
			) );
			foreach ( $bookings as $booking )
			{
				// Get total allocation from booking for all kinds of guests
				$day = get_post_meta( $booking->ID, 'day', true );

				// Ignore past bookings
				$time = strtotime( str_replace( '/', '-', $day ) );
				if ( $time < $now )
					continue;

				// Reformat date
				$day = date( $date_format, $time );

				$total = 0;
				foreach ( $types as $type )
				{
					$num = get_post_meta( $booking->ID, $type, true );
					if ( $num && - 1 != $num )
					{
						// Each family takes 4 allocations, other guest type takes 1 allocation
						$total += 'families' == $type ? 4 * $num : $num;
					}
				}

				if ( isset( $booked_dates[$day] ) )
					$booked_dates[$day] += $total;
				else
					$booked_dates[$day] = $total;
			}

			// Get all booking dates via cart
			$bookings = get_posts( array(
				'post_type'      => 'booking',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'bookings',
						'compare' => 'EXISTS',
					),
					array(
						'key'   => 'paid',
						'value' => 1,
					)
				),
			) );
			foreach ( $bookings as $booking )
			{
				// Get booking data
				$booking_data = get_post_meta( $booking->ID, 'bookings', true );

				foreach ( $booking_data as $booking_item )
				{
					// Check post ID & resource
					$data = $booking_item['data'];
					if ( $data['post_id'] != $post_id || $data['resource'] != $resource['title'] )
						continue;

					// Ignore past bookings
					$day  = $data['day'];
					$time = strtotime( str_replace( '/', '-', $day ) );
					if ( $time < $now )
						continue;

					// Reformat date
					$day = date( $date_format, $time );

					// Get allocation
					$total = 0;
					foreach ( $types as $type )
					{
						if ( isset( $data[$type] ) && - 1 != $data[$type] )
						{
							// Each family takes 4 allocations, other guest type takes 1 allocation
							$total += 'families' == $type ? 4 * absint( $data[$type] ) : absint( $data[$type] );
						}
					}

					if ( isset( $booked_dates[$day] ) )
						$booked_dates[$day] += $total;
					else
						$booked_dates[$day] = $total;
				}
			}

			// Only switch if shared blog is different than current blog
			if ( $share_blog_id != $original_blog_id )
				restore_current_blog();
		}

		// Remove dates which have allocation < the available allocation, e.g. still can book on those dates
		foreach ( $booked_dates as $date => $booked_allocations )
		{
			$allocation = self::get_allocation( $post_id, $resource, $index, $date );
			if ( $booked_allocations < $allocation )
				unset( $booked_dates[$date] );
		}

		// Built the return value in single array
		$untouchable = array_unique( array_filter( array_keys( $booked_dates ) ) );

		// Get schedule allocation = 0, meaning unbookable
		$allocations = get_post_meta( $post_id, 'allocations', true );
		if ( empty( $allocations[$index] ) )
			return $untouchable;

		$values = $allocations[$index];
		foreach ( $values as $value )
		{
			$value = array_merge( array(
				'from'       => '',
				'to'         => '',
				'allocation' => 0,
				'enable'     => 0,
			), $value );

			if ( ! $value['enable'] || $value['allocation'] )
				continue;

			$from = strtotime( str_replace( '/', '-', $value['from'] ) );
			$to   = strtotime( str_replace( '/', '-', $value['to'] ) );

			while ( $from <= $to )
			{
				$untouchable[] = date( $date_format, $from );
				$from += 86400;
			}
		}

		/**
		 * Using 'array_values' to make sure we return array with continuous numeric keys
		 * That will force 'json_encode' to return array instead of object
		 */
		$untouchable = array_values( array_unique( array_filter( $untouchable ) ) );

		return $untouchable;
	}

	/**
	 * Get max. number of allocation for attraction for a certain date
	 *
	 * @param int    $post_id
	 * @param string $resource_title
	 * @param string $date
	 *
	 * @return int
	 */
	public static function get_max_allocation( $post_id, $resource_title, $date )
	{
		$find = sl_find_resource( $post_id, $resource_title, self::$post_type );
		if ( false === $find )
			return 0;

		$resource = $find[1];

		// Total allocation
		$total = self::get_allocation( $post_id, $resource, $find[0], $date );

		// Get all blogs which has this listing shared
		$all_blogs = sl_get_broadcasted_listings( $post_id );

		// Get total booked allocations
		$booked_allocation = 0;
		foreach ( $all_blogs as $share_blog_id => $share_post_id )
		{
			$booked_allocation += Sl_Attraction_Helper::get_site_booked_allocations( $share_blog_id, $share_post_id, $resource, $date );
			$booked_allocation += Sl_Attraction_Helper::get_site_booked_allocations_cart( $share_blog_id, $share_post_id, $resource, $date );
		}

		$max = $total - $booked_allocation;

		if ( $max <= 0 )
			$max = 0;

		return $max;
	}

	/**
	 * Get number of booked allocations for a site in the network
	 *
	 * @param int|bool $blog_id If false: get booked allocation for current (main) site, else get for a specific site
	 * @param int      $post_id Listing ID in the $blog_id, NOT the original listing
	 * @param array    $resource
	 * @param string   $date
	 *
	 * @return int
	 */
	public static function get_site_booked_allocations( $blog_id = false, $post_id, $resource, $date )
	{
		if ( $blog_id && is_multisite() )
			switch_to_blog( $blog_id );

		$total = 0;

		// Get booked allocations
		$bookings = get_posts( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => 'post_id',
					'value' => $post_id,
				),
				array(
					'key'   => 'resource',
					'value' => $resource['title'],
				),
				array(
					'key'   => 'day',
					'value' => $date,
				),
				array(
					'key'   => 'paid',
					'value' => 1,
				)
			),
		) );

		if ( empty( $bookings ) )
		{
			if ( $blog_id && is_multisite() )
				restore_current_blog();

			return $total;
		}

		// Loop into bookings, get number of passengers
		$types = array(
			'adults',
			'children',
			'seniors',
			'families',
			'infants',
		);
		foreach ( $bookings as $booking )
		{
			foreach ( $types as $type )
			{
				$num = get_post_meta( $booking->ID, $type, true );
				if ( $num && - 1 != $num )
				{
					// Each family takes 4 allocations, other guest type takes 1 allocation
					$total += 'families' == $type ? 4 * $num : $num;
				}
			}
		}

		if ( $blog_id && is_multisite() )
			restore_current_blog();

		return $total;
	}

	/**
	 * Get number of booked allocations (via Card) for a site in the network
	 * Cart bookings will store booking date of each attraction and resource in meta key "bookings"
	 * Which is an array of booking data, similar to normal booking data
	 *
	 * @param int|bool $blog_id If false: get booked allocation for current (main) site, else get for a specific site
	 * @param int      $post_id
	 * @param array    $resource
	 * @param string   $date
	 *
	 * @return int
	 */
	public static function get_site_booked_allocations_cart( $blog_id = false, $post_id, $resource, $date )
	{
		if ( $blog_id && is_multisite() )
			switch_to_blog( $blog_id );

		$total = 0;

		// Get all paid cart bookings
		$bookings = get_posts( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'bookings',
					'compare' => 'EXISTS',
				),
				array(
					'key'   => 'paid',
					'value' => 1,
				)
			),
		) );

		if ( empty( $bookings ) )
		{
			if ( $blog_id && is_multisite() )
				restore_current_blog();

			return $total;
		}

		// Loop into bookings, get number of passengers
		$types = array(
			'adults',
			'children',
			'seniors',
			'families',
			'infants',
		);
		foreach ( $bookings as $booking )
		{
			// Get booking data
			$booking_data = get_post_meta( $booking->ID, 'bookings', true );

			foreach ( $booking_data as $booking_item )
			{
				// Check post ID, date and resource
				$data = $booking_item['data'];
				if (
					$data['post_id'] != $post_id ||
					$data['day'] != $date ||
					$data['resource'] != $resource['title']
				)
				{
					continue;
				}

				// Get allocation
				foreach ( $types as $type )
				{
					if ( isset( $data[$type] ) && - 1 != $data[$type] )
					{
						// Each family takes 4 allocations, other guest type takes 1 allocation
						$total += 'families' == $type ? 4 * absint( $data[$type] ) : absint( $data[$type] );
					}
				}
			}
		}

		if ( $blog_id && is_multisite() )
			restore_current_blog();

		return $total;
	}

	/**
	 * Get number of allocation for a booking resource
	 * This will also deal with scheduled allocations
	 *
	 * @param int    $post_id  Post ID
	 * @param array  $resource Resource information
	 * @param int    $index    Resource index
	 * @param string $date
	 *
	 * @return int
	 */
	public static function get_allocation( $post_id = 0, $resource, $index, $date = null )
	{
		$allocations = get_post_meta( $post_id, 'allocations', true );
		$num         = $resource['allocation'];

		// Calculate by seasonal schedules
		if ( empty( $allocations[$index] ) || empty( $date ) )
			return $num;

		$values = $allocations[$index];
		$date   = strtotime( str_replace( '/', '-', $date ) );

		foreach ( $values as $value )
		{
			$value = array_merge( array(
				'from'       => '',
				'to'         => '',
				'allocation' => 0,
				'enable'     => 0,
			), $value );

			$from = strtotime( str_replace( '/', '-', $value['from'] ) );
			$to   = strtotime( str_replace( '/', '-', $value['to'] ) );

			if ( $value['enable'] && $date >= $from && $date <= $to )
				$num = $value['allocation'];
		}

		return $num;
	}

	/**
	 * Send booking notification to user and admin
	 *
	 * @param int    $booking_post_id
	 * @param null   $item
	 * @param string $custom_email Send booking detail to custom email
	 */
	public static function send_booking_emails( $booking_post_id, $item = null, $custom_email = '' )
	{
		$post_id = sl_booking_meta( 'post_id', $booking_post_id );
		$post    = get_post( $post_id );

		$resource_title = sl_booking_meta( 'resource', $booking_post_id );
		$find           = sl_find_resource( $post_id, $resource_title, $post->post_type );

		if ( false === $find )
			return;
		$index    = $find[0];
		$resource = $find[1];

		// Get depart time
		$date        = sl_booking_meta( 'day', $booking_post_id );
		$depart_time = sl_booking_meta( 'depart_time', $booking_post_id );
		$arrive_time = sl_booking_meta( 'arrival_time', $booking_post_id );

		// Get passenger list, and subtotal costs for passengers' tickets
		$num_guests      = array();
		$subtotal_guests = 0;
		$types           = array(
			'adults'   => __( 'Adults', '7listings' ),
			'children' => __( 'Children', '7listings' ),
			'seniors'  => __( 'Seniors', '7listings' ),
			'families' => __( 'Families', '7listings' ),
			'infants'  => __( 'Infants', '7listings' ),
		);
		$price_types     = array(
			'adults'   => 'adult',
			'children' => 'child',
			'seniors'  => 'senior',
			'families' => 'family',
			'infants'  => 'infant',
		);
		$prices          = Sl_Attraction_Helper::get_prices( $post_id, $index, $date );

		foreach ( $types as $type => $label )
		{
			$num = sl_booking_meta( $type, $booking_post_id );
			if ( empty( $num ) || - 1 == $num )
				continue;

			$num_guests[] = "{$num} {$label}";
			$price_type   = $price_types[$type];
			$price        = isset( $prices["price_{$price_type}"] ) ? $prices["price_{$price_type}"] : 0;

			$subtotal_guests += $num * $price;
		}

		// Upsells
		$upsells          = array();
		$subtotal_upsells = 0;
		$upsells_array    = sl_booking_meta( 'upsells', $booking_post_id );
		if ( is_array( $upsells_array ) && ! empty( $upsells_array ) )
		{
			$no_guests = count( $num_guests );
			foreach ( $upsells_array as $upsell )
			{
				if ( ! isset( $upsell['name'] ) || ! isset( $upsell['num'] ) )
					continue;

				$upsells[] = "{$upsell['name']}: {$upsell['num']}";

				if ( isset( $resource['upsell_items'] ) && is_array( $resource['upsell_items'] ) )
				{
					$k = array_search( $upsell['name'], $resource['upsell_items'] );
					if ( ! empty( $resource['upsell_prices'][$k] ) )
					{
						$price = $resource['upsell_prices'][$k];
						if ( sl_setting( 'attraction_multiplier' ) && ! empty( $resource['upsell_multipliers'][$k] ) )
							$price *= $no_guests;
						$subtotal_upsells += $upsell['num'] * $price;
					}
				}
			}
		}

		// Guests info
		$guests_info = '<table width="100%" border="0" cellspacing="0" cellpadding="6">';
		$guests      = sl_booking_meta( 'guests', $booking_post_id );
		$k           = 0;
		foreach ( $guests as $guest )
		{
			$guests_info .= sprintf( sl_email_template_part( 'guest_info' ), $k + 1, $guest['first'], $guest['last'] );

			if ( $guest['email'] )
				$guests_info .= sprintf( sl_email_template_part( 'guest_email' ), $guest['email'] );

			if ( $guest['phone'] )
				$guests_info .= sprintf( sl_email_template_part( 'guest_phone' ), $guest['phone'] );

			$k ++;
		}
		$guests_info .= '</table>';

		$resource_photo = '';
		if ( ! empty( $resource['photos'] ) )
			$resource_photo = sl_resource_photo( $resource['photos'], 'sl_pano_small' );
		elseif ( has_post_thumbnail( $post_id ) )
			$resource_photo = get_the_post_thumbnail( $post_id, 'sl_pano_small' );

		if ( $booking_message = sl_booking_meta( 'booking_message', $booking_post_id ) )
			$booking_message = '<strong>' . __( 'Booking Message', '7listings' ) . '</strong><br>' . $booking_message;

		if ( $payment_policy = get_post_meta( $post_id, 'paymentpol', true ) )
			$payment_policy = '<strong>' . __( 'Payment Policy', '7listings' ) . '</strong><br>' . $payment_policy;

		if ( $cancellation_policy = get_post_meta( $post_id, 'cancellpol', true ) )
			$cancellation_policy = '<strong>' . __( 'Cancellation Policy', '7listings' ) . '</strong><br>' . $cancellation_policy;

		if ( $terms_conditions = get_post_meta( $post_id, 'terms', true ) )
			$terms_conditions = '<strong>' . __( 'Terms And Conditions', '7listings' ) . '</strong><br>' . $terms_conditions;

		$replacements = array(
			'[title]'               => $post->post_title,
			'[resource]'            => $resource_title,
			'[resource_photo]'      => $resource_photo,
			'[date]'                => $date,
			'[depart_time]'         => $depart_time,
			'[arrival_time]'        => $arrive_time,
			'[passengers]'          => implode( ' - ', $num_guests ),
			'[upsells]'             => implode( ' - ', $upsells ),
			'[passengers_info]'     => $guests_info,
			'[total]'               => sl_booking_meta( 'amount', $booking_post_id ),
			'[first_name]'          => $guests[0]['first'],
			'[booking_message]'     => $booking_message,
			'[payment_policy]'      => $payment_policy,
			'[cancellation_policy]' => $cancellation_policy,
			'[terms_conditions]'    => $terms_conditions,
			'[attraction_url]'            => get_permalink( $post_id ),
			'[booking_id]'          => sl_booking_meta( 'booking_id', $booking_post_id ),
			'[subtotal_guests]'     => $subtotal_guests,
			'[subtotal_upsells]'    => $subtotal_upsells,
			'[message]'             => sl_booking_meta( 'customer_message', $booking_post_id ),
		);

		if ( $custom_email )
		{
			// Send email to admin, reply directly to customer
			$to      = $custom_email;
			$subject = sl_email_replace( sl_setting( 'emails_booking_attraction_admin_subject' ), $replacements );
			$body    = sl_email_content( '', 'booking-attraction-admin', $replacements );
			wp_mail( $to, $subject, $body, "Reply-To: {$guests[0]['email']}" );
		}
		else
		{
			// Send email to admin, reply directly to customer
			$to      = sl_email_admin_email( 'emails_booking_attraction_admin_email' );
			$subject = sl_email_replace( sl_setting( 'emails_booking_attraction_admin_subject' ), $replacements );
			$body    = sl_email_content( '', 'booking-attraction-admin', $replacements );
			wp_mail( $to, $subject, $body, "Reply-To: {$guests[0]['email']}" );

			// Email to Passenger
			$to      = $guests[0]['email'];
			$subject = sl_email_replace( sl_setting( 'emails_booking_attraction_guess_subject' ), $replacements );
			$body    = sl_email_content( 'emails_booking_attraction_guess_message', 'booking-attraction', $replacements );
			wp_mail( $to, $subject, $body );
		}

		do_action( 'sl_send_booking_emails', $booking_post_id, $post, $resource, $replacements );
		do_action( self::$post_type . '_send_booking_emails', $booking_post_id, $post, $resource, $replacements );
	}

	/**
	 * Get resource price
	 *
	 * @param array $resource
	 *
	 * @return mixed
	 */
	public static function get_resource_price( $resource )
	{
		$type = isset( $resource['lead_in_rate'] ) ? $resource['lead_in_rate'] : 'adult';
		if ( ! empty( $resource["price_$type"] ) )
			return $resource["price_$type"];

		$price_types = array( 'adult', 'child', 'senior', 'family', 'infant' );

		$prices = array();
		foreach ( $price_types as $type )
		{
			if ( ! empty( $resource["price_$type"] ) )
				$prices[] = $resource["price_$type"];
		}

		return empty( $prices ) ? false : min( $prices );
	}

	/**
	 * Check if resource has multiple prices?
	 *
	 * @param array $resource
	 *
	 * @return bool
	 */
	static function is_multiple_price( $resource )
	{
		$price_types = array( 'adult', 'child', 'senior', 'family', 'infant' );

		$count = 0;
		foreach ( $price_types as $type )
		{
			if ( ! empty( $resource["price_{$type}"] ) )
				$count ++;
		}

		return 1 < $count;
	}

	/**
	 * Get prices
	 *
	 * @param int        $post_id     Post ID
	 * @param int|string $resource_id Resource ID or title
	 * @param string     $date        Date in format dd/mm/yyyy
	 *
	 * @return array
	 */
	public static function get_prices( $post_id, $resource_id, $date )
	{
		$prices    = array();
		$resources = get_post_meta( $post_id, sl_meta_key( 'booking', self::$post_type ), true );

		if ( empty( $resources ) )
			return $prices;

		// Check if resource exist
		// And get its index, stored back to $resource_id
		$resource = null;
		if ( is_string( $resource_id ) )
		{
			$find = sl_find_resource( $post_id, $resource_id, self::$post_type );
			if ( ! $find )
				return $prices;

			$resource_id = $find[0];
			$resource    = $find[1];
		}
		elseif ( is_numeric( $resource_id ) )
		{
			if ( empty( $resources[$resource_id] ) )
				return $prices;

			$resource = $resources[$resource_id];
		}

		$date = strtotime( str_replace( '/', '-', $date ) );

		// Get original prices
		$price_types = array(
			'adult',
			'child',
			'senior',
			'family',
			'infant',
		);
		foreach ( $price_types as $type )
		{
			$prices["price_$type"] = $resource["price_$type"];
		}

		// Get upsell prices
		$prices['upsell_prices'] = isset( $resource['upsell_prices'] ) ? $resource['upsell_prices'] : array();

		// Update permanent price
		$schedule = get_post_meta( $post_id, 'schedule', true );
		if ( ! empty( $schedule[$resource_id] ) )
		{
			// Update permanent price for people
			foreach ( $schedule[$resource_id] as $type => $value )
			{
				// Get seasonal prices for upsells
				if ( 'upsells' == $type )
				{
					foreach ( $value as $upsell_index => $upsell_schedule )
					{
						sl_permanent_price( $prices['upsell_prices'][$upsell_index], $date, $upsell_schedule );
					}
					continue;
				}

				// Update permanent price for people
				sl_permanent_price( $prices["price_$type"], $date, $value );
			}
		}

		// Calculate by seasonal prices
		$season = get_post_meta( $post_id, 'season', true );
		if ( empty( $season[$resource_id] ) )
			return $prices;

		foreach ( $season[$resource_id] as $type => $values )
		{
			// Get seasonal prices for upsells
			if ( 'upsells' == $type )
			{
				foreach ( $values as $upsell_index => $upsell_schedules )
				{
					sl_seasonal_price( $prices['upsell_prices'][$upsell_index], $date, $upsell_schedules );
				}
				continue;
			}

			// Get seasonal prices for people
			sl_seasonal_price( $prices["price_$type"], $date, $values );
		}

		return $prices;
	}

	/**
	 * Display departure and arrive times for a booking resource
	 * Will display only:
	 * - Daily departures/arrives
	 * - Weekday departures/arrives
	 * Won't display custom departures/arrives
	 *
	 * @param array  $resource Booking resource information
	 * @param string $title    Section title
	 * @param bool   $display  Display times or return the output
	 *
	 * @return string
	 */
	public static function booking_times( $resource, $title = '', $display = true )
	{
		$times = '';

		$departure_type = isset( $resource['departure_type'] ) ? $resource['departure_type'] : '';

		// Daily departures/arrives
		if ( 'daily' == $departure_type )
		{
			if ( ! empty( $resource['depart'] ) && ! empty( $resource['arrive'] ) )
			{
				foreach ( $resource['depart'] as $k => $depart )
				{
					$times .= sprintf(
						'<div class="departure daily">
							<label class="day">%s</label>
							<time class="depart time">%s</time>
							<time class="arrive time">%s</time>
						</div>',
						__( 'Daily', '7listings' ),
						Sl_Helper::time_format( $depart ),
						Sl_Helper::time_format( $resource['arrive'][$k] )
					);
				}
			}
		}
		// Weekday departures/arrives
		elseif ( 'specific' == $departure_type )
		{
			$days = array(
				'monday'    => __( 'Monday', '7listings' ),
				'tuesday'   => __( 'Tuesday', '7listings' ),
				'wednesday' => __( 'Wednesday', '7listings' ),
				'thursday'  => __( 'Thursday', '7listings' ),
				'friday'    => __( 'Friday', '7listings' ),
				'saturday'  => __( 'Saturday', '7listings' ),
				'sunday'    => __( 'Sunday', '7listings' ),
			);
			foreach ( $days as $day => $label )
			{
				$key     = substr( $day, 0, 3 );
				$departs = isset( $resource["{$key}_depart"] ) ? $resource["{$key}_depart"] : array();
				$arrives = isset( $resource["{$key}_arrive"] ) ? $resource["{$key}_arrive"] : array();

				if ( empty( $departs ) || empty( $arrives ) )
					continue;

				foreach ( $departs as $k => $depart )
				{
					$times .= sprintf(
						'<div class="departure %s">
							<label class="day">%s</label>
							<time class="depart time">%s</time>
							<time class="arrive time">%s</time>
						</div>',
						$day,
						$label,
						Sl_Helper::time_format( $depart ),
						Sl_Helper::time_format( $arrives[$k] )
					);
				}
			}
		}

		if ( $times )
			$times = "<section class='departures'>{$title}{$times}</section>";

		/**
		 * Allow to change booking times (maybe for other departure type)
		 *
		 * @param string $times          Output HTML for booking times
		 * @param array  $resource       Resource info
		 * @param string $departure_type Departure type
		 * @param string $title          Section title
		 *
		 * @return string
		 */
		$times = apply_filters( 'attraction_booking_times', $times, $resource, $departure_type, $title );

		if ( $display )
			echo $times;

		return $times;
	}

	/**
	 * Get attraction departure types
	 * Has filter to allow developer to add more types
	 *
	 * @return array
	 */
	public static function get_departure_types()
	{
		$types = array(
			'daily'    => __( 'Daily', '7listings' ),
			'specific' => __( 'Specific Days', '7listings' ),
			'custom'   => __( 'Custom/Charter', '7listings' ),
		);

		return apply_filters( 'sl_attraction_departure_types', $types );
	}
}
