<?php

/**
 * This class will hold all settings for tour
 */
class Sl_Tour_Settings extends Sl_Core_Settings
{
	/**
	 * Sanitize options
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		$options_new = parent::sanitize( $options_new, $options );
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array( "{$this->post_type}_multiplier" ) );

		if ( isset( $options_new["{$this->post_type}_base_url"] ) && 'tours' == $options_new["{$this->post_type}_base_url"] )
		{
			$options_new["{$this->post_type}_base_url"] = 'tour';
			add_settings_error( 'sl-settings', "{$this->post_type}-base-url", __( 'Tour base URL cannot be <code>tours</code>.', '7listings' ), 'error' );
		}

		return $options_new;
	}

	/**
	 * Add settings tab in "email" settings page
	 *
	 * @return void
	 */
	function email_tab()
	{
		printf( '<a href="#tour-booking" class="nav-tab">%s</a>', __( 'Tour Booking', '7listings' ) );
	}
}

new Sl_Tour_Settings( 'tour' );
