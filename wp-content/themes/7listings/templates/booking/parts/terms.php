<div class="sl-field error-box hidden">
	<div class="sl-input">
		<div class="error error-terms"></div>
	</div>
</div>

<?php
$payment = get_post_meta( get_the_ID(), 'paymentpol', true );
$cancel  = get_post_meta( get_the_ID(), 'cancellpol', true );
$terms   = get_post_meta( get_the_ID(), 'terms', true );
?>
<?php if ( $payment || $cancel || $terms ): ?>
	<div class="sl-field booking-policies required">
		<div class="sl-input">
			<input type="checkbox" name="agree" id="agree" required tabindex="207"><label for="agree" class="sl-label-inline"><?php _e( 'I have read and agree to the <a href="#policies" class="terms-link" data-toggle="modal" title="Read the terms and conditions">terms and conditions</a>', '7listings' ); ?>.</label>
		</div>
	</div>
<?php endif; ?>
<div class="sl-field newsletter">
	<div class="sl-input">
		<input type="checkbox" name="subscribe" id="subscribe" tabindex="208"><label for="subscribe" class="sl-label-inline"><?php _e( 'Yes, please email me future specials.', '7listings' ); ?></label>
	</div>
</div>
 