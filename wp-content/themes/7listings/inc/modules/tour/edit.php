<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Tour_Edit extends Sl_Core_Edit
{
	/**
	 * Enqueue scripts and styles for editing page
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		$screen = get_current_screen();

		if ( 'post' !== $screen->base || $this->post_type !== $screen->post_type )
			return;

		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'select2' );

		parent::enqueue_scripts();
	}

	/**
	 * Save meta boxes
	 *
	 * @param $post_id
	 */
	public function save_post( $post_id )
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
			if ( false !== strpos( $k, 'tour_detail_title_' ) )
			{
				$index = intval( str_replace( 'tour_detail_title_', '', $k ) );
				$max   = $max < $index ? $index : $max;
			}
		}
		$index = 0;

		/**
		 * Store all booking resource price (adult price or min price depends on adult price is set or not)
		 * The min value of this array will be used to set 'Lead in rate' price ('price_from')
		 */
		$resource_prices = array();
		$prefix          = 'tour_detail';
		for ( $i = 0; $i <= $max; $i ++ )
		{
			if ( empty( $_POST["tour_detail_title_{$i}"] ) )
				continue;
			$title = sanitize_text_field( $_POST["tour_detail_title_{$i}"] );
			$desc  = isset( $_POST["tour_detail_desc_{$i}"] ) ? $_POST["tour_detail_desc_{$i}"] : '';

			$photos       = empty( $_POST["booking_photo_ids_{$i}"] ) ? array() : (array) $_POST["booking_photo_ids_{$i}"];
			$saved_photos = isset( $saved_booking[$i]['photos'] ) ? $saved_booking[$i]['photos'] : array();
			foreach ( $saved_photos as $saved_photo )
			{
				$photos[] = $saved_photo;
			}
			$photos = array_filter( $photos );
			$photos = array_unique( $photos );

			$price_adult  = Sl_Form::post_number( "{$prefix}_price_adult_{$i}" );
			$price_senior = Sl_Form::post_number( "{$prefix}_price_senior_{$i}" );
			$price_child  = Sl_Form::post_number( "{$prefix}_price_child_{$i}" );
			$price_infant = Sl_Form::post_number( "{$prefix}_price_infant_{$i}" );
			$price_family = Sl_Form::post_number( "{$prefix}_price_family_{$i}" );

			$lead_in_rate = isset( $_POST["{$prefix}_lead_in_rate_{$i}"] ) ? wp_strip_all_tags( $_POST["{$prefix}_lead_in_rate_{$i}"] ) : '';

			$allocation     = Sl_Form::post_number( "{$prefix}_allocation_{$i}" );
			$departure_type = isset( $_POST["{$prefix}_departure_type_{$i}"] ) ? wp_strip_all_tags( $_POST["{$prefix}_departure_type_{$i}"] ) : '';
			$location       = empty( $_POST["{$prefix}_location_{$i}"] ) ? 0 : 1;
			$latitude       = sanitize_text_field( $_POST["latitude_{$i}"] );
			$longitude      = sanitize_text_field( $_POST["longitude_{$i}"] );
			$area           = isset( $_POST["{$prefix}_area_{$i}"] ) ? array_map( 'intval', $_POST["{$prefix}_area_{$i}"] ) : array();

			$booking[$index] = array(
				'title'          => $title,
				'desc'           => $desc,
				'photos'         => $photos,
				'price_adult'    => $price_adult,
				'price_senior'   => $price_senior,
				'price_child'    => $price_child,
				'price_infant'   => $price_infant,
				'price_family'   => $price_family,
				'lead_in_rate'   => $lead_in_rate,
				'allocation'     => $allocation,
				'departure_type' => $departure_type,
				'location'       => $location,
				'latitude'       => $latitude,
				'longitude'      => $longitude,
				'area'           => $area,
			);

			// Update tour location area to 'location' taxonomy
			wp_set_post_terms( $post_id, $area, 'location', true );

			// Daily departures
			if ( 'daily' == $departure_type )
			{
				$booking[$index]['depart'] = $_POST["{$prefix}_depart_{$i}"];
				$booking[$index]['arrive'] = $_POST["{$prefix}_arrive_{$i}"];
			}
			// Departures by days
			elseif ( 'specific' == $departure_type )
			{
				$days = array(
					'mon' => __( 'Monday', '7listings' ),
					'tue' => __( 'Tuesday', '7listings' ),
					'wed' => __( 'Wednesday', '7listings' ),
					'thu' => __( 'Thursday', '7listings' ),
					'fri' => __( 'Friday', '7listings' ),
					'sat' => __( 'Saturday', '7listings' ),
					'sun' => __( 'Sunday', '7listings' ),
				);
				foreach ( $days as $day => $day_name )
				{
					if ( empty( $_POST["tour_detail_{$day}_depart_{$i}"] ) )
						continue;
					$depart = array_filter( $_POST["{$prefix}_{$day}_depart_{$i}"] );
					$arrive = array_filter( $_POST["{$prefix}_{$day}_arrive_{$i}"] );

					if ( empty( $depart ) || empty( $arrive ) )
						continue;
					$booking[$index]["{$day}_depart"] = $depart;
					$booking[$index]["{$day}_arrive"] = $arrive;
				}
			}

			// Upsells
			$upsells                    = empty( $_POST["{$prefix}_upsells_{$i}"] ) ? 0 : 1;
			$booking[$index]['upsells'] = $upsells;
			if ( $upsells )
			{
				$items       = isset( $_POST["{$prefix}_upsells_item_{$i}"] ) ? $_POST["{$prefix}_upsells_item_{$i}"] : array();
				$prices      = isset( $_POST["{$prefix}_upsells_price_{$i}"] ) ? $_POST["{$prefix}_upsells_price_{$i}"] : array();
				$multipliers = isset( $_POST["{$prefix}_upsells_multiplier_{$i}"] ) ? $_POST["{$prefix}_upsells_multiplier_{$i}"] : array();

				$items       = array_map( 'trim', $items );
				$prices      = array_map( 'intval', $prices );
				$multipliers = array_map( 'intval', $multipliers );

				foreach ( $items as $k => $item )
				{
					if (
						( ! $item || 'item description' == strtolower( $item ) )
						|| ! isset( $prices[$k] )
					)
					{
						unset( $items[$k] );
						unset( $prices[$k] );
						unset( $multipliers[$k] );
					}
				}

				if ( ! empty( $items ) && ! empty( $prices ) )
				{
					$booking[$index]['upsell_items']       = $items;
					$booking[$index]['upsell_prices']      = $prices;
					$booking[$index]['upsell_multipliers'] = $multipliers;
				}
			}

			$resource_prices[] = Sl_Tour_Helper::get_resource_price( $booking[$index] );
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
		update_post_meta( $post_id, 'price_from', $resource_prices ? min( $resource_prices ) : 0 );

		do_action( "{$this->post_type}_save_post", $post_id );
	}
}

new Sl_Tour_Edit( 'tour' );
