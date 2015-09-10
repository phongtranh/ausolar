<?php
namespace ASQ\Xero;

/**
 * Helper functions
 * @package ASQ\Xero
 */
class Helper
{
	/**
	 * Store the OAuth access token and session handle in session
	 *
	 * @param array $params
	 *
	 * @return void
	 */
	public static function set_session( $params = array() )
	{
		if ( ! $params )
			return;

		$_SESSION['access_token']       = $params['oauth_token'];
		$_SESSION['oauth_token_secret'] = $params['oauth_token_secret'];
		if ( isset( $params['oauth_session_handle'] ) )
			$_SESSION['session_handle'] = $params['oauth_session_handle'];
	}


	/**
	 * Get the OAuth access token and session handle from session
	 *
	 * @return array
	 */
	public static function get_session()
	{
		$response = array();
		if ( isset( $_SESSION['access_token'] ) )
		{
			$response['oauth_token']          = $_SESSION['access_token'];
			$response['oauth_token_secret']   = $_SESSION['oauth_token_secret'];
			$response['oauth_session_handle'] = $_SESSION['session_handle'];
		}

		return $response;
	}

	/**
	 * Generate XML from array for customer to send to Xero
	 *
	 * @param array $info
	 *
	 * @return string
	 */
	public static function build_customer_xml( $info = array() )
	{
		// Get from https://api.xero.com/Preview
		$all_info = '
			<Contacts>
				<Contact>
					<ContactNumber>%s</ContactNumber>
					<Name>%s</Name>
					<ContactStatus>%s</ContactStatus>
					<EmailAddress>%s</EmailAddress>
					<SkypeUserName>%s</SkypeUserName>
					<BankAccountDetails>%s</BankAccountDetails>
					<TaxNumber>%s</TaxNumber>
					<AccountsReceivableTaxType>%s</AccountsReceivableTaxType>
					<AccountsPayableTaxType>%s</AccountsPayableTaxType>
					<FirstName>%s</FirstName>
					<LastName>%s</LastName>
					<DefaultCurrency>%s</DefaultCurrency>
					<Addresses>
						<Address>
							<AddressType>%s</AddressType>
							<AttentionTo>%s</AttentionTo>
							<AddressLine1>%s</AddressLine1>
							<AddressLine2>%s</AddressLine2>
							<AddressLine3>%s</AddressLine3>
							<AddressLine4>%s</AddressLine4>
							<City>%s</City>
							<Region>%s</Region>
							<PostalCode>%s</PostalCode>
							<Country>%s</Country>
						</Address>
					</Addresses>
					<Phones>%s</Phones>
				</Contact>
			</Contacts>
		';

		$phone_info = '
			<Phone>
				<PhoneType>%s</PhoneType>
				<PhoneNumber>%s</PhoneNumber>
				<PhoneAreaCode>%s</PhoneAreaCode>
				<PhoneCountryCode>%s</PhoneCountryCode>
			</Phone>
		';

		$info = array_merge( array(
			'name'         => '',
			'email'        => '',
			'first_name'   => '',
			'last_name'    => '',

			'address_type' => 'POBOX', // Can be 'POBOX' or 'STREET'
			'attention'    => '',
			'address1'     => '',
			'address2'     => '',
			'city'         => '',
			'region'       => '',
			'postcode'     => '',
			'country'      => 'Australia',

			'phones'       => array(),
		), $info );

		$phones = array();
		foreach ( $info['phones'] as $phone )
		{
			$phone    = array_merge( array(
				'phone_type'    => 'DEFAULT', // Can be 'DEFAULT', 'FAX', 'MOBILE', 'DDI'
				'phone'         => '',
				'phone_area'    => '',
				'phone_country' => '',
			), $phone );
			$phones[] = sprintf(
				$phone_info,
				$phone['phone_type'],
				$phone['phone'],
				$phone['phone_area'],
				$phone['phone_country']
			);
		}

		$xml = sprintf(
			$all_info,
			'', // ContactNumber
			$info['name'], // Name
			'', // ContactStatus
			$info['email'], // EmailAddress
			'', // SkypeUserName
			'', // BankAccountDetails
			'', // TaxNumber
			'', // AccountsReceivableTaxType
			'', // AccountsPayableTaxType
			$info['first_name'], // FirstName
			$info['last_name'], // LastName
			'', // DefaultCurrency

			// Address
			$info['address_type'], // AddressType
			$info['attention'], // AttentionTo
			$info['address1'], // AddressLine1
			$info['address2'], // AddressLine2
			'', // AddressLine3
			'', // AddressLine4
			$info['city'], // City
			$info['region'], // Region
			$info['postcode'], // PostalCode
			$info['country'], // Country

			// Phones
			implode( '', $phones )
		);

		// Remove empty elements
		$doc                     = new \DOMDocument;
		$doc->preserveWhiteSpace = false;
		$doc->loadxml( $xml );
		$xpath = new \DOMXPath( $doc );

		foreach ( $xpath->query( '//*[not(node())]' ) as $node )
		{
			$node->parentNode->removeChild( $node );
		}

		$doc->formatOutput = true;
		$xml               = $doc->savexml();

		return $xml;
	}
}
