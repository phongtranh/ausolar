<?php

class Sl_Settings_Design extends Sl_Settings_Page
{
	/**
	 * Class constructor
	 *
	 * Add ajax handler for import design
	 *
	 * @param string $slug
	 * @param string $title
	 * @param string $parent
	 *
	 * @return Sl_Settings_Design
	 */
	function __construct( $slug, $title, $parent = '7listings' )
	{
		parent::__construct( $slug, $title, $parent );

		add_action( 'wp_ajax_sl_design_import', array( $this, 'import' ) );
	}

	/**
	 * Remove the title for settings page
	 *
	 * @return void
	 */
	function page_title()
	{
	}

	/**
	 * Import design
	 *
	 * @return void
	 */
	function import()
	{
		check_ajax_referer( 'design-import', 'nonce' );

		if ( empty( $_POST['settings'] ) )
			wp_send_json_error( __( 'Design settings is empty. Please check the data before import.', '7listings' ) );

		$settings = $_POST['settings'];
		$settings = unserialize( base64_decode( $settings ) );
		$saved    = get_option( THEME_SETTINGS );
		$saved    = array_merge( $saved, $settings );

		Sl_Settings_Advanced::remove_less_cache();

		/**
		 * Custom CSS: write to custom.less file in upload folder
		 * Must write to file to use cache compile with @import
		 */
		$this->write_custom_css( $saved['design_custom_css'] );
		$saved['updated_design'] = 1; // Trigger to know design settings is change, needed to generate custom CSS

		update_option( THEME_SETTINGS, $saved );
		wp_send_json_success();
	}

	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery-ui' );

		wp_enqueue_media();

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-slider' );

		wp_enqueue_script( 'sl-choose-image' );

		wp_enqueue_script( 'sl-design', THEME_JS . 'admin/design.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'sl-background', THEME_JS . 'admin/background.js', array( 'jquery' ), '', true );

		wp_enqueue_script( 'code-mirror-js', THEME_JS . '/admin/codemirror/codemirror.js', '', true );
		wp_enqueue_script( 'code-mirror-js-less', THEME_JS . '/admin/codemirror/less.js', '', true );
		wp_enqueue_script( 'code-mirror-js-hint', THEME_JS . '/admin/codemirror/show-hint.js', '', true );
		wp_enqueue_script( 'code-mirror-js-css-hint', THEME_JS . '/admin/codemirror/css-hint.js', '', true );
		wp_enqueue_script( 'code-mirror-js-css', THEME_JS . '/admin/codemirror/css.js', '', true );
		wp_enqueue_script( 'code-mirror-js-matchbrackets', THEME_JS . '/admin/codemirror/matchbrackets.js', '', true );

		wp_localize_script( 'sl-design', 'SlDesign', array(
			'nonceImport' => wp_create_nonce( 'design-import' ),
		) );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		echo '<h2 class="nav-tab-wrapper sl-tabs">';
		printf( '<a href="#header" class="nav-tab">%s</a>', __( 'Header', '7listings' ) );
		printf( '<a href="#featured" class="nav-tab">%s</a>', __( 'Featured', '7listings' ) );
		printf( '<a href="#main" class="nav-tab">%s</a>', __( 'Main', '7listings' ) );
		printf( '<a href="#sidebar" class="nav-tab">%s</a>', __( 'Sidebar', '7listings' ) );
		printf( '<a href="#footer" class="nav-tab">%s</a>', __( 'Footer', '7listings' ) );
		printf( '<a href="#background" class="nav-tab">%s</a>', __( 'Background', '7listings' ) );
		printf( '<a href="#mobile" class="nav-tab">%s</a>', __( 'Mobile', '7listings' ) );
		printf( '<a href="#advanced" class="nav-tab">%s</a>', __( 'Advanced', '7listings' ) );
		printf( '<a href="#import-export" class="nav-tab">%s</a>', esc_html__( 'Import & Export', '7listings' ) );
		printf( '<a href="#emails" class="nav-tab emails-tab">%s</a>', __( 'Emails', '7listings' ) );
		echo '</h2>';

		echo '<div class="sl-tabs-content">';
		$tabs = array( 'header', 'featured', 'main', 'sidebar', 'footer', 'background', 'mobile', 'advanced', 'import-export', 'emails' );
		foreach ( $tabs as $tab )
		{
			echo "<div id='design-$tab'>";
			include THEME_TABS . "design/$tab.php";
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * Show more submit/reset buttons if needed
	 *
	 * @return void
	 */
	function more_buttons()
	{
		submit_button( __( 'Reset', '7listings' ), 'primary', 'reset', false, array( 'onclick' => 'return confirm(\'' . esc_js( __( 'Are you sure you want to reset Your design settings?', '7listings' ) ) . '\');' ) );
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
		if ( isset( $_POST['reset'] ) )
		{
			foreach ( $options_new as $k => $v )
			{
				if ( 0 === strpos( $k, 'design_' ) )
					unset( $options_new[$k] );
			}

			return $options_new;
		}

		// Cleanup empty options in design page
		foreach ( $options_new as $k => $v )
		{
			if ( 0 !== strpos( $k, 'design_' ) )
				continue;
			if ( ! isset( $options[$k] ) )
				unset( $options_new[$k] );
		}

		self::sanitize_checkboxes( $options_new, $options, array(
			'display_site_title',
			'display_site_description',
			'display_mobile_site_title',
			'display_mobile_site_description',
			'logo_display',
			'svg_display',
			'weather_active',
			'design_header_phone',
			'design_header_search',

			'design_background_type',
			'design_header_background_image',
			'design_featured_area_background_image',
			'design_featured_background_type',
			'design_breadcrumbs_enable',
			'design_mini_cart_enable',
			'design_footer_top_background_image',
			'design_footer_middle_background_image',
			'design_footer_bottom_background_image',
			'design_mobile_logo_display',
			'design_map_disable_dragging',
		) );

		if ( ! empty( $options_new['city'] ) && empty( $options_new['woeid'] ) )
			$options_new['woeid'] = sl_find_woeid( $options_new['city'] );

		// Update site general settings
		if ( ! empty( $options['site_title'] ) )
			update_option( 'blogname', $options['site_title'] );
		if ( ! empty( $options['tag_line'] ) )
			update_option( 'blogdescription', $options['tag_line'] );

		unset( $options_new['site_title'] );
		unset( $options_new['tag_line'] );

		// Set width for mobile nav break point if mobile nav is right
		if ( 'right' == $options_new['design_layout_mobile_nav'] )
		{
			$options_new['design_mobile_nav_break_point'] = '768px';
		}

		/**
		 * Custom CSS: write to custom.less file in upload folder
		 * Must write to file to use cache compile with @import
		 */
		$this->write_custom_css( $options_new['design_custom_css'] );
		$options_new['updated_design'] = 1; // Trigger to know design settings is change, needed to generate custom CSS

		return $options_new;
	}

	/**
	 * Write custom CSS to .less file in cache folder
	 *
	 * @param string $css Custom CSS
	 *
	 * @return void
	 */
	static function write_custom_css( $css )
	{
		// Custom CSS: write to custom.less file in upload folder
		$upload_dir = wp_upload_dir();
		$dir        = path_join( $upload_dir['basedir'], 'peace-less' );
		$file       = trailingslashit( $dir ) . 'custom.less';
		if ( $css )
		{
			wp_mkdir_p( $dir ); // Create directory if it doesn't exists
			@file_put_contents( $file, $css );
		}
		else
		{
			// Remove file and directory
			@unlink( $file );
			@rmdir( $dir );
		}
	}
}

new Sl_Settings_Design( 'design', __( 'Design', '7listings' ), 'themes.php' );
