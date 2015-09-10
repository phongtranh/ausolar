<?php $prefix = 'emails_booking_tour_'; ?>

<h2><?php _e( 'Email to Visitor', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Subject', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}guess_subject]"; ?>" value="<?php echo sl_setting( "{$prefix}guess_subject" ); ?>" class="email-subject">
	</div>
</div>

<h4><?php _e( 'Message', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Leave empty to use default email template.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>
<div class="email-content-box">
	<?php
	$args = array(
		'textarea_name' => THEME_SETTINGS . "[{$prefix}guess_message]",
	);
	wp_editor( sl_setting( "{$prefix}guess_message" ), "{$prefix}guess_message", $args );
	?>
</div>

<div class="email-shortcodes">
	<p class="input-hint"><?php _e( 'You can use the following tags:', '7listings' ); ?></p>
	<code title="<?php _e( 'Tour URL', '7listings' ); ?>">[tour_url]</code>
	<code title="<?php _e( 'Tour Operator Title', '7listings' ); ?>">[title]</code>
	<code title="<?php _e( 'Tour Item Title', '7listings' ); ?>">[resource]</code>
	<code title="<?php _e( 'Tour Photo', '7listings' ); ?>">[resource_photo]</code>
	<code title="<?php _e( 'Booking# - Counter', '7listings' ); ?>">[booking_id]</code>
	<code title="<?php _e( 'Departure Day', '7listings' ); ?>">[date]</code>
	<code title="<?php _e( 'Departure Time (hour)', '7listings' ); ?>">[depart_time]</code>
	<code title="<?php _e( 'Arrival Time (hour)', '7listings' ); ?>">[arrival_time]</code>
	<code title="<?php _e( 'List of Purchased Upsells', '7listings' ); ?>">[upsells]</code>
	<code title="<?php _e( 'Subtotal for Guests', '7listings' ); ?>">[subtotal_guests]</code>
	<code title="<?php _e( 'Subtotal for Purchased Upsells', '7listings' ); ?>">[subtotal_upsells]</code>
	<code title="<?php _e( 'Booking Total', '7listings' ); ?>">[total]</code>
	<br><br>
	<code title="<?php _e( 'First Name of buyer', '7listings' ); ?>">[first_name]</code>
	<code title="<?php _e( 'Number of Passengers', '7listings' ); ?>">[passengers]</code>
	<code title="<?php _e( 'Passenger Contact Details', '7listings' ); ?>">[passengers_info]</code>
	<br><br>
	<code title="<?php _e( 'Booking Message', '7listings' ); ?>">[booking_message]</code>
	<code title="<?php _e( 'Payment Policy', '7listings' ); ?>">[payment_policy]</code>
	<code title="<?php _e( 'Cancellation Policy', '7listings' ); ?>">[cancellation_policy]</code>
	<code title="<?php _e( 'Terms And Conditions', '7listings' ); ?>">[terms_conditions]</code>
	<code title="<?php _e( 'Customer Message', '7listings' ); ?>">[message]</code>
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
