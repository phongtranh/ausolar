<?php

/**
 * This class will hold all things for company ajax requests
 */
class Sl_Company_Ajax extends Sl_Core_Ajax
{
	/**
	 * Class Constructor
	 */
	function __construct( $post_type, $actions = array() )
	{
		parent::__construct( $post_type, $actions );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	/**
	 * Ajax callback for signup new company
	 *
	 * @return void
	 */
	function signup()
	{
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'signup' ) )
			wp_send_json_error( __( 'Form is not submitted properly.', '7listings' ) );

		parse_str( $_POST['data'], $data );
		unset( $_POST['data'], $_POST['nonce'], $_POST['action'] );
		$_POST = array_merge( $_POST, $data );

		$errors = array();

		if ( isset( $data['this_is_my_company'] ) ) 
		{
			if ( empty( $data['post_title'] ) )
			{
				wp_send_json_error([
					'Please select a company. Or choose "Company Not Listed" to create new listing.'
				]);
			}

			$company = get_page_by_title( $data['post_title'], OBJECT, 'company' );

			if ( empty( $company ) )
				wp_send_json_error(['Cannot find your company!']);
		}

		if ( is_user_logged_in() )
		{
			$user_id = get_current_user_id();
		}
		else
		{
			// Required fields
			$required = array(
				'username'           => __( 'Please enter username.', '7listings' ),
				'password'           => __( 'Please enter password.', '7listings' ),
				'user_email'         => __( 'Please enter email.', '7listings' ),
				'user_email_confirm' => __( 'Please enter confirmed email.', '7listings' ),
			);
			foreach ( $required as $k => $v )
			{
				if ( empty( $data[$k] ) )
					$errors[] = $v;
			}

			// Check if email valid
			if ( $data['user_email'] != $data['user_email_confirm'] )
				$errors[] = __( 'Please enter same email for confirmation.', '7listings' );
			if ( ! is_email( $data['user_email'] ) )
				$errors[] = __( 'Please enter valid email.', '7listings' );
			if ( email_exists( $data['user_email'] ) )
				$errors[] = __( 'Email is used. Please use another email.', '7listings' );

			if ( ! empty( $errors ) )
				wp_send_json_error( implode( '<br>', $errors ) );

			// Insert new user
			$user_data = array(
				'user_login' => $data['username'],
				'user_pass'  => $data['password'],
				'user_email' => $data['user_email'],
				'first_name' => isset( $data['first_name'] ) ? $data['first_name'] : '',
				'last_name'  => isset( $data['last_name'] ) ? $data['last_name'] : '',
				'role'       => 'company_owner'
			);

			$user_id   = wp_insert_user( $user_data );
			if ( is_wp_error( $user_id ) || ! $user_id )
			{
				$errors[] = $user_id->get_error_message();
				wp_send_json_error( implode( '<br>', $errors ) );
			}

			// Membership
			$membership      = 'bronze';
			$membership_time = 'month';
			if ( isset( $data['membership'] ) )
			{
				list( $membership, $membership_time ) = explode( ',', $data['membership'] );
			}
			update_user_meta( $user_id, 'membership', $membership );
			update_user_meta( $user_id, 'membership_time', $membership_time );
			update_user_meta( $user_id, 'membership_paid', 0 );
			update_user_meta( $user_id, 'direct_line', $data['direct_line'] );
		}

		if ( isset( $data['this_is_my_company'] ) )
		{
			$post_id = $company->ID;

			// Do not allow assign user to these companies
			$user = get_post_meta( $post_id, 'user', true );
			$leads_enable = get_post_meta( $post_id, 'leads_enable', true );

			if ( ! empty( $user ) || ! empty( $leads_enable ) )
				return;

			// Company need to pending before continue
			wp_update_post( [
				'ID' 			=> $post_id,
				'post_status' 	=> 'pending',
				'post_date'		=> date('Y-m-d H:i:s')
			] );

			// Add user
			update_post_meta( $post_id, 'user', $user_id );

			$membership      = get_user_meta( $user_id, 'membership', true );
			$membership_time = get_user_meta( $user_id, 'membership_time', true );
			$membership_paid = get_user_meta( $user_id, 'membership_paid', true );

			// Check if user has paid for membership
			$price = sl_setting( "{$this->post_type}_membership_price_{$membership}" );
			if ( 'year' == $membership_time )
				$price = sl_setting( "{$this->post_type}_membership_price_year_{$membership}" );

			$user_data = get_userdata( $user_id );
		}
		else
		{
			// Required fields
			$required = array(
				'post_title'   => __( 'Please enter company title.', '7listings' ),
				'post_content' => __( 'Please enter company description.', '7listings' ),
			);
			foreach ( $required as $k => $v )
			{
				if ( empty( $data[$k] ) )
					$errors[] = $v;
			}
			if ( ! empty( $errors ) )
				wp_send_json_error( implode( '<br>', $errors ) );

			// WordPress Administration File API
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			// Insert company
			$post_data = array(
				'post_title'   => $data['post_title'],
				'post_content' => $data['post_content'],
				'post_status'  => 'pending',
				'post_type'    => 'company',
				'post_author'  => $user_id,
			);
			$post_id   = wp_insert_post( $post_data );
			if ( is_wp_error( $post_id ) || ! $post_id )
			{
				$errors[] = __( 'Error while creating new company.', '7listings' );
				wp_send_json_error( implode( '<br>', $errors ) );
			}

			// Add user
			update_post_meta( $post_id, 'user', $user_id );

			$membership      = get_user_meta( $user_id, 'membership', true );
			$membership_time = get_user_meta( $user_id, 'membership_time', true );
			$membership_paid = get_user_meta( $user_id, 'membership_paid', true );

			// Default company 'operates'
			update_post_meta( $post_id, 'operating', 1 );

			// Country
			if ( isset( $data['country'] ) )
				update_post_meta( $post_id, 'country', $data['country'] );

			// Check if user has paid for membership
			$price = sl_setting( "{$this->post_type}_membership_price_{$membership}" );
			if ( 'year' == $membership_time )
				$price = sl_setting( "{$this->post_type}_membership_price_year_{$membership}" );

			$user_data = get_userdata( $user_id );

			do_action( 'company_signup', $user_data, $post_id, $data );
		}

		$replacements = array(
			'[first_name]'    => $user_data->first_name,
			'[membership]'    => $user_data->membership,
			'[company-title]' => $data['post_title'],
		);

		// Send email to admin
		if ( isset( $data['this_is_my_company'] ) ) {
			$to = 'admin@australiansolarquotes.com.au';
			
			$subject = 'New Company Registered';
			
			$body = sprintf("<p>%s has been created and linked to <a href='https://www.australiansolarquotes.com.au/wp-admin/post.php?post=%s'>%s</a>.</p>
				<p>Call %s on %s and welcome him to our network.</p>
			", $user_data->user_login, $post_id, $data['post_title'], $user_data->first_name . ' ' . $user_data->last_name, $user_data->direct_line );
		}
		else
		{		
			$to      = sl_email_admin_email( "emails_{$this->post_type}_membership_admin_email" );
			$subject = sl_email_replace( sl_setting( "emails_{$this->post_type}_membership_admin_subject" ), $replacements );
			$body    = sl_email_content( '', 'company-membership-admin', $replacements );	
		}

		wp_mail( $to, $subject, $body );

		// Email to user
		$to      = $user_data->user_email;
		$subject = sl_email_replace( sl_setting( "emails_{$this->post_type}_membership_user_subject" ), $replacements );
		$body    = sl_email_content( '', 'company-membership-user', $replacements );
		wp_mail( $to, $subject, $body );

		if ( $membership_paid || empty( $price ) || $membership === 'bronze' )
		{
			wp_send_json_success( array(
				'paid'     => 1,
				'message'  => __( 'Company has been added successfully. We will review the company before displaying. Thank you!', '7listings' ),
				'redirect' => get_permalink( sl_setting( "{$this->post_type}_page_dashboard" ) ),
			) );
		}

		$time = 'month' == $membership_time ? __( 'Monthly', '7listings' ) : __( 'Yearly', '7listings' );

		// Redirect to Paypal
		$html = Sl_Payment::paypal_form( 'checkout-form', 'membership_signup_paypal', array(
			'item_name' => ucwords( sprintf( __( '%s Membership (%s)', '7listings' ), $membership, $time ) ),
			'custom'    => $user_id,
			'amount'    => $price,
			'return'    => get_permalink( sl_setting( "{$this->post_type}_page_dashboard" ) ),
		) );

		wp_send_json_success( array(
			'paid' => 0,
			'form' => $html,
		) );
	}

	/**
	 * Ajax callback for renew membership
	 *
	 * @return void
	 */
	function pay()
	{
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pay' ) || ! is_user_logged_in() )
			wp_send_json_error( __( 'Form is not submitted properly.', '7listings' ) );

		$user_id         = get_current_user_id();
		$membership      = get_user_meta( $user_id, 'membership', true );
		$membership_time = get_user_meta( $user_id, 'membership_time', true );

		$time = 'month' == $membership_time ? __( 'Monthly', '7listings' ) : __( 'Yearly', '7listings' );
		if ( 'month' == $membership_time )
			$amount = sl_setting( "{$this->post_type}_membership_price_{$membership}" );
		else
			$amount = sl_setting( "{$this->post_type}_membership_price_year_{$membership}" );

		// Redirect to Paypal
		$html = Sl_Payment::paypal_form( 'pay-form', 'membership_pay_paypal', array(
			'item_name' => ucwords( sprintf( __( '%s Membership (%s)', '7listings' ), $membership, $time ) ),
			'custom'    => $user_id,
			'amount'    => intval( $amount ),
			'return'    => get_permalink( sl_setting( "{$this->post_type}_page_dashboard" ) ),
		) );

		wp_send_json_success( $html );
	}

	/**
	 * Ajax callback for renew membership
	 *
	 * @return void
	 */
	function renew()
	{
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'renew' ) || ! is_user_logged_in() )
			wp_send_json_error( __( 'Form is not submitted properly.', '7listings' ) );

		$user_id    = get_current_user_id();
		$membership = get_user_meta( $user_id, 'membership', true );

		$membership_time = $_POST['time'];
		$time            = 'month' == $membership_time ? __( 'Monthly', '7listings' ) : __( 'Yearly', '7listings' );
		if ( 'month' == $membership_time )
			$amount = sl_setting( "{$this->post_type}_membership_price_{$membership}" );
		else
			$amount = sl_setting( "{$this->post_type}_membership_price_year_{$membership}" );

		// Redirect to Paypal
		$html = Sl_Payment::paypal_form( 'renew-form', 'membership_renew_paypal', array(
			'item_name' => ucwords( sprintf( __( 'Renew %s Membership (%s)', '7listings' ), $membership, $time ) ),
			'custom'    => "$user_id,$membership_time",
			'amount'    => intval( $amount ),
			'return'    => get_permalink( sl_setting( "{$this->post_type}_page_dashboard" ) ),
		) );

		wp_send_json_success( $html );
	}

	/**
	 * Ajax callback for upgrade membership
	 *
	 * @return void
	 */
	function upgrade()
	{
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'upgrade' ) || ! is_user_logged_in() )
			wp_send_json_error( __( 'Form is not submitted properly.', '7listings' ) );

		$user_id = get_current_user_id();
		$type    = $_POST['type'];

		$membership_time = $_POST['time'];
		$time            = 'month' == $membership_time ? __( 'Monthly', '7listings' ) : __( 'Yearly', '7listings' );
		if ( 'month' == $membership_time )
			$amount = sl_setting( "{$this->post_type}_membership_price_{$type}" );
		else
			$amount = sl_setting( "{$this->post_type}_membership_price_year_{$type}" );

		// Redirect to Paypal
		$html = Sl_Payment::paypal_form( 'upgrade-form', 'membership_upgrade_paypal', array(
			'item_name' => ucwords( sprintf( __( '%s Membership Upgrade (%s)', '7listings' ), $type, $time ) ),
			'custom'    => "$user_id,$type,$membership_time",
			'amount'    => intval( $amount ),
			'return'    => get_permalink( sl_setting( "{$this->post_type}_page_dashboard" ) ),
		) );

		wp_send_json_success( $html );
	}

	/**
	 * Ajax callback for changing account on listing page
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	function save_post( $post_id )
	{
		if ( 'inline-save' != $_POST['action'] || 'company' != $_POST['post_type'] )
			return;

		if ( empty( $_POST['tax_input']['location'] ) )
			return;

		$locations = $_POST['tax_input']['location'];
		$locations = array_filter( $locations );
		foreach ( $locations as $location )
		{
			$term = get_term( $location, 'location' );
			if ( empty( $term ) || is_wp_error( $term ) )
				continue;
			// If term has parent => city
			if ( $term->parent )
				update_post_meta( $post_id, 'city', $term->name );
			// No parent => state
			else
				update_post_meta( $post_id, 'state', $term->name );
		}
	}
}

new Sl_Company_Ajax( 'company', array( 'pay', 'signup', 'renew', 'upgrade' ) );
