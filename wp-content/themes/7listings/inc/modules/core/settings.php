<?php

/**
 * This class will hold all settings
 */
abstract class Sl_Core_Settings
{
	/**
	 * Post type: used for post type slug and some checks (prefix or suffix)
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Add hooks
	 *
	 * @param string $post_type
	 *
	 * @return Sl_Core_Settings
	 */
	function __construct( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'sl_settings_modules', array( $this, 'show_settings_module' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'sl_page_menu', array( $this, 'page_menu' ) );
	}

	/**
	 * Check module is enabled and then add hooks if needed
	 *
	 * @return void
	 */
	function init()
	{
		if ( ! Sl_License::is_module_enabled( $this->post_type, false ) )
			return;

		add_action( 'sl_settings_listings_tab', array( $this, 'show_tab' ) );
		add_action( 'sl_settings_listings_tab_content', array( $this, 'show' ) );

		add_action( 'sl_email_tab', array( $this, 'email_tab' ) );
		add_action( 'sl_email_tab_content', array( $this, 'email_tab_content' ) );

		// Save settings for this module in main theme settings page (7listings)
		add_filter( 'sl_settings_sanitize_7listings', array( $this, 'sanitize' ), 10, 2 );

		// Save settings for archive and single pages in Pages \ this module page
		add_filter( "sl_settings_sanitize_page_{$this->post_type}", array( $this, 'sanitize_page' ), 10, 2 );

		/**
		 * Add permalink settings to WordPress Settings \ Permalink screen
		 * If a modules does not need permalink settings (Product), then overwrite this method in child class
		 *
		 * @since 5.2.1
		 */
		$this->permalink_settings();
	}

	/**
	 * Show on/off setting for module
	 *
	 * @return void
	 */
	function show_settings_module()
	{
		$id = uniqid();

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
					<label>%5$s</label>
				</div>
				<div class="sl-input">
					<span class="checkbox">
						<input type="checkbox" id="%1$s" name="%2$s[listing_types][]" value="%3$s"%4$s>
						<label for="%1$s">&nbsp;</label>
					</span>
				</div>
			</div>',
			$id,
			THEME_SETTINGS,
			$this->post_type,
			checked( in_array( $this->post_type, sl_setting( 'listing_types' ) ), 1, false ),
			$label
		);
	}

	/**
	 * Show settings field
	 *
	 * @return void
	 */
	function show_tab()
	{
		/**
		 * Get plural form of the post type label
		 * Use Inflect library if module is not activated, thus post type does not exist which only happens for
		 * product, because other modules require activated to show settings tab
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
		echo "<a class='nav-tab'>$label</a>";
	}

	/**
	 * Show settings field
	 *
	 * @return void
	 */
	function show()
	{
		require THEME_DIR . "inc/admin/tabs/{$this->post_type}/settings.php";
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
		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$this->post_type}_booking",
			"{$this->post_type}_featured_graphics",
		) );
	}

	/**
	 * Add settings tab in "email" settings page
	 *
	 * @return void
	 */
	function email_tab()
	{
	}

	/**
	 * Add settings tab content in "email" settings page
	 *
	 * @return void
	 */
	function email_tab_content()
	{
		echo '<div>';
		require THEME_DIR . "inc/admin/tabs/{$this->post_type}/email.php";
		echo '</div>';
	}

	/**
	 * Add page menu
	 *
	 * @return void
	 */
	function page_menu()
	{
		if ( ! Sl_License::is_module_enabled( $this->post_type, false ) || ! post_type_exists( $this->post_type ) )
			return;

		// Add page under "Page" menu
		$post_type_object = get_post_type_object( $this->post_type );
		$label            = $post_type_object->label;
		$page             = add_pages_page( $label, $label, 'edit_theme_options', $this->post_type, array( $this, 'page' ) );
		add_action( "admin_print_styles-{$page}", array( $this, 'page_enqueue' ) );
		add_action( "load-$page", array( $this, 'page_load' ) );
		add_action( "load-$page", array( $this, 'page_help' ) );
	}

	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function page_enqueue()
	{
		wp_enqueue_script( 'sl-page-settings', THEME_JS . 'admin/page-settings.js', array( 'jquery' ) );
	}

	/**
	 * Show theme setting page_<?php echo $this->post_type; ?> page
	 * Form is not validated to prevent cannot saving fields and fields are hidden (in tabs), which means
	 * users can't see the errors
	 *
	 * @return void
	 */
	function page()
	{
		?>
		<div class="wrap">
			<form method="post" action="options.php" enctype="multipart/form-data" novalidate>
				<h2><?php echo ucwords( $this->post_type ); ?></h2>

				<?php settings_fields( THEME_SETTINGS ); ?>

				<input type="hidden" name="sl_page" value="page_<?php echo $this->post_type; ?>">

				<h2 class="nav-tab-wrapper sl-tabs">
					<a href="#archive" class="nav-tab"><?php _e( 'Archive', '7listings' ); ?></a>
					<a href="#single" class="nav-tab"><?php _e( 'Single', '7listings' ); ?></a>
				</h2>

				<div class="sl-tabs-content">
					<?php
					echo '<div>';
					require THEME_DIR . 'inc/admin/tabs/' . $this->post_type . '/archive.php';
					echo '</div>';
					echo '<div>';
					require THEME_DIR . 'inc/admin/tabs/' . $this->post_type . '/single.php';
					echo '</div>';
					?>
				</div>

				<?php $this->after_form(); ?>

				<?php submit_button( 'Save' ); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Show some content before settings form (meta boxes)
	 *
	 * @return void
	 */
	function after_form()
	{
	}

	/**
	 * Add meta boxes for setting page
	 *
	 * @return void
	 */
	function page_load()
	{
		//add_meta_box( 'archive', __( 'Archive', '7listings' ), array( $this, 'meta_box' ), 'sl-settings-' . $this->post_type, 'normal' );
		//add_meta_box( 'single', __( 'Single', '7listings' ), array( $this, 'meta_box' ), 'sl-settings-' . $this->post_type, 'normal' );
	}

	/**
	 * Add help tab
	 *
	 * @return void
	 */
	function page_help()
	{
		sl_add_help_tabs( 'page-' . $this->post_type );
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

		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			// Archive
			"{$type}_archive_desc_enable",
			"{$type}_archive_rating",
			"{$type}_archive_price",
			"{$type}_archive_booking",
			"{$type}_archive_readmore",
			"{$type}_book_in_archive",

			"{$type}_archive_map",
			"{$type}_archive_map_image",
			"{$type}_archive_map_booking",
			"{$type}_archive_map_price",
			"{$type}_archive_map_rating",
			"{$type}_archive_map_excerpt",

			"{$type}_archive_search_widget",
			"{$type}_archive_search_widget_keyword",
			"{$type}_archive_search_widget_location",
			"{$type}_archive_search_widget_type",
			"{$type}_archive_search_widget_feature",
			"{$type}_archive_search_widget_rating",

			"{$type}_archive_cat_desc",
			"{$type}_archive_cat_image",
			"{$type}_archive_priority",

			// Single
			"{$type}_single_featured_title_map",
			"{$type}_single_featured_title_image",

			"{$type}_link_to_archive",

			"{$type}_single_logo",
			"{$type}_single_address",
			"{$type}_single_contact",
			"{$type}_single_features",
			"{$type}_google_maps",
			"{$type}_comment_status",
			"{$type}_ping_status",

			"{$type}_similar_enable",
			"{$type}_similar_rating",
			"{$type}_similar_price",
			"{$type}_similar_booking",
			"{$type}_similar_excerpt",
		) );
	}

	/**
	 * Adds permalink settings to WordPress Settings \ Permalink screen
	 * Also handles saving permalink option to theme option
	 *
	 * If a modules does not need permalink settings (Product), then overwrite this method in child class
	 *
	 * @since 5.2.1
	 * @return void
	 */
	public function permalink_settings()
	{
		// Add new section to Settings \ Permalink page
		add_settings_section(
			'sl_permalink_' . $this->post_type,
			sprintf( __( '%s permalink base', '7listings' ), sl_setting( $this->post_type . '_label' ) ),
			array( $this, 'permalink_section' ),
			'permalink'
		);

		// Add custom settings field for this module
		add_settings_field(
			'sl_permalink_' . $this->post_type,        // id
			__( 'Custom base', '7listings' ),          // setting title
			array( $this, 'permalink_input' ),         // display callback
			'permalink',                               // settings page
			'sl_permalink_' . $this->post_type         // settings section
		);

		$this->permalink_save();
	}

	/**
	 * Display nonce for saving permalink settings, used to check to before saving permalinks
	 * As the section is used in all modules, we make this method static
	 *
	 * @return void
	 */
	public function permalink_section()
	{
		wp_nonce_field( 'save-' . $this->post_type, 'sl_nonce_permalink_' . $this->post_type, false );
		$post_type_object = get_post_type_object( $this->post_type );
		echo '<p>' . sprintf( __( 'These settings control the permalinks used for %s. These settings only apply when <strong>not using "default" permalinks above</strong>.', '7listings' ), strtolower( $post_type_object->label ) ) . '</p>';
	}

	/**
	 * Display input for permalink settings
	 *
	 * @since 5.2.1
	 * @return void
	 */
	public function permalink_input()
	{
		?>
		<input type="text" name="<?php echo esc_attr( 'sl_permalink_' . $this->post_type ); ?>" value="<?php echo esc_attr( sl_setting( $this->post_type . '_base_url' ) ); ?>">
	<?php
	}

	/**
	 * Saver permalink settings
	 *
	 * @return void
	 */
	public function permalink_save()
	{
		$name = 'sl_nonce_permalink_' . $this->post_type;
		if ( empty( $_POST[$name] ) || ! wp_verify_nonce( $_POST[$name], 'save-' . $this->post_type ) )
			return;

		$name = 'sl_permalink_' . $this->post_type;
		if ( ! empty( $_POST[$name] ) )
			sl_set_setting( $this->post_type . '_base_url', $_POST[$name] );
	}
}
