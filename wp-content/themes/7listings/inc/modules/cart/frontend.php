<?php

class Sl_Cart_Frontend
{
	/**
	 * @var string Module name
	 */
	public $name = 'cart';

	/**
	 * @var array Contains all HTML for all modals
	 */
	public $booking_modals = array();

	/**
	 * Check if we in right page in admin area
	 * Use a separated function allow child class to rewrite the conditions
	 *
	 * @return Sl_Cart_Frontend
	 */
	function __construct()
	{
		add_filter( 'sl_meta_title', array( $this, 'meta_title' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 110 );

		add_filter( 'body_class', array( $this, 'body_class' ) );
		add_action( 'wp_footer', array( $this, 'show_cart' ) );

		add_filter( 'booking_button', array( $this, 'booking_button' ), 20, 3 );
		add_action( 'wp_footer', array( $this, 'booking_modals' ) );

		add_filter( 'sl_featured_title_title', array( $this, 'featured_title' ), 20 );

		add_filter( 'sl_breadcrumbs_general_text', array( $this, 'breadrumbs' ) );
	}

	/**
	 * Change title for archive page
	 *
	 * @param string
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	function meta_title( $title )
	{
		if ( get_query_var( 'cart' ) )
			$title = __( 'Cart', '7listings' );
		elseif ( get_query_var( 'checkout' ) )
			$title = __( 'Checkout', '7listings' );

		return $title;
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
		// wp_enqueue_script( 'sl-cart', THEME_JS . 'cart.js', array( 'sl' ), '', true );

		if ( get_query_var( 'book' ) && isset( $_GET['cart'] ) )
			wp_enqueue_script( 'sl-cart-edit', THEME_JS . 'cart-edit.js', array( 'booking' ), '', true );

		if ( get_query_var( 'cart' ) )
			wp_enqueue_style( 'sl-booking', THEME_LESS . 'booking.less' );
	}

	/**
	 * Show booking button with modal
	 *
	 * @param string $output   Button markup
	 * @param array  $resource Resource params
	 *
	 * @return string
	 */
	function booking_button( $output, $resource )
	{
		global $post;
		static $index;
		if ( ! sl_setting( $post->post_type . '_booking' ) )
			return $output;

		if ( empty( $index ) )
			$index = 0;
		$index ++;

		if ( ! isset( $resource['resource_id'] ) )
			$resource['resource_id'] = 0;
		$resource_slug  = sanitize_title( $resource['title'] );
		$booking_url    = home_url( "book/{$post->post_name}/{$resource_slug}/" );
		$class          = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
		$resource_price = call_user_func( array( $class, 'get_resource_price' ), $resource );

		if ( false !== $resource_price )
		{
			$output = sprintf(
				'<a href="#booking-modal-%s" role="button" class="button booking" data-toggle="modal">%s</a>',
				$index, __( 'Book', '7listings' )
			);
		}
		else
		{
			$output = '';
		}
		$thumb = sl_resource_thumb( $post, $resource, 'sl_pano_medium' );

		/**
		 * Filter vouchers for tour if cart is ON
		 */
		$booking_button = '';
		if ( 1 == sl_setting( 'cart' ) && has_filter( 'sl_7tour_vouchers_booking_button_modal', 'sl_buy_voucher_button_modal' ) )
		{
			$empty_var = '';
			$voucher_url = home_url( "voucher/{$post->post_name}/{$resource_slug}/" );
			$resource_id = $post->ID;
			list( $booking_button, $empty_var ) = apply_filters( 'sl_7tour_vouchers_booking_button_modal', array( array( $voucher_url, $resource_id ), $empty_var ) );
		}

		// Save booking modals here
		// And output it later in the footer
		$this->booking_modals[] = sprintf(
			'<div id="booking-modal-%s" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3>%s</h3>
				</div>
				<div class="modal-body">
					<h4>%s</h4>
					%s
				</div>
				<div class="modal-footer">
					%s
					<a href="#" class="button primary add-to-cart" data-post="%s" data-resource="%s">%s</a>
					<a href="%s" class="button primary">%s</a>
					<a href="#" class="button" data-dismiss="modal" aria-hidden="true">%s</a>
				</div>
			</div>',
			$index,
			$post->post_title,
			$resource['title'],
			$thumb,
			$booking_button,
			$post->ID, $resource['resource_id'], __( 'Add to Cart', '7listings' ),
			$booking_url, __( 'Book Now', '7listings' ),
			__( 'Close', '7listings' )
		);

		return $output;
	}

	/**
	 * Output booking modals
	 *
	 * @return void
	 */
	function booking_modals()
	{
		echo implode( '', $this->booking_modals );
	}

	/**
	 * Show cart
	 *
	 * @since 5.0.5: Only show container, the cart content will be updated via ajax. That makes theme works with cache plugins
	 *
	 * @return void
	 */
	function show_cart()
	{
		echo '<div id="cart" style="display:none"></div>';
	}

	/**
	 * Change body class
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function body_class( $classes )
	{
		if ( get_query_var( 'cart' ) )
			$classes[] = 'cart';

		return $classes;
	}

	/**
	 * Display title for cart page
	 *
	 * @param  string $text
	 *
	 * @return string
	 */
	function featured_title( $text )
	{
		if ( get_query_var( 'cart' ) )
		{
			$text = __( 'Cart', '7listings' );
		}
		if ( get_query_var( 'book' ) && isset( $_GET['cart'] ) )
		{
			$text = __( 'Edit Your Booking Information', '7listings' );
		}

		return $text;
	}

	/**
	 * Change breadcrumb label for Cart page
	 *
	 * @param array $item
	 *
	 * @return array
	 */
	function breadrumbs( $item )
	{
		if ( get_query_var( 'cart' ) )
			$item[1] = __( 'Cart', '7listings' );

		return $item;
	}
}

new Sl_Cart_Frontend;
