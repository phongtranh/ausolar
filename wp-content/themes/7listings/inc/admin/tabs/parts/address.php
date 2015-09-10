<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Address', '7listings' ); ?><span class="warning-sm required right"></span></label>
	</div>
	<div class="sl-input">
		<input type="text" name="address" class="sl-input-large address line-one" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'address', true ) ); ?>" placeholder="<?php esc_attr_e( 'Line 1', '7listings' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Address 2', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="address2" class="sl-input-large address line-two" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'address2', true ) ); ?>" placeholder="<?php esc_attr_e( 'Line 2', '7listings' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'City', '7listings' ); ?><span class="warning-sm required right"></span></label>
	</div>
	<div class="sl-input">
		<?php
		$city = get_post_meta( get_the_ID(), 'city', true );
		if ( $city )
		{
			$city = get_term( $city, 'location' );
			$city = $city && ! is_wp_error( $city ) ? $city->name : '';
		}
		?>
		<input type="text" name="city" class="sl-input-medium city location-autocomplete" value="<?php echo esc_attr( $city ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Area/Suburb', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$area = get_post_meta( get_the_ID(), 'area', true );
		if ( $area )
		{
			$area = get_term( $area, 'location' );
			$area = $area && ! is_wp_error( $area ) ? $area->name : '';
		}
		?>
		<input type="text" name="area" class="sl-input-medium area location-autocomplete" value="<?php echo esc_attr( $area ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'State', '7listings' ); ?><span class="warning-sm required right"></span></label>
	</div>
	<div class="sl-input">
		<?php
		wp_dropdown_categories( array(
			'hide_empty'       => 0,
			'selected'         => get_post_meta( get_the_ID(), 'state', true ),
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
		<label><?php _e( 'Post Code', '7listings' ); ?><span class="warning-sm required right"></span></label>
	</div>
	<div class="sl-input">
		<input type="text" name="postcode" class="sl-input-small postcode" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'postcode', true ) ); ?>">
	</div>
</div>
