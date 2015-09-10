<?php

class Sl_Cart_Booking_Management
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		add_filter( 'booking_management_column', array( $this, 'show' ), 10, 3 );
	}

	/**
	 * Show the columns for the edit booking
	 *
	 * @param string $column Column name
	 * @param int    $item   Booking item data in cart
	 * @param bool   $echo   Whether or not echo the data or return it only
	 * @return string
	 */
	public function show_item_column( $column, $item, $echo = true )
	{
		/**
		 * Allow other modules to change the content of cart booking management column
		 *
		 * @param string $output  The content of the column
		 * @param string $column  Column ID
		 * @param int    $post_id Post (booking ID)
		 * @return string The content of the column
		 */
		if ( $output = apply_filters( 'cart_booking_management_column', '', $column, $item ) )
		{
			if ( $echo )
				echo $output;

			return $output;
		}

		$item = $item['data'];
		switch ( $column )
		{
			case 'listing':
				$output = '<span class="' . get_post_type( $item['post_id'] ) . ' cart-item"><span class="listing-title"><a href="' . get_permalink( $item['post_id'] ) . '" target="_blank">' . get_the_title( $item['post_id'] ) . '</a></span><span class="resource-title">' . $item['resource'] . '</span></span>';
				break;
			case 'booking_date':
				if ( 'tour' == $item['type'] )
				{
					$output = '<span class="cart-item ' . $item['type'] . '">';
					$output .= '<span class="date departure-date">' . $item['day'] . '</span>';
					$output .= '<span class="date departure-time">' . $item['depart_time'] . '</span>';
					$output .= '</span>';
				}
				else
				{
					$in     = explode( ' ', $item['checkin'] );
					$in     = array_shift( $in );
					$out    = explode( ' ', $item['checkout'] );
					$out    = array_shift( $out );
					$output = '<span class="cart-item ' . $item['type'] . '"><span class="date from">' . $in . '</span><br><span class="date to">' . $out . '</span></span>';
				}
				break;
			case 'num_guests':
				if ( 'tour' == $item['type'] )
				{
					$num_guests = array();
					$types      = array(
						'adults'   => __( 'Adults', '7lisitings' ),
						'children' => __( 'Children', '7lisitings' ),
						'seniors'  => __( 'Seniors', '7lisitings' ),
						'families' => __( 'Families', '7lisitings' ),
						'infants'  => __( 'Infants', '7lisitings' ),
					);
					foreach ( $types as $type => $label )
					{
						$num = isset( $item[$type] ) ? $item[$type] : 0;
						if ( empty( $num ) || - 1 == $num )
							continue;

						$num_guests[] = '<dt class="guest-quantity">' . $num . '</dt><dd class="guest-type">' . $label . '</dd>';
					}
					$output = '<dl class="tour-guests cart-item">' . implode( ' ', $num_guests ) . '</dl>';
				}
				else
				{
					$guests = $item['guests'];
					if ( 'accommodation' == $item['type'] )
						$output = '<dl class="accom-guests cart-item"><dt class="quantity">' . count( $guests ) . '</dt><dd class="guest">' . __( 'Guest', '7listings' ) . '</dd></dl>';
					elseif ( 'rental' == $item['type'] )
						$output = '<dl class="rental-items cart-item"><dt class="quantity">' . count( $guests ) . '</dt><dd class="guest">' . __( 'Items', '7listings' ) . '</dd></dl>';
					else
						$output = count( $guests );
				}
				break;
			case 'upsells':
				if ( empty( $item['upsells'] ) )
					continue;

				$upsells_echo = array();
				foreach ( $item['upsells'] as $upsell )
				{
					$upsells_echo[] = '<dt class="upsell-quantity">' . $upsell['num'] . '</dt><dd class="upsell-description">' . $upsell['name'] . '</dd>';
				}
				$output = '<dl class="upsell-items">' . implode( ' ', $upsells_echo ) . '</dl>';
				break;
			case 'amount':
				if ( ! empty( $item['amount'] ) )
				{
					$output = Sl_Currency::format( $item['amount'], 'type=plain' );
				}
				break;
		}

		if ( $echo )
			echo $output;

		return $output;
	}

	/**
	 * Show the columns for the edit screen
	 *
	 * @param string $data    Column data
	 * @param string $column  Column name
	 * @param int    $post_id Current post ID
	 *
	 * @return string
	 */
	public function show( $data, $column, $post_id )
	{
		if ( 'cart' != get_post_meta( $post_id, 'type', true ) )
			return $data;

		$items = get_post_meta( $post_id, 'bookings', true );
		$data  = array();

		switch ( $column )
		{
			case 'listing':
			case 'booking_date':
			case 'num_guests':
			case 'upsells':
				foreach ( $items as $item )
				{
					$data[] = $this->show_item_column( $column, $item, false );
				}
				break;
			case 'who_paid':
                $item  = reset( $items );
                $guest = reset( $item['data']['guests'] );

				$name  = array();
				if ( ! empty( $guest['first'] ) )
					$name[] = $guest['first'];
				if ( ! empty( $guest['last'] ) )
					$name[] = $guest['last'];
				$name = implode( ' ', $name );

				$data[] = '<span class="name">' . $name . '</span><br><a class="email" href="mailto:' . $guest['email'] . '">' . $guest['email'] . '</a>';
				break;
		}

		return implode( '<br>', $data );
	}
}
