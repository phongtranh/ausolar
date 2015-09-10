<?php

/**
 * This class will hold all settings
 */
class Sl_Product_Settings extends SL_Core_Settings
{
	/**
	 * Show on/off setting for module
	 *
	 * @return void
	 */
	function show_settings_module()
	{
		/**
		 * Get plural form of the post type label
		 * Use Inflect library if module is not activated, thus post type does not exist
		 */
		if ( post_type_exists( $this->post_type ) )
		{
			$post_type_object = get_post_type_object( $this->post_type );
			$label            = $post_type_object->label;
		}
		else
		{
			$label = ucwords( Inflect::pluralize( $this->post_type ) );
		}
		printf(
			'<div class="sl-settings">
				<div class="sl-label">
					<label>
						%4$s
						%5$s
					</label>
				</div>
				<div class="sl-input">
					<span class="checkbox">
						<input type="checkbox" id="listing_type_%1$s" name="%2$s[listing_types][]" value="%1$s"%3$s>
						<label for="listing_type_%1$s">&nbsp;</label>
					</span>
				</div>
			</div>',
			$this->post_type,
			THEME_SETTINGS,
			checked( in_array( $this->post_type, sl_setting( 'listing_types' ) ), 1, false ),
			$label,
			do_shortcode( '[tooltip type="info" content="' . __( 'Requires: WooCommerce plugin', '7listings' ) . '"]<span class="icon"></span>[/tooltip]' )
		);
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $options_new New options that will be save in DB
	 * @param array $options     Options that are submitted
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$this->post_type}_cart",
			"{$this->post_type}_featured_graphics",
		) );
	}

	/**
	 * Change admin menu label
	 *
	 * @return void
	 */
	function admin_menu()
	{
	}

	/**
	 * Add settings tab content in "email" settings page
	 *
	 * @return void
	 */
	function email_tab_content()
	{
	}

	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function page_enqueue()
	{
		parent::page_enqueue();
		wp_enqueue_script( 'sl_settings_listings_' . $this->post_type, THEME_JS . 'admin/listings-' . $this->post_type . '.js' );
		wp_enqueue_script( 'sl-utils' );
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $options_new New options that will be save in DB
	 * @param array $options     Options that are submitted
	 *
	 * @return array
	 */
	function sanitize_page( $options_new, $options )
	{
		$type = $this->post_type;
		Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$type}_comment_status",
			"{$type}_ping_status",
			"{$type}_upsells",
			"{$type}_related",
			"{$type}_attributes",
			"{$type}_meta",

			"{$type}_single_brand_logo",
			"{$type}_single_brand_logo_link",

			"{$type}_archive_main_cat",
			"{$type}_archive_main_sub_cat",

			"{$type}_archive_cat",
			"{$type}_archive_sub_cat",
			"{$type}_archive_cat_thumb",
			"{$type}_archive_cat_title",
			"{$type}_archive_cat_count",
			"{$type}_archive_cat_desc",
			"{$type}_archive_cat_image",
			"{$type}_archive_sidebar",

			"{$type}_brand_logo",
			"{$type}_brand_desc",

			"{$type}_archive_price",
			"{$type}_archive_button",
			"{$type}_archive_rating",
			"{$type}_archive_excerpt",

			"{$type}_archive_sort",

			"{$type}_sells_rating",
			"{$type}_sells_price",
			"{$type}_sells_button",
			"{$type}_sells_excerpt_enable",
		) );

		// Add sidebar
		if ( empty( $options_new['sidebars'] ) || ! in_array( 'Product Archive', $options_new['sidebars'] ) )
		{
			if ( empty( $options_new['sidebars'] ) )
				$options_new['sidebars'] = array( 'Product Archive' );
			else
				$options_new['sidebars'][] = 'Product Archive';
		}

		return $options_new;
	}

	/**
	 * Don't adds permalink settings to WordPress Settings \ Permalink screen
	 *
	 * @since 5.2.1
	 * @return void
	 */
	public function permalink_settings()
	{
	}
}

new Sl_Product_Settings( 'product' );
