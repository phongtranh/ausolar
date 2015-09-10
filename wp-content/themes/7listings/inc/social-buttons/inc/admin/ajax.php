<?php

/**
 * This class handles all ajax action for social buttons plugin
 */
class Sl_Social_Buttons_Ajax
{
	/**
	 * Add ajax hooks
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'wp_ajax_sl_social_buttons_get_counter', array( __CLASS__, 'get_counter' ) );
		add_action( 'wp_ajax_nopriv_sl_social_buttons_get_counter', array( __CLASS__, 'get_counter' ) );
	}

	/**
	 * Get counter for social networks via ajax
	 *
	 * @return void
	 */
	public static function get_counter()
	{
		if ( ! check_ajax_referer( 'get-counter', false, false ) )
			wp_send_json_error();

		$url = isset( $_POST['url'] ) ? $_POST['url'] : '';
		if ( ! $url || ! filter_var( $url, FILTER_VALIDATE_URL ) )
			wp_send_json_error();

		$counter = array();
		$options = sl_setting( 'social_buttons' );
		foreach ( $options['buttons'] as $network )
		{
			$counter[$network] = Sl_Social_Buttons_Counter::get( $network, $url );
		}
		wp_send_json_success( $counter );
	}
}
