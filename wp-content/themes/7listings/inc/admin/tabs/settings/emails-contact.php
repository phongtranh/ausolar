<h2><?php _e( 'Email to Visitor', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" id="contact_subject" name="<?php echo THEME_SETTINGS; ?>[contact_subject]" value="<?php echo sl_setting( 'contact_subject' ); ?>" class="email-subject">
	</div>
</div>

<h4><?php _e( 'Message', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Leave empty to use default email template.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>
<div class="email-content-box">
	<?php
	$args = array(
		'textarea_name' => THEME_SETTINGS . '[contact_message]',
	);
	wp_editor( sl_setting( 'contact_message' ), 'contact_message', $args );
	?>
</div>

<div class="email-shortcodes">
	<p class="input-hint"><?php _e( 'You can use the following tags:', '7listings' ); ?></p>
	<code title="<?php _e( 'First Name', '7listings' ); ?>">[first]</code>
	<code title="<?php _e( 'Last Name', '7listings' ); ?>">[last]</code>
	<code title="<?php _e( 'Email', '7listings' ); ?>">[customer-email]</code>
	<code title="<?php _e( 'Phone', '7listings' ); ?>">[customer-phone]</code>
	<code title="<?php _e( 'Subject', '7listings' ); ?>">[subject]</code>
	<code title="<?php _e( 'Message', '7listings' ); ?>">[message]</code>
	<code title="<?php _e( 'Message Counter', '7listings' ); ?>">[message_counter]</code>
	<br><br>
	<code title="<?php _e( 'Website/Business Name', '7listings' ); ?>">[site-title]</code>
	<code title="<?php _e( 'Website Tagline', '7listings' ); ?>">[tagline]</code>
	<code title="<?php _e( 'Website URL', '7listings' ); ?>">[url]</code>
	<code title="<?php _e( 'Facebook Page URL', '7listings' ); ?>">[facebook]</code>
	<code title="<?php _e( 'Twitter URL', '7listings' ); ?>">[twitter]</code>
	<code title="<?php _e( 'Google+ URL', '7listings' ); ?>">[googleplus]</code>
	<code title="<?php _e( 'Main Email', '7listings' ); ?>">[email]</code>
	<code title="<?php _e( 'Main Phonenumber', '7listings' ); ?>">[phone]</code>
	<code title="<?php _e( 'Main Address', '7listings' ); ?>">[address]</code>
	<code title="<?php _e( 'Current Year', '7listings' ); ?>">[current_year]</code>
</div>

<br style="clear:both;">

<h2><?php _e( 'Email to Admin', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Admin Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" id="contact_admin_email" name="<?php echo THEME_SETTINGS; ?>[contact_admin_email]" value="<?php echo sl_setting( 'contact_admin_email' ); ?>">
		<span class="input-hint"><?php echo do_shortcode( '[tooltip content="' . __( 'If empty<br>email address from Contact Us will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></span>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" id="contact_admin_subject" name="<?php echo THEME_SETTINGS; ?>[contact_admin_subject]" value="<?php echo sl_setting( 'contact_admin_subject' ); ?>" class="email-subject">
	</div>
</div>
