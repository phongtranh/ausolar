<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Accommodation_Edit extends Sl_Core_Edit
{
	/**
	 * Save meta boxes
	 *
	 * @param $post_id
	 */
	function save_post( $post_id )
	{
		// Save booking data
		// Because the number of booking resources is not set, we have to looking into $_POST
		$name          = sl_meta_key( 'booking', $this->post_type );
		$saved_booking = get_post_meta( $post_id, $name, true );
		$hotel_booking = array();

		// Get the maximum index
		$max = 0;
		foreach ( $_POST as $k => $v )
		{
			if ( false !== strpos( $k, 'hotel_detail_title_' ) )
			{
				$index = intval( str_replace( 'hotel_detail_title_', '', $k ) );
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
			if ( empty( $_POST["hotel_detail_title_{$i}"] ) )
				continue;
			$title = sanitize_text_field( $_POST["hotel_detail_title_{$i}"] );
			$desc  = isset( $_POST["hotel_detail_desc_{$i}"] ) ? $_POST["hotel_detail_desc_{$i}"] : '';

			$photos       = empty( $_POST["booking_photo_ids_{$i}"] ) ? array() : $_POST["booking_photo_ids_{$i}"];
			$saved_photos = ! empty( $saved_booking[$i]['photos'] ) ? $saved_booking[$i]['photos'] : array();
			foreach ( $saved_photos as $saved_photo )
			{
				$photos[] = $saved_photo;
			}
			$photos = array_filter( $photos );
			$photos = array_unique( $photos );

			$price         = isset( $_POST["hotel_detail_price_{$i}"] ) ? intval( $_POST["hotel_detail_price_{$i}"] ) : '';
			$price_extra   = isset( $_POST["hotel_detail_price_extra_{$i}"] ) ? intval( $_POST["hotel_detail_price_extra_{$i}"] ) : '';
			$occupancy     = isset( $_POST["hotel_detail_occupancy_{$i}"] ) ? intval( $_POST["hotel_detail_occupancy_{$i}"] ) : '';
			$max_occupancy = isset( $_POST["hotel_detail_max_occupancy_{$i}"] ) ? intval( $_POST["hotel_detail_max_occupancy_{$i}"] ) : '';
			$allocation    = isset( $_POST["hotel_detail_allocation_{$i}"] ) ? intval( $_POST["hotel_detail_allocation_{$i}"] ) : '';

			// Don't save price if it's zero
			$price  = $price ? $price : '';
			$price_extra  = $price_extra ? $price_extra : '';

			$book = isset( $_POST["hotel_detail_book_{$i}"] ) ? $_POST["hotel_detail_book_{$i}"] : '';

			$hotel_booking[$index] = array(
				'title'         => $title,
				'desc'          => $desc,
				'photos'        => $photos,
				'price'         => $price,
				'occupancy'     => $occupancy,
				'price_extra'   => $price_extra,
				'max_occupancy' => $max_occupancy,
				'allocation'    => $allocation,
				'book'          => $book,
			);
			$resource_prices[] = intval( $price );
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

		update_post_meta( $post_id, $name, $hotel_booking );
		update_post_meta( $post_id, 'price_from', min( $resource_prices ) );

		// Simple fields
		$fields = array( 'checkin', 'checkout' );
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, $_POST[$field] );
			else
				delete_post_meta( $post_id, $field );
		}

		do_action( "{$this->post_type}_save_post", $post_id );
	}
}

new Sl_Accommodation_Edit( 'accommodation' );
