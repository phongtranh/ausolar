<?php

/**
 * This class will hold all things for booking edit page
 */
class Sl_Booking_Edit
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save' ) );

		// Add help tab
		add_action( 'load-post.php', array( $this, 'help' ) );
		add_action( 'load-post-new.php', array( $this, 'help' ) );
	}

	/**
	 * Enqueue scripts and styles for editing page
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		$screen = get_current_screen();
		if ( 'booking' != $screen->post_type || 'post' != $screen->base )
			return;

		wp_enqueue_script( 'sl-booking', THEME_JS . 'admin/booking.js', array( 'jquery' ) );

		wp_localize_script( 'sl-booking', 'SlBookings', array(
			'nonceGetListPosts'         => wp_create_nonce( 'get-list-posts' ),
			'nonceGetListResources'     => wp_create_nonce( 'get-list-resources' ),
			'nonceGetResourceInfo'      => wp_create_nonce( 'get-resource-info' ),
			'nonceGetTourAllocations'   => wp_create_nonce( 'get-tour-allocations' ),
			'nonceAddBooking'           => wp_create_nonce( 'add-booking' ),
		) );
	}

	/**
	 * Add meta boxes
	 * @param string  $post_type Post type
	 * @param WP_Post $post      Post object
	 *
	 * @return void
	 */
	public function add( $post_type, $post )
	{
		add_meta_box( 'data', $post->post_title, array( $this, 'meta_box' ), 'booking', 'normal' );
		add_meta_box( 'items', __( 'Order Items', '7listings' ), array( $this, 'meta_box' ), 'booking', 'normal' );
		add_meta_box( 'customer', __( 'Customer Details', '7listings' ), array( $this, 'meta_box' ), 'booking', 'normal' );
		add_meta_box( 'actions', __( 'Order Actions', '7listings' ), array( $this, 'meta_box' ), 'booking', 'side', 'high' );
		add_meta_box( 'notes', __( 'Order Notes', '7listings' ), array( $this, 'meta_box' ), 'booking', 'side' );
		add_meta_box( 'author', __( 'Author', '7listings' ), array( $this, 'meta_box' ), 'booking', 'side' );
		add_meta_box( 'add-booking', __( 'Booking Details', '7listings' ), array( $this, 'meta_box' ), 'booking', 'side' );
	}

	/**
	 * Show booking meta box
	 *
	 * @param WP_Post $post Post object of the booking
	 * @param array   $args Meta box info
	 *
	 * @return void
	 */
	public function meta_box( $post, $args )
	{
		locate_template( 'inc/admin/tabs/booking/' . $args['id'] . '.php', true );
	}

	/**
	 * Save booking data
	 * @param int $post_id Post ID
	 *
	 * @return void
	 */
	public function save( $post_id )
	{
		if (
			defined( 'DOING_AJAX' )
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
		)
		{
			return;
		}

		// Get proper post type
		$post_type = '';
		if ( $post = get_post( $post_id ) )
		{
			$post_type = $post->post_type;
		}
		elseif ( isset( $_POST['post_type'] ) && post_type_exists( $_POST['post_type'] ) )
		{
			$post_type = $_POST['post_type'];
		}
		if ( 'booking' != $post_type )
		{
			return;
		}

		if ( ! empty( $_POST['payment_status'] ) )
		{
			update_post_meta( $post_id, 'paid', 1 );
		}
		else
		{
			delete_post_meta( $post_id, 'paid' );
		}

		if ( ! empty( $_POST['payment_gateway'] ) )
		{
			update_post_meta( $post_id, 'payment_gateway', $_POST['payment_gateway'] );
		}

		if ( ! empty( $_POST['customer_message'] ) )
		{
			update_post_meta( $post_id, 'customer_message', $_POST['customer_message'] );
		}

		if ( 'cart' == get_post_meta( $post_id, 'type', true ) )
		{
			$items = get_post_meta( $post_id, 'bookings', true );
			foreach ( $items as $key_item => $item )
			{
				foreach ( $items[$key_item]['data']['guests'] as $key_guest => $guest )
				{
					$key = $key_item . $key_guest;

					$items[$key_item]['data']['guests'][$key_guest] = array(
						'first' => $_POST["_first_name_customer-$key"],
						'last'  => $_POST["_last_name_customer-$key"],
						'email' => $_POST["_email_customer-$key"],
						'phone' => $_POST["_phone_customer-$key"]
					);
				}
			}
			update_post_meta( $post_id, 'bookings', $items );
		}
		elseif ( $guests = get_post_meta( $post_id, 'guests', true ) )
		{
			foreach ( $guests as $key => $guest )
			{
				$guests[$key] = array(
					'first' => $_POST["_first_name_customer-$key"],
					'last'  => $_POST["_last_name_customer-$key"],
					'email' => $_POST["_email_customer-$key"],
					'phone' => $_POST["_phone_customer-$key"]
				);
			}

			update_post_meta( $post_id, 'guests', $guests );
		}
	}

	/**
	 * Add help tab
	 *
	 * @return void
	 */
	public function help()
	{
		$screen = get_current_screen();
		if ( 'booking' != $screen->post_type )
			return;
		sl_add_help_tabs( 'edit-booking' );
	}

}
