<?php

/**
 * This class will hold all settings for accommodation homepage widgets
 */
class Sl_Accommodation_Homepage extends SL_Core_Homepage
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
		$prefix      = 'homepage_' . $this->post_type . '_featured_';
		$checkboxes  = array(
			"{$prefix}post_title",
			"{$prefix}star_rating",
			"{$prefix}price",
			"{$prefix}booking",
			"{$prefix}rating",
			"{$prefix}excerpt",
		);
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $checkboxes );

		$prefix     = 'homepage_' . $this->post_type . '_listings_';
		$checkboxes = array(
			"{$prefix}thumbnail",
			"{$prefix}price",
			"{$prefix}booking",
			"{$prefix}star_rating",
			"{$prefix}rating",
			"{$prefix}excerpt",
			"{$prefix}more_listings",
		);
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $checkboxes );

		return $options_new;
	}
}

new Sl_Accommodation_Homepage( 'accommodation', array( 'featured', 'listings', 'types', 'amenities' ) );
