<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Booking Message', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Message that will be included in the booking email of customer.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input sl-block">
		<textarea name="booking_message" id="booking_message" placeholder="Message customer receives with booking confirmation email"><?php echo get_post_meta( get_the_ID(), 'booking_message', true ); ?></textarea>
	</div>
</div>

<hr class="light">

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Payment Policy', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Payment Policy is displayed on a single listing and in the booking form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input sl-block">
		<textarea name="paymentpol" id="paymentpol"><?php echo get_post_meta( get_the_ID(), 'paymentpol', true ); ?></textarea>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Cancellation Policy', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Cancellation Policy is displayed on a single listing and in the booking form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input sl-block">
		<textarea name="cancellpol" id="cancellpol"><?php echo get_post_meta( get_the_ID(), 'cancellpol', true ); ?></textarea>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Terms And Conditions', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Terms And Conditions is displayed on a single listing and in the booking form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input sl-block">
		<textarea name="terms" id="terms"><?php echo get_post_meta( get_the_ID(), 'terms', true ); ?></textarea>
	</div>
</div>

<hr class="light">