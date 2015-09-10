<?php

/**
 * This class will hold all helper functions
 */
class Sl_Cart_Helper
{
	/**
	 * Send booking notification to user and admin
	 *
	 * @param int    $booking_post_id
	 *
	 * @param string $type_action
	 *
	 * @param string $custom_email Send booking data to custom email
	 *
	 * @return void
	 */
	static function send_booking_emails( $booking_post_id, $type_action = 'all', $custom_email = '' )
	{
		$data                = sl_booking_meta( 'bookings', $booking_post_id );
		$booking_items       = array();
		$booking_items_admin = array();
		$types               = array(
			'adults'   => __( 'Adults', '7listings' ),
			'children' => __( 'Children', '7listings' ),
			'seniors'  => __( 'Seniors', '7listings' ),
			'families' => __( 'Families', '7listings' ),
			'infants'  => __( 'Infants', '7listings' ),
		);
		$price_types         = array(
			'adults'   => 'adult',
			'children' => 'child',
			'seniors'  => 'senior',
			'families' => 'family',
			'infants'  => 'infant',
		);

		$guest_email = '';
		$total       = 0;
		$first_name  = '';

		foreach ( $data as $item )
		{
			$total += sl_booking_meta( 'amount', $booking_post_id, $item );

			$post_id        = $item['post'];
			$post           = get_post( $post_id );
			$resources      = get_post_meta( $post_id, sl_meta_key( 'booking', $post->post_type ), true );
			$resource       = $resources[$item['resource']];
			$resource_title = $resource['title'];

			// Guests info
			$guests_info = '<table width="100%" border="0" cellspacing="0" cellpadding="6">';
			$guests      = sl_booking_meta( 'guests', $booking_post_id, $item );
			$k           = 1;

			$guest_first_name = '';

			foreach ( $guests as $guest )
			{
				if ( empty( $guest['first'] ) && empty( $guest['last'] ) )
					continue;

				if ( ! empty( $guest['first'] ) )
				{
					if ( ! $first_name )
						$first_name = $guest['first'];
					if ( ! $guest_first_name )
						$guest_first_name = $guest['first'];
				}

				$guests_info .= sprintf( sl_email_template_part( 'guest_info' ), $k, $guest['first'], $guest['last'] );

				if ( $guest['email'] )
					$guests_info .= sprintf( sl_email_template_part( 'guest_email' ), $guest['email'] );

				if ( $guest['phone'] )
					$guests_info .= sprintf( sl_email_template_part( 'guest_phone' ), $guest['phone'] );

				$k ++;

				if ( ! $guest_email && $guest['email'] )
					$guest_email = $guest['email'];
			}
			$guests_info .= '</table>';

			$resource_photo = '';
			if ( ! empty( $resource['photos'] ) )
				$resource_photo = sl_resource_photo( $resource['photos'], 'sl_pano_small' );
			elseif ( has_post_thumbnail( $post_id ) )
				$resource_photo = get_the_post_thumbnail( $post_id, 'sl_pano_small' );

			if ( $booking_message = sl_booking_meta( 'booking_message', $booking_post_id, $item ) )
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
				'[total]'               => sl_booking_meta( 'amount', $booking_post_id, $item ),
				'[first_name]'          => $guest_first_name,
				'[booking_message]'     => $booking_message,
				'[payment_policy]'      => $payment_policy,
				'[cancellation_policy]' => $cancellation_policy,
				'[terms_conditions]'    => $terms_conditions,
				'[booking_id]'          => sl_booking_meta( 'booking_id', $booking_post_id ),
				'[message]'             => sl_booking_meta( 'customer_message', $booking_post_id ),
			);

			if ( 'tour' == $post->post_type )
			{
				// Get depart time
				$date        = sl_booking_meta( 'day', $booking_post_id, $item );
				$depart_time = sl_booking_meta( 'depart_time', $booking_post_id, $item );
				$arrive_time = sl_booking_meta( 'arrival_time', $booking_post_id, $item );

				// Get passenger list, and subtotal costs for passengers' tickets
				$num_guests      = array();
				$subtotal_guests = 0;
				$prices          = Sl_Tour_Helper::get_prices( $post_id, $resource_title, $date );

				foreach ( $types as $type => $label )
				{
					$num = sl_booking_meta( $type, $booking_post_id, $item );
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
				$upsells_array    = sl_booking_meta( 'upsells', $booking_post_id, $item );
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
								if ( sl_setting( 'tour_multiplier' ) && ! empty( $resource['upsell_multipliers'][$k] ) )
									$price *= $no_guests;
								$subtotal_upsells += $upsell['num'] * $price;
							}
						}
					}
				}

				$replacements = array_merge( $replacements, array(
					'[date]'             => $date,
					'[depart_time]'      => $depart_time,
					'[arrival_time]'     => $arrive_time,
					'[passengers]'       => implode( ' - ', $num_guests ),
					'[upsells]'          => implode( ' - ', $upsells ),
					'[passengers_info]'  => $guests_info,
					'[tour_url]'         => get_permalink( $post_id ),
					'[subtotal_guests]'  => $subtotal_guests,
					'[subtotal_upsells]' => $subtotal_upsells,
				) );

				$booking_items_admin[] = sl_email_content( '', 'booking-cart-tour', $replacements );
				$booking_items[]       = sl_email_content( '', 'booking-cart-tour', $replacements );
			}
			elseif ( 'accommodation' == $post->post_type )
			{
				$replacements = array_merge( $replacements, array(
					'[checkin]'           => sl_booking_meta( 'checkin', $booking_post_id, $item ),
					'[checkout]'          => sl_booking_meta( 'checkout', $booking_post_id, $item ),
					'[guests]'            => count( $guests ),
					'[guests_info]'       => $guests_info,
					'[accommodation_url]' => get_permalink( $post_id ),
				) );

				$booking_items_admin[] = sl_email_content( '', 'booking-cart-accommodation', $replacements );
				$booking_items[]       = sl_email_content( '', 'booking-cart-accommodation', $replacements );
			}
			elseif ( 'rental' == $post->post_type )
			{
				// Upsells
				$upsells          = array();
				$subtotal_upsells = 0;
				$upsells_array    = sl_booking_meta( 'upsells', $booking_post_id, $item );
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
								$subtotal_upsells += $upsell['num'] * $price;
							}
						}
					}
				}
				$replacements = array_merge( $replacements, array(
					'[checkin]'          => sl_booking_meta( 'checkin', $booking_post_id, $item ),
					'[checkout]'         => sl_booking_meta( 'checkout', $booking_post_id, $item ),
					'[upsells]'          => implode( ' - ', $upsells ),
					'[rental_url]'       => get_permalink( $post_id ),
					'[guests]'           => count( $guests ),
					'[guests_info]'      => $guests_info,
					'[subtotal_upsells]' => $subtotal_upsells,
				) );

				$booking_items_admin[] = sl_email_content( '', 'booking-cart-rental', $replacements );
				$booking_items[]       = sl_email_content( '', 'booking-cart-rental', $replacements );
			}
		}

		$replacements = array(
			'[booking_items]'       => implode( '', $booking_items ),
			'[booking_items_admin]' => implode( '', $booking_items_admin ),
			'[total]'               => $total,
			'[booking_id]'          => sl_booking_meta( 'booking_id', $booking_post_id ),
			'[message]'             => sl_booking_meta( 'customer_message', $booking_post_id ),
			'[first_name]'          => $first_name,
		);

		$prefix       = 'emails_booking_cart_';
		$tpl_customer = 'booking-cart';
		$tpl_admin    = 'booking-cart-admin';
		switch ( $type_action )
		{
			case 'customer':
				$to      = $guest_email;
				$subject = sl_email_replace( sl_setting( $prefix . 'guess_subject' ), $replacements );
				$body    = sl_email_content( $prefix . 'guess_message', $tpl_customer, $replacements );
				wp_mail( $to, $subject, $body );
				break;
			case 'admin':
				$to      = sl_email_admin_email( $prefix . 'admin_email' );
				$subject = sl_email_replace( sl_setting( $prefix . 'admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guest_email}" );
				break;
			case 'custom':
				$to      = $custom_email;
				$subject = sl_email_replace( sl_setting( $prefix . 'admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guest_email}" );
				break;
			default: // Default send emails to both customer and admin
				// Email to Passenger
				$to      = $guest_email;
				$subject = sl_email_replace( sl_setting( $prefix . 'guess_subject' ), $replacements );
				$body    = sl_email_content( $prefix . 'guess_message', $tpl_customer, $replacements );
				wp_mail( $to, $subject, $body );

				// Send email to admin
				$to      = sl_email_admin_email( $prefix . 'admin_email' );
				$subject = sl_email_replace( sl_setting( $prefix . 'admin_subject' ), $replacements );
				$body    = sl_email_content( '', $tpl_admin, $replacements );
				wp_mail( $to, $subject, $body, "Reply-To: {$guest_email}" );
				break;
		}
	}
}
