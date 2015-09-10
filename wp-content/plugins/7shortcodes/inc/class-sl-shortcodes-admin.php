<?php
/**
 * Add shortcode buttons for editor and popup templates
 */
class Sl_Shortcodes_Admin
{
	/**
	 * Constructor
	 *
	 * @return Sl_Shortcodes_Admin
	 */
	function __construct()
	{
		add_action( 'load-post.php', array( $this, 'init' ) );
		add_action( 'load-post-new.php', array( $this, 'init' ) );
		add_action( 'load-pages_page_homepage', array( $this, 'init' ) );
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	function init()
	{
		$screen = get_current_screen();
		if ( 'post' == $screen->base && ! in_array( $screen->post_type, array( 'post', 'page' ) ) )
			return;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_footer', array( $this, 'popup_templates' ) );

		add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
		add_filter( 'mce_buttons_2', array( $this, 'mce_buttons_2' ) );
		add_filter( 'mce_external_plugins', array( $this, 'plugin' ) );

		require SL_SHORTCODES_DIR . 'inc/class-sl-shortcodes-helper.php';
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'sls-admin', SL_SHORTCODES_URL . 'js/admin.js', array( 'sl-choose-image', 'angularjs-sanitize', 'wp-color-picker' ) );

		$params = array();

		// Get supported listing types for reviews
		$types         = array(
			array(
				'value'   => '-1',
				'name'    => __( 'All', '7listings' ),
				'checked' => false,
			),
		);
		$listing_types = array();
		$modules       = array(
			'post'          => __( 'News', '7listings' ),
			'accommodation' => __( 'Accommodation', '7listings' ),
			'tour'          => __( 'Tour', '7listings' ),
			'rental'        => __( 'Rental', '7listings' ),
			'product'       => __( 'Product', '7listings' ),
			'company'       => __( 'Company', '7listings' ),
		);
		foreach ( $modules as $k => $v )
		{
			if ( ! Sl_License::is_module_enabled( $k ) )
				continue;

			$types[]         = array(
				'value'   => $k,
				'name'    => $v,
				'checked' => false,
			);
			$listing_types[] = $k;
		}

		$params['reviewsTypes'] = $types;
		$params['listingTypes'] = implode( ',', $listing_types );

		// Map Controls
		$params['mapControls'] = array();
		$controls              = array(
			'zoom'        => __( 'Zoom', '7listings' ),
			'pan'         => __( 'Pan', '7listings' ),
			'scale'       => __( 'Scale', '7listings' ),
			'map_type'    => __( 'Map type', '7listings' ),
			'street_view' => __( 'Street view', '7listings' ),
			'rotate'      => __( 'Rotate', '7listings' ),
			'overview'    => __( 'Overview map', '7listings' ),
		);
		foreach ( $controls as $k => $v )
		{
			$params['mapControls'][] = array(
				'value'   => $k,
				'name'    => $v,
				'checked' => false,
			);
		}

		// Icons
		$params['icons'] = array_keys( Sl_Form::fa_icons() );

		wp_localize_script( 'sls-admin', 'Sls', $params );
	}

	/**
	 * Popup HTML template
	 *
	 * @return void
	 */
	function popup_templates()
	{
		echo '<div id="sl-shortcodes" ng-app="sls">';

		$this->include_files( array(
			'accordions',
			'button',
			'custom-list',
			'framed-image',
			'icon',
			'map',
			'slideshow',
			'styled-boxes',
			'tabs',
			'toggle',
			'tooltip',
			'widget-area',
			'social-links',
		) );

		echo '</div>';
	}

	function include_files( $files )
	{
		$dir = SL_SHORTCODES_DIR . 'popup';
		foreach ( (array) $files as $file )
		{
			$shortcode = str_replace( '-', '_', $file );
			require "$dir/$file.php";
		}
	}

	/**
	 * Add tinymce button
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	function mce_buttons( $buttons )
	{
		$this->insert( $buttons, 'numlist', 'sls_custom_list', 0 );
		$this->insert( $buttons, 'italic', 'sls_text' );
		$this->insert( $buttons, 'alignright', 'sls_button' );

		return array_values( $buttons );
	}

	/**
	 * Add tinymce button
	 *
	 * @param array $buttons
	 *
	 * @return array
	 */
	function mce_buttons_2( $buttons )
	{
		$this->insert( $buttons, 'charmap', 'sls_icon' );
		$this->insert( $buttons, 'indent', array( 'sls_map', 'sls_slider', 'sls_list', 'sls_normal', 'sls_listings', '|' ) );

		return $buttons;
	}

	/**
	 * Insert buttons to a specific position
	 *
	 * @param  array        $buttons
	 * @param  string       $item
	 * @param  array|string $insert
	 * @param  boolean      $after
	 *
	 * @return void
	 */
	function insert( &$buttons, $item, $insert, $after = true )
	{
		array_splice( $buttons, array_search( $item, $buttons ) + intval( $after ), 0, (array) $insert );
		$buttons = array_values( $buttons );
	}

	/**
	 * Add tinymce plugin
	 *
	 * @param array $plugins
	 *
	 * @return array
	 */
	function plugin( $plugins )
	{
		$plugins['SlShortcodes'] = SL_SHORTCODES_URL . 'js/tinymce-plugin.js';

		return $plugins;
	}
}
