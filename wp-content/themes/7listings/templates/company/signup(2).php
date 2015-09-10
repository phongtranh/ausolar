<?php
if ( is_user_logged_in() )
{
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
	{
		echo '<div class="alert">' . __( 'You do not own a Company, please list your Company now!.', '7listings' ) . '</div>';
	}
	else
	{
		printf( '<div class="alert alert-error">' . __( 'You already own a company, consider <a href="%s">edit company details here</a>.', '7listings' ) . '</div>', get_permalink( sl_setting( 'company_page_edit' ) ) );

		return;
	}
}
?>

<form action="" method="post" enctype="multipart/form-data" class="company-form signup" id="signup-form">

<div id="ajax-message"></div>

<?php if ( ! is_user_logged_in() ) : ?>

	<h2><?php _e( 'User Details', '7listings' ); ?></h2>
	<div class="user-details">
		<div class="row-fluid">
			<div class="span6">
				<label><?php _e( 'Username', '7listings' ); ?> <span class="required">*</span></label>
				<input type="text" name="username" required class="span12">
			</div>
			<div class="span6">
				<label><?php _e( 'Password', '7listings' ); ?> <span class="required">*</span></label>
				<input type="password" name="password" required class="span12">
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<label><?php _e( 'Name', '7listings' ); ?> <span class="required">*</span></label>
				<input required type="text" name="first_name" class="span12">
				<span class="help-block"><?php _e( 'First', '7listings' ); ?></span>
			</div>
			<div class="span6">
				<label>&nbsp;</label> <input required type="text" name="last_name" class="span12">
				<span class="help-block"><?php _e( 'Last', '7listings' ); ?></span>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<label><?php _e( 'Email', '7listings' ); ?> <span class="required">*</span></label>
				<input type="email" name="user_email" required class="span12">
				<span class="help-block"><?php _e( 'Enter Email', '7listings' ); ?></span>
			</div>
			<div class="span6">
				<label>&nbsp;</label> <input type="email" name="user_email_confirm" required class="span12">
				<span class="help-block"><?php _e( 'Confirm Email', '7listings' ); ?></span>
			</div>
		</div>
	</div>

	<?php
	if ( sl_setting( 'company_membership_display' ) )
	{
		echo '<h2>' . __( 'Membership Type', '7listings' ) . '</h2>';
		echo '<div class="membership-type">';

		$types = array(
			'gold'   => __( 'Gold', '7listings' ),
			'silver' => __( 'Silver', '7listings' ),
			'bronze' => __( 'Bronze', '7listings' ),
		);
		foreach ( $types as $k => $v )
		{
			if ( ! sl_setting( "company_membership_$k" ) )
				continue;

			$price_month = sl_setting( "company_membership_price_$k" );
			$price_year  = sl_setting( "company_membership_price_year_$k" );

			if ( ( '' === $price_month || false === $price_month ) && ( '' === $price_year || false === $price_year ) )
				continue;

			if ( ! empty( $price_month ) )
				$price_month = sprintf( __( '$%s/month', '7listings' ), $price_month );
			elseif ( '' !== $price_month && false !== $price_month )
				$price_month = __( 'FREE', '7listings' );

			if ( ! empty( $price_year ) )
				$price_year = sprintf( __( '$%s/year', '7listings' ), $price_year );
			elseif ( '' !== $price_year && false !== $price_year )
				$price_year = __( 'FREE', '7listings' );

			if ( '' !== $price_month && false !== $price_month )
			{
				printf(
					'<label class="radio inline %1$s"><input type="radio" name="membership" value="%1$s,month"> %2$s</label>',
					$k,
					sprintf( __( '%s (%s)', '7listings' ), $v, $price_month )
				);
			}

			if ( '' !== $price_year && false !== $price_year )
			{
				printf(
					'<label class="radio inline %1$s"><input type="radio" name="membership" value="%1$s,year"> %2$s</label>',
					$k,
					sprintf( __( '%s (%s)', '7listings' ), $v, $price_year )
				);
			}
		}

		echo '</div>';
	}
	?>

<?php endif; ?>

<h2><?php _e( 'Company Details', '7listings' ); ?></h2>

<div class="row-fluid">
	<div class="span9">
		<label><?php _e( 'Company Name', '7listings' ); ?> <span class="required">*</span></label>
		<input type="text" name="post_title" required class="span8 title">
		<br>
		<label class="description-label"><?php _e( 'Description', '7listings' ); ?>
			<span class="required">*</span></label>
		<textarea name="post_content" class="span12 description-input"></textarea>
	</div>
	<div class="span3">
		<label><?php _e( 'Logo', '7listings' ); ?></label>
		<input type="file" name="<?php echo sl_meta_key( 'logo', 'company' ); ?>" onchange="preview();" id="logo-upload">
		<img id="logo-preview" class="photo" src="">
		<script>
			function preview()
			{
				var reader = new FileReader();
				reader.readAsDataURL( document.getElementById( 'logo-upload' ).files[0] );
				reader.onload = function ( e )
				{
					document.getElementById( 'logo-preview' ).src = e.target.result;
				};
			}
		</script>
	</div>
</div>

<br>

<div class="row-fluid">
<div class="span6 address-inputs">

<label><?php _e( 'Address', '7listings' ); ?></label>
<input type="text" name="address" class="span12 no-highlight">
<span class="help-block"><?php _e( 'Street Address', '7listings' ); ?></span>

<input type="text" name="address2" class="span12 no-highlight">
<span class="help-block"><?php _e( 'Street Address 2', '7listings' ); ?></span>

<div class="row-fluid">
	<div class="span6">
		<input type="text" name="city" class="span12 location-autocomplete no-highlight">
		<span class="help-block"><?php _e( 'City', '7listings' ); ?></span>
	</div>
	<div class="span6">
		<input type="text" name="state" class="span12 location-autocomplete no-highlight">
		<span class="help-block"><?php _e( 'State / Province / Region', '7listings' ); ?></span>
	</div>
</div>

<div class="row-fluid">
<div class="span6">
	<input type="text" name="postcode" class="span12 no-highlight">
	<span class="help-block"><?php _e( 'Zip Postal Code', '7listings' ); ?></span>
</div>
<div class="span6">
<select name="country" class="span12 no-highlight">
<option value=""><?php _e( 'Select', '7listings' ); ?></option>
<?php
$countries = array(
	'Afghanistan',
	'Albania',
	'Algeria',
	'American Samoa',
	'Andorra',
	'Angola',
	'Antigua and Barbuda',
	'Argentina',
	'Armenia',
	'Australia',
	'Austria',
	'Azerbaijan',
	'Bahamas',
	'Bahrain',
	'Bangladesh',
	'Barbados',
	'Belarus',
	'Belgium',
	'Belize',
	'Benin',
	'Bermuda',
	'Bhutan',
	'Bolivia',
	'Bosnia and Herzegovina',
	'Botswana',
	'Brazil',
	'Brunei',
	'Bulgaria',
	'Burkina Faso',
	'Burundi',
	'Cambodia',
	'Cameroon',
	'Canada',
	'Cape Verde',
	'Cayman Islands',
	'Central African Republic',
	'Chad',
	'Chile',
	'China',
	'Colombia',
	'Comoros',
	'Congo, Democratic Republic of the',
	'Congo, Republic of the',
	'Costa Rica',
	'Cote d\'Ivoire',
	'Croatia',
	'Cuba',
	'Cyprus',
	'Czech Republic',
	'Denmark',
	'Djibouti',
	'Dominica',
	'Dominican Republic',
	'East Timor',
	'Ecuador',
	'Egypt',
	'El Salvador',
	'Equatorial Guinea',
	'Eritrea',
	'Estonia',
	'Ethiopia',
	'Fiji',
	'Finland',
	'France',
	'Gabon',
	'Gambia',
	'Georgia',
	'Germany',
	'Ghana',
	'Greece',
	'Greenland',
	'Grenada',
	'Guam',
	'Guatemala',
	'Guinea',
	'Guinea-Bissau',
	'Guyana',
	'Haiti',
	'Honduras',
	'Hong Kong',
	'Hungary',
	'Iceland',
	'India',
	'Indonesia',
	'Iran',
	'Iraq',
	'Ireland',
	'Israel',
	'Italy',
	'Jamaica',
	'Japan',
	'Jordan',
	'Kazakhstan',
	'Kenya',
	'Kiribati',
	'North Korea',
	'South Korea',
	'Kuwait',
	'Kyrgyzstan',
	'Laos',
	'Latvia',
	'Lebanon',
	'Lesotho',
	'Liberia',
	'Libya',
	'Liechtenstein',
	'Lithuania',
	'Luxembourg',
	'Macedonia',
	'Madagascar',
	'Malawi',
	'Malaysia',
	'Maldives',
	'Mali',
	'Malta',
	'Marshall Islands',
	'Mauritania',
	'Mauritius',
	'Mexico',
	'Micronesia',
	'Moldova',
	'Monaco',
	'Mongolia',
	'Montenegro',
	'Morocco',
	'Mozambique',
	'Myanmar',
	'Namibia',
	'Nauru',
	'Nepal',
	'Netherlands',
	'New Zealand',
	'Nicaragua',
	'Niger',
	'Nigeria',
	'Norway',
	'Northern Mariana Islands',
	'Oman',
	'Pakistan',
	'Palau',
	'Palestine',
	'Panama',
	'Papua New Guinea',
	'Paraguay',
	'Peru',
	'Philippines',
	'Poland',
	'Portugal',
	'Puerto Rico',
	'Qatar',
	'Romania',
	'Russia',
	'Rwanda',
	'Saint Kitts and Nevis',
	'Saint Lucia',
	'Saint Vincent and the Grenadines',
	'Samoa',
	'San Marino',
	'Sao Tome and Principe',
	'Saudi Arabia',
	'Senegal',
	'Serbia and Montenegro',
	'Seychelles',
	'Sierra Leone',
	'Singapore',
	'Slovakia',
	'Slovenia',
	'Solomon Islands',
	'Somalia',
	'South Africa',
	'Spain',
	'Sri Lanka',
	'Sudan',
	'Sudan, South',
	'Suriname',
	'Swaziland',
	'Sweden',
	'Switzerland',
	'Syria',
	'Taiwan',
	'Tajikistan',
	'Tanzania',
	'Thailand',
	'Togo',
	'Tonga',
	'Trinidad and Tobago',
	'Tunisia',
	'Turkey',
	'Turkmenistan',
	'Tuvalu',
	'Uganda',
	'Ukraine',
	'United Arab Emirates',
	'United Kingdom',
	'United States',
	'Uruguay',
	'Uzbekistan',
	'Vanuatu',
	'Vatican City',
	'Venezuela',
	'Vietnam',
	'Virgin Islands, British',
	'Virgin Islands, U.S.',
	'Yemen',
	'Zambia',
	'Zimbabwe',
);
foreach ( $countries as $country )
{
	printf( '<option value="%1$s">%1$s</option>', $country );
}
?>
</select>
<span class="help-block"><?php _e( 'Country', '7listings' ); ?></span>
</div>
</div>

</div>
<!-- .span6 -->

<div class="span5 offset1 contact-inputs">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Website', '7listings' ); ?></label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class="icon-globe"></i></span>
				<input type="text" name="website" class="span12 no-highlight">
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Company Email', '7listings' ); ?></label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class="icon-envelope-alt"></i></span>
				<input type="email" name="email" class="span8 no-highlight">
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Phone', '7listings' ); ?></label>
		<div class="controls">
			<div class="input-prepend">
				<span class="add-on"><i class="icon-phone"></i></span>
				<input type="text" name="phone" class="span8 no-highlight">
			</div>
		</div>
	</div>
</div>
<!-- .span6 -->
</div>
<!-- .row-fluid -->

<?php do_action( 'company_signup_form_after' ); ?>

<div class="submit">
	<input type="submit" name="submit" class="button booking large" value="<?php _e( 'Sign Up', '7listings' ); ?>">
</div>
</form>
