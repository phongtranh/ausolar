<?php $booking_management = new Sl_Booking_Management; ?>
<table class="widefat order-items">
	<thead>
	<tr>
		<th class="thumb"></th>
		<th class="listing"><?php _e( 'Listing', '7listings' ); ?></th>
		<th class="upsells"><?php _e( 'Upsells', '7listings' ); ?></th>
		<th class="date"><?php _e( 'Date', '7listings' ); ?></th>
		<th class="guests"><?php _e( '#Guests', '7listings' ); ?></th>
		<th class="total price"><?php _e( 'Total', '7listings' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	/**
	 * Allow other modules to output booking item data
	 * For ex.: cart module, 7tour-bundles plugin
	 * @param string $output HTML output of booking item
	 */
	if ( $output = apply_filters( 'sl_booking_items', '' ) )
	{
		echo $output;
	}
	else
	{
		?>
		<tr>
			<td class="thumb"><?php sl_broadcasted_thumbnail( 'sl_thumb_tiny', '', get_post_meta( get_the_ID(), 'post_id', true ) ); ?></td>
			<td class="listing">
				<?php $booking_management->show( 'listing', get_the_ID() ); ?>
			</td>
			<td class="upsells"><?php $booking_management->show( 'upsells', get_the_ID() ); ?></td>
			<td class="date"><?php $booking_management->show( 'booking_date', get_the_ID() ); ?></td>
			<td class="guests"><?php $booking_management->show( 'num_guests', get_the_ID() ); ?></td>
			<td class="total price"><?php $booking_management->show( 'total', get_the_ID() ); ?></td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
