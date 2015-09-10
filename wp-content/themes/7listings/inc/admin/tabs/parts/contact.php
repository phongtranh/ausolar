<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Phone', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="phone" class="sl-input-medium phone" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'phone', true ) ); ?>" placeholder="<?php esc_attr_e( 'Phone number', '7listings' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="email" name="email" class="sl-input-large email" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'email', true ) ); ?>" placeholder="<?php esc_attr_e( 'email@mywebsite.com', '7listings' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Website', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="url" name="website" class="sl-input-large website" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'website', true ) ); ?>" placeholder="<?php esc_attr_e( 'http://mywebsite.com', '7listings' ); ?>">
	</div>
</div>
