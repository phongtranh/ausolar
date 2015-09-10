<?php

/**
 * Common controls for a module
 */
abstract class Sl_Core
{
	/**
	 * Post type: used for post type slug and some checks (prefix or suffix)
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Constructor
	 *
	 * @param string $post_type
	 *
	 * @return Sl_Core
	 */
	function __construct( $post_type = '' )
	{
		$this->post_type = $post_type;
		add_action( 'after_setup_theme', array( $this, 'init' ), 20 );
		add_action( 'sl_default_settings', array( $this, 'default_settings' ) );
	}

	/**
	 * Load files add hooks for this custom post type
	 * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
	 *
	 * @return void
	 */
	function init()
	{
		if ( ! Sl_License::is_module_activated( $this->post_type ) )
			return;

		$settings_file = THEME_MODULES . $this->post_type . '/settings.php';
		if ( is_admin() && file_exists( $settings_file ) )
			require $settings_file;

		// Safe here because the hooks() function runs after default settings are set
		if ( ! Sl_License::is_module_enabled( $this->post_type, false ) )
			return;

		$this->load_files();

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ), 20 );
		add_filter( 'sl_sharing_option_post_types', array( $this, 'add_broadcasted_post_types' ) );

		// Post type feature
		add_action( 'init', array( $this, 'views_support' ) );

		// Check if we're on taxonomy admin page
		add_filter( 'sl_taxonomy_image_taxonomies', array( $this, 'taxonomy_image_add' ) );
		add_filter( 'sl_taxonomy_icon_taxonomies', array( $this, 'taxonomy_icon_add' ) );

		// Register widgets
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		$this->hooks();
	}

	/**
	 * Custom hooks, run after all check, e.g. module is activated
	 *
	 * @return void
	 */
	function hooks()
	{
	}

	/**
	 * Load files
	 *
	 * @return void
	 */
	function load_files()
	{
		$dir = THEME_MODULES . $this->post_type;

		if ( file_exists( "$dir/helper.php" ) )
			require "$dir/helper.php";

		// Load here to allow frontend editing if needed
		if ( file_exists( "$dir/edit.php" ) )
			require "$dir/edit.php";

		if ( is_admin() )
		{
			if ( file_exists( "$dir/homepage.php" ) )
				require "$dir/homepage.php";
			if ( file_exists( "$dir/management.php" ) )
				require "$dir/management.php";
			if ( defined( 'DOING_AJAX' ) && file_exists( "$dir/ajax.php" ) )
				require "$dir/ajax.php";
		}
		else
		{
			if ( file_exists( "$dir/featured-title.php" ) )
				require "$dir/featured-title.php";
			if ( file_exists( "$dir/frontend.php" ) )
				require "$dir/frontend.php";
		}
	}

	/**
	 * Set default settings for this custom post type
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function default_settings( $settings )
	{
		return $settings;
	}

	/**
	 * Default settings for ATR: accommodation, tour, rental
	 *
	 * @return array
	 */
	function atr_default_settings()
	{
		$type = $this->post_type;

		// For homepage
		$settings = array(
			'homepage_' . $type . '_featured_total'               => 7,
			'homepage_' . $type . '_featured_orderby'             => 'price-asc',
			'homepage_' . $type . '_featured_post_title'          => 1,
			'homepage_' . $type . '_featured_price'               => 1,
			'homepage_' . $type . '_featured_booking'             => 1,
			'homepage_' . $type . '_featured_rating'              => 1,
			'homepage_' . $type . '_featured_excerpt'             => 1,
			'homepage_' . $type . '_featured_excerpt_length'      => 65,
			'homepage_' . $type . '_featured_transition'          => 'fade',
			'homepage_' . $type . '_featured_transition_delay'    => 0,
			'homepage_' . $type . '_featured_transition_speed'    => 500,
			'homepage_' . $type . '_featured_priority'            => 2,

			'homepage_' . $type . '_listings_display'             => 12,
			'homepage_' . $type . '_listings_layout'              => 'grid',
			'homepage_' . $type . '_listings_columns'             => 6,
			'homepage_' . $type . '_listings_thumbnail'           => 1,
			'homepage_' . $type . '_listings_image_size'          => 'sl_pano_small',
			'homepage_' . $type . '_listings_price'               => 1,
			'homepage_' . $type . '_listings_booking'             => 1,
			'homepage_' . $type . '_listings_excerpt'             => 1,
			'homepage_' . $type . '_listings_excerpt_length'      => 25,
			'homepage_' . $type . '_listings_priority'            => 1,
			'homepage_' . $type . '_listings_orderby'             => 'price-asc',
			'homepage_' . $type . '_listings_more_listings'       => 1,
			'homepage_' . $type . '_listings_more_listings_text'  => __( 'See more listings', '7listings' ),
			'homepage_' . $type . '_listings_more_listings_style' => 'button',

			'homepage_' . $type . '_types_display'                => 12,
			'homepage_' . $type . '_types_columns'                => 6,
			'homepage_' . $type . '_types_image'                  => 1,
			'homepage_' . $type . '_types_image_size'             => 'sl_pano_small',
			'homepage_' . $type . '_types_desc'                   => 1,
			'homepage_' . $type . '_types_orderby'                => 'listings-asc',
		);

		// For listings
		$settings = array_merge( array(
			$type . '_booking'                          => 1,
			$type . '_featured_graphics'                => 1,
			$type . '_base_url'                         => $type,

			// Page Settings

			// Archive Listings
			$type . '_archive_image_size'               => 'sl_pano_small',
			$type . '_archive_desc_enable'              => 1,
			$type . '_archive_desc'                     => 50,
			$type . '_archive_readmore'                 => 1,
			$type . '_archive_readmore_type'            => 'button',
			$type . '_archive_readmore_text'            => __( 'Read more', '7listings' ),
			$type . '_book_in_archive'                  => 1,
			$type . '_archive_resource_desc'            => 25,

			// Archive Layout
			$type . '_archive_map_image_size'           => 'sl_pano_small',
			$type . '_archive_map_excerpt_length'       => 25,
			$type . '_archive_cat_image'                => 1,
			$type . '_archive_cat_image_type'           => 'thumbnail',
			$type . '_archive_cat_image_size'           => 'sl_thumb_tiny',
			$type . '_archive_num'                      => get_option( 'posts_per_page' ),
			$type . '_archive_priority'                 => 1,
			$type . '_archive_orderby'                  => 'price-asc',
			$type . '_archive_layout'                   => 'list',
			$type . '_archive_columns'                  => 4,
			$type . '_archive_sidebar_layout'           => 'none',

			// Single Listings
			$type . '_single_title'                     => '%LISTING_NAME%',
			$type . '_single_featured_title_image_size' => 'full',
			$type . '_brand_logo_image_size'            => 'sl_thumb_small',
			$type . '_slider_image_size'                => 'sl_pano_large',
			$type . '_link_to_archive'                  => 1,
			$type . '_single_address'                   => 1,
			$type . '_single_contact'                   => 1,
			$type . '_single_features'                  => 1,
			$type . '_google_maps'                      => 1,
			$type . '_comment_status'                   => 1,
			$type . '_ping_status'                      => 1,

			// Single Similar Listings
			$type . '_similar_enable'                   => 1,
			$type . '_similar_by'                       => 'type',
			$type . '_similar_columns'                  => 3,
			$type . '_similar_image_size'               => 'sl_pano_small',
			$type . '_similar_display'                  => 3,
			$type . '_similar_excerpt_length'           => 25,

			// Single Layout
			$type . '_single_sidebar_layout'            => 'none',
		), $settings );

		return $settings;
	}

	/**
	 * Register custom post type
	 *
	 * @return void
	 */
	function register_post_type()
	{
	}

	/**
	 * Register custom taxonomies
	 *
	 * @return void
	 */
	function register_taxonomies()
	{
	}

	/**
	 * Add custom post type to broadcasted post type list
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
	function add_broadcasted_post_types( $post_types )
	{
		$post_types = (array) $post_types;
		if ( ! in_array( $this->post_type, $post_types ) )
			$post_types[] = $this->post_type;

		return $post_types;
	}

	/**
	 * Add 'views' support for custom post type
	 *
	 * @return void
	 */
	function views_support()
	{
		add_post_type_support( $this->post_type, array( 'entry-views' ) );
	}

	/**
	 * Add taxonomy to supported taxonomy image list
	 *
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	function taxonomy_image_add( $taxonomies )
	{
		$taxonomies[] = sl_meta_key( 'tax_type', $this->post_type );

		return $taxonomies;
	}

	/**
	 * Add taxonomy to supported taxonomy map icon list
	 *
	 * @param array $taxonomies
	 *
	 * @return array
	 */
	function taxonomy_icon_add( $taxonomies )
	{
		$taxonomies[] = sl_meta_key( 'tax_type', $this->post_type );

		return $taxonomies;
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	function register_widgets()
	{
	}
}
