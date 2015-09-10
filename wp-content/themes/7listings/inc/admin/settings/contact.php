<?php

class Sl_Settings_Contact extends Sl_Settings_Page
{
	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'sl-contact', THEME_JS . 'admin/contact.js', array( 'jquery-ui-timepicker' ), '', true );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		include THEME_TABS . 'settings/contact.php';
	}

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
		$fields = array(
			'contact_custom_contact_form',
			'google_map',
			'business_hours',
			'open_247',
		);
		$days   = array( 'mo', 'tu', 'we', 'th', 'fr', 'sa', 'su' );
		foreach ( $days as $day )
		{
			$fields[] = "business_hours_$day";
		}

		return self::sanitize_checkboxes( $options_new, $options, $fields );
	}
}

new Sl_Settings_Contact( 'contact', __( 'Contact Us', '7listings' ), 'edit.php?post_type=page' );
