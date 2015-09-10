<?php

/**
 * This class will hold all settings for product
 */
class Sl_Post_Homepage extends SL_Core_Homepage
{
	/**
	 * Sanitize settings
	 *
	 * @param array $options_new Submitted options
	 * @param array $options     Saved options
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		$options_new = parent::sanitize( $options_new, $options );

		// Checkboxes
		$checkboxes = array(
			'homepage_' . $this->post_type . '_listings_readmore',
			'homepage_' . $this->post_type . '_listings_featured',
			'homepage_' . $this->post_type . '_listings_more_listings',
		);

		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $checkboxes );
	}
}

new Sl_Post_Homepage( 'post', array( 'listings' ) );
