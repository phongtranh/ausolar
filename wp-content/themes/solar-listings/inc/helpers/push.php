<?php
// Replace with real BROWSER API key from Google APIs
define( "GOOGLE_API_KEY", "AIzaSyAyWFY9Ov_XixSsptdvzEPrKfnJQj3c65Y" );

class  PushNotification
{
	/**
	 * push android using GCM
	 * @param string $device_token
	 * @param string $content
	 * @return mixed
	 */
	public static function pushAndroid( $device_token, $content )
	{
		// Replace with real BROWSER API key from Google APIs
		$apiKey = GOOGLE_API_KEY;

		// Replace with real client registration IDs
		$registrationIDs = array(
			$device_token
		);

		// Set POST variables
		$url = 'https://android.googleapis.com/gcm/send';

		$fields = array(
			'registration_ids' => $registrationIDs,
			'data'             => $content
		);

		$headers = array(
			'Authorization: key=' . $apiKey,
			'Content-Type: application/json'
		);

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );

		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

		// Execute post
		$result = curl_exec( $ch );

		if ( $result === false )
		{
			die ( 'Curl failed: ' . curl_error( $ch ) );
		}
		// Close connection
		curl_close( $ch );

		return $result;
	}

	/**
	 * push notification iOS
	 * @param string $device_token
	 * @param        $message
	 * @param array  $return_fields
	 * @return int
	 */
	public static function pushIos( $device_token, $message, $return_fields = array() )
	{
		$passphrase = 'apple';

		////////////////////////////////////////////////////////////////////////////////
		$path = '/home/bangnd/www/asq/wp-content/themes/solar-listings/inc/helpers/SolarEnergyk.pem';
		$ctx  = stream_context_create();
		stream_context_set_option( $ctx, 'ssl', 'local_cert', $path );
		stream_context_set_option( $ctx, 'ssl', 'passphrase', $passphrase );

		// Open a connection to the APNS server
		$fp = stream_socket_client( 'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx );
		//or ssl://gateway.sandbox.push.apple.com:2195
		//or ssl://gateway.push.apple.com:2195
		if ( ! $fp )
			exit( "Failed to connect: $err $errstr" . PHP_EOL );

		// Put your device token here (without spaces):
		//$device_token = "554124af 3ac4d714 feebda02 5e9db34c 0b9efb08 786ff82e 28db51a2 fc807547";
		$device_token = str_replace( " ", "", $device_token );

		// Create the payload body
		$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default'
		);
		if ( $return_fields )
			$body['aps'] = array_merge( $body['aps'], $return_fields );

		// Encode the payload as JSON
		$payload = json_encode( $body );

		// Build the binary notification
		$msg = chr( 0 ) . pack( 'n', 32 ) . pack( 'H*', $device_token ) . pack( 'n', strlen( $payload ) ) . $payload;
		// Send it to the server
		$result = fwrite( $fp, $msg, strlen( $msg ) );

		fclose( $fp );

		return $result;
	}
}
