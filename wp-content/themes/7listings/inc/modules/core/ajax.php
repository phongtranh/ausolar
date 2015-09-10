<?php

/**
 * This class will hold all things for rental ajax requests
 */
class Sl_Core_Ajax
{
	/**
	 * @var string Post type: used for post type slug and some checks (prefix or suffix)
	 */
	public $post_type;

	/**
	 * Class Constructor
	 *
	 * @param string $post_type Post Type
	 * @param array  $actions   List of ajax actions
	 *
	 * @return Sl_Core_Ajax
	 */
	public function __construct( $post_type, $actions = array() )
	{
		$this->post_type = $post_type;
		if ( empty( $actions ) )
		{
			$actions = array( 'add_booking' );
		}
		foreach ( $actions as $action )
		{
			add_action( "wp_ajax_sl_{$post_type}_{$action}", array( $this, $action ) );
			add_action( "wp_ajax_nopriv_sl_{$post_type}_{$action}", array( $this, $action ) );
		}

		/**
		 * Allow each sub-module to add custom booking data
		 * Because each sub-module requires different data when add booking
		 *
		 * We use 'filter_booking_data' as a callback function for 'sl_booking_get_data' filter
		 * which checks for post type of booking to make sure the 'add_booking_data' runs
		 * for correct module only
		 *
		 * Sub-module needs to implement 'add_booking_data'
		 */
		add_filter( 'sl_booking_get_data', array( $this, 'filter_booking_data' ), 10, 3 );
	}

	/**
	 * Ajax callback for booking accommodation
	 *
	 * @return void
	 */
	public function add_booking()
	{
		$data = sl_booking_get_data( $this->post_type );

		if ( is_string( $data ) )
		{
			echo $data;
			die;
		}

		list( $data, $resource, $booking_post_id ) = $data;

		// Gather all information
		$post = get_post( $data['post_id'] );
		sl_booking_process_payment( $post, $resource, $data, $booking_post_id );
	}

	/**
	 * Callback function to add custom booking data for current module
	 *
	 * @param array  $data      Booking data, sent via $_POST
	 * @param string $post_type Post type
	 * @param array  $resource  Resource parameters
	 *
	 * @return array
	 */
	public function filter_booking_data( $data, $post_type, $resource )
	{
		if ( $post_type != $this->post_type )
			return $data;

		return $this->add_booking_data( $data, $resource );
	}

	/**
	 * Add custom booking data for current module
	 *
	 * @param array $data     Booking data, sent via $_POST
	 * @param array $resource Resource parameters
	 *
	 * @return array
	 */
	public function add_booking_data( $data, $resource )
	{
		return $data;
	}

	/**
	 * Add upsells to booking data
	 * This helper function is used in Tour and Rental module
	 *
	 * @param array $data     Booking data, sent via $_POST
	 * @param array $resource Resource parameters
	 *
	 * @return array
	 */
	public function add_booking_upsells( &$data, $resource )
	{
		if ( empty( $resource['upsell_items'] ) )
			return;

		$upsells = array();
		foreach ( $resource['upsell_items'] as $k => $item )
		{
			if ( ! empty( $data["upsell_$k"] ) && - 1 != $data["upsell_$k"] )
			{
				$upsells[] = array(
					'num'  => $data["upsell_$k"],
					'name' => $item,
				);
			}
			unset( $data["upsell_$k"] );
		}
		$data['upsells'] = $upsells;
	}
}
