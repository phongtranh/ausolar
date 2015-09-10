<?php
$payment = get_post_meta( get_the_ID(), 'paymentpol', true );
$cancel  = get_post_meta( get_the_ID(), 'cancellpol', true );
$terms   = get_post_meta( get_the_ID(), 'terms', true );

if ( ! $payment && ! $cancel && ! $terms )
	return;

printf( '
	<a href="#policies" data-toggle="modal" id="listing-policies">%s</a>

	<div id="policies" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>%s</h3>
		</div>

		<div class="modal-body">
			%s
			%s
			%s
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal">%s</button>
		</div>
	</div>',
	__( 'Read policies', '7listings' ),
	get_the_title(),
	! $payment ? '' : sprintf( '
		<section class="payment-policy">
			<h5>%s</h5>
			%s
		</section>',
		__( 'Payment Policy', '7listings' ),
		$payment
	),
	! $cancel ? '' : sprintf( '
		<section class="cancel-policy">
			<h5>%s</h5>
			%s
		</section>',
		__( 'Cancellation Policy', '7listings' ),
		$cancel
	),
	! $terms ? '' : sprintf( '
		<section class="terms-policy">
			<h5>%s</h5>
			%s
		</section>',
		__( 'Terms And Conditions', '7listings' ),
		$terms
	),
	__( 'Close', '7listings' )
);
