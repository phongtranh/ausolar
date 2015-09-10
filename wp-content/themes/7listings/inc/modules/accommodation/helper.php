<?php

/**
 * This class will hold all helper functions
 */
class Sl_Accommodation_Helper
{
	/**
	 * Post type
	 *
	 * @var string
	 */
	static $post_type = 'accommodation';

	/**
	 * This function gets all dates are 'unbookable' for an accommodation resource
	 *
	 * @param int    $post_id
	 * @param array  $resource
	 * @param int    $index       Resource index
	 * @param string $date_format The format of dates to return
	 *
	 * @return array|string
	 */

	static function get_unbookable_dates( $post_id = 0, $resource, $index = null, $date_format = 'd/m/Y' )
	{
		$booked_dates = array();
		$bookings     = get_posts( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				array(
					'key'   => 'post_id',
					'value' => $post_id,
				),
				array(
					'key'   => 'resource',
					'value' => $resource['title'],
				),
				array(
					'key'   => 'paid',
					'value' => 1,
				),
			)
		) );

		foreach ( $bookings as $booking )
		{
			$checkin  = get_post_meta( $booking->ID, 'checkin', true );
			$checkin  = str_replace( '/', '-', array_shift( explode( ' ', $checkin . ' ' ) ) );
			$checkin  = strtotime( $checkin );
			$checkout = get_post_meta( $booking->ID, 'checkout', true );
			$checkout = str_replace( '/', '-', array_shift( explode( ' ', $checkout . ' ' ) ) );
			$checkout = strtotime( $checkout );

			while ( $checkin <= $checkout )
			{
				$booked_dates[] = date( $date_format, $checkin );
				$checkin += 86400;
			}
		}

		$booked_dates = array_count_values( $booked_dates );

		if ( ! is_array( $booked_dates ) || empty( $booked_dates ) )
			$booked_dates = array();

		// Store all booked dates what have count equal or lager than the room quantity
		foreach ( $booked_dates as $k => $v )
		{
			$booked_date = strtotime( str_replace( '/', '-', $k ) );
			$allocation  = self::get_allocation( $post_id, $resource, $index, $booked_date );
			if ( $v >= $allocation )
				unset( $booked_dates[$k] );
		}

		// Built the return value in single array
		$untouchable = array_keys( $booked_dates );

		$allocations = get_post_meta( $post_id, 'allocations', true );
		if ( empty( $allocations[$index] ) )
			return $untouchable;

		// Get schedule allocation = 0, meaning unbookable
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
	 * Get number of allocation for a booking resource
	 *
	 * @param int    $post_id
	 * @param array  $resource
	 * @param int    $index Resource index
	 * @param string $date
	 *
	 * @return int
	 */
	static function get_allocation( $post_id = 0, $resource, $index, $date = null )
	{
		$allocations = get_post_meta( $post_id, 'allocations', true );
		$num         = $resource['allocation'];

		// Calculate by seasonal prices
		if ( empty( $allocations[$index] ) || empty( $date ) )
			return $num;

		$values = $allocations[$index];

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

			if ( empty( $value['enable'] ) || $date < $from || $date > $to )
				continue;

			$num = $value['allocation'];
		}

		return $num;
	}

	/**
	 * Send booking notification to user and admin
	 *
	 * @param int    $booking_post_id
	 * @param string $type_action
	 * @param string $custom_email Send booking data to custom email
	 *
	 * @return void
	 */
	static function send_booking_emails( $booking_post_id, $type_action = 'all', $custom_email = '' )
	{
		$post_id = sl_booking_meta( 'post_id', $booking_post_id );
		$post    = get_post( $post_id );

		$resource_title = sl_booking_meta( 'resource', $booking_post_id );
		$find           = sl_find_resource( $post_id, $resource_title, $post->post_type );

		if ( false === $find )
			return;
		$resource = $find[1];

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
			'[checkin]'             => sl_booking_meta( 'checkin', $booking_post_id ),
			'[checkout]'            => sl_booking_meta( 'checkout', $booking_post_id ),
			'[guests]'              => count( $guests ),
			'[guests_info]'         => $guests_info,
			'[total]'               => sl_booking_meta( 'amount', $booking_post_id ),
			'[first_name]'          => $guests[0]['first'],
			'[booking_message]'     => $booking_message,
			'[payment_policy]'      => $payment_policy,
			'[cancellation_policy]' => $cancellation_policy,
			'[terms_conditions]'    => $terms_conditions,
			'[accommodation_url]'   => get_permalink( $post_id ),
			'[booking_id]'          => sl_booking_meta( 'booking_id', $booking_post_id ),
			'[message]'             => sl_booking_meta( 'customer_message', $booking_post_id ),
		);

		$prefix       = "emails_booking_{$post->post_type}_";
		$tpl_customer = "booking-{$post->post_type}";
		$tpl_admin    = "booking-{$post->post_type}-admin";
		switch ( $type_action )
		{
			case 'customer':
				$to      = $guests[0]['email'];
				$subject = sl_email_replace( sl_setting( $prefix . 'guess_subject' ), $replacements );
				$body    = sl_email_content( $prefix . 'guess_message', $tpl_customer, $replacements );
				wp_mail( $to, $subject, $body );
				break;
			case 'admin':
				$to      = sl_email_admin_email( $prefix . 'admin_email' );
				$subject = sl_email_replace( sl_setting( $prefix . 'admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guests[0]['email']}" );
				break;
			case 'custom';
				$to      = $custom_email;
				$subject = sl_email_replace( sl_setting( 'emails_booking_acco_admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guests[0]['email']}" );
				break;
			default:
				// Email to Passenger
				$to      = $guests[0]['email'];
				$subject = sl_email_replace( sl_setting( $prefix . 'guess_subject' ), $replacements );
				$body    = sl_email_content( $prefix . 'guess_message', $tpl_customer, $replacements );
				wp_mail( $to, $subject, $body );

				// Send email to admin, reply directly to customer
				$to      = sl_email_admin_email( $prefix . 'admin_email' );
				$subject = sl_email_replace( sl_setting( $prefix . 'admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guests[0]['email']}" );
				break;
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
	static function get_resource_price( $resource )
	{
		return isset( $resource['price'] ) ? $resource['price'] : false;
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
		return false;
	}
}
