<?php
class SH_Account
{
	/**
	 * Run when the class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		$actions = array(
			'close'             => 2,
			'change'            => 6,
			'signup'            => 2,
			'pay'               => 2,
			'renew'             => 3,
			'upgrade'           => 6,
			'invoice_recipient' => 2,
		);
		foreach ( $actions as $action => $num )
		{
			add_action( "company_account_$action", array( __CLASS__, $action ), 10, $num );
		}
	}

	/**
	 * Log close account
	 *
	 * @param int    $user_id
	 * @param object $company
	 *
	 * @return void
	 */
	public static function close( $user_id, $company )
	{
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Close', 'sch' ),
			'description' => sprintf( __( '<span class="label">Company name:</span> <span class="detail">%s</span>', 'sch' ), $company->post_title ),
			'object'      => $company->ID,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log change account
	 *
	 * @param int    $user_id
	 * @param object $company
	 *
	 * @param string $type      Current membership type
	 * @param string $prev      Previous membership type
	 * @param string $time      Current payment time (month or year)
	 * @param string $prev_time Previous payment time (month or year)
	 *
	 * @return void
	 */
	public static function change( $user_id, $company, $type, $prev, $time, $prev_time )
	{
		$type = $type ? $type : 'none';
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Change', 'sch' ),
			'description' => sprintf(
				__( '<span class="label">Type:</span> <span class="detail">%s</span><br><span class="label">Period:</span> <span class="detail">%s</span>', 'sch' ),
				ucwords( $type ),
				ucwords( $time )
			),
			'object'      => $company->ID,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log account signup
	 *
	 * @param int $user_id
	 * @param int $time
	 *
	 * @return void
	 */
	public static function signup( $user_id, $time )
	{
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
		{
			$company = '';
		}
		else
		{
			$company = current( $company );
			$company = $company->ID;
		}

		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s', $time ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Signup paid', 'sch' ),
			'description' => '',
			'object'      => $company,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log account membership payment
	 *
	 * @param int $user_id
	 * @param int $time
	 *
	 * @return void
	 */
	public static function pay( $user_id, $time )
	{
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
		{
			$company = '';
		}
		else
		{
			$company = current( $company );
			$company = $company->ID;
		}

		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s', $time ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Membership Payment', 'sch' ),
			'description' => '',
			'object'      => $company,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log account membership renew payment
	 *
	 * @param int    $user_id
	 * @param int    $time
	 * @param string $type
	 *
	 * @return void
	 */
	public static function renew( $user_id, $time, $type )
	{
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
		{
			$company = '';
		}
		else
		{
			$company = current( $company );
			$company = $company->ID;
		}

		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s', $time ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Membership Renew', 'sch' ),
			'description' => '',
			'object'      => $company,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log upgrade account membership
	 *
	 * @param int    $user_id
	 * @param string $type      Current membership type
	 * @param string $prev      Previous membership type
	 * @param string $time      Current payment time (month or year)
	 * @param string $prev_time Previous payment time (month or year)
	 * @param int    $now       Current time
	 *
	 * @return void
	 */
	public static function upgrade( $user_id, $type, $prev, $time, $prev_time, $now )
	{
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
		{
			$company = '';
		}
		else
		{
			$company = current( $company );
			$company = $company->ID;
		}

		$type = $type ? $type : 'none';
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s', $now ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Change', 'sch' ),
			'description' => sprintf(
				__( '<span class="label">Type:</span> <span class="detail">%s</span><br><span class="label">Period:</span> <span class="detail">%s</span>', 'sch' ),
				ucwords( $type ),
				ucwords( $time )
			),
			'object'      => $company,
			'user'        => $user_id,
		) );
	}

	/**
	 * Log edit invoice recipient
	 *
	 * @param int    $user_id
	 * @param object $company
	 */
	public static function invoice_recipient( $user_id, $company )
	{
		$fields = array(
			'invoice_name'  => __( 'Invoice Name', 'sch' ),
			'invoice_email' => __( 'Invoice Email', 'sch' ),
			'invoice_phone' => __( 'Invoice Phone', 'sch' ),
			'paypal_email'  => __( 'Paypal Email', 'sch' ),
		);
		$description = array();
		foreach ( $fields as $k => $v )
		{
			if ( !empty( $_POST[$k] ) )
				$description[] = "<span class='label'>$v:</span> <span class='detail'>{$_POST[$k]}</span>";
		}
		SH::log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Account', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => implode( '<br>', $description ),
			'object'      => $company->ID,
			'user'        => $user_id,
		) );
	}
}

SH_Account::load();