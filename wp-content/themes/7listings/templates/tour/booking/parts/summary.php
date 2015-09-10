<div class="row depart hidden">
	<div class="left"><?php _e( 'Depart', '7listings' ); ?></div>
	<div class="right">
		<span class="day"></span>
		<span class="time"></span>
	</div>
</div>

<div class="passengers hidden">
	<h4><?php _e( 'Passengers', '7listings' ); ?></h4>
	<div class="row adults hidden">
		<div class="left"><?php _e( '0 Adults', '7listings' ); ?></div>
		<div class="right"></div>
	</div>
	<div class="row children hidden">
		<div class="left"><?php _e( '0 Children', '7listings' ); ?></div>
		<div class="right"></div>
	</div>
	<div class="row seniors hidden">
		<div class="left"><?php _e( '0 Seniors', '7listings' ); ?></div>
		<div class="right"></div>
	</div>
	<div class="row families hidden">
		<div class="left"><?php _e( '0 Families', '7listings' ); ?></div>
		<div class="right"></div>
	</div>
	<div class="row infants hidden">
		<div class="left"><?php _e( '0 Infants', '7listings' ); ?></div>
		<div class="right"></div>
	</div>

	<div class="row total_guests subtotal hidden">
		<div class="left"><?php _e( 'Total', '7listings' ); ?></div>
		<div class="right"></div>
	</div>
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
