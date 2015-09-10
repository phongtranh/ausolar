<?php

class Sl_Settings_Homepage extends Sl_Settings_Page
{
	/**
	 * Add more hook to enqueue script for WordPress reading settings page
	 *
	 * @return void
	 */
	function add_page()
	{
		parent::add_page();

		add_action( 'admin_print_styles-options-reading.php', array( $this, 'wp_reading_settings_script' ) );
	}

	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui' );

		wp_enqueue_script( 'code-mirror-js', THEME_JS . '/admin/codemirror/codemirror.js', '', true );
		wp_enqueue_script( 'code-mirror-js-hint', THEME_JS . '/admin/codemirror/show-hint.js', '', true );
		wp_enqueue_script( 'code-mirror-js-html-hint', THEME_JS . '/admin/codemirror/html-hint.js', '', true );
		wp_enqueue_script( 'code-mirror-js-xml-hint', THEME_JS . '/admin/codemirror/xml-hint.js', '', true );
		wp_enqueue_script( 'code-mirror-xml', THEME_JS . '/admin/codemirror/xml.js', '', true );
		wp_enqueue_script( 'code-mirror-javascript', THEME_JS . '/admin/codemirror/javascript.js', '', true );
		wp_enqueue_script( 'code-mirror-js-css', THEME_JS . '/admin/codemirror/css.js', '', true );
		wp_enqueue_script( 'code-mirror-html-mixed', THEME_JS . '/admin/codemirror/htmlmixed.js', '', true );
		wp_enqueue_script( 'code-mirror-js-matchbrackets', THEME_JS . '/admin/codemirror/matchbrackets.js', '', true );

		wp_enqueue_script( 'sl-settings-homepage', THEME_JS . 'admin/homepage.js', array( 'jquery', 'sl-choose-image' ), '', true );
	}

	/**
	 * Enqueue script for WordPress reading settings page
	 *
	 * @return void
	 */
	function wp_reading_settings_script()
	{
		wp_enqueue_script( 'sl-settings-homepage-wp', THEME_JS . 'admin/homepage-wp.js', array( 'jquery' ), '', true );
		wp_localize_script( 'sl-settings-homepage-wp', 'SlHome', array(
			'text' => sprintf( __( 'To use 7 Listings homepage, <a href="%s">click here</a>.', '7listings' ), admin_url( 'edit.php?post_type=page&page=homepage' ) ),
		) );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		?>
		<p class="homepage-enable">
			<?php _e( '7Listings Homepage', '7listings' ); ?>
			<?php echo do_shortcode( '[tooltip content="' . __( 'Move widgets in desired order<br>(drag &amp; drop)<br>and configure settings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
			<?php Sl_Form::checkbox( 'homepage_enable' ); ?>
		</p>
		<div class="metabox-holder">
			<div class="postbox-container normal">
				<?php do_meta_boxes( 'sl-settings-homepage', 'normal', null ); ?>
			</div>
		</div>
		<div class="updated settings-error">
			<p><a href="<?php echo admin_url( 'options-reading.php' ); ?>"><?php _e( 'Use WordPress reading settings for homepage', '7listings' ); ?></a></p>
		</div>
	<?php
	}

	/**
	 * Add meta boxes for setting page
	 *
	 * @return void
	 */
	function load()
	{
		add_meta_box( 'homepage', __( 'Homepage Widgets', '7listings' ), array( $this, 'box' ), 'sl-settings-homepage', 'normal' );
	}

	/**
	 * Meta box
	 *
	 * @return void
	 */
	function box()
	{
		include THEME_TABS . 'settings/homepage.php';
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
		self::sanitize_checkboxes( $options_new, $options, array(
			'homepage_enable',
			'homepage_featured_area_active',
			'homepage_custom_content_active',
			'homepage_custom_html_active',
			'homepage_listings_search_active',
			'homepage_footer',

			// Listings Search widget
			'homepage_listings_search_star_rating',
			'homepage_listings_search_location',
			'homepage_listings_search_type',
			'homepage_listings_search_type_counter',
		) );

		// Add all widgets if they're missed
		$widgets = array(
			'featured_area',
			'custom_content',
			'custom_html',
			'listings_search',
		);

		foreach ( $widgets as $widget )
		{
			if ( ! in_array( $widget, $options_new['homepage_order'] ) )
				$options_new['homepage_order'][] = $widget;
		}

		// Allow modules to sanitize options better
		$options_new = apply_filters( 'sl_homepage_settings_sanitize', $options_new, $options );

		return $options_new;
	}
}

new Sl_Settings_Homepage( 'homepage', __( 'Homepage', '7listings' ), 'edit.php?post_type=page' );
