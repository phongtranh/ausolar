<?php

/**
 * This class will hold all settings for product
 */
class Sl_Product_Homepage extends SL_Core_Homepage
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
		$prefix      = 'homepage_' . $this->post_type . '_categories_';

		// Checkboxes
		$checkboxes = array(
			"{$prefix}sub",
			"{$prefix}thumbnail",
			"{$prefix}category_title",
			"{$prefix}count",
			'homepage_' . $this->post_type . '_listings_more_listings',
		);
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $checkboxes );

		return $options_new;
	}
}

new Sl_Product_Homepage( 'product', array( 'featured', 'listings', 'categories' ) );
