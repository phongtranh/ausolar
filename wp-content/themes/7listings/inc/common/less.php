<?php
if ( ! class_exists( 'Sl_Less' ) )
{
	/**
	 * Enables the use of LESS in WordPress
	 */
	class Sl_Less
	{
		/**
		 * Add hooks to compile LESS
		 *
		 * @return void
		 */
		public static function load()
		{
			// Load LESS parser
			if ( ! class_exists( 'lessc' ) )
				require_once PEACE_DIR . 'extensions/lessc.inc.php';

			// Normal LESS
			add_filter( 'style_loader_src', array( __CLASS__, 'parse' ), 1000, 2 );

			// Editor LESS
			add_filter( 'mce_css', array( __CLASS__, 'parse_editor' ), 1000 );
		}

		/**
		 * Lessify the stylesheet and return the href of the compiled file
		 *
		 * @param string $src Source URL of the file to be parsed
		 * @param string $handle
		 *
		 * @return string        URL of the compiled stylesheet
		 */
		public static function parse( $src, $handle )
		{
			// Parse only .less file that has handle begins with our prefix
			if (
				0 !== strpos( $handle, 'sl-' )
				|| ! preg_match( '/\.less$/', preg_replace( '/\?.*$/', '', $src ) )
			)
				return $src;

			// Use stylesheet path & url to make sure it works with network & domain mapping
			// Use dirname() to get /wp-content/ path & url => not confused of child theme if $src is in parent theme
			$content_dir = dirname( dirname( CHILD_DIR ) );
			$content_url = dirname( dirname( CHILD_URL ) );
			list( $less_file ) = explode( '?', str_replace( $content_url, $content_dir, $src . '?' ) );

			// Cache directory & file
			$dir        = self::get_dir();
			$cache_file = "$dir/$handle.cache";

			// Load the cache
			$cache = $less_file;
			if ( file_exists( $cache_file ) )
				$cache = unserialize( file_get_contents( $cache_file ) );

			$less = new lessc;
			$less->setVariables( self::get_vars() );
			$less->setFormatter( 'compressed' );

			try
			{
				$new_cache = $less->cachedCompile( $cache );

				$previous_cached_time = isset( $cache['updated'] ) ? $cache['updated'] : 0;
				$time                 = $new_cache['updated'] > $previous_cached_time ? $new_cache['updated'] : $previous_cached_time;
				$suffix               = date( 'YmdHi', $time );

				// Check if cache is out-dated or design settings is updated
				if ( $new_cache['updated'] > $previous_cached_time || sl_setting( 'updated_design' ) )
				{
					file_put_contents( $cache_file, serialize( $new_cache ) );
					$css = $new_cache['compiled'];

					// Add custom fonts and custom CSS to main stylesheet
					if ( 'sl-main' == $handle )
					{
						$css = self::get_font_css() . $css . self::get_custom_css();

						// Remove "updated_design" for not trigger re-compile later
						$settings = get_option( THEME_SETTINGS );
						unset( $settings['updated_design'] );
						update_option( THEME_SETTINGS, $settings );
					}

					// Save to file: admin to theme admin file, other file to upload folder
					if ( false !== strpos( $less_file, '/admin/' ) )
					{
						// Create folder if it doesn't exist yet
						wp_mkdir_p( THEME_DIR . 'css/admin' );
						$css_file = THEME_DIR . 'css/admin/' . str_replace( array( 'sl-admin-', 'sl-' ), '', $handle ) . '.css';
					}
					else
					{
						$css_file = "$dir/$handle.css";
					}

					// Prefixes css
					// $css = self::autoprefixer( $css );

					file_put_contents( $css_file, $css );
				}

				// Save CSS for admin in theme/css, while for front end in the upload folder
				if ( false !== strpos( $less_file, '/admin/' ) )
					$url = THEME_URL . 'css/admin/' . str_replace( array( 'sl-admin-', 'sl-' ), '', $handle ) . '.css';
				else
					$url = trailingslashit( self::get_dir( false ) ) . "$handle.css";

				$url = sl_setting( 'css_no_var' ) ? add_query_arg( 'ver', $suffix, $url ) : $url;
				return $url;
			}
			catch ( Exception $e )
			{
				do_action( 'add_debug_info', $e->getMessage(), '7listings LESS compiling error' );
				return '';
			}
		}

		/**
		 * Autoprefixer
		 *
		 * @param $css
		 *
		 * @return array
		 */
		public static function autoprefixer( $css )
		{
			// Load Autoprefixer
			if ( ! class_exists( 'Autoprefixer' ) )
				require_once PEACE_DIR . 'autoprefixer/Autoprefixer.php';

			// Many rules
			$autoprefixer = new Autoprefixer( array( 'last 2 version', 'ie 8' ) );

			$css = $autoprefixer->compile( $css );

			return $css;
		}

		/**
		 * Compile editor stylesheets registered via add_editor_style()
		 *
		 * @param  string $mce_css Comma separated list of CSS file URLs
		 *
		 * @return string $mce_css New comma separated list of CSS file URLs
		 */
		public static function parse_editor( $mce_css )
		{
			$mce_css = explode( ',', $mce_css );

			if ( empty( $mce_css ) )
				return '';

			$css = array();
			foreach ( $mce_css as $src )
			{
				$css[] = self::parse( $src, self::url_to_handle( $src ) );
			}

			return implode( ',', $css );
		}

		/**
		 * Get a nice handle to use for the compiled CSS file name
		 *
		 * @param  string $url File URL to generate a handle from
		 *
		 * @return string $url Sanitized string to use for handle
		 */
		public static function url_to_handle( $url )
		{
			$url   = parse_url( $url );
			$url   = trim( str_replace( '.less', '', basename( $url['path'] ) ), '/' );
			$parts = explode( '/', $url );

			return 'sl-' . end( $parts );
		}

		/**
		 * Get (and create if unavailable) the compiled CSS cache directory
		 *
		 * @param  bool $path If true this method returns the cache's system path. Set to false to return the cache URL
		 *
		 * @return string $dir  The system path or URL of the cache folder
		 */
		public static function get_dir( $path = true )
		{
			$upload_dir = wp_upload_dir();

			if ( $path )
			{
				$dir = path_join( $upload_dir['basedir'], 'peace-less' );

				// Create folder if it doesn't exist yet
				wp_mkdir_p( $dir );
			}
			else
			{
				$dir = path_join( $upload_dir['baseurl'], 'peace-less' );
			}

			return rtrim( $dir, '/' );
		}

		/**
		 * Get LESS variables
		 *
		 * @return array
		 */
		public static function get_vars()
		{
			$defaults = array(
				// Header
				'design_weather_color'                        => '#fff',
				'design_header_background'                    => '#fff',

				// Site title & description
				'design_site_title_color'                     => 'rgba(0,0,0,.8)',
				'design_site_title_size'                      => 32,
				'design_site_title_font'                      => 'DIN Cond Bold',

				'design_site_description_color'               => 'rgba(0,0,0,.5)',
				'design_site_description_size'                => 24,
				'design_site_description_font'                => 'DIN Cond Normal',

				'design_header_phone_color'                   => '#99c5ff',

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
				'design_featured_heading_color'               => '#fff',
				'design_featured_custom_text'                 => '#eaeaea',

				// Breadcrumbs
				'design_breadcrumbs_background'               => '#fff',
				'design_breadcrumbs_separator'                => '#ccc',
				'design_breadcrumbs_current'                  => '#a8a8a8',

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
				'design_label_text_color'                     => '#54bae5',
				'design_label_background_color'               => '#FFF',

				// Link
				'design_link_color'                           => '#08c',
				'design_link_color_hover'                     => '#f60',

				// Thumbnail
				'design_thumbnail_border_width'               => 7,
				'design_thumbnail_border_color'               => '#f7f7f7',
				'design_thumbnail_background_color'           => '#fff',

				'design_base_border_radius'                   => 4,

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

				'design_text_size'                            => 14,
				'design_text_color'                           => '#000',
				'design_text_font'                            => 'Arial, Helvetica',

				'design_list_icon'                            => '\f111',
				'design_list_icon_color'                      => '#6d6d6d',

				// Sidebar
				'design_sidebar_width'                        => 33,
				'design_sidebar_background'                   => '#fff',
				'design_sidebar_heading_color'                => '#333',
				'design_sidebar_text_color'                   => '#000',
				'design_sidebar_link_color'                   => '#08c',
				'design_sidebar_link_hover_color'             => '#f60',

				// Sidebar button
				'design_sidebar_button_primary_text'          => '#fff',
				'design_sidebar_button_primary_background'    => '#00adee',
				'design_sidebar_button_text'                  => '#999',
				'design_sidebar_button_background'            => '#ddd',

				// Sidebar price
				'design_sidebar_price_text'                   => '#ba9121',
				'design_sidebar_price_background'             => '#f4d838',

				// Footer
				'design_footer_top_background'                => 'rgba(255,255,255,.4)',
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

				'design_footer_middle_background'             => 'rgba(255,255,255,.4)',
				'design_footer_middle_title'                  => '#686868',
				'design_footer_middle_text'                   => '#000',
				'design_footer_middle_link'                   => '#08c',
				'design_footer_middle_link_hover'             => '#f60',

				'design_footer_bottom_background'             => 'rgba(255,255,255,.9)',
				'design_footer_bottom_text'                   => '#000',
				'design_footer_bottom_link'                   => '#08c',
				'design_footer_bottom_link_hover'             => '#f60',

				// Background
				'design_body_background'                      => '#eaeaea',

				// Mobile
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

				// Social buttons
				'design_header_social_color'                  => '#f00', // This value will be overwrite if it's used
				'design_social_icon_color'                    => '#f00', // This value will be overwrite if it's used
				'design_contact_social_color'                 => '#f00', // This value will be overwrite if it's used
			);

			$vars = $defaults;

			// Get values from settings
			foreach ( $defaults as $k => $v )
			{
				$value = sl_setting( $k );
				// Ignore
				if (
					false === $value || // Unset values
					'#' == $value ||    // Unset colors
					( '#' == substr( $v, 0, 1 ) && ! $value ) // Empty colors
				)
				{
					continue;
				}

				$vars[$k] = $value;
			}

			// Background
			foreach ( array( 'body', 'main', 'header', 'featured', 'sidebar', 'footer_top', 'footer_middle', 'footer_bottom' ) as $base )
			{
				$vars = array_merge( $vars, self::background_vars( $base ) );
			}

			// Sanitize some values
			$fields = array(
				'design_list_icon',
				'design_review_rating_icon',
				'design_review_rating_background_icon',
			);
			foreach ( $fields as $field )
			{
				$vars[$field] = self::sanitize( $vars[$field] );
			}

			// Add 'px' to value
			$sizes = array(
				'design_site_title_size',
				'design_site_description_size',

				'design_featured_min_height',
				'design_navbar_height_desktop',
				'design_navbar_font_size',

				'design_button_primary_font_size',
				'design_button_font_size',

				'design_h1_size',
				'design_h2_size',
				'design_h3_size',
				'design_h4_size',
				'design_h5_size',
				'design_h6_size',
				'design_text_size',

				'design_thumbnail_border_width',
				'design_base_border_radius',

				'design_mobile_menu_height',
				'design_mobile_site_title_size',
				'design_mobile_site_description_size',
				'design_mobile_link_size',
				'design_mobile_link_size_sub',
			);
			foreach ( $sizes as $size )
			{
				if ( strpos( $vars[$size], 'px' ) === false )
					$vars[$size] .= 'px';
			}

			// Add '%' to value
			$sizes = array(
				'design_sidebar_width',
			);
			foreach ( $sizes as $size )
			{
				if ( strpos( $vars[$size], '%' ) === false )
					$vars[$size] .= '%';
			}

			// Change variable names from some_thing to someThing
			$return = array();
			foreach ( $vars as $k => $v )
			{
				$k          = substr( $k, 7 );
				$k          = self::to_camel_case( $k );
				$return[$k] = $v;
			}

			// Custom variables
			$return['siteTitleDisplay']       = sl_setting( 'display_site_title' ) ? 'inline-block' : 'none';
			$return['siteDescriptionDisplay'] = sl_setting( 'display_site_description' ) ? 'inline-block' : 'none';
			if ( sl_setting( 'logo_display' ) )
			{
				$return['logoWidth']  = sl_setting( 'logo_width' ) . 'px';
				$return['logoHeight'] = sl_setting( 'logo_height' ) . 'px';
			}
			if ( sl_setting( 'design_mobile_logo_display' ) )
			{
				$return['mobileLogoWidth']  = sl_setting( 'mobile_logo_width' ) . 'px';
				$return['mobileLogoHeight'] = sl_setting( 'mobile_logo_height' ) . 'px';
			}
			$return['mobileSiteTitleDisplay']       = sl_setting( 'display_mobile_site_title' ) ? 'inline-block' : 'none';
			$return['mobileSiteDescriptionDisplay'] = sl_setting( 'display_mobile_site_description' ) ? 'inline-block' : 'none';

			// Fonts
			$all_fonts = sl_get_fonts();
			$fonts     = array(
				'design_site_title_font',
				'design_site_description_font',
				'design_navbar_font',
				'design_button_primary_font',
				'design_button_font',
				'design_h1_font',
				'design_h2_font',
				'design_h3_font',
				'design_h4_font',
				'design_h5_font',
				'design_h6_font',
				'design_text_font',
			);
			foreach ( $fonts as $k )
			{
				$k          = substr( $k, 7 );
				$k          = self::to_camel_case( $k );
				$return[$k] = $all_fonts[$return[$k]]['font-family'];
			}

			/**
			 * Theme URLs are relative if in admin
			 * This allows using admin CSS file for enqueue
			 */
			$theme_url = is_admin() ? '../../' : THEME_URL;
			$imagePath = is_admin() ? '../../images/' : THEME_IMG;

			$return = array_merge( $return, array(
				'themeDir'  => self::sanitize( THEME_DIR ),
				'childDir'  => self::sanitize( CHILD_DIR ),
				'themeUrl'  => self::sanitize( $theme_url ),
				'childUrl'  => self::sanitize( CHILD_URL ),
				'imagePath' => self::sanitize( $imagePath ),
			) );
			// Debug
			ksort( $return );
			do_action( 'add_debug_info', $return, '7listings LESS variables' );

			return $return;
		}

		/**
		 * Create less variables for background
		 *
		 * @param string $base
		 *
		 * @return array
		 */
		public static function background_vars( $base )
		{
			/**
			 * $prefix:        The prefix for returned variables
			 * $option_prefix: The prefix for option names. In most cases it's the same as $prefix. But for body we don't use prefix at all
			 */
			$prefix = $option_prefix = 'design_' . $base . '_background_';
			if ( 'body' == $base )
			{
				$option_prefix = 'design_background_';
			}

			// Image size. For all cases it's 'full'. But for featured area we have settings for it.
			$size = sl_setting( $prefix . 'size' ) ? sl_setting( $prefix . 'size' ) : 'full';

			// Return variables
			$vars = array( $prefix . 'image' => 'none' );
			if ( sl_setting( $option_prefix . 'image' ) && sl_setting( $option_prefix . 'image_id' ) )
			{
				list( $src ) = wp_get_attachment_image_src( sl_setting( $option_prefix . 'image_id' ), $size );
				$vars[$prefix . 'image'] = self::sanitize( 'url(' . $src . ')' );
			}

			// Get variables for background parameters
			if ( 'full' == sl_setting( $option_prefix . 'type' ) )
			{
				$vars = array_merge( $vars, array(
					$prefix . 'position'   => 'center center',
					$prefix . 'repeat'     => 'no-repeat',
					$prefix . 'attachment' => sl_setting( $option_prefix . 'attachment' ),
					$prefix . 'size'       => 'cover',
				) );
			}
			else
			{
				$vars = array_merge( $vars, array(
					$prefix . 'position'   => sl_setting( $option_prefix . 'position_x' ) . ' ' . sl_setting( $option_prefix . 'position_y' ),
					$prefix . 'repeat'     => sl_setting( $option_prefix . 'repeat' ),
					$prefix . 'attachment' => sl_setting( $option_prefix . 'attachment' ),
					$prefix . 'size'       => sl_setting( $option_prefix . 'background_size' ) ? sl_setting( $option_prefix . 'background_size' ) : 'auto auto',
				) );
			}

			return $vars;
		}

		/**
		 * Convert underscores to camelCase
		 *
		 * @param string $s
		 *
		 * @return string
		 */
		public static function to_camel_case( $s )
		{
			$parts    = explode( '_', $s );
			$parts    = array_map( 'ucfirst', $parts );
			$parts[0] = strtolower( $parts[0] );

			return implode( '', $parts );
		}

		/**
		 * Sanitize a string
		 *
		 * @param string $s
		 *
		 * @return string
		 */
		public static function sanitize( $s )
		{
			return '~"' . $s . '"';
		}

		/**
		 * Get CSS for custom fonts
		 *
		 * @return string
		 */
		public static function get_font_css()
		{
			$all_fonts = sl_get_fonts();
			$fonts     = array(
				'design_site_title_font',
				'design_site_description_font',
				'design_navbar_font',
				'design_button_primary_font',
				'design_button_font',
				'design_h1_font',
				'design_h2_font',
				'design_h3_font',
				'design_h4_font',
				'design_h5_font',
				'design_h6_font',
				'design_text_font',
			);

			$font_names = array();
			foreach ( $fonts as $font )
			{
				$value = sl_setting( $font );
				if ( ! $value || empty( $all_fonts[$value] ) )
					continue;
				$font_names[] = $value;
			}

			$font_names = array_unique( $font_names );
			$style      = array();
			foreach ( $font_names as $font )
			{
				$style[] = $all_fonts[$font]['css'];
			}

			return implode( '', $style );
		}

		/**
		 * Get custom CSS
		 *
		 * @return string
		 */
		public static function get_custom_css()
		{
			$custom_css = sl_setting( 'design_custom_css' );
			if ( ! $custom_css )
				return '';

			// Cache directory & file
			$dir        = self::get_dir();
			$cache_file = "$dir/custom.cache";

			// Load the cache
			$cache = "$dir/custom.less";
			if ( file_exists( $cache_file ) )
				$cache = unserialize( file_get_contents( $cache_file ) );

			$less = new lessc;
			$vars = self::get_vars();

			$vars = array_merge( $vars, array(
				'themeDir'  => self::sanitize( THEME_DIR ),
				'childDir'  => self::sanitize( CHILD_DIR ),
				'themeUrl'  => self::sanitize( THEME_URL ),
				'childUrl'  => self::sanitize( CHILD_URL ),
				'imagePath' => self::sanitize( THEME_IMG ),
			) );
			$less->setVariables( $vars );
			$less->setFormatter( 'compressed' );

			try
			{
				$new_cache = $less->cachedCompile( $cache );

				// Check if cache is out-dated
				if ( ! is_array( $cache ) || $new_cache['updated'] > $cache['updated'] )
					file_put_contents( $cache_file, serialize( $new_cache ) );

				delete_option( '7listings_less_custom_log' );

				return $new_cache['compiled'];
			}
			catch ( Exception $e )
			{
				update_option( '7listings_less_custom_log', $e->getMessage() );

				return '';
			}
		}
	}

	Sl_Less::load();
}
