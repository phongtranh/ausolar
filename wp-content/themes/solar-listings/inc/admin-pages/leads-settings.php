<p>
	<label><?php _e( 'Automatic Emails', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable automatic sending of leads to companies as soon as a new lead is received', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	<?php Sl_Form::checkbox( 'auto_leads_email_sending' ); ?>
</p>

<br><br>

<h2><?php _e( 'Alert', '7listings' ); ?></h2>
<p>
	<label><?php _e( 'Paypal', '7listings' ); ?></label>
	<?php Sl_Form::checkbox( 'solar_notification_paypal' ); ?>
</p>

<br><br>

<h2><?php _e( 'Lead Value', '7listings' ); ?></h2>
<p>
	<label><?php _e( 'Website', '7listings' ); ?></label>
	<span class="input-append">
		<input type="number" name="<?php echo THEME_SETTINGS; ?>[solar_website_lead_value]" class="amount value price" value="<?php echo sl_setting( 'solar_website_lead_value' ); ?>">
		<span class="add-on">$ / lead</span>
	</span>
</p>
<p>
	<label><?php _e( 'Energy Smart', '7listings' ); ?></label>
	<span class="input-append">
		<input type="number" name="<?php echo THEME_SETTINGS; ?>[solar_es_lead_value]" class="amount value price" value="<?php echo sl_setting( 'solar_es_lead_value' ); ?>">
		<span class="add-on">$ / lead</span>
	</span>
</p>

<br><br>

<h2><?php _e( 'Payment Terms', '7listings' ); ?></h2>
<p>
	<label><?php _e( 'Post Pay', '7listings' ); ?></label>
	<?php Sl_Form::checkbox( 'solar_payment_post_pay' ); ?>
</p>
<p>
	<label><?php _e( 'Direct Debit', '7listings' ); ?></label>
	<?php Sl_Form::checkbox( 'solar_payment_direct_debit' ); ?>
</p>
<p>
	<label><?php _e( 'Upfront', '7listings' ); ?></label>
	<?php Sl_Form::checkbox( 'solar_payment_upfront' ); ?>
</p>

<h2><?php _e( 'Available Leads Frequency for Solar Installers', '7listings' ); ?></h2>
<p>
	<label for="lead_frequency_day">Daily</label>
	<?php Sl_Form::checkbox( 'lead_frequency_day' ); ?>
</p>

<p>
	<label for="lead_frequency_week">Weekly</label>
	<?php Sl_Form::checkbox( 'lead_frequency_week' ); ?>
</p>

<p>
	<label for="lead_frequency_month">Monthly</label>
	<?php Sl_Form::checkbox( 'lead_frequency_month' ); ?>
</p>

<br><br>
<h2><?php _e( 'Membership Priority' ); ?></h2>

<p>
	<label><?php _e( 'Enable for Matching', '7listings' ); ?></label>
	<?php Sl_Form::checkbox( 'enable_compare_membership' ); ?>
</p>
<br><br>


<h2><?php _e( 'Next Match Offset', '7listings' ); ?></h2>
<p>
	<label><?php _e( 'Next Match Days', '7listings' ); ?></label>
	<span class="input-append">
		<input type="number" name="<?php echo THEME_SETTINGS; ?>[solar_lead_next_match_offset]" value="<?php echo sl_setting( 'solar_lead_next_match_offset' ); ?>">
		<span class="add-on">days</span>
	</span>
</p>

<h2><?php _e( 'Security', '7listings' ); ?></h2>
<p>
	<label for="ip_blacklist"><?php _e( 'Block IP addresses', '7listings' ); ?></label>
	<textarea id="ip_blacklist" name="<?php echo THEME_SETTINGS; ?>[ip_blacklist]"><?php echo sl_setting( 'ip_blacklist' ); ?></textarea>
</p>

<h2><?php _e( 'Company Admin', '7listings' ); ?></h2>
<p>
	<label><?php _e( 'Leads Rejection', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter number of days<br />leads are available for the rejection', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	<span class="input-append">
		<input type="number" name="<?php echo THEME_SETTINGS; ?>[solar_lead_rejection_duration]" class="amount value days" value="<?php echo sl_setting( 'solar_lead_rejection_duration' ); ?>">
		<span class="add-on">days</span>
	</span>
</p>
<h4><?php _e( 'Agreement Popup', '7listings' ); ?></h4>
<p class="checkbox-toggle">
	<label><?php _e( 'Require new Agreement', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable a popup on login with terms and conditions that has to be agreed on to proceed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	<?php Sl_Form::checkbox( 'terms_cond_popup' ); ?>
</p>
<div id="tc-options">
	<p>
		<label><?php _e( 'New T&C', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Click the button to display new terms and conditions that users have to accept to proceed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		<input type="submit" id="terms-cond-display" class="button" value="<?php _e( 'Display Now', '7listings' ); ?>">
	    <span class="input-hint">
		    <?php _e( 'Last Update: ', '7listings' ); ?>
		    <span id="terms-cond-update">
		    <?php
		    if ( $update = sl_setting( 'terms_cond_update' ) )
		    {
			    $time_offset = sl_timezone_offset() * 3600;
			    echo date( get_option( 'date_format' ), $update + $time_offset );
		    }
		    ?>
		    </span>
	    </span>
	</p>

	<p id="tc-grace-period">
		<label><?php _e( 'Grace Period', '7listings' ); ?></label>
	<span class="input-append">
		<input type="number" name="<?php echo THEME_SETTINGS; ?>[terms_cond_grace_period]" class="amount value days" value="<?php echo sl_setting( 'terms_cond_grace_period' ); ?>">
		<span class="add-on">days</span>
	</span>
	</p>
	<label><?php _e( 'Popup Message', '7listings' ); ?></label>
	<?php
	wp_editor( sl_setting( 'terms_cond_popup_message' ), 'term-popup', array(
		'textarea_name' => THEME_SETTINGS . '[terms_cond_popup_message]'
	) );
	?>
</div>


<br><br>

<br><br>

<h2><?php _e( 'Terms &amp; Conditions', '7listings' ); ?></h2>
<?php
wp_editor( sl_setting( 'solar_term_cond' ), 'term-cond', array(
	'textarea_name' => THEME_SETTINGS . '[solar_term_cond]'
) );
?>

<br><br>
<?php $leads_cap_notification = sl_setting( 'leads_cap_notification' ); ?>
<div id="installer-leads-cap-notification">
	<h2>Leads Cap Notification</h2>
	<p>Available variable: {{owner name}} {{company name}}</p>
	<?php for ($i = 0; $i <= 3; $i++ ) { ?>
	<p>
		<label>When <br>
			<input type="number" name="<?= THEME_SETTINGS . "[leads_cap_notification][$i][percent]"; ?>" value="<?= $leads_cap_notification[$i]['percent'] ?>" />%
		</label>
	</p>
	<p>
		<label>Title <br>
			<input type="text" name="<?= THEME_SETTINGS . "[leads_cap_notification][$i][title]"; ?>" value="<?= $leads_cap_notification[$i]['title'] ?>">
		</label>
	</p>
	<p>
		<label>Content</label>
		<?php
		wp_editor( $leads_cap_notification[$i]['content'], 'leads-cap-notification-content-' . $i, array(
			'textarea_name' => THEME_SETTINGS . "[leads_cap_notification][$i][content]"
		) );
		?>
	</p>
	<?php } ?>
</div>