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
		get_template_part( 'templates/company/user-admin/no-company' );

		return;
	}
	$company = current( $company );
}
else
{
	get_template_part( 'templates/company/user-admin/form-login' );

	return;
}

$address  = get_post_meta( $company->ID, 'address', true );
$address2 = get_post_meta( $company->ID, 'address2', true );
$postcode = get_post_meta( $company->ID, 'postcode', true );

$state = get_post_meta( $company->ID, 'state', true );
$city  = get_post_meta( $company->ID, 'city', true );
$area  = get_post_meta( $company->ID, 'area', true );

$latitude  = get_post_meta( $company->ID, 'latitude', true );
$longitude = get_post_meta( $company->ID, 'longitude', true );
?>
<form action="" method="post" enctype="multipart/form-data" class="company-form">

	<?php wp_nonce_field( 'edit-company' ) ?>
	<?php wp_nonce_field( 'save-post-company', 'sl_nonce_save_company' ) ?>

	<?php
	global $errors;
	if ( ! empty( $errors ) )
		echo '<div class="alert alert-error">' . implode( '<br>', $errors ) . '</div>';
	elseif ( isset( $_GET['updated'] ) )
		echo '<div class="alert alert-success">' . __( 'Company has been updated successfully.', '7listings' ) . '</div>';
	?>

	<input type="hidden" name="post_id" value="<?php echo $company->ID; ?>">
	<input type="hidden" name="user" value="<?php echo get_current_user_id(); ?>">

	<div class="row-fluid">
		<div class="span9">
			<label><?php _e( 'Company Name', '7listings' ); ?> <span class="required">*</span></label>
			<input required disabled type="text" name="post_title" value="<?php echo get_post_field( 'post_title', $company ); ?>" class="span8 title">
			<br> <label class="description-label"><?php _e( 'Description', '7listings' ); ?>
				<span class="required">*</span></label>
			<textarea required name="post_content" class="span12 description-input"><?php echo esc_textarea( get_post_field( 'post_content', $company ) ); ?></textarea>
		</div>
		<div class="span3">
			<label><?php _e( 'Logo', '7listings' ); ?></label>
			<input type="file" name="<?php echo sl_meta_key( 'logo', 'company' ); ?>" onchange="preview();" id="logo-upload">
			<img id="logo-preview" class="photo" src="">
			<?php echo get_the_post_thumbnail( $company->ID, 'full' ); ?>
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

	<h2><?php _e( 'Location & Contact', '7listings' ); ?></h2>

	<div class="row-fluid">
		<div class="span6 address-inputs">

			<label><?php _e( 'Address', '7listings' ); ?></label>
			<input type="text" name="address" value="<?php echo $address; ?>" class="span12">
			<span class="help-block"><?php _e( 'Street Address', '7listings' ); ?></span>

			<input type="text" name="address2" value="<?php echo $address2; ?>" class="span12 no-highlight">
			<span class="help-block"><?php _e( 'Street Address 2', '7listings' ); ?></span>

			<div class="row-fluid">
				<div class="span6">
					<input type="text" name="city" value="<?php echo $city; ?>" class="span12 location-autocomplete">
					<span class="help-block"><?php _e( 'City', '7listings' ); ?></span>
				</div>
				<div class="span6">
					<input type="text" name="state" value="<?php echo $state; ?>" class="span12 location-autocomplete">
					<span class="help-block"><?php _e( 'State / Province / Region', '7listings' ); ?></span>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<input type="text" name="postcode" value="<?php echo $postcode; ?>" class="span12">
					<span class="help-block"><?php _e( 'Zip Postal Code', '7listings' ); ?></span>
				</div>
				<div class="span6">
					<select name="country" class="span12">
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
							printf( '<option value="%1$s"%2$s>%1$s</option>', $country, selected( $country, get_post_meta( $company->ID, 'country', true ), 0 ) );
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
						<input type="text" name="website" value="<?php echo get_post_meta( $company->ID, 'website', true ); ?>" class="span12">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php _e( 'Company Email', '7listings' ); ?></label>

				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-envelope-alt"></i></span>
						<input type="email" name="email" value="<?php echo get_post_meta( $company->ID, 'email', true ); ?>" class="span12">
					</div>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label"><?php _e( 'Phone', '7listings' ); ?></label>

				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-phone"></i></span>
						<input type="text" name="phone" value="<?php echo get_post_meta( $company->ID, 'phone', true ); ?>" class="span8">
					</div>
				</div>
			</div>
		</div>
		<!-- .span6 -->
	</div>
	<!-- .row-fluid -->

	<h2><?php _e( 'Business Hours', '7listings' ); ?></h2>

	<div class="form-horizontal">
		<?php
		$days = array(
			'mon' => __( 'Monday', '7listings' ),
			'tue' => __( 'Tuesday', '7listings' ),
			'wed' => __( 'Wednesday', '7listings' ),
			'thu' => __( 'Thursday', '7listings' ),
			'fri' => __( 'Friday', '7listings' ),
			'sat' => __( 'Saturday', '7listings' ),
			'sun' => __( 'Sunday', '7listings' ),
		);
		foreach ( $days as $k => $v )
		{
			?>
			<div class="control-group">
				<label class="control-label"><?php echo $v; ?></label>
				<div class="controls">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox_general( "business_hours_$k", get_post_meta( $company->ID, "business_hours_$k", true ) ); ?>
				</span>
				<span>
					<span class="input-prepend">
						<span class="add-on"><?php _e( 'From', '7listings' ); ?></span>
						<input type="text" name="business_hours_<?php echo $k; ?>_from" class="hours timepicker" value="<?php echo get_post_meta( $company->ID, "business_hours_{$k}_from", true ); ?>">
					</span>&nbsp;&nbsp;
					<span class="input-prepend">
						<span class="add-on"><?php _e( 'To', '7listings' ); ?></span>
						<input type="text" name="business_hours_<?php echo $k; ?>_to" class="hours timepicker" value="<?php echo get_post_meta( $company->ID, "business_hours_{$k}_to", true ); ?>">
					</span>
				</span>
				</div>
			</div>
		<?php
		}
		?>
	</div>

	<h2><?php _e( 'Service Area', '7listings' ); ?></h2>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Service Area', '7listings' ); ?></label>
			<div class="controls">
				<select name="service_radius">
					<?php
					Sl_Form::options( get_post_meta( $company->ID, 'service_radius', true ), array(
						'radius'    => __( 'Radius', '7listings' ),
						'postcodes' => __( 'Postcodes', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="control-group service-postcodes">
			<label class="control-label"><?php _e( 'Postcodes', '7listings' ); ?></label>
			<div class="controls">
				<textarea name="service_postcodes" class="input-xxlarge" rows="3"><?php echo esc_textarea( get_post_meta( $company->ID, 'service_postcodes', true ) ); ?></textarea>
				<p class="input-hint"><?php _e( 'Enter postcodes separated by commas', '7listings' ); ?></p>
			</div>
		</div>
		<div class="control-group service-radius">
			<label class="control-label"><?php _e( 'From Office', '7listings' ); ?></label>
			<div class="controls">
			<span class="input-append">
        <input type="number" name="leads_service_radius" id="service-radius" value="<?php echo get_post_meta( $company->ID, 'leads_service_radius', true ); ?>">
        <span class="add-on">km</span>
    </span>
			</div>
		</div>
	</div>

	<?php
	$args = array(
		'height' => '350px',
	);

	if ( $latitude && $longitude )
	{
		$args = array_merge( $args, array(
			'type'      => 'latlng',
			'latitude'  => $latitude,
			'longitude' => $longitude,
			'class'     => 'map clearfix',
		) );
	}
	else
	{
		$args = array_merge( $args, array(
			'type'    => 'address',
			'address' => "$address, $city, $postcode",
			'class'   => 'map',
		) );
	}

	if ( get_post_meta( $company->ID, 'leads_service_radius', true ) )
		$args['js_callback'] = 'SlCompanyRadiusCallback';

	sl_map( $args );
	?>

	<h2><?php _e( 'Social Media', '7listings' ); ?></h2>

	<div class="row-fluid social-inputs">
		<?php
		$tpl     = '
			<div class="span4">
				<label>%2$s</label>
				<span class="input-prepend">
					<span class="add-on"><i class="icon-%4$s"></i></span>
					<input type="text" name="%1$s" value="%3$s" class="span12">
				</span>
			</div>';
		$socials = array(
			'facebook'   => 'Facebook',
			'twitter'    => 'Twitter',
			'googleplus' => 'Google+',
			'pinterest'  => 'Pinterest',
			'linkedin'   => 'LinkedIn',
			'instagram'  => 'Instagram',
			'rss'        => 'RSS',
		);
		foreach ( $socials as $k => $v )
		{
			$icon = $k == 'googleplus' ? 'google-plus' : $k;
			printf( $tpl, $k, $v, get_post_meta( $company->ID, $k, true ), $icon );
		}
		?>
	</div>

	<div class="row-fluid">
		<div class="span4">
			<h2><?php _e( 'Products', '7listings' ); ?></h2>

			<div class="company-products">
				<?php
				$terms    = get_terms( 'company_product', array( 'hide_empty' => 0 ) );
				$products = wp_get_post_terms( $company->ID, 'company_product', array( 'fields' => 'ids' ) );
				foreach ( $terms as $term )
				{
					printf(
						'<label class="checkbox"><input type="checkbox" name="products[]" value="%s"%s> %s</label>',
						$term->term_id,
						checked( in_array( $term->term_id, $products ), 1, 0 ),
						$term->name
					);
				}
				?>
			</div>
		</div>

		<div class="span4">
			<h2><?php _e( 'Services', '7listings' ); ?></h2>

			<div class="company-services">
				<?php
				$terms    = get_terms( 'company_service', array( 'hide_empty' => 0 ) );
				$services = wp_get_post_terms( $company->ID, 'company_service', array( 'fields' => 'ids' ) );
				foreach ( $terms as $term )
				{
					printf(
						'<label class="checkbox"><input type="checkbox" name="services[]" value="%s"%s> %s</label>',
						$term->term_id,
						checked( in_array( $term->term_id, $services ), 1, 0 ),
						$term->name
					);
				}
				?>
			</div>
		</div>

		<div class="span4">
			<h2><?php _e( 'Brands', '7listings' ); ?></h2>

			<div class="company-brand">
				<?php
				$terms  = get_terms( 'brand', array( 'hide_empty' => 0 ) );
				$brands = wp_get_post_terms( $company->ID, 'brand', array( 'fields' => 'ids' ) );
				foreach ( $terms as $term )
				{
					printf(
						'<label class="checkbox"><input type="checkbox" name="brands[]" value="%s"%s> %s</label>',
						$term->term_id,
						checked( in_array( $term->term_id, $brands ), 1, 0 ),
						$term->name
					);
				}
				?>
			</div>
		</div>
	</div>

	<?php do_action( 'company_edit_form_after', $company ); ?>

	<div class="submit">
		<input type="submit" name="submit" class="button booking large" value="<?php _e( 'Update Details', '7listings' ); ?>">
	</div>
</form>
