<?php
namespace ASQ\Xero;

/**
 * Add company info to Xero when sign up
 * @package ASQ\Xero
 */
class Signup
{
	/**
	 * Run when file is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'company_signup', array( __CLASS__, 'send_to_xero' ), 10, 3 );
	}

	/**
	 * Send company info to Xero when signup
	 *
	 * @param \WP_User $user_data
	 * @param int      $post_id Company post ID
	 * @param array    $data    Full company form data sent via $_POST
	 */
	public static function send_to_xero( $user_data, $post_id, $data )
	{
		// Name
		$name       = $data['post_title'];
		$first_name = $user_data->first_name;
		$last_name  = $user_data->last_name;

		// Email
		$email = $user_data->user_email;
		//if ( isset( $data['email'] ) )
		//	$email = $data['email'];
		//if ( isset( $data['invoice_email'] ) )
		//	$email = $data['invoice_email'];

		$phones = array();
		if ( isset( $data['invoice_phone'] ) )
		{
			$phones[] = array(
				'phone_type' => 'MOBILE', // Can be 'DEFAULT', 'FAX', 'MOBILE', 'DDI'
				'phone'      => $data['invoice_phone'],
			);
		}
		if ( isset( $data['invoice_direct_line'] ) )
		{
			$phones[] = array(
				'phone_type' => 'DDI', // Can be 'DEFAULT', 'FAX', 'MOBILE', 'DDI'
				'phone'      => $data['invoice_direct_line'],
			);
		}
		$customer = array(
			'name'         => $name,
			'first_name'   => $first_name,
			'last_name'    => $last_name,
			'email'        => $email,

			'address_type' => isset( $data['address_type'] ) ? $data['address_type'] : 'POBOX',
			'address1'     => isset( $data['address'] ) ? $data['address'] : '',
			'address2'     => isset( $data['address2'] ) ? $data['address2'] : '',
			'city'         => isset( $data['city'] ) ? $data['city'] : '',
			'region'       => isset( $data['state'] ) ? $data['state'] : '',
			'postcode'     => isset( $data['postcode'] ) ? $data['postcode'] : '',

			'phones'       => $phones,
		);

		Customer::load();
		if ( ! Customer::add( $customer ) )
		{
			// For debugging, it should *not* add this option if successful
			add_option( 'asq_xero_log', "Cannot add company ID $post_id" );
		}
	}
}
