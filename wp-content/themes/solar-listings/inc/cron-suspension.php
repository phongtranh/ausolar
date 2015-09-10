<?php
add_action( 'company_settings_page_after', 'solar_time_check' );

/**
 * Add settings for time to check suspension
 *
 * @return void
 */
function solar_time_check()
{
	?>
	<h2><?php _e( 'When to check company owners reactivate buying leads?', '7listings' ); ?></h2>

	<input class="time-picker" type="text" name="<?php echo THEME_SETTINGS; ?>[leads_check_time1]" value="<?php echo sl_setting( 'leads_check_time1' ); ?>">
	<input class="time-picker" type="text" name="<?php echo THEME_SETTINGS; ?>[leads_check_time2]" value="<?php echo sl_setting( 'leads_check_time2' ); ?>">
	<input class="time-picker" type="text" name="<?php echo THEME_SETTINGS; ?>[leads_check_time3]" value="<?php echo sl_setting( 'leads_check_time3' ); ?>">
<?php
}

add_action( 'admin_print_styles-toplevel_page_7listings', 'solar_settings_enqueue' );

/**
 * Enqueue scripts and styles for settings page
 *
 * @return void
 */
function solar_settings_enqueue()
{
	wp_enqueue_style( 'jquery-ui' );
	wp_enqueue_style( 'jquery-ui-timepicker' );
	wp_enqueue_script( 'solar-settings', sl_locate_url( 'js/admin/settings.js' ), array( 'jquery-ui-timepicker' ), '', true );
}

add_action( 'wp', 'solar_setup_schedule' );

/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 *
 * Check 3 schedules
 *
 * @return void
 */
function solar_setup_schedule()
{
	$hooks    = array( 'leads_check_time1', 'leads_check_time2', 'leads_check_time3' );
	$tomorrow = date( 'd-m-Y', strtotime( 'tomorrow' ) );
	foreach ( $hooks as $hook )
	{
		if ( ! wp_next_scheduled( $hook ) )
			wp_schedule_event( strtotime( $tomorrow . ' ' . sl_setting( $hook ) ), 'daily', $hook );
	}
}

// Add hooks
$hooks = array( 'leads_check_time1', 'leads_check_time2', 'leads_check_time3' );
foreach ( $hooks as $hook )
{
	add_action( $hook, 'solar_check_suspension' );
}

/**
 * On the scheduled action hook, run a function.
 *
 * @return void
 */
function solar_check_suspension()
{
	$companies = get_posts( array(
		'post_type'  => 'company',
		'meta_key'   => 'cancel_reason',
		'meta_value' => 'too_many_temp',
	) );

	$from_name  = get_bloginfo( 'name' );
	$from_email = 'no-reply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', HOME_URL );
	$to         = 'installer@australiansolarquotes.com.au';

	$now = time();
	foreach ( $companies as $company )
	{
		// Check for manually suspended companies (by admin)
		if ( get_post_meta( $company->ID, 'leads_manually_suspend', true ) )
		{
			$start = intval( get_post_meta( $company->ID, 'leads_manually_suspend_start', true ) );

			// If suspension period >= 7 days, reset date buying leads to blank
			// And leads_payment_type to direct
			if ( $start + 7 * 86400 < $now )
			{
				delete_post_meta( $company->ID, 'leads_paid' );
				update_post_meta( $company->ID, 'leads_payment_type', 'direct' );
			}
		}

		// Check for companies that suspended by themselves
		$end = intval( get_post_meta( $company->ID, 'suspend_end', true ) );
		if ( $end > $now )
			continue;

		// Update number of leads
		$num = get_post_meta( $company->ID, 'leads_old', true );
		update_post_meta( $company->ID, 'leads', $num );

		// If company suspended buying leads for < 7 days: keep its buying date
		// If > 7 days: reset buying date
		$days = intval( get_post_meta( $company->ID, 'suspend_days', true ) );
		if ( $days >= 7 )
		{
			update_post_meta( $company->ID, 'leads_paid', time() ); // Paid time
			update_post_meta( $company->ID, 'leads_payment_type', 'direct' );
		}

		// Remove all cancel meta fields
		delete_post_meta( $company->ID, 'cancel_reason' );
		delete_post_meta( $company->ID, 'cancel_other_reason' );
		delete_post_meta( $company->ID, 'suspend_end' );
		delete_post_meta( $company->ID, 'suspend_days' );
		delete_post_meta( $company->ID, 'leads_old' );

		// Send email to admin

		$company_name = $company->post_title;
		$user         = get_userdata( get_post_meta( $company->ID, 'user', true ) );
		$user_name    = $user->display_name;
		if ( $user->first_name && $user->last_name )
			$user_name = "{$user->first_name} {$user->last_name}";

		$subject = sprintf( __( '%s reactivates buying leads', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just reactivated buying leads.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s', '7listings' );

		$body = sprintf( $body, $user_name, $company_name );
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}
}
