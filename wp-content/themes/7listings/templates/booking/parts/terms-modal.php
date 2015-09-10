<?php
$payment = get_post_meta( get_the_ID(), 'paymentpol', true );
$cancel  = get_post_meta( get_the_ID(), 'cancellpol', true );
$terms   = get_post_meta( get_the_ID(), 'terms', true );

if ( ! $payment && ! $cancel && ! $terms )
{
	return;
}
?>
<div id="policies" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3><?php the_title(); ?></h3>
	</div>
	<div class="modal-body">
		<?php if ( $payment ) : ?>
			<div class='payment-policy'>
				<h5><?php _e( 'Payment Policy', '7listings' ); ?></h5>
				<p><?php echo esc_html( $payment ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $cancel ) : ?>
			<div class='cancel-policy'>
				<h5><?php _e( 'Cancellation Policy', '7listings' ); ?></h5>
				<p><?php echo esc_html( $cancel ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( $terms ) : ?>
			<div class='terms-policy'>
				<h5><?php _e( 'Terms And Conditions', '7listings' ); ?></h5>
				<p><?php echo esc_html( $terms ); ?></p>
			</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<button class="button primary agree" data-dismiss="modal"><?php _e( 'I agree', '7listings' ); ?></button>
		<button class="button" data-dismiss="modal"><?php _e( 'Close', '7listings' ); ?></button>
	</div>
</div>
