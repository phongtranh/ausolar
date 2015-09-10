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
			<span class="admin_label"><?php _e( 'Account Type', '7listings' ); ?></span>
			<?php
			$types = array(
				'none'   => __( 'None', '7listings' ),
				'gold'   => __( 'Gold', '7listings' ),
				'silver' => __( 'Silver', '7listings' ),
				'bronze' => __( 'Bronze', '7listings' ),
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
	</div>
	<div id="account-actions">
		<a href="#modal-close-account" role="button" data-toggle="modal" class="button small cancel"><?php _e( 'Close Account', '7listings' ); ?></a>

		<?php // Close account modal ?>
		<form action="" method="post" id="modal-close-account" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php _e( 'Close Account', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<?php _e( 'Would you really like to close your Company account?', '7listings' ); ?>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_close" class="button primary" value="<?php _e( 'Yes, close account', '7listings' ); ?>">
			</div>
		</form>
	</div>

	<?php // Change account modal ?>
	<form action="" method="post" id="modal-change-account" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php _e( 'Change Account', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Account Type', '7listings' ); ?></label>

					<div class="controls">
						<select name="membership">
							<?php
							unset( $types[$membership] );
							Sl_Form::options( $membership, $types );
							?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Payment', '7listings' ); ?></label>

					<div class="controls">
						<select name="time">
							<?php
							Sl_Form::options( $time, array(
								'month' => __( 'Monthly', '7listings' ),
								'year'  => __( 'Yearly', '7listings' ),
							) );
							?>
						</select> <span id="membership-price"></span>
					</div>
				</div>
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
					<label class="control-label"><?php _e( 'Email', '7listings' ); ?></label>

					<div class="controls">
					<span class="input-prepend">
						<span class="add-on"><i class="icon-envelope-alt"></i></span>
						<input type="email" name="invoice_email" value="<?php echo get_post_meta( $company->ID, 'invoice_email', true ); ?>">
					</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Phone', '7listings' ); ?></label>

					<div class="controls">
					<span class="input-prepend">
						<span class="add-on"><i class="icon-phone"></i></span>
						<input type="text" name="invoice_phone" value="<?php echo get_post_meta( $company->ID, 'invoice_phone', true ); ?>">
					</span>
					</div>
				</div>
				<hr class="light">
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

	<?php do_action( 'company_account_page_after' ); ?>
