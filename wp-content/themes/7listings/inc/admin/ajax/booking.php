<?php
add_action( 'wp_ajax_sl_booking_send_email', 'sl_ajax_booking_send_email' );

/**
 * Ajax callback for sending booking report to custom email
 * This is used in 2 places:
 * - Booking management page: send booking report to custom email
 * - Edit booking page: send booking report to admin or customer email
 *
 * @return void
 */
function sl_ajax_booking_send_email()
{
	/**
	 * Allow to receive request from other domains to trigger sending emails
	 * Used in network report plugin
	 */
	header( 'Access-Control-Allow-Origin: *' );

	if ( empty( $_POST['type'] ) || empty( $_POST['booking_id'] ) )
		wp_send_json_error();

	$booking_id = $_POST['booking_id'];
	$email = empty( $_POST['email'] ) ? '' : $_POST['email'];

	$class = 'Sl_' . ucfirst( get_post_meta( $booking_id, 'type', true ) ) . '_Helper';
	call_user_func( array( $class, 'send_booking_emails' ), $booking_id, $_POST['type'], $email );

	wp_send_json_success();
}

add_action( 'wp_ajax_sl_toggle_paid', 'wp_ajax_sl_toggle_paid' );

/**
 * Ajax callback for toggle paid status of booking
 *
 * @return void
 */
function wp_ajax_sl_toggle_paid()
{
	$id   = isset( $_POST['id'] ) ? $_POST['id'] : 0;
	$paid = isset( $_POST['paid'] ) ? $_POST['paid'] : 0;

	if ( ! $id )
		wp_send_json_error();

	if ( ! $paid )
		update_post_meta( $id, 'paid', 1 );
	else
		delete_post_meta( $id, 'paid' );
	wp_send_json_success();
}

