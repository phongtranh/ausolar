<div class="row checkin-time hidden">
	<div class="left"><?php _e( 'Pick Up', '7listings' ); ?></div>
	<div class="right"></div>
</div>

<div class="row checkout-time hidden">
	<div class="left"><?php _e( 'Drop Off', '7listings' ); ?></div>
	<div class="right"></div>
</div>

<div class="row nights hidden">
	<div class="left"><?php _e( 'Days', '7listings' ); ?></div>
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

<?php
$zero = Sl_Currency::format( '0.00', 'type=plain' );

if ( ! empty( $resource['upsells'] ) && ! empty( $resource['upsell_items'] ) )
{
	echo '
		<div class="options hidden">
			<h4>' . __( 'Options', '7listings' ) . '</h4>
	';

	foreach ( $resource['upsell_items'] as $k => $item )
	{
		echo "
			<div class='row upsell_$k hidden'>
				<div class='left'>$item</div>
				<div class='right'>$zero</div>
			</div>
		";
	}

	echo '
		</div>
	';
}
?>

<div class="row total">
	<div class="left"><?php _e( 'Total', '7listings' ); ?></div>
	<div class="right"><?php echo $zero; ?></div>
</div>
