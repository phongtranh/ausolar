<?php

/**
 * This class will hold all things for accommodation management page
 */
class Sl_Booking_Management extends Peace_Post_Management
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->post_type = 'booking';
		parent::__construct();
	}

	/**
	 * Change the columns for the edit screen
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function columns( $columns )
	{
		$columns = array(
			'cb'           => '<input type="checkbox">',
			'id'           => __( 'ID', '7listings' ),
			'book_on'      => __( 'Booked', '7listings' ),
			'total'        => __( 'Total', '7listings' ),
			'who_paid'     => __( 'Name', '7listings' ),
			'listing'      => __( 'Listing', '7listings' ),
			'booking_date' => __( 'Date', '7listings' ),
			'num_guests'   => __( 'Guests', '7listings' ),
			'upsells'      => __( 'Upsells', '7listings' ),
			'paid'         => __( 'Paid', '7listings' ),
		);

		return $columns;
	}

	/**
	 * Show the columns for the edit screen
	 *
	 * @param string $column
	 * @param int    $post_id
	 * @param bool   $echo Echo data?
	 *
	 * @return string
	 */
	public function show( $column, $post_id, $echo = true )
	{
		$listing = get_post_meta( $post_id, 'post_id', true );
		$type    = get_post_meta( $post_id, 'type', true );

		/**
		 * Allow other modules to change the content of booking management column
		 * This is used in (at least) cart module, tour ticket plugin
		 *
		 * @param string $output  The content of the column
		 * @param string $column  Column ID
		 * @param int    $post_id Post (booking ID)
		 * @return string The content of the column
		 */
		if ( $output = apply_filters( 'booking_management_column', '', $column, $post_id ) )
		{
			if ( $echo )
				echo $output;

			return $output;
		}

		switch ( $column )
		{
			case 'id':
				$permalink = get_permalink( $post_id );
				$output    = '<a href="' . $permalink . '">' . get_post_meta( $post_id, 'booking_id', true ) . '</a>';
				$output .= '<div class="row-actions">';
				if ( current_user_can( 'administrator' ) )
				{
					$output .= '
					<span class="edit"><a href="' . get_edit_post_link( $post_id ) . '">' . __( 'Edit', '7listings' ) . '</a> | </span>
					<span class="trash"><a class="submitdelete" title="' . __( 'Delete booking', '7listings' ) . '" href="' . get_delete_post_link( $post_id ) . '">' . __( 'Trash', '7listings' ) . '</a> | </span>
				';
				}
				$type = get_post_meta( $post_id, 'type', true );
				$output .= '
					<span class="view"><a href="' . $permalink . '" target="_blank">' . __( 'View', '7listings' ) . '</a> | </span>
					<a href="#" class="email" data-booking_id="' . $post_id . '" data-type="' . $type . '">' . __( 'Email', '7listings' ) . '</a>
				</div>
				<div class="media-modal hidden" id="' . $post_id . '-email-form">
					<a class="media-modal-close" href="#"><span class="media-modal-icon"></span></a>
					<div class="media-modal-content">
						<div class="media-frame hide-menu hide-router hide-toolbar">
							<div class="media-frame-title">
								<h1>' . __( 'Send booking email', '7listings' ) . '</h1>
							</div>
							<div class="media-frame-content">
								<input type="email" placeholder="' . __( 'Enter email address', '7listings' ) . '">
								<button class="button-primary">' . __( 'Send', '7listings' ) . '</button>
								<span class="spinner"></span>
							</div>
						</div>
					</div>
				</div>
			';
				break;
			case 'book_on':
				$output = '<span class="date booking-date">' . get_the_time( 'M j, Y', $post_id ) . '</span><span class="time booking-time">' . get_the_time( 'g:i a', $post_id ) . '</span>';
				break;
			case 'listing':
				$output = '<span class="listing-title"><a href="' . get_permalink( $listing ) . '" target="_blank">' . get_the_title( $listing ) . '</a></span><span class="resource-title">' . get_post_meta( $post_id, 'resource', true ) . '</span>';
				break;
			case 'resource_type':
				$output = get_post_meta( $post_id, 'resource_type', true );
				break;
			case 'who_paid':
				$guests = get_post_meta( $post_id, 'guests', true );
				$name   = get_post_meta( $post_id, 'card_holders_name', true );

				// Get name from Guests
				if ( ! $name )
				{
					$name = array();
					if ( ! empty( $guests[0]['first'] ) )
						$name[] = $guests[0]['first'];
					if ( ! empty( $guests[0]['last'] ) )
						$name[] = $guests[0]['last'];

					$name = empty( $name ) ? '' : implode( ' ', $name );
				}

				if ( $name )
					$output = '<span class="name">' . $name . '</span>';
				if ( ! empty( $guests[0]['email'] ) )
					$output .= '<br><a class="email" href="mailto:' . $guests[0]['email'] . '">' . $guests[0]['email'] . '</a>';
				break;
			case 'booking_date':
				if ( 'tour' == $type )
				{
					$output = '<span class="date departure-date">' . get_post_meta( $post_id, 'day', true ) . '</span>';
					$output .= '<span class="time departure-time">' . get_post_meta( $post_id, 'depart_time', true ) . '</span>';
				}
				else
				{
					$in     = get_post_meta( $post_id, 'checkin', true );
					$in     = explode( ' ', $in );
					$in     = array_shift( $in );
					$out    = get_post_meta( $post_id, 'checkout', true );
					$out    = explode( ' ', $out );
					$out    = array_shift( $out );
					$output = '<span class="date from">' . $in . '</span><br><span class="date to">' . $out . '</span>';
				}
				break;
			case 'num_guests':
				if ( 'tour' == $type )
				{
					$num_guests = array();
					$types      = array(
						'adults'   => __( 'Adults', '7listings' ),
						'children' => __( 'Children', '7listings' ),
						'seniors'  => __( 'Seniors', '7listings' ),
						'families' => __( 'Families', '7listings' ),
						'infants'  => __( 'Infants', '7listings' ),
					);
					foreach ( $types as $type => $label )
					{
						$num = get_post_meta( $post_id, $type, true );
						if ( empty( $num ) || - 1 == $num )
							continue;

						$num_guests[] = '<dt class="guest-quantity">' . $num . '</dt><dd class="guest-type">' . $label . '</dd>';
					}
					$output = '<dl class="tour-guests">' . implode( ' ', $num_guests ) . '</dl>';
				}
				else
				{
					$guests = get_post_meta( $post_id, 'guests', true );
					if ( 'accommodation' == $type )
						$output = '<dl class="accom-guests"><dt class="quantity">' . count( $guests ) . '</dt><dd class="guest">' . __( 'Guest', '7listings' ) . '</dd></dl>';
					elseif ( 'rental' == $type )
						$output = '<dl class="rental-items"><dt class="quantity">' . count( $guests ) . '</dt><dd class="guest">' . __( 'Items', '7listings' ) . '</dd></dl>';
					else
						$output = count( $guests );
				}
				break;
			case 'upsells':
				$upsells = get_post_meta( $post_id, 'upsells', true );
				if ( empty( $upsells ) )
					break;
				$upsells_echo = array();
				foreach ( $upsells as $k => $upsell )
				{
					$upsells_echo[] = '<dt class="upsell-quantity">' . $upsell['num'] . '</dt><dd class="upsell-description">' . $upsell['name'] . '</dd>';
				}
				$output = '<dl class="upsell-items">' . implode( ' ', $upsells_echo ) . '</dl>';
				break;
			case 'total':
				$price  = get_post_meta( $post_id, 'amount', true );
				$output = Sl_Currency::format( $price, 'type=plain' );
				break;
			case 'paid':
				$paid   = intval( get_post_meta( $post_id, 'paid', true ) );
				$class  = $paid ? 'yes-sm' : 'cross-sm';
				$output = "<a href='#' class='{$class} toggle-paid' data-id='{$post_id}' data-paid='{$paid}'></a>";
				break;
			default:
				$output = '';
		}

		if ( $echo )
			echo $output;

		return $output;
	}

	/**
	 * Make columns sortable
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function sortable_columns( $columns )
	{
		$columns = array_merge( $columns, array(
			'id'            => 'id',
			'book_on'       => 'book_on',
			'total'         => 'total',
			'paid'          => 'paid',
			'resource_type' => 'resource_type',
		) );

		return $columns;
	}

	/**
	 * Filter the request to just give posts for the given taxonomy, if applicable.
	 *
	 * @return void
	 */
	public function show_filters()
	{
		$rtype = isset( $_GET['resource_type'] ) ? $_GET['resource_type'] : - 1;

		echo '<select name="resource_type">';
		echo '<option value="-1" ' . selected( - 1, $rtype, false ) . '>' . __( 'Show all types', '7listings' ) . '</option>';

		$bookings = &get_posts( array(
			'post_type'      => 'booking',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );
		if ( ! empty( $bookings ) )
		{
			$types = array();
			foreach ( $bookings as $booking )
			{
				$resource_type = get_post_meta( $booking->ID, 'resource_type', true );
				if ( in_array( $resource_type, $types ) )
					continue;
				$types[] = $resource_type;
				printf( '<option value="%s"%s>%s</option>', $resource_type, selected( $resource_type, $rtype, false ), $resource_type );
			}
		}
		echo '</select>';

		$type = isset( $_GET['listing_type'] ) ? $_GET['listing_type'] : - 1;
		echo '<select name="listing_type">';
		$types         = array(
			'-1' => __( 'Show all listings', '7listings' ),
		);
		$listing_types = sl_setting( 'listing_types' );
		foreach ( $listing_types as $listing_type )
		{
			$post_type_object     = get_post_type_object( $listing_type );
			$types[$listing_type] = $post_type_object->labels->singular_name;
		}

		Sl_Form::options( $type, $types );
		echo '</select>';

		$paid = isset( $_GET['paid'] ) ? intval( $_GET['paid'] ) : - 1;
		echo '<select name="paid">';
		Sl_Form::options( $paid, array(
			- 1 => __( 'All bookings', '7listings' ),
			1   => __( 'Completed (paid)', '7listings' ),
			0   => __( 'Abandoned', '7listings' ),
		) );
		echo '</select>';
	}

	/**
	 * Add taxonomy filter when request posts (in screen)
	 *
	 * @param WP_Query $query
	 *
	 * @return mixed
	 */
	public function filter( $query )
	{
		global $wpdb;

		// Filter by listing type
		if ( isset( $_GET['listing_type'] ) && in_array( $_GET['listing_type'], array( 'tour', 'accommodation' ) ) )
		{
			if ( empty( $query->query_vars['meta_query'] ) )
				$query->query_vars['meta_query'] = array();

			$query->query_vars['meta_query'][] = array(
				'key'   => 'type',
				'value' => $_GET['listing_type'],
			);
		}

		// Filter by payment status
		if ( isset( $_GET['paid'] ) && in_array( $_GET['paid'], array( 0, 1 ) ) )
		{
			if ( empty( $query->query_vars['meta_query'] ) )
				$query->query_vars['meta_query'] = array();

			$query->query_vars['meta_query'][] = array(
				'key'   => 'paid',
				'value' => $_GET['paid'],
			);
		}

		// Filter by types
		if ( isset( $_GET['resource_type'] ) && in_array( $_GET['resource_type'], array( 0, 1 ) ) )
		{
			if ( empty( $query->query_vars['meta_query'] ) )
				$query->query_vars['meta_query'] = array();

			$query->query_vars['meta_query'][] = array(
				'key'   => 'resource_type',
				'value' => $_GET['resource_type'],
			);
		}

		// Search
		if ( ! empty( $_GET['s'] ) )
		{
			$keyword = strip_tags( $_GET['s'] );

			$keyword = "%{$keyword}%";

			// Search in all custom fields
			$post_ids_meta = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT post_id FROM {$wpdb->postmeta}
			WHERE meta_value LIKE '%s'
		", $keyword ) );

			// Search in post_title and post_content
			$post_ids_post = $wpdb->get_col( $wpdb->prepare( "
			SELECT DISTINCT ID FROM {$wpdb->posts}
			WHERE
				post_type = 'booking' AND
				post_status = 'publish' AND
				(post_title LIKE '%s' OR post_content LIKE '%s' )
		", $keyword, $keyword ) );

			$post_ids = array_merge( $post_ids_meta, $post_ids_post );

			// Query arguments
			$query->query_vars['post__in']    = $post_ids;
			$query->query_vars['post_status'] = 'publish';
		}

		// Sort by booking ID
		if ( ! empty( $query->query_vars['orderby'] ) && 'id' === $query->query_vars['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value_num';
			$query->query_vars['meta_key'] = 'booking_id';
		}

		// Sort by total
		elseif ( ! empty( $_GET['orderby'] ) && 'total' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value_num';
			$query->query_vars['meta_key'] = 'amount';
		}

		// Sort by paid
		elseif ( ! empty( $_GET['orderby'] ) && 'paid' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value_num';
			$query->query_vars['meta_key'] = 'paid';
		}

		// Sort by booking time
		elseif ( ! empty( $_GET['orderby'] ) && 'booked_on' === $_GET['orderby'] )
		{
			$query->query_vars['orderby'] = 'post_date';
		}

		// Sort by type
		elseif ( ! empty( $_GET['orderby'] ) && 'resource_type' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value';
			$query->query_vars['meta_key'] = 'resource_type';
		}
	}

	/**
	 * Enqueue scripts and styles for listing page
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_enqueue_style( 'media-views' );
		wp_enqueue_script( 'sl-booking-list', THEME_JS . 'admin/booking-list.js', array( 'jquery', 'sl-utils' ), '', true );
		wp_localize_script( 'sl-booking-list', 'SlBookingList', array(
			'confirm'      => __( 'Are you sure to change booking payment status?', '7listings' ),
			'noEmail'      => __( 'Please enter email address.', '7listings' ),
			'invalidEmail' => __( 'Please enter valid email address.', '7listings' ),
		) );
	}

	/**
	 * Add more hooks
	 *
	 * @return void
	 */
	public function hooks()
	{
		add_filter( 'views_edit-booking', array( $this, 'filter_views' ) );
		add_action( 'admin_footer', array( $this, 'show_totals' ) );
		add_filter( 'post_class', array( $this, 'row_class' ) );

		do_action( 'booking_management_hooks' );
	}

	/**
	 * Show more views
	 *
	 * @param array $views
	 *
	 * @return array
	 */
	public function filter_views( $views )
	{
		unset( $views['publish'] );

		// Don't filter
		remove_filter( 'parse_query', array( $this, 'filter' ) );

		$type = isset( $_GET['listing_type'] ) ? $_GET['listing_type'] : '';

		if ( $type )
			$views['all'] = str_replace( ' class="current"', '', $views['all'] );

		$listing_types = sl_setting( 'listing_types' );
		foreach ( $listing_types as $listing_type )
		{
			if ( ! post_type_exists( $listing_type ) )
				continue;

			$posts = get_posts( array(
				'post_type'      => 'booking',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_key'       => 'type',
				'meta_value'     => $listing_type,
			) );
			$class = $listing_type == $type ? ' class="current"' : '';

			$post_type_object = get_post_type_object( $listing_type );

			$views[$listing_type] = sprintf(
				'<a%s href="%s">%s <span class="count">(%s)</span></a>',
				$class,
				add_query_arg( array( 'listing_type' => $listing_type, 'post_type' => 'booking' ), '' ),
				$post_type_object->labels->singular_name,
				count( $posts )
			);
		}

		// Re-add filter
		add_filter( 'parse_query', array( $this, 'filter' ) );

		return $views;
	}

	/**
	 * Show total of paid, unpaid bookings
	 * We will move it under headings by JS
	 *
	 * @return void
	 */
	public function show_totals()
	{
		// Don't filter
		remove_filter( 'parse_query', array( $this, 'filter' ) );

		// Paid total
		$paid_bookings = new WP_Query( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'meta_key'       => 'paid',
			'meta_value'     => 1,
			'fields'         => 'ids',
		) );
		$paid_bookings = (array) $paid_bookings->posts;

		$paid_total = 0;
		foreach ( $paid_bookings as $paid_booking )
		{
			$paid_total += (float) get_post_meta( $paid_booking, 'amount', true );
		}

		// Unpaid total
		$unpaid_bookings = new WP_Query( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'post__not_in'   => $paid_bookings,
			'fields'         => 'ids',
		) );
		$unpaid_bookings = (array) $unpaid_bookings->posts;

		$unpaid_total = 0;
		foreach ( $unpaid_bookings as $unpaid_booking )
		{
			$unpaid_total += (float) get_post_meta( $unpaid_booking, 'amount', true );
		}

		$total = $paid_total + $unpaid_total;
		echo '<div id="total-overview">
			<span class="paid">' . __( 'Site Revenue:', '7listings' ) . Sl_Currency::format( $paid_total, 'type=plain' ) . '</span> |
			<span class="unpaid">' . __( 'Abandoned Bookings:', '7listings' ) . Sl_Currency::format( $unpaid_total, 'type=plain' ) . '</span> |
			<span class="total">' . __( 'Potential:', '7listings' ) . Sl_Currency::format( $total, 'type=plain' ) . '</span>
		</div>';

		// Re-add filter
		add_filter( 'parse_query', array( $this, 'filter' ) );
	}

	/**
	 * Add CSS classes to table row
	 *
	 * @param array $classes CSS classes
	 *
	 * @return array
	 */
	public function row_class( $classes )
	{
		// Booking type
		$type      = get_post_meta( get_the_ID(), 'type', true );
		$classes[] = 'cart' == $type ? 'cart-booking' : $type;

		// Payment gateway
		if ( $payment_gateway = get_post_meta( get_the_ID(), 'payment_gateway', true ) )
			$classes[] = $payment_gateway;

		// Payment status
		if ( 1 == get_post_meta( get_the_ID(), 'paid', true ) )
			$classes[] = 'paid';

		// Device
		$device    = get_post_meta( get_the_ID(), 'device', true );
		$classes[] = $device ? "device-$device" : 'device-desktop';

		return $classes;
	}
}
