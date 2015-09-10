<?php

class Sl_Payment
{
	/**
	 * Get Paypal form
	 *
	 * @param  string $id     HTML form ID
	 * @param  string $action Action for IPN listener
	 * @param  array  $args   Form parameters
	 *
	 * @return string
	 * @since  4.12.1
	 */
	public static function paypal_form( $id, $action, $args )
	{
		// http://go.fitwp.com/3
		$inputs = wp_parse_args( $args, array(
			'cmd'           => '_xclick',
			'business'      => sl_setting( 'paypal_email' ),
			'no_shipping'   => 1,
			'rm'            => 2,
			'no_note'       => 1,

			// Item
			'item_name'     => '',
			'item_number'   => '',
			'amount'        => '',
			'currency_code' => sl_setting( 'currency' ),
			'custom'        => '',

			// Callback URLs
			'return'        => '',
			'cancel_return' => '',
			'notify_url'    => add_query_arg( 'sl_payment_listener', $action, trailingslashit( home_url( 'index.php' ) ) ),
		) );
		$inputs = array_filter( $inputs );
		$hidden = '';
		foreach ( $inputs as $k => $v )
		{
			$hidden .= sprintf( '<input type="hidden" name="%s" value="%s">', $k, $v );
		}

		$output = sprintf( '
			<form id="%s" class="modal hide fade" action="%s" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>%s</h3>
				</div>
				<div class="modal-body">
					<div class="paypal logo">PayPal</div>
					%s
					<div class="alert">%s</div>
				</div>
				<div class="modal-footer">
					<input type="submit" class="button primary" value="%s">
				</div>
			</form>',
			esc_attr( $id ),
			esc_url( self::paypal_get_url() ),
			__( 'Redirecting to PayPal', '7listings' ),
			$hidden,
			__( 'After your transaction,<br>please <strong>come back!</strong><br><br>Closing the window may cause problems<br>with receiving your <strong>confirmation email</strong>.', '7listings' ),
			esc_attr__( 'Continue', '7listings' )
		);

		return $output;
	}

	/**
	 * Get eWay payment form
	 * Note that the procedure to create eWay form is different from Paypal form. We need to send request
	 * to eWay to get access payment code, then build form with parameters sent from eWay
	 *
	 * @param  string $id     HTML form ID
	 * @param  string $action Action for payment listener
	 * @param  array  $args   Form parameters
	 *
	 * @return string
	 * @since  5.7.1
	 */
	public static function eway_form( $id, $action, $args )
	{
		$args = wp_parse_args( $args, array(
			'id'                  => sl_setting( 'eway_id' ),
			'username'            => sl_setting( 'eway_username' ),
			'amount'              => '',
			'currency'            => sl_setting( 'currency' ),
			'reference'           => '',
			'resource'            => '',
			'cancel_url'          => '',
			'return_url'          => '',
			'company'             => get_option( 'blogname' ),
			'customer_first_name' => '',
			'customer_last_name'  => '',
			'customer_email'      => '',
			'customer_phone'      => '',
			'booking_post_id'     => '',
		) );

		/**
		 * Add booking post ID to URL to get it faster later in notification listener callback
		 * @see Sl_Booking_Payment::process_eway_notification()
		 */
		$args['return_url'] = add_query_arg( array(
			'sl_payment_listener' => $action,
			'sl_booking_post_id'  => $args['booking_post_id'],
		), trailingslashit( home_url( 'index.php' ) ) );

		$url_args = array_map( 'rawurlencode', array(
			'CustomerID'         => $args['id'],
			'UserName'           => $args['username'],
			'Amount'             => number_format( $args['amount'], 2, '.', '' ),
			'Currency'           => $args['currency'],
			'MerchantReference'  => $args['reference'],
			'InvoiceDescription' => $args['resource'],
			'CancelURL'          => $args['cancel_url'],
			'ReturnUrl'          => $args['return_url'],
			'CompanyName'        => $args['company'],
			'CustomerFirstName'  => $args['customer_first_name'],
			'CustomerLastName'   => $args['customer_last_name'],
			'CustomerEmail'      => $args['customer_email'],
			'CustomerPhone'      => $args['customer_phone'],
		) );
		$eway_url = add_query_arg( $url_args, 'https://au.ewaygateway.com/Request/' );
		$request  = wp_remote_post( $eway_url );
		if ( is_wp_error( $request ) )
		{
			return __( 'Cannot connect to eWAY. Please try again', '7listings' );
		}

		$response = wp_remote_retrieve_body( $request );
		$status   = strtolower( eway_fetch_data( $response, '<result>', '</result>' ) );

		if ( 'true' != $status )
		{
			$error = eway_fetch_data( $response, '<error>', '</error>' );
			return empty( $error ) ? __( 'Error connecting to eWAY. Please try again', '7listings' ) : $error;
		}

		$response_url = eway_fetch_data( $response, '<uri>', '</uri>' );
		$url_parts    = parse_url( $response_url );

		$redirect_url = "{$url_parts['scheme']}://{$url_parts['host']}{$url_parts['path']}";
		$query_args   = wp_parse_args( $url_parts['query'] );
		$hidden       = '';
		foreach ( $query_args as $key => $value )
		{
			$hidden .= "<input type='hidden' name='$key' value='$value'>";
		}

		return sprintf( '
			<form id="%s" class="modal hide fade" action="%s" method="get">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>%s</h3>
				</div>
				<div class="modal-body">
					<div class="eway logo">eWay</div>
					%s
					<div class="alert">%s</div>
				</div>
				<div class="modal-footer">
					<input type="submit" class="button primary" value="%s">
				</div>
			</form>',
			esc_attr( $id ),
			esc_url( $redirect_url ),
			__( 'Redirecting to eWAY', '7listings' ),
			$hidden,
			__( 'After your transaction,<br>please <strong>come back!</strong><br><br>Closing the window may cause problems<br>with receiving your <strong>confirmation email</strong>.', '7listings' ),
			esc_attr__( 'Continue', '7listings' )
		);
	}

	/**
	 * Run when class is loaded
	 *
	 * @return void
	 */
	public static function listen()
	{
		if ( ! isset( $_GET['sl_payment_listener'] ) )
		{
			return;
		}

		$action = $_GET['sl_payment_listener'];
		$data   = array();

		// If payment method is Paypal
		if ( false !== strpos( $action, 'paypal' ) )
		{
			$data = self::paypay_verify_ipn();
			if ( ! is_array( $data ) )
			{
				self::log( array(
					'action'  => $action,
					'message' => $data,
					'time'    => date( 'Y-m-d H:i' ),
				) );
				die;
			}
		}
		// If payment method is eWay
		elseif ( false !== strpos( $action, 'eway' ) )
		{
			$data = self::eway_verify();
			if ( true !== $data )
			{
				self::log( array(
					'action'  => $action,
					'message' => $data,
					'time'    => date( 'Y-m-d H:i' ),
				) );
				die;
			}
		}

		// Allow modules attach callback here
		do_action( "sl_payment_listener_$action", $data );
		die;
	}

	/**
	 * Get IPN info and verify with paypal
	 *
	 * @return string|array Array of data if success, error message if failure
	 * @since  4.12.1
	 */
	private static function paypay_verify_ipn()
	{
		// Read POST data
		$post_data = '';

		// Read from stream input first, and then via $_POST, but need to check post_max_size
		if ( ini_get( 'allow_url_fopen' ) )
		{
			$post_data = file_get_contents( 'php://input' );
		}
		else
		{
			ini_set( 'post_max_size', '12M' );
			if ( empty( $_POST ) )
			{
				return __( 'No data sent, empty $_POST.', '7listings' );
			}
		}

		$req = 'cmd=_notify-validate';
		$sep = ini_get( 'arg_separator.output' );
		if ( ! empty( $post_data ) )
		{
			$req .= $sep . $post_data;
		}
		else
		{
			foreach ( $_POST as $key => $value )
			{
				$req .= $sep . "$key=" . urlencode( $value );
			}
		}

		// Convert collected post data to an array
		parse_str( $req, $data );

		// Verify data
		if ( 'completed' != strtolower( $data['payment_status'] ) )
		{
			return __( 'Payment is not completed.', '7listings' );
		}

		$post_vars = array(
			'method'      => 'POST',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(
				'host'         => 'www.paypal.com',
				'connection'   => 'close',
				'content-type' => 'application/x-www-form-urlencoded',
				'post'         => '/cgi-bin/webscr HTTP/1.1',
			),
			'sslverify'   => false,
			'body'        => $data,
		);

		// Get response
		$response = wp_remote_post( self::paypal_get_url(), $post_vars );
		if ( is_wp_error( $response ) || 'VERIFIED' !== $response['body'] )
		{
			return __( 'Cannot verify payment. Response body: ' . $response['body'], '7listings' );
		}

		// Return ipn data
		return $data;
	}

	/**
	 * Get Paypal URL
	 * @return string
	 * @since  4.12
	 */
	private static function paypal_get_url()
	{
		return sprintf( 'https://www%s.paypal.com/cgi-bin/webscr', sl_setting( 'sandbox_mode' ) ? '.sandbox' : '' );
	}

	/**
	 * Verify payment with eWay
	 *
	 * @return bool|string True if payment is valid, error string otherwise
	 * @since  5.7.1
	 */
	private static function eway_verify()
	{
		$args = array(
			'CustomerID'        => sl_setting( 'eway_id' ),
			'UserName'          => sl_setting( 'eway_username' ),
			'AccessPaymentCode' => $_REQUEST['AccessPaymentCode'],
		);

		$eway_url = add_query_arg( $args, 'https://au.ewaygateway.com/Result/' );

		$request = wp_remote_post( $eway_url );
		if ( is_wp_error( $request ) )
		{
			return __( 'Cannot connect to eWay.', '7listings' );
		}

		$response = wp_remote_retrieve_body( $request );
		$status   = strtolower( eway_fetch_data( $response, '<trxnstatus>', '</trxnstatus>' ) );

		return 'true' == $status ? true : $response;
	}

	/**
	 * Log errors
	 *
	 * @param  mixed $data Data need to log
	 *
	 * @return void
	 */
	private static function log( $data )
	{
		static $option = '7listings_payment_log';
		$log   = get_option( $option, array() );
		$log[] = $data;
		update_option( $option, $log );
	}
}

add_action( 'init', array( 'Sl_Payment', 'listen' ) );
