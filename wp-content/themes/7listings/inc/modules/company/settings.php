<?php

/**
 * This class will hold all settings for company
 */
class Sl_Company_Settings extends SL_Core_Settings
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

		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$this->post_type}_membership_display",
			"{$this->post_type}_membership_gold",
			"{$this->post_type}_membership_silver",
			"{$this->post_type}_membership_bronze",
		) );
	}

	/**
	 * Sanitize options
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	function sanitize_page( $options_new, $options )
	{
		$type = $this->post_type;

		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$type}_archive_cat_desc",
			"{$type}_archive_priority",

			"{$type}_single_featured_title_map",
		) );

		// Checkboxes
		$checkboxes  = array(
			"{$type}_comment_status",
			"{$type}_ping_status",
			"{$type}_google_maps",

			"{$type}_single_logo",
			"{$type}_single_address",
			"{$type}_single_phone",
			"{$type}_single_url",
			"{$type}_single_email",
			"{$type}_single_social_media",

			"{$type}_similar",
		);
		$memberships = array( 'none', 'bronze', 'silver', 'gold' );
		foreach ( $checkboxes as $cb )
		{
			foreach ( $memberships as $membership )
			{
				if ( empty( $options["{$cb}_{$membership}"] ) )
					unset( $options_new["{$cb}_{$membership}"] );
			}
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
		printf( '<a href="#company-membership" class="nav-tab">%s</a>', __( 'Company Membership', '7listings' ) );
	}
}

new Sl_Company_Settings( 'company' );
