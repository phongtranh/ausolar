<?php

class Booking_Ajax
{
	private $post_type      = 'tour';
	private $post_id        = 1;
	private $resource_index = 0;

	function __construct()
	{
		$functions = array(
			'get_list_posts',
			'get_list_resources',
			'get_resource_info',
			'get_tour_allocations',
			'add_booking',
		);

		foreach( $functions as $function )
			add_action( 'wp_ajax_' . $function, array( $this, $function ) );
	}

	/**
	 * Ajax callback to get all post with specific post type
	 *
	 * @return void
	 */
	function get_list_posts()
	{
		check_ajax_referer( 'get-list-posts' );

		if( $this->get_obj() )
			wp_send_json_error();

		$html = sprintf( '<option value="-1">%s</option>', __( 'Select', '7listings'  ) );

		if ( ! empty ( $this->post_type ) )
		{
			$args = array(
				'posts_per_page'    => -1,
				'post_type'         => $this->post_type,
				'post_status'       => 'publish',
				'orderby'           => 'title',
				'order'             => 'ASC'
			);

			if ( 'tour' == $this->post_type )
				$args['meta_query'] = array(
					array(
						'key'       => 'tour_type',
						'value'     => 'tour-bundle',
						'compare'   => '!='
					)
				);

			$list = get_posts( $args );
			foreach ( $list as $post )
			{
				$html .= sprintf( '<option value="%s">%s</option>', $post->ID, $post->post_title );
			}
		}

		wp_send_json_success( array(
			'items'     => $html
		) );
	}

	/**
	 * Ajax callback to get all resources with specific post
	 *
	 * @return void
	 */
	function get_list_resources()
	{
		check_ajax_referer( 'get-list-resources' );

		if( $this->get_obj() )
			wp_send_json_error();

		$html = sprintf( '<option value="-1">%s</option>', __( 'Select', '7listings'  ) );

		$resources = $this->get_resources();

		if ( ! empty( $resources ) )
		{
			foreach ( $resources as $k => $resource )
			{
				$html .= sprintf( '<option value="%s">%s</option>', $k, $resource['title'] );
			}
		}

		wp_send_json_success( $html );
	}

	/**
	 * Ajax callback to get tour allocations with specific resource
	 * use only in tour post type
	 *
	 * @return void
	 */
	function get_tour_allocations()
	{
		check_ajax_referer( 'get-tour-allocations' );

		if( $this->get_obj() )
			wp_send_json_error();

		$resource   = $this->get_resource();
		$max        = Sl_Tour_Helper::get_max_allocation( $this->post_id, $resource['title'], $_GET['depart'], false );

		$html = '<div class="sl-settings">';
		$html .= '<div class="sl-label"><label>' .  __( 'Passengers', '7listings' ) . '</label></div>';
		$html .= '<div class="sl-input error exceed-allocation">' .  __( 'Max. guests: ', '7listings' ) . $max . '</div>';
		$html .= '</div>';
		$html .= '<div class="sl-sub-settings">';

		$types = array(
			'adults'   => array( __( 'Adults', '7listings' ), 'adult' ),
			'children' => array( __( 'Children', '7listings' ), 'child' ),
			'seniors'  => array( __( 'Seniors', '7listings' ), 'senior' ),
			'families' => array( __( 'Families', '7listings' ), 'family' ),
			'infants'  => array( __( 'Infants', '7listings' ), 'infant' ),
		);

		$not_free       = false !== Sl_Tour_Helper::get_resource_price( $resource );
		$lead_in_rate   = isset( $resource['lead_in_rate'] ) ? $resource['lead_in_rate'] : 'adult';

		foreach ( $types as $name => $type )
		{
			if ( $not_free && ( ! isset( $resource["price_{$type[1]}"] ) || '' === $resource["price_{$type[1]}"] ) )
			{
				continue;
			}

			$max_allocation = 'families' == $name ? floor( $max / 4 ) : $max;

			if ( ! $max_allocation )
			{
				continue;
			}

			$default        = apply_filters( 'tour_guests_select_default', 1, $resource );
			$default_option = 'families' == $name ? ceil( $default / 4 ) : $default;

			$items = "<select name='$name' id='guest-{$type[1]}' data-type='$type[1]' class='guest-type'>
						<option value='-1'>-</option>";

			for ( $i = 1; $i <= $max_allocation; $i ++ )
			{
				$items .= sprintf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $i ),
					selected( $lead_in_rate == $type[1] && $default_option == $i, true, false ),
					esc_html( $i )
				);
			}

			$items .= '</select>';

			$html .= sprintf( '<div class="sl-settings">
								<div class="sl-label">
									<label>%s</label>
								</div>
								<div class="sl-input">
									%s %s
								</div>
							</div>', $type[0], $items, Sl_Currency::format( $resource["price_{$type[1]}"] ) );
		}
		$html .= '</div>';

		wp_send_json_success( $html );
	}

	/**
	 * Ajax add booking
	 *
	 * @return void
	 */
	function add_booking()
	{
		global $settings;

		check_ajax_referer( 'add-booking' );

		if ( empty( $_POST['data'] ) )
		{
			wp_send_json_error();
		}

		$booking = array();

		// Parse data
		$data = wp_parse_args( $_POST['data'] );

		$booking['type']     = $data['sl-post-type'];
		$booking['post_id']  = $data['sl-post'];

		// Get resource
		$resources              = get_post_meta( $booking['post_id'], sl_meta_key( 'booking', $booking['type'] ), true );
		$resource               = $resources[$data['sl-resource']];
		$booking['resource']    = $resource['title'];

		// Get counter
		$counter = intval( sl_setting( 'counter' ) );
		$counter ++;
		$booking['booking_id']  = $counter;
		$settings['counter']    = $counter;
		update_option( THEME_SETTINGS, $settings );

		// Resource type
		$resource_type              = wp_get_post_terms( $booking['post_id'], sl_meta_key( 'tax_type', $booking['type'] ), array( 'fields' => 'names' ) );
		$booking['resource_type']   = empty( $resource_type ) ? '' : array_pop( $resource_type );

		$guests = 1;

		switch ( $booking['type'] )
		{
			case 'accommodation':
				$booking['checkin']     = $data['accommodation-checkin'];
				$booking['checkout']    = $data['accommodation-checkout'];
				$guests                 = $data['accommodation-guests'];
				break;
			case 'rental':
				$booking['checkin']  = $data['rental-checkin'];
				$booking['checkout'] = $data['rental-checkout'];
				break;
			case 'tour':
				$booking['day']         = $data['tour-depart'];
				$departs                = $resource['depart'];
				$booking['depart_time'] = $departs[$data['tour-time']];

				$guests = 0;
				$types  = array( 'adults', 'children', 'seniors', 'families', 'infants' );
				foreach ( $types as $type )
				{
					$nums = $data[$type];
					if ( ! empty ( $nums ) )
					{
						$booking[$type] = $nums;
						$guests         += ( 'families' == $type ) ? $nums * 4 : $nums;
					}
				}
				break;
		}

		// Set upsells items
		$upsells = array();
		if( ! empty( $resource['upsell_items'] ) )
		{
			foreach ( $resource['upsell_items'] as $k => $item )
			{
				if ( ! empty( $data["upsell-{$k}"] ) && - 1 != $data["upsell-{$k}"] )
				{
					$upsells[] = array(
						'num'  => $data["upsell-{$k}"],
						'name' => $item,
					);
				}
			}
		}
		$booking['upsells']         = $upsells;
		$booking['payment_gateway'] = __( 'Office', '7listings' );
		$booking['amount']          = $data['total-prices'];

		// Set guests information
		$booking['guests']  = array(
			array(
				'first' => $data['customer-first-name'],
				'last'  => $data['customer-last-name'],
				'email' => $data['customer-email'],
				'phone' => $data['customer-phone'],
			),
		);
		for ( $i = 1; $i < $guests; $i ++ )
		{
			$booking['guests'][$i] = array(
				'first' => '',
				'last'  => '',
				'email' => '',
				'phone' => '',
			);
		}

		$booking['customer_message'] = $data['customer-message'];

		// Create booking post
		$booking_post = array(
			'post_type'   => 'booking',
			'post_title'  => sprintf( __( 'Booking #%d', '7listings' ), $counter ),
			'post_status' => 'publish',
		);

		$booking_post_id = wp_insert_post( $booking_post );

		foreach ( $booking as $k => $v )
		{
			update_post_meta( $booking_post_id, $k, $v );
		}

		wp_send_json_success( admin_url() . 'post.php?post=' . $booking_post_id . '&action=edit' );
	}

	/**
	 * Get object from json and set values to variables
	 *
	 * @return boolean
	 */
	function get_obj()
	{
		if ( empty( $_GET['obj'] ) )
		{
			return true;
		}

		$obj = json_decode( str_replace( '\\', '', $_GET['obj'] ) );

		$this->post_type        = $obj->post_type;
		$this->post_id          = $obj->post_id;
		$this->resource_index   = $obj->resource_index;

		return false;
	}

	/**
	 * Get resources
	 *
	 * @return array
	 */
	function get_resources()
	{
		return get_post_meta( $this->post_id, sl_meta_key( 'booking', $this->post_type ), true );
	}

	/**
	 * Get resource
	 *
	 * @return array
	 */
	function get_resource()
	{
		$resources  = $this->get_resources();
		return $resources[$this->resource_index];
	}

	/**
	 * Ajax callback get resource's information
	 *
	 * @return void
	 */
	function get_resource_info()
	{
		check_ajax_referer( 'get-resource-info' );

		if ( $this->get_obj() )
			wp_send_json_error();

		$resource   = $this->get_resource();
		$args       = array();
		$prices     = array();

		// List options for number of guest. Use only in accommodation post type
		$number_of_guests   = '';
		$max_occupancy      = $resource['max_occupancy'];

		if ( ! empty( $max_occupancy ) )
		{
			for ( $i = 1; $i <= $max_occupancy; $i ++ )
			{
				$number_of_guests .= sprintf( '<option value="%d">%d</option>', $i, $i );
			}

			$args['numberOfGuests']     = $number_of_guests;
			$prices['maxOccupancy']     = $max_occupancy;
		}

		// Check and set unbookable days
		$days           = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
		$allowed_days   = array();

		foreach ( $days as $k => $day )
		{
			if ( ! empty( $resource["{$day}_depart"] ) )
			{
				$allowed_days[] = $k;
			}
		}

		$args['allowedDays']  = implode( ',', $allowed_days );
		$args['allocation']   = empty( $resource['allocation'] ) ? 0 : $resource['allocation'];

		$unbookable_days = '';

		switch ( $this->post_type )
		{
			case 'tour':
				$unbookable_days = Sl_Tour_Helper::get_unbookable_dates( $this->post_id, $resource, $this->resource_index );

				$types = array( 'adult', 'child', 'senior', 'family', 'infant' );

				foreach ( $types as $type )
				{
					$price_type = $resource['price_' . $type ];
					if ( ! empty( $price_type ) )
						$prices[$type] = $price_type;
				}

				break;
			case 'rental':
				$unbookable_days = Sl_Rental_Helper::get_unbookable_dates( $this->post_id, $resource, $this->resource_index );

				if ( ! empty( $resource['price'] ) )
					$prices['price'] = $resource['price']; // array

				break;
			case 'accommodation':
				$unbookable_days = Sl_Accommodation_Helper::get_unbookable_dates( $this->post_id, $resource, $this->resource_index );

				if ( ! empty( $resource['occupancy'] ) )
					$prices['occupancy'] = $resource['occupancy'];

				if ( ! empty( $resource['price'] ) )
					$prices['price'] = $resource['price'];

				if ( ! empty( $resource['price_extra'] ) )
					$prices['priceExtra'] = $resource['price_extra'];

				break;
		}

		if ( ! empty( $unbookable_days ) )
			$args['unbookable'] = $unbookable_days;

		// Get upsell items
		if ( ! empty( $resource['upsell_items'] ) )
		{
			$upsells = '<div class="sl-settings"><div class="sl-label"><label>';
			$upsells .= __( 'Upsells items', '7listings' );
			$upsells .= '</label></div></div>';

			$upsells .= '<div class="sl-sub-settings ">';

			$upsell_prices = array();

			foreach ( $resource['upsell_items'] as $k => $item )
			{
				if ( empty( $item ) || empty( $resource['upsell_prices'][$k] ) )
				{
					continue;
				}

				$upsells_items = "<select id='upsell-{$k}' name='upsell-{$k}' class='sl-input-tiny upsells-item'>";
				$upsells_items .= '<option value="-1">-</option>';
				for ( $i = 1; $i <= 10; $i ++ )
				{
					$upsells_items .= sprintf( '<option value="%d">%d</option>', $i, $i );
				}
				$upsells_items .= '</select>';

				$upsells .= sprintf( '	<div class="sl-settings">
									<div class="sl-label">
										<label></label>
									</div>
									<div class="sl-input upsell">
										%s
										<label for="upsell_%d" class="sl-label-inline description">%s</label>
										%s
									</div>
								</div>', $upsells_items, $k, $item, Sl_Currency::format( $resource['upsell_prices'][$k] ) );

				$upsell_prices[] = $resource['upsell_prices'][$k];
			}

			$upsells .= "</div>";
			$prices['upsellPrices'] = $upsell_prices;

			$args['upsells'] = $upsells;
		}

		// Get tour daily time
		if ( ! empty( $resource['depart'] ) )
		{
			$departs            = $resource['depart'];
			$tour_daily_time    = '';

			if ( count( $departs ) )
			{
				foreach ( $departs as $k => $depart )
				{
					if ( empty( $depart ) )
					{
						continue;
					}

					$tour_daily_time .= sprintf( '<option value="%s">%s</option>', $k, esc_html( Sl_Helper::time_format( $depart ) ) );
				}

				$args['tourDailyTime'] = $tour_daily_time;
			}
		}

		$args['prices'] = $prices;

		wp_send_json_success( $args );
	}
}

new Booking_Ajax();


