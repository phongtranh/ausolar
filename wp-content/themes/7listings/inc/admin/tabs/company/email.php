<?php $prefix = 'emails_company_membership_'; ?>

<h2><?php _e( 'Email to Visitor', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}user_subject]"; ?>" value="<?php echo sl_setting( "{$prefix}user_subject" ); ?>" class="email-subject">
	</div>
</div>

<h4><?php _e( 'Message', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Leave empty to use default email template.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>
<div class="email-content-box">
	<?php
	$args = array(
		'textarea_name' => THEME_SETTINGS . "[{$prefix}user_message]",
	);
	wp_editor( sl_setting( "{$prefix}user_message" ), "{$prefix}user_message", $args );
	?>
</div>

<div class="email-shortcodes">
	<p class="input-hint"><?php _e( 'You can use the following tags:', '7listings' ); ?></p>
	<code title="<?php _e( 'First Name of User', '7listings' ); ?>">[first_name]</code>
	<code title="<?php _e( 'Membership Type', '7listings' ); ?>">[membership]</code>
	<code title="<?php _e( 'Company Title', '7listings' ); ?>">[company-title]</code>
	<br><br>
	<code title="<?php _e( 'Website/Business Name', '7listings' ); ?>">[site-title]</code>
	<code title="<?php _e( 'Website Tagline', '7listings' ); ?>">[tagline]</code>
	<code title="<?php _e( 'Website URL', '7listings' ); ?>">[url]</code>
	<code title="<?php _e( 'Facebook Page URL', '7listings' ); ?>">[facebook]</code>
	<code title="<?php _e( 'Twitter URL', '7listings' ); ?>">[twitter]</code>
	<code title="<?php _e( 'Google+ URL', '7listings' ); ?>">[googleplus]</code>
	<code title="<?php _e( 'Main Email', '7listings' ); ?>">[email]</code>
	<code title="<?php _e( 'Main Phone Number', '7listings' ); ?>">[phone]</code>
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
