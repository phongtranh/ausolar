<?php

/**
 * Control featured title for booking page
 */
class Sl_Booking_Featured_Title
{
	/**
	 * Prefix for all hooks
	 * @var string
	 */
	public static $prefix = 'sl_featured_title_';

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		// Hook to 'template_redirect' to make sure all WordPress functions ready
		add_action( 'template_redirect', array( $this, 'run' ) );
	}

	/**
	 * Add hooks for featured title
	 * Must be hooked in 'template_redirect' to make sure all WordPress functions ready
	 *
	 * @return void
	 */
	public function run()
	{
		if ( ! get_query_var( 'book' ) )
		{
			return;
		}

		add_filter( 'sl_featured_title_title', array( $this, 'title' ) );
		add_filter( 'sl_featured_title_subtitle', '__return_empty_string' );
		add_filter( 'sl_featured_title_bottom', array( $this, 'bottom' ) );
	}

	/**
	 * Display title for booking page
	 *
	 * @return string
	 */
	public function title()
	{
		return sprintf( __( 'Book Your %s', '7listings' ), sl_setting( get_post_type() . '_label' ) );
	}

	/**
	 * Display additional text at the bottom of featured title for booking page
	 *
	 * @return void
	 */
	public function bottom()
	{
		$text = isset( $_GET['cart'] ) ? '' : __( 'in 3 easy steps', '7listings' );
		echo '<h3 class="subtitle">' . $text . '</h3>';
	}
}
