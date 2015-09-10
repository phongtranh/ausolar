<?php

/**
 * Change data appears in booking edit page
 */
class Sl_Cart_Booking_Edit
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		add_filter( 'sl_booking_items', array( $this, 'booking_items' ) );
	}

	/**
	 * Show booking items in edit booking page
	 *
	 * @param string $output HTML output of booking items
	 * @return string
	 */
	public function booking_items( $output = '' )
	{
		if ( 'cart' != get_post_meta( get_the_ID(), 'type', true ) )
		{
			return $output;
		}
		ob_start();

		$booking_management      = new Sl_Booking_Management;
		$cart_booking_management = new Sl_Cart_Booking_Management;
		$items                   = get_post_meta( get_the_ID(), 'bookings', true );

		foreach ( $items as $item )
		{
			?>
			<tr>
				<td class="thumb"><?php sl_broadcasted_thumbnail( 'sl_thumb_tiny', '', $item['post'] ); ?></td>
				<td class="listing"><?php $cart_booking_management->show_item_column( 'listing', $item ); ?></td>
				<td class="upsells"><?php $cart_booking_management->show_item_column( 'upsells', $item ); ?></td>
				<td class="date"><?php $cart_booking_management->show_item_column( 'booking_date', $item ); ?></td>
				<td class="guests"><?php $cart_booking_management->show_item_column( 'num_guests', $item ); ?></td>
				<td class="total price"><?php $cart_booking_management->show_item_column( 'amount', $item );; ?></td>
			</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="6" class="total price">
				<strong>
					<?php
					_e( 'Total: ', '7listings' );
					$booking_management->show( 'total', get_the_ID() );
					?>
				</strong>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

}
