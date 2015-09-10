<?php
/**
 * Theme settings main file
 * This file create theme top-level settings menu and handle saving settings
 * It also add hooks for other settings pages to register settings menu and save their settings
 */

add_action( 'admin_init', 'sl_settings_init' );
add_action( 'admin_menu', 'sl_settings_add_page' );
add_action( 'admin_notices', 'sl_admin_notices' );

/**
 * Register setting
 *
 * @return void
 */
function sl_settings_init()
{
	register_setting( THEME_SETTINGS, THEME_SETTINGS, 'sl_settings_sanitize' );
}

/**
 * Add theme setting page
 *
 * @return void
 */
function sl_settings_add_page()
{
	add_menu_page( __( 'Listings', '7listings' ), __( 'Listings', '7listings' ), 'edit_theme_options', '7listings', '__return_zero' );
	if ( Sl_License::is_activated() )
	{
		do_action( 'sl_page_menu' );
		do_action( 'sl_admin_menu', '7listings' );

		/**
		 * Add Help menu with URL to theme documentation
		 * WordPress does not allow that natively, so we have to loop through global variable and change it manually
		 *
		 * @since  4.12
		 */
		add_submenu_page( '7listings', __( 'Help', '7listings' ), __( 'Help', '7listings' ), 'edit_theme_options', '7help', '__return_null' );
		global $submenu;

		if ( empty( $submenu['7listings'] ) )
			return;
		foreach ( $submenu['7listings'] as &$item )
		{
			if ( '7help' == $item[2] )
			{
				$item[2] = 'http://support.7ad.in/knowledgebase-category/7listings-theme/';
			}
		}
	}
	else
	{
		do_action( 'sl_admin_menu_no_license', '7listings' );
	}
}

/**
 * Sanitize theme settings
 *
 * @param array $options
 *
 * @return array
 */
function sl_settings_sanitize( $options )
{
	/**
	 * Don't sanitize when in ajax mode or not theme settings page
	 * Theme settings page are pages that have $_POST['sl_page']: under 7listings, Pages, Appearance menus
	 * This prevents sanitizing in other admin pages like Settings \ Media, Permalinks
	 *
	 * For backward compatibility: uses $_POST['page'] instead
	 */
	if ( empty( $_POST['sl_page'] ) && ! empty( $_POST['page'] ) )
	{
		$_POST['sl_page'] = $_POST['page'];
	}
	if ( defined( 'DOING_AJAX' ) || empty( $_POST['sl_page'] ) )
		return $options;

	// Merge with old settings to make sure the settings in both pages (homepage & listings) are saved correctly
	$settings    = get_option( THEME_SETTINGS, array() );
	$options_new = array_merge( $settings, $options );

	/**
	 * Theme modules or settings page can filter to add its own options
	 * The first filter is used widely, for backward compatibility
	 * While the second filter is used for a specific page. That will reduce the load when saving settings for 1 page.
	 */
	$options_new = apply_filters( 'sl_settings_sanitize', $options_new, $options, $_POST['sl_page'] );
	$options_new = apply_filters( 'sl_settings_sanitize_' . $_POST['sl_page'], $options_new, $options );

	// Force to flush rewrite rules
	flush_rewrite_rules();

	// Recompile CSS
	Sl_Settings_Advanced::remove_less_cache();

	/**
	 * Clears the WP or W3TC cache depending on which is used
	 *
	 * @see WPSEO_Options::clear_cache() of WordPress SEO Plugin by Yoast,
	 */
	if ( function_exists( 'w3tc_pgcache_flush' ) )
	{
		w3tc_pgcache_flush();
	}
	if ( function_exists( 'wp_cache_clear_cache' ) )
	{
		wp_cache_clear_cache();
	}
	if ( defined( 'W3TC_DIR' ) && function_exists( 'w3tc_objectcache_flush' ) )
	{
		w3tc_objectcache_flush();
	}

	// Force options to be an array
	// In case of reset, e.g. remove all settings
	$options_new = (array) $options_new;

	add_settings_error( 'sl-settings', 'all', __( 'Settings Updated', '7listings' ), 'updated' );

	return $options_new;
}

/**
 * Show error messages of validation
 *
 * @return void
 */
function sl_admin_notices()
{
	settings_errors( 'sl-settings' );
}
