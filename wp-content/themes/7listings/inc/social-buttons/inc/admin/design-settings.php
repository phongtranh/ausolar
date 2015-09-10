<?php

/**
 * Handle social button settings for design, including
 *
 * - Show settings for header, contact page and social buttons
 * - Save settings
 */
class Sl_Social_Buttons_Design_Settings
{
	/**
	 * Add hooks
	 *
	 * @return void
	 */
	public static function load()
	{
		// Show settings
		add_action( 'sl_settings_design_advanced_before_map', array( __CLASS__, 'share_buttons_settings' ) );
		add_action( 'sl_settings_design_header_after_header', array( __CLASS__, 'header_links_settings' ) );

		// Save settings
		add_filter( 'sl_settings_sanitize_design', array( __CLASS__, 'save_settings' ), 10, 2 );

		// For contact page
		add_action( 'sl_settings_contact_col1_bottom', array( __CLASS__, 'contact_links_settings' ) );
		add_filter( 'sl_settings_sanitize_contact', array( __CLASS__, 'save_contact_settings' ), 10, 2 );
	}

	/**
	 * Output design settings for header links in Design Header page
	 *
	 * @return void
	 */
	public static function header_links_settings()
	{
		include SSB_DIR . 'inc/admin/tabs/design-header-links.php';
	}

	/**
	 * Output design settings for share buttons in Design Advanced page
	 *
	 * @return void
	 */
	public static function share_buttons_settings()
	{
		include SSB_DIR . 'inc/admin/tabs/design-share-buttons.php';
	}

	/**
	 * Save settings for social buttons
	 *
	 * @param array $options_new Array of theme options
	 * @param array $options     Submitted options
	 *
	 * @return array
	 */
	public static function save_settings( $options_new, $options )
	{
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			// Design Header page
			'design_header_social_display',
			'design_header_social_counter',

			// Design Advanced page
			'design_social_icon_counter',
		) );

		return $options_new;
	}

	/**
	 * Output design settings for contact us page
	 *
	 * @return void
	 */
	public static function contact_links_settings()
	{
		include SSB_DIR . 'inc/admin/tabs/contact.php';
	}

	/**
	 * Save settings for contact page
	 *
	 * @param array $options_new Array of theme options
	 * @param array $options     Submitted options
	 *
	 * @return array
	 */
	public static function save_contact_settings( $options_new, $options )
	{
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			'contact_social_links',
			'contact_social_counter',
		) );

		return $options_new;
	}
}
