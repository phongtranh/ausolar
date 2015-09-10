<?php
add_action( 'company_account_invoice_recipient', 'solar_account_invoice_recipient', 10, 4 );

/**
 * Send admin notification when company owner changes invoice recipient
 *
 * @param int     $user_id Current user ID
 * @param WP_Post $company Current company
 * @param array   $old     Old meta values
 * @param array   $new     New meta values
 *
 * @return void
 */
function solar_account_invoice_recipient( $user_id, $company, $old, $new )
{
	// Position
	$old['invoice_position'] = get_post_meta( $company->ID, 'invoice_position', true );
	$new['invoice_position'] = empty( $_POST['invoice_position'] ) ? '' : $_POST['invoice_position'];
	if ( $new['invoice_position'] )
		update_post_meta( $company->ID, 'invoice_position', $new['invoice_position'] );

	// Direct line
	$old['invoice_direct_line'] = get_post_meta( $company->ID, 'invoice_direct_line', true );
	$new['invoice_direct_line'] = empty( $_POST['invoice_direct_line'] ) ? '' : $_POST['invoice_direct_line'];
	if ( $new['invoice_direct_line'] )
		update_post_meta( $company->ID, 'invoice_direct_line', $new['invoice_direct_line'] );

	// Paypal
	$old['paypal_enable'] = intval( get_post_meta( $company->ID, 'paypal_enable', true ) );
	$new['paypal_enable'] = empty( $_POST['paypal_enable'] ) ? 0 : $_POST['paypal_enable'];

	if ( $new['paypal_enable'] )
		update_post_meta( $company->ID, 'paypal_enable', 1 );
	else
		delete_post_meta( $company->ID, 'paypal_enable' );

	$user = get_userdata( $user_id );
	$name = $user->display_name;
	if ( $user->first_name && $user->last_name )
		$name = "{$user->first_name} {$user->last_name}";

	$to      = 'installer@australiansolarquotes.com.au';
	$subject = sprintf( __( '%s edited invoice recipient', '7listings' ), $company->post_title );
	$body    = __( 'Dear admin,<br><br>
		There is one company owner has just edited invoice recipient.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Name:</b> %s &rarr; %s<br>
		<b>Position:</b> %s &rarr; %s<br>
		<b>Email:</b> %s &rarr; %s<br>
		<b>Mobile:</b> %s &rarr; %s<br>
		<b>Direct Line:</b> %s &rarr; %s<br>
		<b>Use Paypal:</b> %s &rarr; %s<br>
		<b>Paypal Email:</b> %s &rarr; %s', '7listings' );
	$body    = sprintf(
		$body, $name, $company->post_title,
		$old['invoice_name'], $new['invoice_name'],
		$old['invoice_position'], $new['invoice_position'],
		$old['invoice_email'], $new['invoice_email'],
		$old['invoice_phone'], $new['invoice_phone'],
		$old['invoice_direct_line'], $new['invoice_direct_line'],
		$old['paypal_enable'], $new['paypal_enable'],
		$old['paypal_email'], $new['paypal_email']
	);
	wp_mail( $to, $subject, $body );
}

add_action( 'company_account_close', 'solar_account_close', 10, 2 );

/**
 * Send admin notification when company owner closes account
 *
 * @param int    $user_id Current user ID
 * @param object $company Current company
 *
 * @return void
 */
function solar_account_close( $user_id, $company )
{
	// Reset company leads to 0
	delete_post_meta( $company->ID, 'leads' );

	// Send email notification to admin
	$user = get_userdata( $user_id );
	$name = $user->display_name;
	if ( $user->first_name && $user->last_name )
		$name = "{$user->first_name} {$user->last_name}";

	$to      = 'installer@australiansolarquotes.com.au';
	$subject = sprintf( __( '%s closed account', '7listings' ), $company->post_title );
	$body    = __( 'Dear admin,<br><br>
		There is one company owner has just closed accout.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s', '7listings' );
	$body    = sprintf( $body, $name, $company->post_title );
	wp_mail( $to, $subject, $body );
}

add_action( 'company_account_change', 'solar_account_change', 10, 6 );

/**
 * Send admin notification when company change account type
 *
 * @param int    $user_id   Current user ID
 * @param object $company   Current company
 *
 * @param string $type      New membership type
 * @param string $prev      Old membership type
 * @param string $time      New membership period time
 * @param string $prev_time Old membership period time
 *
 * @return void
 */
function solar_account_change( $user_id, $company, $type, $prev, $time, $prev_time )
{
	global $current_user;
	get_currentuserinfo();

	$user_name = $current_user->display_name;
	if ( $current_user->first_name && $current_user->last_name )
		$user_name = "{$current_user->first_name} {$current_user->last_name}";
	
	if ( $type === 'bronze' )
	{
		$now  = time();
		update_user_meta( $user_id, 'membership', $type );
		update_user_meta( $user_id, 'membership_time', $time );
		update_user_meta( $user_id, 'membership_paid', $now );
	}

	$to      = 'installer@australiansolarquotes.com.au';
	$subject = sprintf( __( '%s edited invoice recipient', '7listings' ), $company->post_title );
	$body    = __( 'Dear admin,<br><br>
			There is one company owner has just edited invoice recipient.<br><br>
			<b>User:</b> %s<br>
			<b>Company:</b> %s<br>
			<b>Membership:</b> %s &rarr; %s<br>
			<b>Membership Pay Time:</b> %s &rarr; %s', '7listings' );

	$type = $type ? $type : 'none';
	$prev = $prev ? $prev : 'none';
	$body = sprintf(
		$body, $user_name, $company->post_title,
		$prev, $type,
		$prev_time, $time
	);
	wp_mail( $to, $subject, $body );
}

// Close account page
add_filter( 'wp_footer', 'solar_close_account_content' );

/**
 * Add forms to "Close Account" page
 *
 * @return void
 */
function solar_close_account_content()
{
	// Add content for "Close Account" page only
	if ( ! is_page( 17666 ) )
		return;

	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
		return;

	$company = current( $company );
	?>

	<?php // Close account modal ?>
	<form action="/" method="post" id="modal-close-account" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Close Account', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<?php
			// If user hasn't stop buying leads: ask him to do so
			if ( ! get_post_meta( $company->ID, 'cancel_reason', true ) )
			{
				_e( 'To deactivate your account, you must first deactivate the lead service', '7listings' );
			}
			// If user stopped buying leads: allow him to close account
			else
			{
				_e( 'Would you really like to close your Company account?', '7listings' );
			}
			?>
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<?php
			// If user hasn't stop buying leads: ask him to do so
			if ( ! get_post_meta( $company->ID, 'cancel_reason', true ) )
			{
				?>
				<a id="stop" href="#modal-stop" role="button" class="button primary"><?php _e( 'Suspend the lead service', '7listings' ); ?></a>
			<?php
			}
			else
			{
				?>
				<input type="submit" name="submit_close" class="button primary" value="<?php esc_attr_e( 'Yes, close account', '7listings' ); ?>">
			<?php
			}
			?>
		</div>
	</form>

	<?php // Stop buying leads modal ?>
	<form action="" method="post" id="modal-stop" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Stop Buying Leads', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<div class="control-group">
				<label class="control-label"><?php _e( 'Reason', '7listings' ); ?></label>

				<div class="controls toggle-choices">
					<select name="cancel_reason" class="full-width">
						<?php
						SL_Form::options( get_post_meta( $company->ID, 'cancel_reason', true ), array(
							//							'too_many_temp'    => __( 'I have too many leads and wish to suspend temporarily', '7listings' ),
							'too_many_ind'     => __( 'I have too many leads and wish to suspend indefinitely', '7listings' ),
							'another_provider' => __( 'I am using another solar quote provider', '7listings' ),
							'poor_quality'     => __( 'I am not happy with the quality of leads', '7listings' ),
							'poor_amount'      => __( 'I am not happy with the amount of leads', '7listings' ),
							'poor_service'     => __( 'I am not happy with the service provided by Australian Solar Quotes', '7listings' ),
							'other'            => __( 'Other Reason', '7listings' ),
						) );
						?>
					</select>
				</div>
			</div>

			<div class="control-group" data-name="cancel_reason" data-value="too_many_temp">
				<?php _e( 'Suspend leads for', '7listings' ); ?>

				<span class="input-append">
								<input type="number" name="suspend_days" class="input-mini" min="1">
								<span class="add-on"><?php _e( 'days', '7listings' ); ?></span>
							</span>
			</div>

			<div class="control-group" data-name="cancel_reason" data-value="other">
				<label class="control-label"><strong><?php _e( 'Please describe your reason for deactivation.', '7listings' ); ?></strong></label>
				<div class="controls">
					<textarea name="other_reason" class="full-width" rows="5"></textarea>
				</div>
			</div>

			<?php _e( '
				<h4>Terms & Conditions</h4>
				<div class="company-terms">
					I hereby wish to deactivate the leads for the reason described, and the time frame selected in this form. I am aware that if my account is suspended for a period of 15 days or longer, I will be placed in a cue and may not start receiving leads immediately.
					<br><br>
					I am aware that an invoice will be raised within 48 hours for the leads that I have received this month if I have selected any of the following reasons for suspension:
					<ul>
					  <li>I have too many leads and wish to suspend indefinitely</li>
					  <li>I am using another solar quote provider</li>
					  <li>I am not happy with the quality of leads</li>
					  <li>I am not happy with the amount of leads</li>
					  <li>I am not happy with the service provided by Australian Solar Quotes</li>
					  <li>Other Reason</li>
					</ul>
				</div>
				<hr class="light">
				By clicking "<strong>Stop buying leads</strong>" you consent to the agreement above between Australian Solar Quotes and your business.
				<hr class="light">
				<strong>Note:</strong> This will not close or remove your company account/listing.
			', '7listings' ); ?>
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<input type="submit" name="submit_stop" class="button primary" value="<?php _e( 'Stop buying leads', '7listings' ); ?>">
		</div>
	</form>
	<?php
}

add_action( 'wp_enqueue_scripts', 'solar_close_account_script' );

/**
 * Enqueue scripts to "Close Account"
 *
 * @return void
 */
function solar_close_account_script()
{
	// Add JS for "Close Account" page only
	if ( is_page( 17666 ) )
		wp_enqueue_script( 'solar-close-account', CHILD_URL . 'js/close-account.js', array( 'jquery' ), '', true );
}

add_action( 'template_redirect', 'solar_close_account' );

/**
 * Handle closing account action
 * Must be implemented because parent theme handles closing account action only on account page
 *
 * @return void
 */
function solar_close_account()
{
	if ( empty( $_POST['submit_close'] ) )
		return;

	// Get user company
	$user_id = get_current_user_id();
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => $user_id,
	) );

	if ( empty( $company ) )
		return;

	$company = current( $company );
	delete_post_meta( $company->ID, 'user' );
	do_action( 'company_account_close', $user_id, $company );

	// Delete user
	require_once ABSPATH . 'wp-admin/includes/user.php';
	wp_delete_user( $user_id );
}

add_action( 'company_special_page_after_login', 'solar_process_edit_account' );

function solar_process_edit_account()
{
	if ( get_the_ID() != sl_setting( 'company_page_account' ) )
		return;

	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
		return;

	$company = current( $company );

	// Email variables
	$from_name    = get_bloginfo( 'name' );
	$from_email   = 'no-reply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', home_url() );
	$to           = 'installer@australiansolarquotes.com.au';
	$company_name = $company->post_title;
	global $current_user;
	get_currentuserinfo();
	$user_name = $current_user->display_name;
	if ( $current_user->first_name && $current_user->last_name )
		$user_name = "{$current_user->first_name} {$current_user->last_name}";

	// Edit payment type
	if ( ! empty( $_POST['submit_payment'] ) )
	{
		$payment_types 	= solar_get_payment_methods();
		$old 			= get_post_meta( $company->ID, 'leads_payment_type', true );
		$new 			= isset( $_POST['leads_payment_type'] ) ? $_POST['leads_payment_type'] : 'direct';

		update_post_meta( $company->ID, 'leads_payment_type', $new );

		$amount = get_post_meta( $company->ID, 'leads', true );
		
		if ( ! $amount && $new === 'upfront' )
		{
			update_post_meta( $company->ID, 'leads', 30 );
			update_post_meta( $company->ID, 'lead_frequency', 'month' );
		}

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Payment Type:</span> <span class="detail">%s</span>', '7listings' ), $payment_types[$new] ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed payment type', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed payment type.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Payment Type:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			$payment_types[$old], $payment_types[$new]
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}
}
