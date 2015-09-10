<?php

class Sl_Cart_Ajax
{
	/**
	 * Cart object
	 *
	 * @var Sl_Cart
	 */
	public $cart;

	/**
	 * Check if we in right page in admin area
	 * Use a separated function allow child class to rewrite the conditions
	 *
	 * @return Sl_Cart_Ajax
	 */
	function __construct()
	{
		add_action( 'after_setup_theme', array( $this, 'init' ), 100 );
	}

	/**
	 * Check module is enabled and then add hooks if needed
	 *
	 * @return void
	 */
	function init()
	{
		$this->cart = Sl_Cart::get_instance();

		$ajax_actions = array( 'add', 'edit', 'remove', 'book', 'show' );
		foreach ( $ajax_actions as $action )
		{
			add_action( "wp_ajax_sl_cart_{$action}", array( $this, $action ) );
			add_action( "wp_ajax_nopriv_sl_cart_{$action}", array( $this, $action ) );
		}
	}

	/**
	 * Add an item to cart
	 *
	 * @return void
	 */
	function add()
	{
		$post_id  = (int) $_POST['post'];
		$resource = (int) $_POST['resource'];

		// See if this product and its options is already in the cart
		$item_key = $this->cart->find_product_in_cart( $post_id, $resource );
		if ( - 1 != $item_key )
		{
			wp_send_json_error( __( 'This booking resource is already in your cart', '7listings' ) );
		}

		// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item
		$item = array(
			'post'     => $post_id,
			'resource' => $resource,
		);

		$this->cart->content[] = $item;
		$this->cart->set_session();

		wp_send_json_success( array(
			'item' => $this->get_item_template( $item ),
			'url'  => $this->get_checkout_url()
		) );
	}

	/**
	 * Process the booking edit via ajax
	 *
	 * @return void
	 */
	function edit()
	{
		if ( ! isset( $_POST['type'] ) )
		{
			wp_send_json_error( __( 'Invalid booking type', '7listings' ) );
		}

		$data = sl_booking_get_data( $_POST['type'], false );
		if ( is_string( $data ) )
		{
			wp_send_json_error( $data );
		}

		list( $data, $resource ) = $data;

		$item_key = $this->cart->find_product_in_cart( $data['post_id'], $resource['resource_id'] );
		if ( - 1 != $item_key )
		{
			$this->cart->content[$item_key]['data'] = $data;
		}
		else
		{
			$this->cart->content[] = array(
				'post'     => $data['post_id'],
				'resource' => $resource['resource_id'],
				'data'     => $data,
			);
		}
		$this->cart->set_session();
		wp_send_json_success( home_url( 'cart' ) );
	}

	/**
	 * Remove an item from cart
	 *
	 * @return void
	 */
	function remove()
	{
		if ( ! isset( $_POST['index'] ) )
		{
			wp_send_json_error( __( 'Invalid request', '7listings' ) );
		}

		unset( $this->cart->content[$_POST['index']] );
		$this->cart->set_session();
		wp_send_json_success( '' );
	}

	/**
	 * Add booking for cart
	 *
	 * @return void
	 */
	function book()
	{
		global $settings;

		// Get booking data
		$data             = array();
		$data['bookings'] = $this->cart->get_cart();

		// Calculate total, get titles
		$data['amount'] = 0;
		$title          = array();
		foreach ( $data['bookings'] as $item )
		{
			$data['amount'] += floatval( $item['data']['amount'] );
			$title[] = get_the_title( $item['post'] );
		}
		$title = implode( ', ', $title );

		$data['type'] = 'cart';
		$data['paid'] = 0;

		/**
		 * Set payment gateway if it's missed
		 * If there are more than 1 payment gateways are activated, user can select one of them and payment gateway will
		 * be sent via $_POST
		 * If there is only 1 payment gateway activated, then user just click "Book" button and no payment gateway is
		 * sent via $_POST (and won't be stored in $data). In this case:
		 * - We have to check the settings and get activate payment gateway
		 * - And save payment gateway into booking data
		 */
		$data['payment_gateway'] = isset( $_POST['payment_gateway'] ) ? strip_tags( $_POST['payment_gateway'] ) : '';
		if ( ! $data['payment_gateway'] )
		{
			if ( sl_setting( 'paypal' ) )
			{
				$data['payment_gateway'] = 'paypal';
			}
			elseif ( sl_setting( 'eway' ) )
			{
				$data['payment_gateway'] = 'eway';
			}
		}

		// Counter
		$counter = intval( $settings['counter'] );
		$counter ++;
		$data['booking_id']  = $counter;
		$settings['counter'] = $counter;
		update_option( THEME_SETTINGS, $settings );

		/**
		 * Get common fields from cart items and save as cart fields or remove them from cart items
		 * They're common information for cart booking, not for specific cart items
		 * Removing them will save space and make data more easy to read
		 *
		 * - true: set cart field = items field
		 * - false: do net set cart field = items field
		 * In both case: remove them from cart items
		 */
		$fields_from_items = array(
			'device'          => true,
			'payment_gateway' => false,
			'ip_address'      => true,
			'paid'            => false,
		);
		foreach ( $data['bookings'] as &$booking_item )
		{
			foreach ( $booking_item['data'] as $k => $v )
			{
				if ( isset( $fields_from_items[$k] ) )
				{
					if ( $fields_from_items[$k] )
					{
						$data[$k] = $v;
					}
					unset( $booking_item['data'][$k] );
				}
			}
		}

		// Save booking data
		$booking_post = array(
			'post_type'   => 'booking',
			'post_title'  => sprintf( __( 'Booking #%d', '7listings' ), $counter ),
			'post_status' => 'publish',
		);
		$post_id      = wp_insert_post( $booking_post );
		if ( ! $post_id )
		{
			wp_send_json_error( __( 'ERROR: Cannot save booking data. Please try again.', '7listings' ) );
		}
		foreach ( $data as $k => $v )
		{
			update_post_meta( $post_id, $k, $v );
		}

		// Process payment
		$booking_url  = home_url( "/cart/?id={$post_id}" );
		$invoice_code = sl_setting( 'invoice_code' ) ? sl_setting( 'invoice_code' ) : get_option( 'blogname' );
		$thank_url    = Sl_Helper::get_url_by_template( 'templates/thank-you-booking.php' );

		// Clear cart content
		$this->cart->remove_all();

		// Get payment gateway to process the payment
		$payment_gateway = '';
		switch ( $data['payment_gateway'] )
		{
			case 'paypal':
				$payment_gateway = 'paypal';
				break;
			case 'eway':
				if ( sl_setting( 'eway_shared' ) )
				{
					$payment_gateway = 'eway_shared';
				}
				if ( sl_setting( 'eway_hosted' ) )
				{
					$payment_gateway = 'eway_hosted';
				}
		}

		// Checkout using Paypal
		if ( 'paypal' == $payment_gateway )
		{
			$form = Sl_Payment::paypal_form( 'checkout_form', 'booking_paypal', array(
				'item_name'   => $title,
				'item_number' => "{$invoice_code} #{$counter}",
				'amount'      => $data['amount'],
				'return'      => $thank_url,
				'custom'      => $post_id,
			) );
			wp_send_json_success( $form );
		}

		// eWAY shared payment
		if ( 'eway_shared' == $payment_gateway )
		{
			$amount = number_format( $data['amount'], 2, '.', '' );
			$guest  = $data['bookings'][0]['data']['guests'][0];
			$form   = Sl_Payment::eway_form( 'checkout_form', 'booking_eway', array(
				'amount'              => $amount,
				'reference'           => "$invoice_code #$post_id",
				'resource'            => $title,
				'cancel_url'          => $booking_url,
				'customer_first_name' => $guest['first'],
				'customer_last_name'  => empty( $guest['last'] ) ? '' : $guest['last'],
				'customer_email'      => empty( $guest['email'] ) ? '' : $guest['email'],
				'customer_phone'      => empty( $guest['phone'] ) ? '' : $guest['phone'],
				'booking_post_id'     => $post_id,
			) );

			wp_send_json_success( $form );
		}

		wp_send_json_error( __( 'Error payment settings. Please contact admin to fix this.', '7listings' ) );
	}

	/**
	 * Show cart
	 *
	 * @return void
	 */
	function show()
	{
		$num  = $this->cart->get_cart_contents_count();
		$data = $this->cart->get_cart();

		$items = array();
		foreach ( $data as $item )
		{
			$items[] = $this->get_item_template( $item );
		}

		$output = sprintf(
			'<div id="cart"%1$s>
				<a class="title" href="%2$s">%3$s <span class="count">%4$d</span></a>
				<ul class="items">%5$s</ul>
				<a href="%2$s" class="button booking">%6$s</a>
			</div>',
			$num ? '' : ' style="display: none"',
			$this->get_checkout_url(),
			__( 'Cart', '7listings' ),
			$num,
			implode( '', $items ),
			__( 'Checkout', '7listings' )
		);
		wp_send_json_success( $output );
	}

	/**
	 * Get checkout URL for cart
	 * Default is /cart/, but if cart has only 1 item - booking URL of that item
	 *
	 * @return string URL to checkout page
	 */
	function get_checkout_url()
	{
		// If cart contains more than 1 item, return the default URL /cart/
		if ( 1 != ( $num = $this->cart->get_cart_contents_count() ) )
		{
			return home_url( 'cart' );
		}

		// Get listing ID and resource index from the only item in the cart
		$data     = reset( $this->cart->get_cart() );
		$post_id  = $data['post'];
		$resource = $data['resource'];

		// Generate checkout URL - same as booking URL for that item
		$resources     = get_post_meta( $post_id, sl_meta_key( 'booking', get_post_type( $post_id ) ), true );
		$resource_slug = sanitize_title( $resources[$resource]['title'] );
		$post          = get_post( $post_id );
		$checkout_url  = home_url( "book/{$post->post_name}/{$resource_slug}/" );

		return $checkout_url;
	}

	/**
	 * Get output of item in cart
	 * @param array $item Array of item configuration array(post, resource)
	 * @return string
	 */
	function get_item_template( $item )
	{
		$thumb = sl_resource_thumb( $item['post'], $item['resource'] );

		return sprintf(
			'<li class="cart-item"><a href="%s" title="%s"><div class="thumbnail">%s</div></a></li>',
			get_permalink( $item['post'] ),
			the_title_attribute( array( 'post' => $item['post'], 'echo' => false ) ),
			$thumb
		);
	}
}

new Sl_Cart_Ajax;
