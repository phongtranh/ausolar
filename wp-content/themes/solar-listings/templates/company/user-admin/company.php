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

$address  	= get_post_meta( $company->ID, 'address', true );
$address2 	= get_post_meta( $company->ID, 'address2', true );
$postcode 	= get_post_meta( $company->ID, 'postcode', true );
$state 		= get_post_meta( $company->ID, 'state', true );
$city  		= get_post_meta( $company->ID, 'city', true );
$area  		= get_post_meta( $company->ID, 'area', true );
$latitude  	= get_post_meta( $company->ID, 'latitude', true );
$longitude 	= get_post_meta( $company->ID, 'longtitude', true );
?>
<form action="" method="post" enctype="multipart/form-data" class="company-form">

<?php wp_nonce_field( 'edit-company' ) ?>
<?php wp_nonce_field( 'save-post-company', 'sl_nonce_save_company' ) ?>

<?php
global $errors;
if ( !empty( $errors ) )
	echo '<div class="alert alert-error">' . implode( '<br>', $errors ) . '</div>';
elseif ( isset( $_GET['updated'] ) )
	echo '<div class="alert alert-success">' . __( 'Company has been updated successfully.', '7listings' ) . '</div>';
?>

<input type="hidden" name="post_id" value="<?php echo $company->ID; ?>">
<input type="hidden" name="user" value="<?php echo get_current_user_id(); ?>">

<div class="row-fluid">
	<div class="span9">
		<label><?php _e( 'Trading Name', '7listings' ); ?> <span class="required">*</span></label>
		<input required disabled type="text" name="post_title" value="<?php echo get_post_field( 'post_title', $company ); ?>" class="span8 title">
		<br> <label class="description-label"><?php _e( 'Description', '7listings' ); ?> <span class="required">*</span></label>
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
<?php
$my_account = sl_setting( 'company_page_dashboard' );
?>
<div class="row-fluid">
    <div class="span12">
        <a href="<?php echo get_permalink( $my_account ) . 'order-content'; ?>" target="_blank" >
            <img class="logo-ad" src="<?php echo get_stylesheet_directory_uri() . '/images/Order-company-description.jpg'; ?>" />
        </a>
    </div>
</div>

<div class="row-fluid">
    <div class="span5">
        <a class="order-content button large" href="<?php echo get_permalink( $my_account ) . 'order-content'; ?>" target="_blank"><?php _e( 'Order now', '7listings' ); ?></a>
    </div>
</div>

<?php
$name = get_post_meta( $company->ID, 'trading_name', true );
$abn = get_post_meta( $company->ID, 'company_abn', true );
?>

<div class="row-fluid">
	<div class="span6">
		<label><?php _e( 'Company Name', '7listings' ); ?> <span class="required">*</span></label>
		<input required type="text" name="trading_name" value="<?php echo $name; ?>" class="span12">
	</div>
	<div class="span5 offset1">
		<label><?php _e( 'ABN', '7listings' ); ?> <span class="required">*</span></label>
		<input required type="number" name="company_abn" value="<?php echo $abn; ?>">
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<label><?php _e( 'What best describes your company', '7listings' ); ?></label>
		<select name="range">
			<?php
			$selected = get_post_meta( $company->ID, 'range', true );
			if ( ! $selected )
				$selected = 'local';
			SL_Form::options( $selected, array(
				'nationwide' => __( 'Nationwide', '7listings' ),
				'statewide'  => __( 'Statewide', '7listings' ),
				'local'      => __( 'Local', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>

<h2><?php _e( 'Location & Contact', '7listings' ); ?></h2>

<div class="row-fluid">
	<div class="span6 address-inputs">

		<label><?php _e( 'Address', '7listings' ); ?></label>
		<input type="text" name="address" value="<?php echo $address; ?>" class="span12">
		<span class="help-block"><?php _e( 'Level or unit and Building Name', '7listings' ); ?></span>

		<input type="text" name="address2" value="<?php echo $address2; ?>" class="span12 no-highlight">
		<span class="help-block"><?php _e( 'Street Address', '7listings' ); ?></span>
		
		<div class="row-fluid">
			<div class="span6">
				<input type="number" name="postcode" min="0" max="9999" id="auto_fill_postcode" value="<?php echo $postcode; ?>" class="span12">
				<span class="help-block"><?php _e( 'Zip Postal Code', '7listings' ); ?></span>
			</div>
			<div class="span6">
				<input type="text" name="state" id="auto_fill_state" value="<?php echo $state ?>" class="span12" readonly>
				<?php
					// $states = \ASQ\Location\Location::all_states();
					// $options = array();
					// foreach ( $states as $single_state )
					// 	$options[$single_state['name']] = $single_state['name'];

					// echo '<select name="state" id="auto_fill_state" class="span12" readonly>';
					// Sl_Form::options( $state, $options );
					// echo '</select>';
				?>
				<span class="help-block"><?php _e( 'State / Province / Region', '7listings' ); ?></span>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span6">
				<select id="auto_fill_suburb" name="area" class="span12">
					<?php 
					if ( ! empty( $postcode ) ) : 
						$suburbs = \ASQ\Location\Location::find( array( 'postcode' => $postcode, 'type' => 'suburb' ) );
						foreach ( $suburbs as $suburb ) :
							$selected = ( !empty( $area ) && $area == $suburb['name'] ) ? 'selected' : '';
						?>
						<option value="<?php echo $suburb['name'] ?>" <?php echo $selected ?>><?php echo $suburb['name'] ?></option>
						<?php
						endforeach;
					endif; 
					?>
				</select>
				<span class="help-block"><?php _e( 'Suburb / Town', '7listings' ); ?></span>
			</div>
			<div class="span6">
				<input type="text" name="city" id="auto_fill_city" value="<?php echo $city; ?>" class="span12" readonly>
				<span class="help-block"><?php _e( 'City', '7listings' ); ?></span>
			</div>
		</div>
	</div><!-- .span6 -->

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
	</div><!-- .span6 -->
</div><!-- .row-fluid -->

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
					<?php SL_Form::checkbox_general( "business_hours_$k", get_post_meta( $company->ID, "business_hours_$k", true ) ); ?>
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

<?php
$args = array(
	'height'    => '350px',
	'output_js' => true,
);

if ( $latitude && $longitude )
{
	$args = array_merge( $args, array(
		'type'       => 'latlng',
		'latitude'   => $latitude,
		'longtitude' => $longitude,
		'class'      => 'map clearfix',
	) );
}
else
{
	$args = array_merge( $args, array(
		'type'    => 'address',
		'address' => "$address2, $area, $state, $postcode",
		'class'   => 'map',
	) );
}

sl_map( $args );
?>

<h2><?php _e( 'Social Media', '7listings' ); ?></h2>

<div class="row-fluid social-inputs">
	<?php
	$tpl = '
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
			$terms = get_terms( 'company_product', array( 'hide_empty' => 0 ) );
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
			$terms = get_terms( 'company_service', array( 'hide_empty' => 0 ) );
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
			$terms = get_terms( 'brand', array( 'hide_empty' => 0 ) );
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
