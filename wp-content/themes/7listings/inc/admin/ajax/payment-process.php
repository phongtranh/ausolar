<?php
// Define URLs for payment gateway
define( 'EWAY_PAYMENT_LIVE_REAL_TIME', 'https://www.eway.com.au/gateway/xmlpayment.asp' );
define( 'EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE', 'https://www.eway.com.au/gateway/xmltest/testpage.asp' );

/**
 * Get information from booking page via ajax
 *
 * @param string $type        Booking type
 * @param bool   $create_post Create post or not
 *
 * @return array|string Array of information or string of error
 */
function sl_booking_get_data( $type = 'tour', $create_post = true )
{
	global $settings;

	$data = empty( $_POST['data'] ) ? array() : $_POST['data'];
	$data = wp_parse_args( $data );

	/**
	 * Verify form from proper submit
	 * We have 2 inputs to check:
	 * - amount for total amount of the booking
	 * - verify = ( amount * 3 - 10 ) + ',' + nonce
	 */
	$amount = isset( $data['amount'] ) ? $data['amount'] : 0;
	$amount = $amount * 3 - 10;
	if ( isset( $data['verify'] ) && isset( $_POST['_wpnonce'] ) && ( (string) $amount . ',' . $_POST['_wpnonce'] != $data['verify'] ) )
	{
		return __( 'Invalid form submit.', '7listings' );
	}

	unset( $data['verify'] );

	if ( empty( $_POST['post_id'] ) )
	{
		return __( 'Invalid post ID', '7listings' );
	}

	if ( empty( $_POST['resource'] ) )
	{
		return __( 'Invalid booking resource', '7listings' );
	}

	$data['type']     = $type;
	$data['post_id']  = (int) $_POST['post_id'];
	$data['resource'] = $_POST['resource'];
	if ( isset( $data['card_number'] ) )
	{
		$data['card_type'] = sl_get_cc_type( $data['card_number'] );
	}
	$data['paid'] = 0;

	// Counter
	if ( $create_post )
	{
		$counter = intval( sl_setting( 'counter' ) );
		$counter ++;
		$data['booking_id']  = $counter;
		$settings['counter'] = $counter;
		update_option( THEME_SETTINGS, $settings );
	}

	// Get booking resource
	$find = sl_find_resource( $data['post_id'], $data['resource'], $type );
	if ( false == $find )
	{
		return __( 'ERROR: No booking resource. Please try again.', '7listings' );
	}
	$resource                = $find[1];
	$resource['resource_id'] = $find[0];

	// Resource type
	$resource_type         = wp_get_post_terms( $data['post_id'], sl_meta_key( 'tax_type', $type ), array( 'fields' => 'names' ) );
	$data['resource_type'] = empty( $resource_type ) ? '' : array_pop( $resource_type );

	/**
	 * Set payment gateway if it's missed
	 * If there are more than 1 payment gateways are activated, user can select one of them and payment gateway will
	 * be sent via $_POST (which will be stored in $data)
	 * If there is only 1 payment gateway activated, then user just click "Book" button and no payment gateway is
	 * sent via $_POST (and won't be stored in $data). In this case:
	 * - We have to check the settings and get activate payment gateway
	 * - And save payment gateway into booking data
	 */
	if ( ! isset( $data['payment_gateway'] ) )
	{
		if ( sl_setting( 'paypal' ) )
		{
			$data['payment_gateway'] = 'paypal';
		}
		elseif ( sl_setting( 'eway' ) )
		{
			$data['payment_gateway'] = 'eway';
		}
	}

	// User IP Address
	$data['ip_address'] = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

	// Guest information
	$data['guests'] = Sl_Helper::compact_guests( $data );
	unset( $data['first'], $data['last'], $data['email'], $data['phone'] );

	// Don't save these fields
	$no_save_fields = array(
		'submit',
		'agree',
		'card_number',
		'card_expiry_month',
		'card_expiry_year',
		'card_cvn',
	);
	foreach ( $data as $k => $v )
	{
		if ( in_array( $k, $no_save_fields ) )
		{
			unset( $data[$k] );
		}
	}

	$data = apply_filters( 'sl_booking_get_data', $data, $type, $resource );

	if ( $create_post )
	{
		// Save booking data
		$booking_post = array(
			'post_type'   => 'booking',
			'post_title'  => sprintf( __( 'Booking #%d', '7listings' ), $counter ),
			'post_status' => 'publish',
		);
		$post_id      = wp_insert_post( $booking_post );
		if ( ! $post_id )
		{
			return __( 'ERROR: Cannot save booking data. Please try again.', '7listings' );
		}

		foreach ( $data as $k => $v )
		{
			update_post_meta( $post_id, $k, $v );
		}
	}
	else
	{
		$post_id = null;
	}

	return array( $data, $resource, $post_id );
}

/**
 * Process payment for booking
 *
 * @param $post
 * @param $resource
 * @param $data
 * @param $booking_post_id
 */
function sl_booking_process_payment( $post, $resource, $data, $booking_post_id )
{
	$thank_url = Sl_Helper::get_url_by_template( 'templates/thank-you-booking.php' );

	/**
	 * If this is a free booking resource, don't redirect user to payment gateways
	 * But redirect them to thank you page, update booking status to 'paid' and send booking email
	 */
	if ( empty( $data['amount'] ) || ! intval( $data['amount'] ) )
	{
		printf( '
			<form id="checkout_form" class="modal hide fade" action="%s" method="get">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>%s</h3>
				</div>
				<div class="modal-body">
					%s
				</div>
				<div class="modal-footer">
					<input type="submit" class="button primary" value="%s">
				</div>
			</form>',
			$thank_url,
			__( 'Thank you for your booking.', '7listings' ),
			__( 'Please wait while we redirect you to the confirmation page.', '7listings' ),
			__( 'Continue', '7listings' )
		);

		update_post_meta( $booking_post_id, 'paid', 1 );
		$class = 'Sl_' . ucfirst( $post->post_type ) . '_Helper';
		call_user_func( array( $class, 'send_booking_emails' ), $booking_post_id );
		die;
	}

	$slug         = sanitize_title( $resource['title'] );
	$booking_url  = home_url( "/book/{$post->post_name}/{$slug}/" );
	$booking_id   = get_post_meta( $booking_post_id, 'booking_id', true );
	$invoice_code = sl_setting( 'invoice_code' ) ? sl_setting( 'invoice_code' ) : get_option( 'blogname' );

	// Save info in session so we can access later
	if ( '' == session_id() )
	{
		session_start();
	}
	$name = $data['guests'][0]['first'];
	$name .= empty( $data['guests'][0]['last'] ) ? '' : ' ' . $data['guests'][0]['last'];
	$_SESSION['name']    = $name;
	$_SESSION['email']   = empty( $data['guests'][0]['email'] ) ? '' : $data['guests'][0]['email'];
	$_SESSION['invoice'] = "{$invoice_code} #{$booking_id}";

	if ( 'tour' == $data['type'] )
	{
		$start = $data['day'] . ' ' . $data['depart_time'];
	}
	else
	{
		$start = isset( $data['checkin'] ) ? $data['checkin'] : '';
	}

	$payment_gateway = '';
	switch ( $data['payment_gateway'] )
	{
		case 'paypal':
			$payment_gateway = 'paypal';
			break;
		case 'eway':
			if ( sl_setting( 'eway_shared' ) )
			{
				$payment_gateway = 'eway_shared';
			}
			if ( sl_setting( 'eway_hosted' ) )
			{
				$payment_gateway = 'eway_hosted';
			}
	}

	// Checkout using Paypal
	if ( 'paypal' == $payment_gateway )
	{
		echo Sl_Payment::paypal_form( 'checkout_form', 'booking_paypal', array(
			'item_name'   => $resource['title'] . ' ' . $start,
			'item_number' => "$invoice_code #$booking_id",
			'amount'      => $data['amount'],
			'return'      => $thank_url,
			'custom'      => $booking_post_id,
		) );
		die;
	}

	// eWAY shared payment
	if ( 'eway_shared' == $payment_gateway )
	{
		$amount = number_format( $data['amount'], 2, '.', '' );
		echo Sl_Payment::eway_form( 'checkout_form', 'booking_eway', array(
			'amount'              => $amount,
			'reference'           => "$invoice_code #$booking_id",
			'resource'            => $data['resource'],
			'cancel_url'          => $booking_url,
			'customer_first_name' => $data['guests'][0]['first'],
			'customer_last_name'  => empty( $data['guests'][0]['last'] ) ? '' : $data['guests'][0]['last'],
			'customer_email'      => empty( $data['guests'][0]['email'] ) ? '' : $data['guests'][0]['email'],
			'customer_phone'      => empty( $data['guests'][0]['phone'] ) ? '' : $data['guests'][0]['phone'],
			'booking_post_id'     => $booking_post_id,
		) );
		die;
	}

	// eWAY hosted payment
	if ( 'eway_hosted' == $payment_gateway )
	{
		if ( empty( $data['card_holders_name'] ) || empty( $data['card_number'] ) || empty( $data['card_expiry_month'] ) || empty( $data['card_expiry_year'] ) )
		{
			die( __( 'Please enter credit card details.', '7listings' ) );
		}

		echo sl_booking_eway_hosted( array(
			'type'                => get_post_type( $post ),
			'amount'              => $data['amount'],
			'resource'            => $data['resource'],
			'card_holders_name'   => $data['card_holders_name'],
			'card_number'         => $data['card_number'],
			'card_expiry_month'   => $data['card_expiry_month'],
			'card_expiry_year'    => $data['card_expiry_year'],
			'customer_first_name' => $data['guests'][0]['first'],
			'customer_last_name'  => empty( $data['guests'][0]['last'] ) ? '' : $data['guests'][0]['last'],
			'customer_email'      => empty( $data['guests'][0]['email'] ) ? '' : $data['guests'][0]['email'],
			'transaction_number'  => $booking_id,
			'reference'           => "$invoice_code #$booking_id",
			'cvn'                 => $data['card_cvn'],
		) );

		exit;
	}

	die( __( 'Error payment settings. Please contact admin to fix this.', '7listings' ) );
}

/**
 * Process eWAY hosted payment
 *
 * @param array $args
 *
 * @return string
 */
function sl_booking_eway_hosted( $args = array() )
{
	if ( ! sl_setting( 'eway_id' ) )
	{
		return __( 'No customer information. Please contact admin to fix this.', '7listings' );
	}

	$args = wp_parse_args( $args, array(
		'type'                => 'tour',
		'id'                  => sl_setting( 'eway_id' ),
		'amount'              => '',
		'reference'           => '',
		'resource'            => '',
		'card_holders_name'   => '',
		'card_number'         => '',
		'card_expiry_month'   => '',
		'card_expiry_year'    => '',
		'customer_first_name' => '',
		'customer_last_name'  => '',
		'customer_email'      => '',
		'customer_address'    => '',
		'customer_postcode'   => '',
		'transaction_number'  => '',
		'option1'             => '',
		'option2'             => '',
		'option3'             => '',
		'cvn'                 => '',
	) );

	// Amount sent in 'cents'
	$amount = (float) $args['amount'] * 100;
	$amount = (int) $amount;

	$post_args = array(
		'ewayCustomerID'                 => $args['id'],
		'ewayTotalAmount'                => $amount,
		'ewayCustomerInvoiceRef'         => $args['reference'],
		'ewayCustomerInvoiceDescription' => $args['resource'],
		'ewayCardHoldersName'            => $args['card_holders_name'],
		'ewayCardNumber'                 => $args['card_number'],
		'ewayCardExpiryMonth'            => $args['card_expiry_month'],
		'ewayCardExpiryYear'             => $args['card_expiry_year'],
		'ewayCustomerFirstName'          => $args['customer_first_name'],
		'ewayCustomerLastName'           => $args['customer_last_name'],
		'ewayCustomerEmail'              => $args['customer_email'],
		'ewayCustomerAddress'            => $args['customer_address'],
		'ewayCustomerPostcode'           => $args['customer_postcode'],
		'ewayTrxnNumber'                 => $args['transaction_number'],
		'ewayOption1'                    => $args['option1'],
		'ewayOption2'                    => $args['option2'],
		'ewayOption3'                    => $args['option3'],
		'ewayCVN'                        => $args['cvn'],
	);

	$post_args = array_map( 'rawurlencode', $post_args );

	$xml = '<ewaygateway>';
	foreach ( $post_args as $key => $value )
	{
		$xml .= "<{$key}>{$value}</{$key}>";
	}
	$xml .= '</ewaygateway>';

	// Note: Use real time gateway
	$eway_url = sl_setting( 'eway_sandbox' ) ? EWAY_PAYMENT_LIVE_REAL_TIME_TESTING_MODE : EWAY_PAYMENT_LIVE_REAL_TIME;

	$request = wp_remote_post( $eway_url, array(
		'body' => $xml,
	) );
	if ( is_wp_error( $request ) )
	{
		return 'Cannot connect to eWAY. Please try again';
	}

	$response = wp_remote_retrieve_body( $request );

	// return $response );

	$fields = eway_parse_response( $response );

	$status = isset( $fields['EWAYTRXNSTATUS'] ) ? strtolower( $fields['EWAYTRXNSTATUS'] ) : '';

	// Transaction error
	if ( 'false' == $status )
	{
		if ( empty( $fields['EWAYTRXNERROR'] ) )
		{
			return __( 'Cannot complete payment processing. Please try again.', '7listings' );
		}
		if ( false !== strpos( $fields['EWAYTRXNERROR'], '58' ) )
		{
			return sl_setting( 'eway_sandbox' ) ? __( 'Testing Credit Card does not work. The details have gone through the system, hit the bank and your connection to eway is working correctly.', '7listings' ) : __( 'Invalid Credit Card', '7listings' );
		}
		return sprintf( __( 'Transaction error: %s', '7listings' ), $fields['EWAYTRXNERROR'] );
	}

	// Payment successfully sent to gateway
	elseif ( 'true' == $status )
	{
		if ( empty( $fields['EWAYTRXNREFERENCE'] ) )
		{
			return __( 'Unknown transaction number. Please try again.', '7listings' );
		}

		$reference = $fields['EWAYTRXNREFERENCE'];

		update_post_meta( $reference, 'paid', 1 );
		call_user_func( array( 'Sl_' . $args['type'] . '_Helper', 'send_booking_emails' ), $reference );
		return Sl_Helper::get_url_by_template( 'templates/thank-you-booking.php' );
	}

	// Invalid response received from server.
	else
	{
		return __( 'Error: An invalid response was received from the payment gateway.', '7listings' );
	}
}

/**
 * Get credit card type
 *
 * @param string $number
 *
 * @return string
 */
function sl_get_cc_type( $number )
{
	$result = 'unknown';

	if ( preg_match( '/^5[1-5]/', $number ) )
	{
		$result = 'mastercard';
	}
	elseif ( preg_match( '/^4/', $number ) )
	{
		$result = 'visa';
	}
	elseif ( preg_match( '/^3[47]/', $number ) )
	{
		$result = 'amex';
	}

	return $result;
}
