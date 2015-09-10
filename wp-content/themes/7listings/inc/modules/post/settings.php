<?php

/**
 * This class will hold all settings
 */
class Sl_Post_Settings extends Sl_Core_Settings
{

	/**
	 * Add hooks
	 * Remove some hooks from parent class and add only hooks for show single/archive pages
	 *
	 * @param string $post_type
	 *
	 * @return Sl_Post_Settings
	 */
	function __construct( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'sl_page_menu', array( $this, 'page_menu' ) );
	}

	/**
	 * Check module is enabled and then add hooks if needed
	 * Remove some hooks from parent class and add only hook for sanitizing single/archive pages
	 *
	 * @return void
	 */
	function init()
	{
		// Save settings for archive and single pages in Pages \ this module page
		add_filter( "sl_settings_sanitize_page_{$this->post_type}", array( $this, 'sanitize_page' ), 10, 2 );
	}

	/**
	 * Add page menu
	 * Use label "Post", not post type label from settings
	 *
	 * @return void
	 */
	function page_menu()
	{
		// Add page under "Page" menu
		$label = __( 'Posts', '7listings' );
		$page  = add_pages_page( $label, $label, 'edit_theme_options', $this->post_type, array( $this, 'page' ) );
		add_action( "admin_print_styles-{$page}", array( $this, 'page_enqueue' ) );
		add_action( "load-$page", array( $this, 'page_load' ) );
		add_action( "load-$page", array( $this, 'page_help' ) );
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
			"{$type}_archive_featured",
			"{$type}_archive_readmore",
			"{$type}_archive_cat_desc",
			"{$type}_archive_cat_image",

			// "{$type}_archive_meta",

			// Single
			"{$type}_single_featured_title_image",
			"{$type}_single_meta",
			"{$type}_author_details",
			"{$type}_related",
			"{$type}_nextprev",
			"{$type}_recent",
			"{$type}_popular",
			"{$type}_related_excerpt",
			"{$type}_comment_status",
			"{$type}_ping_status",

			// "{$type}_sidebar",
			"{$type}_single_featured",
		) );

		update_option( 'posts_per_page', $options["{$type}_posts_per_page"] );
		unset( $options_new["{$type}_posts_per_page"] );

		return $options_new;
	}
}

new Sl_Post_Settings( 'post' );
