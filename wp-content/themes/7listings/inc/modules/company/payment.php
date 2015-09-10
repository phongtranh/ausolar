<?php

class Sl_Company_Payment
{
	/**
	 * Listen to Paypal IPN
	 *
	 * @return Sl_Company_Payment
	 */
	function __construct()
	{
		$actions = array(
			'signup',
			'renew',
			'upgrade',
			'pay',
		);
		foreach ( $actions as $action )
		{
			add_action( "sl_payment_listener_membership_{$action}_paypal", array( $this, $action ), 10, 1 );
		}
	}

	/**
	 * User signup
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	function signup( $data )
	{
		// Update user membership
		$user_id = $data['custom'];
		$now     = time();
		update_user_meta( $user_id, 'membership_paid', $now );

		do_action( 'company_account_signup', $user_id, $now );
	}

	/**
	 * User pay for payment
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	function pay( $data )
	{
		// Update user membership
		$user_id = $data['custom'];
		$now     = time();
		update_user_meta( $user_id, 'membership_paid', $now );

		do_action( 'company_account_pay', $user_id, $now );
	}

	/**
	 * User renew
	 *
	 * @param $data
	 *
	 * @return void
	 */
	function renew( $data )
	{
		// Update user membership
		list( $user_id, $time ) = explode( ',', $data['custom'] );
		$now = time();
		update_user_meta( $user_id, 'membership_paid', $now );
		update_user_meta( $user_id, 'membership_time', $time );

		do_action( 'company_account_renew', $user_id, $now, $time );
	}

	/**
	 * User upgrade
	 *
	 * @param $data
	 *
	 * @return void
	 */
	function upgrade( $data )
	{
		// Update user membership
		list( $user_id, $type, $time ) = explode( ',', $data['custom'] );
		$now  = time();
		$prev = get_user_meta( $user_id, 'membership', true );
		update_user_meta( $user_id, 'membership_paid', $now );
		update_user_meta( $user_id, 'membership', $type );

		$prev_time = get_user_meta( $user_id, 'membership_time', true );
		update_user_meta( $user_id, 'membership_time', $time );

		do_action( 'company_account_upgrade', $user_id, $type, $prev, $time, $prev_time, $now );
	}
}

new Sl_Company_Payment;
