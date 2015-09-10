<?php

if ( ! is_user_logged_in() )
{
	get_template_part( 'templates/company/user-admin/form-login' );

	return;
}

$user_id = get_current_user_id();
$company = get_posts( array(
	'post_type'      => 'company',
	'post_status'    => 'any',
	'posts_per_page' => 1,
	'meta_key'       => 'user',
	'meta_value'     => $user_id,
) );

if ( empty( $company ) )
{
	get_template_part( 'templates/company/user-admin/no-company' );

	return;
}

$company = current( $company );

$membership = get_user_meta( $user_id, 'membership', true );
if ( ! $membership )
	$membership = 'none';
$paid      = get_user_meta( $user_id, 'membership_paid', true );
$time      = get_user_meta( $user_id, 'membership_time', true );
$user_data = get_userdata( $user_id );
?>

<div id="company-admin">

<?php
// If company is NOT manually suspended
// Show all settings
if ( !get_post_meta( $company->ID, 'leads_manually_suspend', true ) ) : ?>

	<h2><?php _e( 'Account', '7listings' ); ?></h2>

	<div id="account-settings">
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Since', '7listings' ); ?></span>
			<?php
			$registered = $user_data->user_registered;
			echo date( 'd/m/Y H:i', strtotime( $registered ) );
			?>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Subscription', '7listings' ); ?></span>
			<?php
			$types = array(
				'bronze' => __( 'Bronze (Free)', '7listings' ),
				'silver' => __( 'Silver ($204)', '7listings' ),
				'gold'   => __( 'Gold ($300)', '7listings' )
			);

			echo $types[$membership];
			?>
			<a href="#modal-change-account" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Invoice Recipient', '7listings' ); ?></span>
			<?php echo get_post_meta( $company->ID, 'invoice_name', true ); ?>
			<a href="#modal-invoice-recipient" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Payment Type', '7listings' ); ?></span>
			<?php
			$type = get_post_meta( $company->ID, 'leads_payment_type', true );
			if ( ! $type )
				$type = 'direct';
			
			$payment_types = solar_get_payment_methods();
			
			echo $payment_types[$type];
			?>
			<a href="#modal-payment" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
	</div>
	<div id="account-actions">
		<a href="http://www.australiansolarquotes.com.au/my-account/close-account/" role="button" data-toggle="modal" class="button white small cancel"><?php _e( 'Close Account', '7listings' ); ?></a>
		<!--<a href="#modal-close-account" role="button" data-toggle="modal" class="button white small cancel"><?php _e( 'Close Account', '7listings' ); ?></a>-->

		<?php // Close account modal ?>
		<form action="" method="post" id="modal-close-account" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Close Account', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<?php
				// If user hasn't stop buying leads: ask him to do so
				if ( ! get_post_meta( $company->ID, 'cancel_reason', true ) )
					_e( 'To deactivate your account, you must first deactivate the lead service', '7listings' );
				// If user stopped buying leads: allow him to close account
				else
					_e( 'Would you really like to close your Company account?', '7listings' );
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

	</div>

	<?php // Change account modal ?>
	<form action="" method="post" id="modal-change-account" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Upgrade Subscription', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">

				<div class="control-group">
					<label class="control-label"><?php _e( 'Account Type', '7listings' ); ?></label>
					<div class="controls">
						<select name="membership">
							<?php SL_Form::options( $membership, $types ); ?>
						</select>
					</div>
				</div>
				
				<input type="hidden" name="time" value="year">
			</div>
		</div>


		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<input type="submit" name="submit_account" class="button primary" value="<?php _e( 'Update', '7listings' ); ?>">
		</div>
	</form>

	<?php // Invoice recipient modal ?>
	<form action="" method="post" id="modal-invoice-recipient" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php _e( 'Invoice Recipient', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<p class="input-hint"><?php _e( 'Leave empty to use same details as Company Owner.', '7listings' ); ?></p>

			<div class="form-horizontal">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Name', '7listings' ); ?></label>

					<div class="controls">
						<span class="input-prepend">
							<span class="add-on"><i class="icon-user"></i></span>
							<input type="text" name="invoice_name" value="<?php echo get_post_meta( $company->ID, 'invoice_name', true ); ?>">
						</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Position', '7listings' ); ?></label>

					<div class="controls">
						<span class="input-prepend">
							<span class="add-on"><i class="icon-user"></i></span>
							<input type="text" name="invoice_position" value="<?php echo get_post_meta( $company->ID, 'invoice_position', true ); ?>">
						</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Email', '7listings' ); ?></label>

					<div class="controls">
						<span class="input-prepend">
							<span class="add-on"><i class="icon-envelope-alt"></i></span>
							<input type="email" name="invoice_email" value="<?php echo get_post_meta( $company->ID, 'invoice_email', true ); ?>">
						</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Mobile', '7listings' ); ?></label>

					<div class="controls">
						<span class="input-prepend">
							<span class="add-on"><i class="icon-phone"></i></span>
							<input type="text" name="invoice_phone" pattern="04[0-9]{8}" placeholder="04xxxxxxxx" title="<?php esc_attr_e( 'Must start with 04 and have 10 characters', '7listings' ); ?>" value="<?php echo get_post_meta( $company->ID, 'invoice_phone', true ); ?>">
						</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Direct Line', '7listings' ); ?></label>

					<div class="controls">
						<span class="input-prepend">
							<span class="add-on"><i class="icon-phone"></i></span>
							<input type="text" name="invoice_direct_line" value="<?php echo get_post_meta( $company->ID, 'invoice_direct_line', true ); ?>">
						</span>
					</div>
				</div>
				<hr class="light">
				<div class="control-group checkbox-toggle" style="float:none">
					<label class="control-label"><?php _e( 'Does your business have Paypal account', '7listings' ); ?></label>
					<div class="controls">
						<?php SL_Form::checkbox_general( 'paypal_enable', get_post_meta( $company->ID, 'paypal_enable', true ) ); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Paypal Email', '7listings' ); ?></label>

					<div class="controls">
						<input type="email" name="paypal_email" value="<?php echo get_post_meta( $company->ID, 'paypal_email', true ); ?>">
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<input type="submit" name="submit_invoice" class="button primary" value="<?php _e( 'Update', '7listings' ); ?>">
		</div>
	</form>

	<?php // Edit payment type modal ?>
	<form action="" method="post" id="modal-payment" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Edit payment type', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Payment Type' ); ?></label>
					<div class="controls toggle-choices">
						<select name="leads_payment_type">
							<?php if ( sl_setting( 'solar_payment_direct_debit' ) ) : ?>
								<option value="direct"><?php _e( 'Direct Debit', '7listings' ); ?></option>
							<?php endif; ?>
							<?php if ( sl_setting( 'solar_payment_post_pay' ) ) : ?>
								<option value="post"><?php _e( 'Post Pay', '7listings' ); ?></option>
							<?php endif; ?>
							<?php if ( sl_setting( 'solar_payment_upfront' ) ) : ?>
								<option value="upfront"><?php _e( 'Upfront', '7listings' ); ?></option>
							<?php endif; ?>
						</select>
					</div>
				</div>

				<div class="condition-direct" data-name="leads_payment_type" data-value="direct">
					<a href="<?php echo CHILD_URL . 'files/DDR_37533-Australian Solar Quotes.pdf'; ?>" target="_blank">Download EziDebit application (pdf)</a><br /><br />
					and email it to <a href="mailto:accounts@australiansolarquotes.com.au" target="_blank">accounts@australiansolarquotes.com.au</a>
					<hr class="light">
					<?php echo sl_setting( 'solar_term_cond' ); ?>
				</div>
				<div class="condition-post" data-name="leads_payment_type" data-value="post">
					<hr class="light">
					<?php echo sl_setting( 'solar_term_cond' ); ?>
				</div>
			</div>
			<hr class="light">
			<?php _e( 'By clicking "<strong>Update payment type</strong>" you consent to the agreement above between Australian Solar Quotes and your business.', '7listings' ); ?>
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<input type="submit" name="submit_payment" class="button primary" value="<?php _e( 'Update payment type', '7listings' ); ?>">
		</div>
	</form>

<?php endif; ?>

<?php do_action( 'company_account_page_after' ); ?>
