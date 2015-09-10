<?php

/**
 * This class will hold all settings for company
 */
class Sl_Company_Homepage extends SL_Core_Homepage
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
		$prefix     = 'homepage_' . $this->post_type . '_logos_';
		$checkboxes = array(
			$prefix . 'featured',
			$prefix . 'none',
			$prefix . 'bronze',
			$prefix . 'silver',
			$prefix . 'gold',
		);
		foreach ( $checkboxes as $field )
		{
			if ( empty( $options[$field] ) )
				$options_new[$field] = 0;
		}

		return $options_new;
	}
}

new Sl_Company_Homepage( 'company', array( 'logos' ) );
