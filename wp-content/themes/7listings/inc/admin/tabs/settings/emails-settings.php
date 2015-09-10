<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'From Name', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert from name for all emails', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . '[emails_from_name]'; ?>" value="<?php echo sl_setting( 'emails_from_name' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'From Email', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert from email address for all emails', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="email" name="<?php echo THEME_SETTINGS . '[emails_from_email]'; ?>" value="<?php echo sl_setting( 'emails_from_email' ); ?>">
	</div>
</div>

<h2><?php _e( 'Templates', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Use for all WP emails', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable to use this email template<br>for all emails sent in Wordpress, applies to emails sent from plugins as well', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'emails_use_template' ); ?>
	</div>
</div>
<br>
<p class="description"><?php _e( 'For more advanced control copy <code>templates/emails/</code> to your theme.', '7listings' ); ?></p>
