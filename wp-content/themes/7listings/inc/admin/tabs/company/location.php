<?php
$address  = get_post_meta( get_the_ID(), 'address', true );
$address2 = get_post_meta( get_the_ID(), 'address2', true );
$postcode = get_post_meta( get_the_ID(), 'postcode', true );

$state = get_post_meta( get_the_ID(), 'state', true );
$city  = get_post_meta( get_the_ID(), 'city', true );
$area  = get_post_meta( get_the_ID(), 'area', true );
?>

<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Address 1', '7listings' ); ?> <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="address" class="sl-input-large address line-one" value="<?php echo $address; ?>" placeholder="Line 1">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( '', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="address2" class="sl-input-large address line-two" value="<?php echo $address2; ?>" placeholder="Line 2">
			</div>
		</div>
		<br>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'City', '7listings' ); ?> <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="city" id="city" class="sl-input-medium city location-autocomplete" value="<?php echo $city; ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Area/Suburb', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="area" id="area" class="sl-input-medium area location-autocomplete" value="<?php echo $area; ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'State', '7listings' ); ?> <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<?php
				$state_term = get_term_by( 'name', $state, 'location' );
				$id         = empty( $state_term ) || is_wp_error( $state_term ) ? 0 : $state_term->term_id;
				wp_dropdown_categories( array(
					'hide_empty'       => 0,
					'selected'         => $id,
					'show_option_none' => __( 'Select', '7listings' ),
					'hierarchical'     => 1,
					'depth'            => 1,
					'name'             => 'state',
					'id'               => 'state',
					'taxonomy'         => 'location',
				) );
				?>
			</div>
		</div>
		<br>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Post Code', '7listings' ); ?> <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="postcode" class="sl-input-small postcode" value="<?php echo $postcode; ?>">
			</div>
		</div>
	</div>
	<div class="column-2">
		<?php include THEME_TABS . 'parts/map.php'; ?>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label for="service-radius"><?php _e( 'Service Area', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'service_area', get_post_meta( get_the_ID(), 'service_area', true ) ); ?>
	</div>
</div>

<div class="sl-sub-settings">
	<div class="sl-settings toggle-choices">
		<div class="sl-label">
			<label><?php _e( 'Service Area', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="service_radius">
				<?php
				Sl_Form::options( get_post_meta( get_the_ID(), 'service_radius', true ), array(
					'radius'    => __( 'Radius', '7listings' ),
					'postcodes' => __( 'Postcodes', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="sl-settings" data-name="service_radius" data-value="postcodes">
		<div class="sl-label">
			<label><?php _e( 'Postcodes', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<textarea name="service_postcodes" class="input-xxlarge" rows="3"><?php echo esc_textarea( get_post_meta( get_the_ID(), 'service_postcodes', true ) ); ?></textarea><br>
			<p class="description"><?php _e( 'Enter postcodes separated by commas', '7listings' ); ?></p>
		</div>
	</div>
	<div class="sl-settings" data-name="service_radius" data-value="radius">
		<div class="sl-label">
			<label><?php _e( 'From Office', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
	<span class="input-append">
		<input type="number" name="leads_service_radius" id="service-radius" value="<?php echo get_post_meta( get_the_ID(), 'leads_service_radius', true ); ?>">
		<span class="add-on">km</span>
	</span>
		</div>
	</div>
</div>