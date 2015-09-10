<?php
/**
 * Define for file includes. The certs directory is best stored out of web root so moving the directory
 * and updating the reference to BASE_PATH is the best way to ensure things keep working
 */
define( 'BASE_PATH', dirname( __FILE__ ) );

/**
 * Define which app type you are using:
 * Private - private app method
 * Public - standard public app method
 * Partner - partner app method
 */
define( 'XERO_APP_TYPE', 'Private' );

/**
 * Define application name
 * Will be used to set a user agent string
 */
define( 'XERO_APP_NAME', 'CRM' );

/**
 * Set your callback url or set 'oob' if none required
 */
//define('XERO_OAUTH_CALLBACK',     'http://localhost/XeroOAuth-PHP/example.php');
define( 'XERO_OAUTH_CALLBACK', 'oob' );

/**
 * Application specific settings
 * Not all are required for given application types
 * consumer_key: required for all applications
 * consumer_secret:  for partner applications, set to: s (cannot be blank)
 * rsa_private_key: application certificate private key - not needed for public applications
 * rsa_public_key:  application certificate public cert - not needed for public applications
 */
define( 'XERO_CONSUMER_KEY', 'QVRJXRYRG9XRDAMACQKMHENQ3TC4RO' );
define( 'XERO_SHARE_SECRET', 'OXVWFOA7AZXDZIXPMDESOGTGBGEZ0U' );
define( 'XERO_CORE_VERSION', '2.0' );
define( 'XERO_PAYROLL_VERSION', '1.0' );

if ( XERO_APP_TYPE == 'Private' || XERO_APP_TYPE == 'Partner' )
{
	define( 'XERO_RSA_PRIVATE_KEY', BASE_PATH . '/certs/privatekey.pem' );
	define( 'XERO_RSA_PUBLIC_KEY', BASE_PATH . '/certs/publickey.cer' );
}

/**
 * Special options for Partner applications
 * Partner applications require a Client SSL certificate which is issued by Xero
 * the certificate is issued as a .p12 cert which you will then need to split into a cert and private key:
 * openssl pkcs12 -in entrust-client.p12 -clcerts -nokeys -out entrust-cert.pem
 * openssl pkcs12 -in entrust-client.p12 -nocerts -out entrust-private.pem <- you will be prompted to enter a password
 */
if ( XERO_APP_TYPE == 'Partner' )
{
	define( 'XERO_CURL_SSL_CERT', BASE_PATH . '/certs/entrust-cert-RQ3.pem' );
	define( 'XERO_CURL_SSL_PASSWORD', '1234' );
	define( 'XERO_CURL_SSL_KEY', BASE_PATH . '/certs/entrust-private-RQ3.pem' );
}



