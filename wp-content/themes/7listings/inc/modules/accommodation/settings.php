<?php

/**
 * This class will hold all settings for accommodation
 */
class Sl_Accommodation_Settings extends SL_Core_Settings
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

		if ( isset( $options_new["{$this->post_type}_base_url"] ) && 'accommodations' == $options_new["{$this->post_type}_base_url"] )
		{
			$options_new["{$this->post_type}_base_url"] = 'accommodation';
			add_settings_error( 'sl-settings', "{$this->post_type}-base-url", __( 'Accommodation base URL cannot be <code>accommodations</code>.', '7listings' ), 'error' );
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
		printf( '<a href="#accommodation-booking" class="nav-tab">%s</a>', __( 'Accommodation Booking', '7listings' ) );
	}

	/**
	 * Sanitize page single/archive options
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	function sanitize_page( $options_new, $options )
	{
		$options_new = parent::sanitize_page( $options_new, $options );

		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			$this->post_type . '_archive_rating',
			$this->post_type . '_archive_search_widget_rating',
			$this->post_type . '_single_star_rating',
		) );
	}
}

new Sl_Accommodation_Settings( 'accommodation' );
