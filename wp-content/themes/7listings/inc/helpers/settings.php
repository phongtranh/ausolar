<?php
/**
 * This file handles all common functions for theme settings, including
 * - Setup default settings when theme is loaded
 * - Helper function to get setting
 * - A settings page class which will be extended to create setting pages
 */

add_action( 'after_setup_theme', 'sl_get_all_settings', 1 );

/**
 * Load theme settings and store in global variable for quick reference
 *
 * @return array
 */
function sl_get_all_settings()
{
	global $settings;
	$settings = get_option( THEME_SETTINGS, array() );
	$settings = sl_default_settings( $settings );

	return $settings;
}

/**
 * Helper function to get theme setting
 *
 * @param  string $name
 *
 * @return mixed
 */
function sl_setting( $name )
{
	global $settings;
	$value = isset( $settings[$name] ) ? $settings[$name] : false;

	/**
	 * Add filter to allow developers to adjust setting based on context
	 *
	 * @since 13/8/2014
	 */
	$value = apply_filters( 'sl_setting', $value, $name );
	$value = apply_filters( "sl_setting-$name", $value, $name );

	return $value;
}

/**
 * Helper function to set theme setting
 *
 * @param string $name  Setting name
 * @param mixed  $value Setting value
 * @param bool   $unset If $value is empty, then unset setting or not?
 *
 * @return mixed
 */
function sl_set_setting( $name, $value, $unset = true )
{
	$settings = get_option( THEME_SETTINGS, array() );
	if ( $value )
		$settings[$name] = $value;
	elseif ( $unset )
		unset( $settings[$name] );

	update_option( THEME_SETTINGS, $settings );
}

/**
 * Get default settings
 *
 * @return array
 */
function sl_default_settings( $settings )
{
	// Default settings in each section/page
	$sections = array( 'general', 'home', 'listings', 'payment', 'design', 'email', 'contact' );
	foreach ( $sections as $section )
	{
		add_filter( 'sl_default_settings', "sl_default_settings_$section", 1 );
	}
	$settings = apply_filters( 'sl_default_settings', $settings );

	return $settings;
}

/**
 * Get default settings: General
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_general( $settings )
{
	return array_merge( array(
		'sidebars'         => array(),
		'comments_style'   => 1,
		'comments_website' => 1,
	), $settings );
}

/**
 * Get default settings: Home
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_home( $settings )
{
	$settings = array_merge( array(
		'homepage_featured_area_height'          => 'medium',
		'homepage_custom_content_sidebar_layout' => 'none',
		'homepage_footer'                        => 1,
		'homepage_order'                         => array( 'featured_area', 'custom_content', 'custom_html', 'listings_search' ),

		// Listings Search widget
		'homepage_listings_search_post_types'    => array(),
		'homepage_listings_search_type_number'   => 12,
	), $settings );

	// Check for missed widgets
	$widgets = array(
		'featured_area',
		'custom_content',
		'custom_html',
		'listings_search',
	);

	foreach ( $widgets as $widget )
	{
		if ( ! in_array( $widget, $settings['homepage_order'] ) )
			$settings['homepage_order'][] = $widget;
	}

	// Check if panels are active
	$fields = array(
		'homepage_featured_area_active'   => 1,
		'homepage_custom_content_active'  => 1,
		'homepage_custom_html_active'     => 1,
		'homepage_listings_search_active' => 1,
	);

	$has_field = false;
	foreach ( $fields as $field => $active )
	{
		if ( isset( $settings[$field] ) )
		{
			$has_field = true;
			break;
		}
	}

	// Default: all panels are active
	if ( ! $has_field )
	{
		$settings = array_merge( $fields, $settings );
	}
	else
	{
		foreach ( $fields as $field => $active )
		{
			if ( empty( $settings[$field] ) )
				$settings[$field] = 0;
		}
	}

	return $settings;
}

/**
 * Get default settings: Listings
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_listings( $settings )
{
	return array_merge( array(
		'listing_types' => array( 'accommodation', 'tour', 'rental', 'cart' ),
	), $settings );
}

/**
 * Get default settings: Payment
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_payment( $settings )
{
	return array_merge( array(
		'currency'           => 'AUD',
		'currency_position'  => 'left',
		'eway_description'   => __( 'Pay via eWay; eWay is a secure payment gateway that allows you to pay safely with Mastercard or Visa.', '7listings' ),
		'paypal_description' => __( 'Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.', '7listings' ),
	), $settings );
}

/**
 * Get default settings: Design
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_design( $settings )
{
	$settings = array_merge( array(
		'logo_width'                      => 32,
		'logo_height'                     => 32,

		'mobile_logo_width'               => 32,
		'mobile_logo_height'              => 32,

		'display_site_title'              => 1,
		'display_site_description'        => 1,

		'display_mobile_site_title'       => 1,
		'display_mobile_site_description' => 0,

		'excerpt_limit'                   => 75,
		'excerpt_more'                    => '...',
	), $settings );

	// Weather
	$current_offset = sl_timezone_offset();
	$tzstring       = get_option( 'timezone_string' );

	// Remove old Etc mappings.  Fallback to gmt_offset.
	if ( false !== strpos( $tzstring, 'Etc/GMT' ) )
		$tzstring = '';

	// Create a UTC+- zone if no timezone string exists
	if ( empty( $tzstring ) )
	{
		if ( 0 == $current_offset )
			$tzstring = 'UTC+0';
		elseif ( $current_offset < 0 )
			$tzstring = 'UTC' . $current_offset;
		else
			$tzstring = 'UTC+' . $current_offset;
	}

	$settings = array_merge( array(
		'design_weather_color'        => '#f00',
		'design_weather_color_scheme' => 'dark',
		'weather_unit'                => 'c',
		'weather_timezone'            => $tzstring, // Default WP timezone
		'design_weather_style'        => 'image',
	), $settings );

	// Colors
	$settings = array_merge( array(
		// Header
		'design_header_background'                    => '#fff',
		'design_header_background_position_x'         => 'center',
		'design_header_background_position_y'         => 'center',
		'design_header_background_repeat'             => 'repeat',
		'design_header_background_attachment'         => 'scroll',
		'design_header_background_size'               => 'full',

		// Site title & description
		'design_site_title_color'                     => 'rgba(0,0,0,.8)',
		'design_site_title_size'                      => 32,
		'design_site_title_font'                      => 'DIN Cond Bold',

		'design_site_description_color'               => 'rgba(0,0,0,.5)',
		'design_site_description_size'                => 24,
		'design_site_description_font'                => 'DIN Cond Normal',

		'design_header_phone_color_scheme'            => 'dark',
		'design_header_phone_color'                   => '#f00',

		'design_header_search'                        => 1,

		// Navbar
		'design_navbar_background_color'              => '#565656',
		'design_navbar_link_background_hover'         => '#000',
		'design_navbar_link_background_active'        => '#424242',
		'design_navbar_link_color'                    => '#999',
		'design_navbar_link_color_hover'              => '#FFF',
		'design_navbar_link_color_active'             => '#FFF',
		'design_navbar_font_size'                     => 14,
		'design_navbar_font'                          => 'Arial, Helvetica',
		'design_navbar_height_desktop'                => 40,

		// Main
		'design_main_background'                      => '#fff',
		'design_main_background_position_x'           => 'center',
		'design_main_background_position_y'           => 'center',
		'design_main_background_repeat'               => 'repeat',
		'design_main_background_attachment'           => 'scroll',
		'design_main_background_size'                 => 'full',

		// Dropdown
		'design_dropdown_background'                  => '#383838',
		'design_dropdown_link_background_hover'       => '#000',
		'design_dropdown_link_background_active'      => '#191919',
		'design_dropdown_link_color'                  => '#999',
		'design_dropdown_link_color_hover'            => '#FFF',
		'design_dropdown_link_color_active'           => '#dbdbdb',

		// Featured Area
		'design_featured_min_height'                  => 80,
		'design_featured_background'                  => '#7f7f7f',
		'design_featured_background_position_x'       => 'center',
		'design_featured_background_position_y'       => 'center',
		'design_featured_background_repeat'           => 'repeat',
		'design_featured_background_attachment'       => 'scroll',
		'design_featured_background_size'             => 'full',
		'design_featured_heading_color'               => '#fff',
		'design_featured_custom_text'                 => '#eaeaea',

		// Buttons
		'design_button_primary_text'                  => '#FFF',
		'design_button_primary_background'            => '#00ADEE',
		'design_button_primary_font_size'             => 16,
		'design_button_primary_font'                  => 'DIN Cond Normal',
		'design_button_text'                          => '#999',
		'design_button_background'                    => '#DDD',
		'design_button_font_size'                     => 14,
		'design_button_font'                          => 'Arial, Helvetica',

		// Price
		'design_price_text'                           => '#ba9121',
		'design_price_background'                     => '#f4d838',

		// Colors
		'design_star_rating_color'                    => '#f9d60c',
		'design_review_rating_color'                  => '#e20000',
		'design_review_rating_icon'                   => '\f004',
		'design_review_rating_background_icon'        => '\f08a',

		// Label
		'design_label_text_color'                     => '#fff',
		'design_label_background_color'               => '#7ec5fc',

		// Link
		'design_link_color'                           => '#08c',
		'design_link_color_hover'                     => '#f60',

		// Headings
		'design_heading_transform'                    => 'uppercase',

		'design_h1_size'                              => 38,
		'design_h1_color'                             => '#333',
		'design_h1_font'                              => 'DIN Cond Bold',

		'design_h2_size'                              => 32,
		'design_h2_color'                             => '#333',
		'design_h2_font'                              => 'DIN Cond Bold',

		'design_h3_size'                              => 24,
		'design_h3_color'                             => '#333',
		'design_h3_font'                              => 'DIN Cond Bold',

		'design_h4_size'                              => 18,
		'design_h4_color'                             => '#333',
		'design_h4_font'                              => 'DIN Cond Normal',

		'design_h5_size'                              => 14,
		'design_h5_color'                             => '#333',
		'design_h5_font'                              => 'DIN Cond Normal',

		'design_h6_size'                              => 12,
		'design_h6_color'                             => '#333',
		'design_h6_font'                              => 'DIN Cond Normal',

		// Text
		'design_text_size'                            => 14,
		'design_text_color'                           => '#000',
		'design_text_font'                            => 'Arial, Helvetica',

		// List
		'design_list_icon'                            => '\f111',
		'design_list_icon_color'                      => '#6d6d6d',

		// Thumbnail
		'design_thumbnail_border_width'               => 7,
		'design_thumbnail_border_color'               => '#f7f7f7',
		'design_thumbnail_background_color'           => '#ccc',

		'design_base_border_radius'                   => 4,

		// Sidebar
		'design_sidebar_background'                   => '#fff',
		'design_sidebar_background_position_x'        => 'center',
		'design_sidebar_background_position_y'        => 'center',
		'design_sidebar_background_repeat'            => 'repeat',
		'design_sidebar_background_attachment'        => 'scroll',
		'design_sidebar_background_size'              => 'full',

		'design_sidebar_width'                        => 33,
		'design_sidebar_heading_color'                => '#333',
		'design_sidebar_text_color'                   => '#000',
		'design_sidebar_link_color'                   => '#08c',
		'design_sidebar_link_hover_color'             => '#f60',

		// Sidebar buttons
		'design_sidebar_button_primary_text'          => '#fff',
		'design_sidebar_button_primary_background'    => '#00adee',
		'design_sidebar_button_text'                  => '#999',
		'design_sidebar_button_background'            => '#ddd',

		// Sidebar price
		'design_sidebar_price_text'                   => '#ba9121',
		'design_sidebar_price_background'             => '#f4d838',

		'design_layout_default_page'                  => 'right',
		'design_sidebar_default_page'                 => 'page',

		// Google maps
		'design_map_zoom'                             => 8,
		'design_map_controls'                         => array(),
		'design_map_type'                             => 'road',
		'design_map_stroke_color'                     => '#f00',
		'design_map_stroke_opacity'                   => 80,
		'design_map_fill_color'                       => '#f00',
		'design_map_fill_opacity'                     => 30,

		// Breadcrumbs
		'design_breadcrumbs_enable'                   => 1,
		'design_breadcrumbs_background'               => '#fff',
		'design_breadcrumbs_separator'                => '#ccc',
		'design_breadcrumbs_current'                  => '#a8a8a8',

		// Mini cart
		'design_mini_cart_enable'                     => 1,

		// Footer
		'design_footer_top_background'                => 'rgba(255,255,255,.4)',
		'design_footer_top_background_position_x'     => 'center',
		'design_footer_top_background_position_y'     => 'center',
		'design_footer_top_background_repeat'         => 'repeat',
		'design_footer_top_background_attachment'     => 'scroll',
		'design_footer_top_background_size'           => 'full',
		'design_footer_top_title'                     => '#686868',
		'design_footer_top_text'                      => '#000',
		'design_footer_top_link'                      => '#08c',
		'design_footer_top_link_hover'                => '#f60',

		// Footer buttons
		'design_footer_top_button_primary_text'       => '#fff',
		'design_footer_top_button_primary_background' => '#00adee',
		'design_footer_top_button_text'               => '#999',
		'design_footer_top_button_background'         => '#ddd',

		// Footer price
		'design_footer_top_price_text'                => '#ba9121',
		'design_footer_top_price_background'          => '#f4d838',

		'design_footer_middle_background'             => 'rgba(255,255,255,.7)',
		'design_footer_middle_background_position_x'  => 'center',
		'design_footer_middle_background_position_y'  => 'center',
		'design_footer_middle_background_repeat'      => 'repeat',
		'design_footer_middle_background_attachment'  => 'scroll',
		'design_footer_middle_background_size'        => 'full',
		'design_footer_middle_title'                  => '#686868',
		'design_footer_middle_text'                   => '#000',
		'design_footer_middle_link'                   => '#08c',
		'design_footer_middle_link_hover'             => '#f60',

		'design_footer_bottom_background'             => 'rgba(255,255,255,.9)',
		'design_footer_bottom_background_position_x'  => 'center',
		'design_footer_bottom_background_position_y'  => 'center',
		'design_footer_bottom_background_repeat'      => 'repeat',
		'design_footer_bottom_background_attachment'  => 'scroll',
		'design_footer_bottom_background_size'        => 'full',
		'design_footer_bottom_text'                   => '#000',
		'design_footer_bottom_link'                   => '#08c',
		'design_footer_bottom_link_hover'             => '#f60',

		// Background
		'design_body_background'                      => '#eaeaea',
		'design_background_type'                      => 'full',
		'design_background_position_x'                => 'center',
		'design_background_position_y'                => 'top',
		'design_background_repeat'                    => 'repeat',
		'design_background_attachment'                => 'scroll',
		'design_background_size'                      => 'full',
		'layout_option'                               => 'no-box',
		'layout_position'                             => 'center',

		'design_layout_mobile_nav'                    => 'left',
		'design_mobile_menu_height'                   => 44,
		'design_mobile_nav_background'                => '#565656',
		'design_mobile_site_title_color'              => 'rgba(255,255,255,.95)',
		'design_mobile_site_title_size'               => 24,
		'design_mobile_site_description_color'        => 'rgba(255,255,255,.8)',
		'design_mobile_site_description_size'         => 16,

		'design_mobile_menu_background'               => '#323232',
		'design_mobile_link_color'                    => '#C0C0C0',
		'design_mobile_link_size'                     => 16,
		'design_mobile_link_size_sub'                 => 15,
		'design_mobile_link_color_active'             => '#FFF',
		'design_mobile_link_background_active'        => 'rgba(0,0,0,.5)',

		'design_mobile_nav_break_point'               => '1024px',
	), $settings );

	return $settings;
}

/**
 * Get default settings: Email
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_email( $settings )
{
	return array_merge( array(
		'emails_booking_cart_guess_subject' => __( 'Booking Received', '7listings' ),
		'emails_booking_cart_admin_subject' => __( 'Cart Booking - #[booking_id] : [total]', '7listings' ),

		'emails_booking_acco_guess_subject' => __( 'Accommodation Booking Received', '7listings' ),
		'emails_booking_acco_admin_subject' => __( 'Accommodation Booking - #[booking_id] : [total]', '7listings' ),

		'emails_booking_tour_guess_subject' => __( 'Tour Booking Received', '7listings' ),
		'emails_booking_tour_admin_subject' => __( 'Tour Booking - #[booking_id] : [total]', '7listings' ),

		'emails_footer_text'                => __( 'Powered by 7Listings', '7listings' ),
		'emails_header_color'               => '#fff',
		'emails_base_color'                 => '#f00',
		'emails_background'                 => '#f7f7f7',
		'emails_body_background'            => '#fff',
		'emails_body_text_color'            => '#333',
		'emails_heading_color'              => '#f00',
		'emails_use_template'               => 1,

		'emails_smtp_secure'                => 'none',

		'emails_from_name'                  => '[site-title]',
		'emails_from_email'                 => get_option( 'admin_email' ),
	), $settings );
}

/**
 * Get default settings: Contact
 *
 * @param array $settings
 *
 * @return array
 */
function sl_default_settings_contact( $settings )
{
	return array_merge( array(
		'contact_admin_subject' => __( '[subject] - #[message_counter]', '7listings' ),
		'contact_subject'       => __( 'Message Received!', '7listings' ),
		'contact_social_links'  => 1,
	), $settings );
}

/**
 * Scan all settings and get list of settings begin with $prefix and convert it into an array
 *
 * Example:
 * - From:
 *     array (
 *         'homepage_tour_listing_thumbnail' => 1,
 *         'homepage_tour_listing_rating' => 1,
 *         'homepage_tour_listing_booking' => 1,
 *     )
 * - To:
 *     array(
 *         'thumbnail' => 1,
 *         'rating' => 1,
 *         'booking' => 1,
 *     )
 *
 * @param string $prefix Prefix of settings name
 *
 * @return array
 */
function sl_settings_to_array( $prefix )
{
	global $settings;

	$list  = array();
	$start = strlen( $prefix );
	foreach ( $settings as $k => $v )
	{
		$position = strpos( $k, $prefix );
		if ( 0 !== $position )
			continue;
		$name        = substr( $k, $start );
		$list[$name] = $v;
	}

	return $list;
}

/**
 * Common class for settings page
 */
class Sl_Settings_Page
{
	/**
	 * @var string Page slug
	 */
	public $slug;

	/**
	 * @var string Menu title
	 */
	public $title;

	/**
	 * @var string Parent page
	 */
	public $parent;

	/**
	 * Constructor
	 *
	 * @param string $slug
	 * @param string $title
	 * @param string $parent
	 *
	 * @return Sl_Settings_Page
	 */
	function __construct( $slug, $title, $parent = '7listings' )
	{
		$this->slug   = $slug;
		$this->title  = $title;
		$this->parent = $parent;

		add_action( 'sl_admin_menu', array( $this, 'add_page' ) );
		add_filter( "sl_settings_sanitize_$slug", array( $this, 'sanitize' ), 10, 2 );
	}

	/**
	 * Add admin page
	 *
	 * @return void
	 */
	function add_page()
	{
		$page = add_submenu_page( $this->parent, $this->title, $this->title, 'edit_theme_options', $this->slug, array( $this, 'show' ) );
		add_action( "load-$page", array( $this, 'load' ) );
		add_action( "load-$page", array( $this, 'help' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 * Form is not validated to prevent cannot saving fields and fields are hidden (in tabs), which means
	 * users can't see the errors
	 *
	 * @return void
	 */
	function show()
	{
		?>
		<div class="wrap">
			<form method="post" action="options.php" enctype="multipart/form-data" novalidate>

				<?php settings_fields( THEME_SETTINGS ); ?>
				<input type="hidden" name="sl_page" value="<?php echo $this->slug; ?>">

				<?php $this->page_title(); ?>
				<?php $this->page_content(); ?>

				<p class="submit">
					<?php submit_button( __( 'Save', '7listings' ), 'primary', 'submit', false ); ?>
					<?php $this->more_buttons(); ?>
				</p>

				<?php $this->form_bottom(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Show more submit/reset buttons if needed
	 *
	 * @return void
	 */
	function more_buttons()
	{
	}

	/**
	 * Add more custom content on the bottom of the form
	 *
	 * @return void
	 */
	function form_bottom()
	{
	}

	/**
	 * Display title for settings page
	 * Overwrite this function in subclass to change or remove the title
	 *
	 * @return void
	 */
	function page_title()
	{
		echo "<h2>$this->title</h2>";
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function load()
	{
	}

	/**
	 * Add help tab
	 *
	 * @return void
	 */
	function help()
	{
		sl_add_help_tabs( $this->slug );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
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
		return $options_new;
	}

	/**
	 * Helper function Sanitize checkboxes value
	 *
	 * @param array $options_new
	 * @param array $options
	 * @param array $fields Array of checkboxes
	 *
	 * @return array
	 */
	static function sanitize_checkboxes( &$options_new, $options, $fields )
	{
		foreach ( $fields as $field )
		{
			if ( empty( $options[$field] ) )
				$options_new[$field] = 0;
		}

		return $options_new;
	}
}
