<h2><?php _e( 'Email to Visitor', '7listings' ); ?></h2>

<?php $prefix = 'emails_booking_cart_'; ?>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}guess_subject]"; ?>" value="<?php echo sl_setting( "{$prefix}guess_subject" ); ?>" class="email-subject">
	</div>
</div>

<h2><?php _e( 'Email to Admin', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Admin Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}admin_email]"; ?>" value="<?php echo sl_setting( "{$prefix}admin_email" ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}admin_subject]"; ?>" value="<?php echo sl_setting( "{$prefix}admin_subject" ); ?>" class="email-subject">
	</div>
</div>
