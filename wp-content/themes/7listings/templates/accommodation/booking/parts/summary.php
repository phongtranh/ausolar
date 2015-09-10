<div class="row checkin-time hidden">
	<div class="left"><?php _e( 'Check-In', '7listings' ); ?></div>
	<div class="right"></div>
</div>

<div class="row checkout-time hidden">
	<div class="left"><?php _e( 'Check-Out', '7listings' ); ?></div>
	<div class="right"></div>
</div>

<div class="row nights hidden">
	<div class="left"><?php _e( 'Nights', '7listings' ); ?></div>
	<div class="right"></div>
</div>

<div class="row guests hidden">
	<div class="left"></div>
	<div class="right"></div>
</div>

<div class="row extra-guests hidden">
	<div class="left"></div>
	<div class="right"></div>
</div>

<?php $zero = Sl_Currency::format( '0.00', 'type=plain' ); ?>
<div class="row total">
	<div class="left"><?php _e( 'Total', '7listings' ); ?></div>
	<div class="right"><?php echo $zero; ?></div>
</div>
