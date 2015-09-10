<?php
require 'lib/XeroOAuth.php';

define ( 'BASE_PATH', dirname( __FILE__ ) );
define ( "XERO_APP_TYPE", "Private" );
define ( "XERO_OAUTH_CALLBACK", "oob" );
$useragent = "XeroOAuth-PHP Private App Test";

$signatures = array(
	'consumer_key'    => 'ZRUKREXNAFJ2XTXLTWEWMPUWLHO1YA',
	'shared_secret'   => 'UK4RMKXPEC7PJVZ5Q2IMPML3J9YYUN',
	// API versions
	'core_version'    => '2.0',
	'payroll_version' => '1.0'
);

if ( XERO_APP_TYPE == "Private" || XERO_APP_TYPE == "Partner" )
{
	$signatures ['rsa_private_key'] = BASE_PATH . '/certs/privatekey.pem';
	$signatures ['rsa_public_key']  = BASE_PATH . '/certs/publickey.cer';
}

$XeroOAuth = new XeroOAuth ( array_merge( array(
	'application_type' => XERO_APP_TYPE,
	'xERO_OAUTH_CALLBACK'   => XERO_OAUTH_CALLBACK,
	'user_agent'       => $useragent
), $signatures ) );
include 'tests/testRunner.php';

$initialCheck = $XeroOAuth->diagnostics();
$checkErrors  = count( $initialCheck );
if ( $checkErrors > 0 )
{
	// you could handle any config errors here, or keep on truckin if you like to live dangerously
	foreach ( $initialCheck as $check )
	{
		echo 'Error: ' . $check . PHP_EOL;
	}
}
else
{
	$session      = persistSession( array(
		'oauth_token'          => $XeroOAuth->config ['consumer_key'],
		'oauth_token_secret'   => $XeroOAuth->config ['shared_secret'],
		'oauth_session_handle' => ''
	) );
	$oauthSession = retrieveSession();

	if ( isset ( $oauthSession ['oauth_token'] ) )
	{
		$XeroOAuth->config ['access_token']        = $oauthSession ['oauth_token'];
		$XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];

		include 'tests/tests.php';
	}

	testLinks();
}
