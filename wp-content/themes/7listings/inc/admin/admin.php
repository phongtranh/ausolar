<?php
// Some pages require admin styles and scripts, so we use low priority to make sure they're loaded before them
add_action( 'admin_enqueue_scripts', 'sl_admin_less_styles', 5 );

/**
 * Enqueue common script and style for theme admin pages
 * They are used in all theme admin pages and provide common behaviours / elements like checkboxes, toggle, etc.
 *
 * @return void
 */
function sl_admin_less_styles()
{
	/**
	 * Can't use get_current_screen() to get parent_base because it's set in <body>, not when page load
	 * e.g. after enqueue script action is fired
	 */
	global $parent_file;
	list( $parent_base ) = explode( '?', $parent_file . '?' );
	$parent_base = str_replace( '.php', '', $parent_base );

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
		wp_enqueue_style( 'sl-admin-menu', THEME_LESS . 'admin/menu.less' );
	else
		wp_enqueue_style( 'sl-admin-menu', THEME_CSS . 'admin/menu.css' );

	if (
		/**
		 * - Post (any post type) add new / edit page
		 * - Post (any post type) management page
		 * - Post (any post type) single / archive settings page, under Pages menu
		 *   Including homepage settings
		 */
		'edit' == $parent_base
		|| '7listings' == $parent_base // All settings page under 7listings menu
		|| 'themes' == $parent_base // All settings page under Appearance: design, sidebar, widgets
	)
	{
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
			wp_enqueue_style( 'sl-admin', THEME_LESS . 'admin/admin.less', array( 'wp-color-picker' ) );
		else
			wp_enqueue_style( 'sl-admin', THEME_CSS . 'admin/admin.css', array( 'wp-color-picker' ) );
		wp_enqueue_script( 'sl-admin', THEME_JS . 'admin/admin.js', array( 'wp-color-picker', 'bootstrap-transition' ) );
	}
}

add_action( 'load-post.php', 'sl_no_booking_except_admins' );
add_action( 'load-post-new.php', 'sl_no_booking_except_admins' );

/**
 * Don't allow users create/edit bookings, except admin
 *
 * @return void
 */
function sl_no_booking_except_admins()
{
	if ( ! sl_is_screen( 'booking' ) )
		return;

	if ( ! current_user_can( 'administrator' ) )
		wp_die( __( 'You have insufficient privileges to add/edit bookings.', '7listings' ) );
}

add_action( 'load-edit.php', 'sl_export_bookings' );

/**
 * Export bookings to CSV
 *
 * @return void
 */
function sl_export_bookings()
{
	if ( ! sl_is_screen( 'booking', 'edit' ) || empty( $_GET['export'] ) || 'csv' != $_GET['export'] )
		return;

	$content = '';
	$row     = array(
		'ID',
		'Tour',
		'Resource',
		// 'Tour Types',
		'Total$',
		'Card Type',
		'Booked On',
		'Booking Date',
		'Booking Time',
		'#Guests',
		'Upsells',
		'Buyer - First',
		'Buyer - Last',
		'Buyer - Email',
		'Buyer - Phone',
		'Booking Message',
	);
	for ( $i = 2; $i <= 20; $i ++ )
	{
		$row[] = "Guest #{$i}";
		$row[] = 'Email';
		$row[] = 'Phone';
	}
	$content .= implode( ',', $row ) . "\n";

	$bookings = &get_posts( array(
		'post_type'      => 'booking',
		'posts_per_page' => - 1,
	) );

	if ( empty( $bookings ) )
		return;

	foreach ( $bookings as $booking )
	{
		$row = array();

		$post_id = $booking->ID;

		$resource_post = get_post_meta( $post_id, 'post_id', true );
		$type          = get_post_meta( $post_id, 'type', true );

		$row[] = get_post_meta( $post_id, 'booking_id', true );
		$row[] = get_the_title( $resource_post );
		$row[] = get_post_meta( $post_id, 'resource', true );
		// $row[] = get_post_meta( $post_id, 'resource_type', true );
		$row[] = get_post_meta( $post_id, 'amount', true );
		$row[] = get_post_meta( $post_id, 'card_type', true );

		// Book On
		$date_format = str_replace( ',', '', get_option( 'date_format' ) );
		$time_format = str_replace( ',', '', get_option( 'time_format' ) );

		$row[] = get_the_time( $date_format, $post_id ) . ' ' . get_the_time( $time_format, $post_id );

		// Booking Date, Booking Time
		if ( 'tour' == $type )
		{
			$row[] = get_post_meta( $post_id, 'day', true );
			$row[] = get_post_meta( $post_id, 'depart_time', true );
		}
		else
		{
			$in    = get_post_meta( $post_id, 'checkin', true );
			$in    = array_shift( explode( ' ', $in ) );
			$out   = get_post_meta( $post_id, 'checkout', true );
			$out   = array_shift( explode( ' ', $out ) );
			$row[] = 'in: ' . $in . ' out: ' . $out;
			$row[] = '';
		}

		// #Guests
		if ( 'tour' == $type )
		{
			$num_guests = array();
			$types      = array(
				'adults'   => 'Adults',
				'children' => 'Children',
				'seniors'  => 'Seniors',
				'families' => 'Families',
				'infants'  => 'Infants',
			);
			foreach ( $types as $type => $label )
			{
				$num = get_post_meta( $post_id, $type, true );
				if ( empty( $num ) || - 1 == $num )
					continue;

				$num_guests[] = "{$num} {$label}";
			}
			$row[] = implode( ' ', $num_guests );
		}
		else
		{
			$guests = get_post_meta( $post_id, 'guests', true );
			$row[]  = count( $guests );
		}

		// Upsells
		$upsells      = get_post_meta( $post_id, 'upsells', true );
		$upsell_array = array();
		if ( ! empty( $upsells ) )
		{
			foreach ( $upsells as $upsell )
			{
				$upsell_array[] = "{$upsell['num']} {$upsell['name']}";
			}
		}
		$row[] = implode( ' ', $upsell_array );

		// First Buyer Info
		$guests      = get_post_meta( $post_id, 'guests', true );
		$first_guest = array_shift( $guests );
		$row[]       = $first_guest['first'];
		$row[]       = $first_guest['last'];
		$row[]       = $first_guest['email'];
		$row[]       = $first_guest['phone'];
		foreach ( $guests as $guest )
		{
			$row[] = $guest['first'] . ' ' . $row[] = $guest['last'];
			$row[] = $guest['email'];
			$row[] = $guest['phone'];
		}

		$content .= implode( ',', $row ) . "\n";
	}

	sl_download( $content, 'booking.csv' );
}

/**
 * Output string as a file download
 *
 * @param string $str
 * @param string $name
 *
 * @return void
 */
function sl_download( $str, $name )
{
	$size = strlen( $str );
	$name = rawurldecode( $name );

	// Required for IE, otherwise Content-Disposition may be ignored
	if ( ini_get( 'zlib.output_compression' ) )
		ini_set( 'zlib.output_compression', 'Off' );

	header( 'Content-Type: application/force-download' );
	header( "Content-Disposition: attachment; filename=\"{$name}\"" );
	header( 'Content-Transfer-Encoding: binary' );
	header( "Content-Length: {$size}" );

	// Make the download non-cacheable
	header( 'Cache-control: private' );
	header( 'Pragma: private' );
	header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );

	die( $str );
}

/**
 * Check if current screen valid
 *
 * @param string $type Post type
 * @param string $base Screen base type
 *
 * @see http://www.deluxeblogtips.com/2012/01/get-admin-screen-information.html
 *
 * @return bool
 */
function sl_is_screen( $type = 'post', $base = 'post' )
{
	if ( ! function_exists( 'get_current_screen' ) )
		return false;

	$screen = get_current_screen();

	return (
		isset( $screen->base ) && $base === $screen->base &&
		isset( $screen->post_type ) && $type === $screen->post_type
	);
}

add_action( 'admin_init', 'sl_editor_style' );

/**
 * Custom styling for tinymce editor
 *
 * @return void
 */
function sl_editor_style()
{
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
		add_editor_style( 'css/less/admin/shortcodes/tinymce-preview.less' );
	else
		add_editor_style( 'css/admin/tinymce-preview.css' );
}

/**
 * Enqueue script for photo delete, change description and reorder
 *
 * @return void
 */
function sl_enqueue_photo_script()
{
	wp_enqueue_script( 'sl-photos' );
	wp_localize_script( 'sl-photos', 'SlPhoto', array(
		'nonce_set_featured'       => wp_create_nonce( 'set_post_thumbnail-' . get_the_ID() ),
		'nonce_update_description' => wp_create_nonce( 'update-description' ),
		'nonce_delete'             => wp_create_nonce( 'delete' ),
		'nonce_booking_delete'     => wp_create_nonce( 'booking-delete' ),
		'nonce_reorder'            => wp_create_nonce( 'reorder' ),
	) );
}

/**
 * Helper function to quick add help tab
 *
 * @param string $basename Base name for help and warning files, in 'inc/admin/help' folder
 *
 * @since 5.2.1
 * @return void
 */
function sl_add_help_tabs( $basename )
{
	$screen = get_current_screen();
	$tabs   = array(
		$basename           => __( 'Help', '7listings' ),
		"$basename-warning" => __( 'Warning', '7listings' ),
	);
	foreach ( $tabs as $filename => $title )
	{
		$file = THEME_ADMIN . 'help/' . $filename . '.php';
		if ( ! file_exists( $file ) )
			continue;
		ob_start();
		include $file;
		$content = ob_get_clean();
		$screen->add_help_tab( array(
			'id'      => uniqid(),
			'title'   => $title,
			'content' => $content,
		) );
	}
}

add_filter( 'fitws_widget_list', 'sl_widget_shortcode_list' );

/**
 * Change FitWP Widget Shortcode plugin to show only theme widgets
 * Theme widgets PHP Class always start with 'Sl_Widget'
 *
 * @param  array $widgets List of available widgets
 * @return array
 */
function sl_widget_shortcode_list( $widgets )
{
	foreach ( $widgets as $class_name => $widget_obj )
	{
		if ( 0 !== strpos( $class_name, 'Sl_Widget' ) )
		{
			unset( $widgets[$class_name] );
		}
	}
	return $widgets;
}

add_action( 'tgmpa_register', 'sl_register_required_plugins' );

/**
 * Add notification to let users install required and recommended plugins
 *
 * @use TGM Activation Class
 *
 * @return void
 */
function sl_register_required_plugins()
{
	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// 7shortcodes
		array(
			'name'               => '7shortcodes', // The plugin name.
			'slug'               => '7shortcodes', // The plugin slug (typically the folder name).
			'source'             => '7shortcodes.zip', // The plugin source.
			'required'           => false, // If false, the plugin is only 'recommended' instead of required.
			'version'            => '0.1.3', // E.g. 1.0.0. If set, the active plugin must be this version or higher.
			'force_activation'   => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
			'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			'external_url'       => '', // If set, overrides default API URL and points to an external URL.
		),

		// Admin Menu Editor
		array(
			'name'     => 'Admin Menu Editor',
			'slug'     => 'admin-menu-editor',
			'required' => false,
		),

		// iTheme Security
		array(
			'name'     => 'iThemes Security',
			'slug'     => 'better-wp-security',
			'required' => false,
		),

		// WordPress SEO by Yoast
		array(
			'name'     => 'WordPress SEO by Yoast',
			'slug'     => 'wordpress-seo',
			'required' => false,
		),

		// WP Super Cache
		array(
			'name'     => 'WP Super Cache',
			'slug'     => 'wp-super-cache',
			'required' => false,
		),

		// BackWPup Free
		array(
			'name'     => 'BackWPup Free',
			'slug'     => 'backwpup',
			'required' => false,
		),

		// Contact Form 7
		array(
			'name'     => 'Contact Form 7',
			'slug'     => 'contact-form-7',
			'required' => false,
		),

		// Akismet
		array(
			'name'     => 'Akismet',
			'slug'     => 'akismet',
			'required' => false,
		),

		// Kraken Image Optimizer
		array(
			'name'     => 'Kraken Image Optimizer',
			'slug'     => 'kraken-image-optimizer',
			'required' => false,
		),

		// 7 Post Classes
		array(
			'name'     => '7 Post Classes',
			'slug'     => '7-post-classes',
			'required' => false,
		),
	);

	// If module Product is activated, then add WooCommerce to the list
	// If module Product is enabled, make it required
	if ( Sl_License::is_module_activated( 'product' ) )
	{
		$woocommerce = array(
			'name'     => 'WooCommerce',
			'slug'     => 'woocommerce',
			'required' => false,
		);
		if ( Sl_License::is_module_enabled( 'product' ) )
		{
			$woocommerce['required'] = true;
		}
		$plugins[] = $woocommerce;
	}

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'default_path' => THEME_DIR . 'plugins/',// Default absolute path to pre-packaged plugins.
		'menu'         => 'tgmpa-install-plugins', // Menu slug.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', '7listings' ),
			'menu_title'                      => __( 'Install Plugins', '7listings' ),
			'installing'                      => __( 'Installing Plugin: %s', '7listings' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', '7listings' ),
			'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
			'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
			'return'                          => __( 'Return to Required Plugins Installer', '7listings' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', '7listings' ),
			'complete'                        => __( 'All plugins installed and activated successfully. %s', '7listings' ), // %s = dashboard link.
			'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		)
	);

	tgmpa( $plugins, $config );
}
