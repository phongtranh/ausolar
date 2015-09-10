<?php $class = Sl_License::is_activated() ? 'valid' : 'invalid'; ?>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="<?php echo $class; ?>"><input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS; ?>[license_email]" value="<?php echo sl_setting( 'license_email' ); ?>"></span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'License Key', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="<?php echo $class; ?>"><input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS; ?>[license_key]" value="<?php echo sl_setting( 'license_key' ); ?>"></span>
	</div>
</div>
