<?php

/**
 * Control the booking payment
 * Receive and process payment notifications from gateways
 */
class Sl_Booking_Payment
{
	/**
	 * Constructor: add hooks
	 */
	public function __construct()
	{
		add_action( 'sl_payment_listener_booking_paypal', array( $this, 'process_paypal_notification' ), 10, 1 );
		add_action( 'sl_payment_listener_booking_eway', array( $this, 'process_eway_notification' ) );
	}

	/**
	 * Process notification from Paypal
	 *
	 * @param array $data Booking data
	 *
	 * @return void
	 */
	public function process_paypal_notification( $data )
	{
		$this->update_booking( $data['custom'] );
	}

	/**
	 * Process notification from eWay
	 *
	 * @return void
	 */
	public function process_eway_notification()
	{
		$this->update_booking( $_GET['sl_booking_post_id'] );
		$url = Sl_Helper::get_url_by_template( 'templates/thank-you-booking.php' );
		header( "Location: $url" );
	}

	/**
	 * Update booking status and send email notifications
	 *
	 * @param int $id
	 * @return void
	 */
	protected function update_booking( $id )
	{
		// Prevent duplicated emails
		if ( 1 == get_post_meta( $id, 'paid', true ) )
		{
			return;
		}
		update_post_meta( $id, 'paid', 1 );

		$type = get_post_meta( $id, 'type', true );

		$class = 'Sl_' . $type . '_Helper';

		if ( 'bundle' == $type )
		{
			$class = 'Sl\TourBundles\Helper';
		}

		call_user_func( array( $class, 'send_booking_emails' ), $id );
	}
}
