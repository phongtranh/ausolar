<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Type', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select class="knowledge_graph_type" name="<?php echo THEME_SETTINGS; ?>[knowledge_graph_type]">
			<?php
			Sl_Form::options( sl_setting( 'knowledge_graph_type' ), array(
				'organization' => 'Organization',
				'person'       => 'Person',
			) );
			?>
		</select>
	</div>
</div>
<div class="sl-settings site-logo">
	<div class="sl-label">
		<label><?php _e( 'Logo', '7listings' ); ?></label>
	</div>
	<div class="sl-input logo upload">
		<?php
		$src = '';
		if ( sl_setting( 'knowledge_graph_logo' ) )
		{
			// Show thumb in admin for faster load
			list( $src ) = wp_get_attachment_image_src( sl_setting( 'knowledge_graph_logo' ), 'sl_thumb_tiny' );
		}
		?>
		<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>">
		<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[knowledge_graph_logo]" value="<?php echo sl_setting( 'knowledge_graph_logo' ); ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
	</div>
</div>

<br>

<?php
// Default contact point parameters
$default        = array(
	'phone'    => '',
	'type'     => '',
	'option'   => array(),
	'area'     => array(),
	'language' => '',
);

// Get saved contact points
$contact_points = sl_setting( 'knowledge_graph_contact_points' );
if ( empty( $contact_points ) )
{
	$contact_points = array( $default );
}

// Register list of countries (areas) for contact points
$countries = array(
	'AF' => __( 'Afghanistan', '7listings' ),
	'AX' => __( '&#197;land Islands', '7listings' ),
	'AL' => __( 'Albania', '7listings' ),
	'DZ' => __( 'Algeria', '7listings' ),
	'AD' => __( 'Andorra', '7listings' ),
	'AO' => __( 'Angola', '7listings' ),
	'AI' => __( 'Anguilla', '7listings' ),
	'AQ' => __( 'Antarctica', '7listings' ),
	'AG' => __( 'Antigua and Barbuda', '7listings' ),
	'AR' => __( 'Argentina', '7listings' ),
	'AM' => __( 'Armenia', '7listings' ),
	'AW' => __( 'Aruba', '7listings' ),
	'AU' => __( 'Australia', '7listings' ),
	'AT' => __( 'Austria', '7listings' ),
	'AZ' => __( 'Azerbaijan', '7listings' ),
	'BS' => __( 'Bahamas', '7listings' ),
	'BH' => __( 'Bahrain', '7listings' ),
	'BD' => __( 'Bangladesh', '7listings' ),
	'BB' => __( 'Barbados', '7listings' ),
	'BY' => __( 'Belarus', '7listings' ),
	'BE' => __( 'Belgium', '7listings' ),
	'PW' => __( 'Belau', '7listings' ),
	'BZ' => __( 'Belize', '7listings' ),
	'BJ' => __( 'Benin', '7listings' ),
	'BM' => __( 'Bermuda', '7listings' ),
	'BT' => __( 'Bhutan', '7listings' ),
	'BO' => __( 'Bolivia', '7listings' ),
	'BQ' => __( 'Bonaire, Saint Eustatius and Saba', '7listings' ),
	'BA' => __( 'Bosnia and Herzegovina', '7listings' ),
	'BW' => __( 'Botswana', '7listings' ),
	'BV' => __( 'Bouvet Island', '7listings' ),
	'BR' => __( 'Brazil', '7listings' ),
	'IO' => __( 'British Indian Ocean Territory', '7listings' ),
	'VG' => __( 'British Virgin Islands', '7listings' ),
	'BN' => __( 'Brunei', '7listings' ),
	'BG' => __( 'Bulgaria', '7listings' ),
	'BF' => __( 'Burkina Faso', '7listings' ),
	'BI' => __( 'Burundi', '7listings' ),
	'KH' => __( 'Cambodia', '7listings' ),
	'CM' => __( 'Cameroon', '7listings' ),
	'CA' => __( 'Canada', '7listings' ),
	'CV' => __( 'Cape Verde', '7listings' ),
	'KY' => __( 'Cayman Islands', '7listings' ),
	'CF' => __( 'Central African Republic', '7listings' ),
	'TD' => __( 'Chad', '7listings' ),
	'CL' => __( 'Chile', '7listings' ),
	'CN' => __( 'China', '7listings' ),
	'CX' => __( 'Christmas Island', '7listings' ),
	'CC' => __( 'Cocos (Keeling) Islands', '7listings' ),
	'CO' => __( 'Colombia', '7listings' ),
	'KM' => __( 'Comoros', '7listings' ),
	'CG' => __( 'Congo (Brazzaville)', '7listings' ),
	'CD' => __( 'Congo (Kinshasa)', '7listings' ),
	'CK' => __( 'Cook Islands', '7listings' ),
	'CR' => __( 'Costa Rica', '7listings' ),
	'HR' => __( 'Croatia', '7listings' ),
	'CU' => __( 'Cuba', '7listings' ),
	'CW' => __( 'Cura&Ccedil;ao', '7listings' ),
	'CY' => __( 'Cyprus', '7listings' ),
	'CZ' => __( 'Czech Republic', '7listings' ),
	'DK' => __( 'Denmark', '7listings' ),
	'DJ' => __( 'Djibouti', '7listings' ),
	'DM' => __( 'Dominica', '7listings' ),
	'DO' => __( 'Dominican Republic', '7listings' ),
	'EC' => __( 'Ecuador', '7listings' ),
	'EG' => __( 'Egypt', '7listings' ),
	'SV' => __( 'El Salvador', '7listings' ),
	'GQ' => __( 'Equatorial Guinea', '7listings' ),
	'ER' => __( 'Eritrea', '7listings' ),
	'EE' => __( 'Estonia', '7listings' ),
	'ET' => __( 'Ethiopia', '7listings' ),
	'FK' => __( 'Falkland Islands', '7listings' ),
	'FO' => __( 'Faroe Islands', '7listings' ),
	'FJ' => __( 'Fiji', '7listings' ),
	'FI' => __( 'Finland', '7listings' ),
	'FR' => __( 'France', '7listings' ),
	'GF' => __( 'French Guiana', '7listings' ),
	'PF' => __( 'French Polynesia', '7listings' ),
	'TF' => __( 'French Southern Territories', '7listings' ),
	'GA' => __( 'Gabon', '7listings' ),
	'GM' => __( 'Gambia', '7listings' ),
	'GE' => __( 'Georgia', '7listings' ),
	'DE' => __( 'Germany', '7listings' ),
	'GH' => __( 'Ghana', '7listings' ),
	'GI' => __( 'Gibraltar', '7listings' ),
	'GR' => __( 'Greece', '7listings' ),
	'GL' => __( 'Greenland', '7listings' ),
	'GD' => __( 'Grenada', '7listings' ),
	'GP' => __( 'Guadeloupe', '7listings' ),
	'GT' => __( 'Guatemala', '7listings' ),
	'GG' => __( 'Guernsey', '7listings' ),
	'GN' => __( 'Guinea', '7listings' ),
	'GW' => __( 'Guinea-Bissau', '7listings' ),
	'GY' => __( 'Guyana', '7listings' ),
	'HT' => __( 'Haiti', '7listings' ),
	'HM' => __( 'Heard Island and McDonald Islands', '7listings' ),
	'HN' => __( 'Honduras', '7listings' ),
	'HK' => __( 'Hong Kong', '7listings' ),
	'HU' => __( 'Hungary', '7listings' ),
	'IS' => __( 'Iceland', '7listings' ),
	'IN' => __( 'India', '7listings' ),
	'ID' => __( 'Indonesia', '7listings' ),
	'IR' => __( 'Iran', '7listings' ),
	'IQ' => __( 'Iraq', '7listings' ),
	'IE' => __( 'Republic of Ireland', '7listings' ),
	'IM' => __( 'Isle of Man', '7listings' ),
	'IL' => __( 'Israel', '7listings' ),
	'IT' => __( 'Italy', '7listings' ),
	'CI' => __( 'Ivory Coast', '7listings' ),
	'JM' => __( 'Jamaica', '7listings' ),
	'JP' => __( 'Japan', '7listings' ),
	'JE' => __( 'Jersey', '7listings' ),
	'JO' => __( 'Jordan', '7listings' ),
	'KZ' => __( 'Kazakhstan', '7listings' ),
	'KE' => __( 'Kenya', '7listings' ),
	'KI' => __( 'Kiribati', '7listings' ),
	'KW' => __( 'Kuwait', '7listings' ),
	'KG' => __( 'Kyrgyzstan', '7listings' ),
	'LA' => __( 'Laos', '7listings' ),
	'LV' => __( 'Latvia', '7listings' ),
	'LB' => __( 'Lebanon', '7listings' ),
	'LS' => __( 'Lesotho', '7listings' ),
	'LR' => __( 'Liberia', '7listings' ),
	'LY' => __( 'Libya', '7listings' ),
	'LI' => __( 'Liechtenstein', '7listings' ),
	'LT' => __( 'Lithuania', '7listings' ),
	'LU' => __( 'Luxembourg', '7listings' ),
	'MO' => __( 'Macao S.A.R., China', '7listings' ),
	'MK' => __( 'Macedonia', '7listings' ),
	'MG' => __( 'Madagascar', '7listings' ),
	'MW' => __( 'Malawi', '7listings' ),
	'MY' => __( 'Malaysia', '7listings' ),
	'MV' => __( 'Maldives', '7listings' ),
	'ML' => __( 'Mali', '7listings' ),
	'MT' => __( 'Malta', '7listings' ),
	'MH' => __( 'Marshall Islands', '7listings' ),
	'MQ' => __( 'Martinique', '7listings' ),
	'MR' => __( 'Mauritania', '7listings' ),
	'MU' => __( 'Mauritius', '7listings' ),
	'YT' => __( 'Mayotte', '7listings' ),
	'MX' => __( 'Mexico', '7listings' ),
	'FM' => __( 'Micronesia', '7listings' ),
	'MD' => __( 'Moldova', '7listings' ),
	'MC' => __( 'Monaco', '7listings' ),
	'MN' => __( 'Mongolia', '7listings' ),
	'ME' => __( 'Montenegro', '7listings' ),
	'MS' => __( 'Montserrat', '7listings' ),
	'MA' => __( 'Morocco', '7listings' ),
	'MZ' => __( 'Mozambique', '7listings' ),
	'MM' => __( 'Myanmar', '7listings' ),
	'NA' => __( 'Namibia', '7listings' ),
	'NR' => __( 'Nauru', '7listings' ),
	'NP' => __( 'Nepal', '7listings' ),
	'NL' => __( 'Netherlands', '7listings' ),
	'AN' => __( 'Netherlands Antilles', '7listings' ),
	'NC' => __( 'New Caledonia', '7listings' ),
	'NZ' => __( 'New Zealand', '7listings' ),
	'NI' => __( 'Nicaragua', '7listings' ),
	'NE' => __( 'Niger', '7listings' ),
	'NG' => __( 'Nigeria', '7listings' ),
	'NU' => __( 'Niue', '7listings' ),
	'NF' => __( 'Norfolk Island', '7listings' ),
	'KP' => __( 'North Korea', '7listings' ),
	'NO' => __( 'Norway', '7listings' ),
	'OM' => __( 'Oman', '7listings' ),
	'PK' => __( 'Pakistan', '7listings' ),
	'PS' => __( 'Palestinian Territory', '7listings' ),
	'PA' => __( 'Panama', '7listings' ),
	'PG' => __( 'Papua New Guinea', '7listings' ),
	'PY' => __( 'Paraguay', '7listings' ),
	'PE' => __( 'Peru', '7listings' ),
	'PH' => __( 'Philippines', '7listings' ),
	'PN' => __( 'Pitcairn', '7listings' ),
	'PL' => __( 'Poland', '7listings' ),
	'PT' => __( 'Portugal', '7listings' ),
	'QA' => __( 'Qatar', '7listings' ),
	'RE' => __( 'Reunion', '7listings' ),
	'RO' => __( 'Romania', '7listings' ),
	'RU' => __( 'Russia', '7listings' ),
	'RW' => __( 'Rwanda', '7listings' ),
	'BL' => __( 'Saint Barth&eacute;lemy', '7listings' ),
	'SH' => __( 'Saint Helena', '7listings' ),
	'KN' => __( 'Saint Kitts and Nevis', '7listings' ),
	'LC' => __( 'Saint Lucia', '7listings' ),
	'MF' => __( 'Saint Martin (French part)', '7listings' ),
	'SX' => __( 'Saint Martin (Dutch part)', '7listings' ),
	'PM' => __( 'Saint Pierre and Miquelon', '7listings' ),
	'VC' => __( 'Saint Vincent and the Grenadines', '7listings' ),
	'SM' => __( 'San Marino', '7listings' ),
	'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', '7listings' ),
	'SA' => __( 'Saudi Arabia', '7listings' ),
	'SN' => __( 'Senegal', '7listings' ),
	'RS' => __( 'Serbia', '7listings' ),
	'SC' => __( 'Seychelles', '7listings' ),
	'SL' => __( 'Sierra Leone', '7listings' ),
	'SG' => __( 'Singapore', '7listings' ),
	'SK' => __( 'Slovakia', '7listings' ),
	'SI' => __( 'Slovenia', '7listings' ),
	'SB' => __( 'Solomon Islands', '7listings' ),
	'SO' => __( 'Somalia', '7listings' ),
	'ZA' => __( 'South Africa', '7listings' ),
	'GS' => __( 'South Georgia/Sandwich Islands', '7listings' ),
	'KR' => __( 'South Korea', '7listings' ),
	'SS' => __( 'South Sudan', '7listings' ),
	'ES' => __( 'Spain', '7listings' ),
	'LK' => __( 'Sri Lanka', '7listings' ),
	'SD' => __( 'Sudan', '7listings' ),
	'SR' => __( 'Suriname', '7listings' ),
	'SJ' => __( 'Svalbard and Jan Mayen', '7listings' ),
	'SZ' => __( 'Swaziland', '7listings' ),
	'SE' => __( 'Sweden', '7listings' ),
	'CH' => __( 'Switzerland', '7listings' ),
	'SY' => __( 'Syria', '7listings' ),
	'TW' => __( 'Taiwan', '7listings' ),
	'TJ' => __( 'Tajikistan', '7listings' ),
	'TZ' => __( 'Tanzania', '7listings' ),
	'TH' => __( 'Thailand', '7listings' ),
	'TL' => __( 'Timor-Leste', '7listings' ),
	'TG' => __( 'Togo', '7listings' ),
	'TK' => __( 'Tokelau', '7listings' ),
	'TO' => __( 'Tonga', '7listings' ),
	'TT' => __( 'Trinidad and Tobago', '7listings' ),
	'TN' => __( 'Tunisia', '7listings' ),
	'TR' => __( 'Turkey', '7listings' ),
	'TM' => __( 'Turkmenistan', '7listings' ),
	'TC' => __( 'Turks and Caicos Islands', '7listings' ),
	'TV' => __( 'Tuvalu', '7listings' ),
	'UG' => __( 'Uganda', '7listings' ),
	'UA' => __( 'Ukraine', '7listings' ),
	'AE' => __( 'United Arab Emirates', '7listings' ),
	'GB' => __( 'United Kingdom (UK)', '7listings' ),
	'US' => __( 'United States (US)', '7listings' ),
	'UY' => __( 'Uruguay', '7listings' ),
	'UZ' => __( 'Uzbekistan', '7listings' ),
	'VU' => __( 'Vanuatu', '7listings' ),
	'VA' => __( 'Vatican', '7listings' ),
	'VE' => __( 'Venezuela', '7listings' ),
	'VN' => __( 'Vietnam', '7listings' ),
	'WF' => __( 'Wallis and Futuna', '7listings' ),
	'EH' => __( 'Western Sahara', '7listings' ),
	'WS' => __( 'Western Samoa', '7listings' ),
	'YE' => __( 'Yemen', '7listings' ),
	'ZM' => __( 'Zambia', '7listings' ),
	'ZW' => __( 'Zimbabwe', '7listings' )
);
foreach ( $contact_points as $index => $contact_point )
{
	// Make sure all params exist
	$contact_point = array_merge( $default, $contact_point );
	include( 'template-phone.php' );
}
?>
<div class="sl-settings">
	<div class="sl-label">
		<label>&nbsp;</label>
	</div>
	<div class="sl-input">
		<a href="#" id="add-contact-point" class="button" title="<?php _e( 'Add a phone number', '7listings' ); ?>"><?php _e( 'Add Phone Number', '7listings' ); ?></a>
	</div>
</div>

<br><br><br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Contact Info', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter more contact info Knowledge Graph', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<a href="<?php $url = admin_url(); ?>edit.php?post_type=page&page=contact" class="button"><?php _e( 'Contact details', '7listings' ); ?></a>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Social Media', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter social media profiles for Knowledge Graph', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<a href="<?php $url = admin_url(); ?>admin.php?page=social#settings" class="button"><?php _e( 'Social profiles', '7listings' ); ?></a>
	</div>
</div>

