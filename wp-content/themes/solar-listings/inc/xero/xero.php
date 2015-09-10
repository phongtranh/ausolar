<?php
require 'XeroOAuth-PHP/config.php';
require 'XeroOAuth-PHP/lib/XeroOAuth.php';
require 'helper.php';
require 'customer.php';

require 'signup.php';

\ASQ\Xero\Signup::load();

//$user_data             = new StdClass;
//$user_data->first_name = 'Anh';
//$user_data->last_name  = 'Tran';
//$user_data->user_email = 'rilwis@gmail.com';
//$post_id               = 9999;
//$data                  = array(
//	'invoice_name'        => 'Anh Tran',
//	'invoice_phone'       => '123456',
//	'invoice_direct_line' => '987654',
//	'invoice_email'       => 'rilwis@gmail.com',
//	'address'             => '233 Xuan Thuy',
//	'city'                => 'Hanoi',
//	'state'               => 'Hanoi',
//	'postcode'            => 10000,
//);
//ASQ\Xero\Signup::send_to_xero( $user_data, $post_id, $data );
//echo 'done';
