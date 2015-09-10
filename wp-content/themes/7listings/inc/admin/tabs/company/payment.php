<h2><?php _e( 'Accounts Contact', '7listings' ); ?></h2>
<p class="description" style="margin: -1.6em 0 2em 0;"><?php _e( 'Leave empty to use same details as Company Owner.', '7listings' ); ?></p>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Name', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-prepend">
			<span class="add-on"><i class="icon-user"></i></span>
			<input type="text" name="invoice_name" value="<?php echo get_post_meta( get_the_ID(), 'invoice_name', true ); ?>">
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-prepend">
			<span class="add-on"><i class="icon-envelope-alt"></i></span>
			<input type="email" name="invoice_email" value="<?php echo get_post_meta( get_the_ID(), 'invoice_email', true ); ?>">
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Phone', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-prepend">
			<span class="add-on"><i class="icon-phone"></i></span>
			<input type="tel" name="invoice_phone" value="<?php echo get_post_meta( get_the_ID(), 'invoice_phone', true ); ?>">
		</span>
	</div>
</div>
<hr class="light">
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Paypal Email', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="email" name="paypal_email" value="<?php echo get_post_meta( get_the_ID(), 'paypal_email', true ); ?>">
	</div>
</div>

<?php do_action( 'company_edit_tab_payment_after' ); ?>
