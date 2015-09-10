<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Rental_Edit extends Sl_Core_Edit
{
	/**
	 * Save meta boxes
	 *
	 * @param $post_id
	 */
	function save_post( $post_id )
	{
		// Save booking resources
		// Because the number of booking resources is not set, we have to looking into $_POST
		$name          = sl_meta_key( 'booking', $this->post_type );
		$saved_booking = get_post_meta( $post_id, $name, true );
		$booking       = array();

		// Get the maximum index
		$max = 0;
		foreach ( $_POST as $k => $v )
		{
			if ( false !== strpos( $k, 'detail_title_' ) )
			{
				$index = intval( str_replace( 'detail_title_', '', $k ) );
				$max   = $max < $index ? $index : $max;
			}
		}
		$index = 0;

		/**
		 * Store all booking resource price (adult price or min price depends on adult price is set or not)
		 * The min value of this array will be used to set 'Lead in rate' price ('price_from')
		 */
		$resource_prices = array();
		for ( $i = 0; $i <= $max; $i ++ )
		{
			if ( empty( $_POST["detail_title_{$i}"] ) )
				continue;
			$title = sanitize_text_field( $_POST["detail_title_{$i}"] );
			$desc  = isset( $_POST["detail_desc_{$i}"] ) ? $_POST["detail_desc_{$i}"] : '';

			$photos       = empty( $_POST["booking_photo_ids_{$i}"] ) ? array() : $_POST["booking_photo_ids_{$i}"];
			$saved_photos = ! empty( $saved_booking[$i]['photos'] ) ? $saved_booking[$i]['photos'] : array();
			foreach ( $saved_photos as $saved_photo )
			{
				$photos[] = $saved_photo;
			}
			$photos = array_unique( array_filter( $photos ) );

			$price      = isset( $_POST["detail_price_{$i}"] ) ? array_filter( array_map( 'intval', $_POST["detail_price_{$i}"] ) ) : array();
			$allocation = isset( $_POST["detail_allocation_{$i}"] ) ? intval( $_POST["detail_allocation_{$i}"] ) : '';
			$book       = isset( $_POST["detail_book_{$i}"] ) ? $_POST["detail_book_{$i}"] : '';

			$booking[$index] = array(
				'title'      => $title,
				'desc'       => $desc,
				'photos'     => $photos,
				'price'      => $price,
				'allocation' => $allocation,
				'book'       => $book,
			);

			// Upsells
			$upsells                    = empty( $_POST["detail_upsells_{$i}"] ) ? 0 : 1;
			$booking[$index]['upsells'] = $upsells;
			if ( $upsells )
			{
				$items  = isset( $_POST["detail_upsells_item_{$i}"] ) ? array_map( 'trim', $_POST["detail_upsells_item_{$i}"] ) : array();
				$prices = isset( $_POST["detail_upsells_price_{$i}"] ) ? array_map( 'intval', $_POST["detail_upsells_price_{$i}"] ) : array();

				foreach ( $items as $k => $item )
				{
					if (
						( ! $item || 'item description' == strtolower( $item ) )
						|| ! isset( $prices[$k] )
					)
					{
						unset( $items[$k] );
						unset( $prices[$k] );
					}
				}

				if ( ! empty( $items ) && ! empty( $prices ) )
				{
					$booking[$index]['upsell_items']  = $items;
					$booking[$index]['upsell_prices'] = $prices;
				}
			}

			$resource_prices[] = Sl_Rental_Helper::get_resource_price( $booking[$index] );
			$index ++;
		}

		/*
		 *  Remove element "empty" in array
		 *  Once the price would not be filled, the element would be exist emptily in the array
		 * 	leave out the empty element so that price_form get the smallest value differing from emptiness.
		 * 	list of post would be aranged correctly.
		 */
		foreach ( $resource_prices as $key => $value )
		{
			if ( false === $value )
				unset( $resource_prices[$key] );
		}

		update_post_meta( $post_id, $name, $booking );
		update_post_meta( $post_id, 'price_from', min( $resource_prices ) );

		// Text and Select fields
		$fields = array( 'checkin', 'checkout' );
		$days   = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
		foreach ( $days as $day )
		{
			$fields[] = "business_hours_{$day}_from";
			$fields[] = "business_hours_{$day}_to";
		}
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, $_POST[$field] );
			else
				delete_post_meta( $post_id, $field );
		}

		// Checkboxes
		$fields = array( 'open_247' );
		foreach ( $days as $day )
		{
			$fields[] = "business_hours_$day";
		}
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, 1 );
			else
				delete_post_meta( $post_id, $field );
		}

		do_action( "{$this->post_type}_save_post", $post_id );
	}
}

new Sl_Rental_Edit( 'rental' );
