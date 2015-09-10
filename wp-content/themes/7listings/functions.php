<?php
// Load the framework
require get_template_directory() . '/lib/peace.php';
new Peace;

// Class to work with singular, plural forms in English
require THEME_DIR . 'lib/Inflect.php';


// Class to show require or recommend plugins for this WordPress themes (and plugins)
require_once THEME_DIR . 'lib/class-tgm-plugin-activation.php';

define( 'THEME_IMG', trailingslashit( THEME_URL . 'images' ) );
define( 'THEME_CSS', trailingslashit( THEME_URL . 'css' ) );
define( 'THEME_LESS', trailingslashit( THEME_CSS . 'less' ) );
define( 'THEME_JS', trailingslashit( THEME_URL . 'js' ) );

define( 'THEME_TPL', trailingslashit( THEME_DIR . 'templates' ) );
define( 'THEME_INC', trailingslashit( THEME_DIR . 'inc' ) );
define( 'THEME_MODULES', trailingslashit( THEME_INC . 'modules' ) );
define( 'THEME_ADMIN', trailingslashit( THEME_INC . 'admin' ) );
define( 'THEME_TABS', trailingslashit( THEME_ADMIN . 'tabs' ) );

define( 'THEME_SETTINGS', '7listings' );

peace_include_dir( THEME_INC . 'helpers' );
peace_include_dir( THEME_INC . 'common' );

// Avatar
require THEME_INC . 'avatar/avatar.php';

// Booking
require THEME_INC . 'booking/booking.php';

// Load core module
require THEME_MODULES . 'core/core.php';
require THEME_MODULES . 'core/edit.php'; // Allow frontend submission or edit
if ( is_admin() )
{
	require THEME_MODULES . 'core/settings.php';
	require THEME_MODULES . 'core/management.php';
	require THEME_MODULES . 'core/homepage.php';

	require THEME_ADMIN . 'updater.php';

	// Tooltip can run both in ajax (widgets update) and non-ajax (normal) modes
	require THEME_ADMIN . 'tooltip.php';
	Sl_Tooltip::load();

	if ( defined( 'DOING_AJAX' ) )
	{
		require THEME_MODULES . 'core/ajax.php';

		peace_include_dir( THEME_ADMIN . 'ajax' );
	}
	else
	{
		// Update database if required
		require THEME_ADMIN . 'db-update.php';
		new Sl_Database_Update;

		require THEME_ADMIN . 'meta-boxes.php';

		require THEME_ADMIN . 'media-screen.php';
		Sl_Media_Screen::load();

		require THEME_ADMIN . 'media-settings.php';
		new Sl_Media_Settings;

		require THEME_ADMIN . 'admin.php';
	}

	/**
	 * Settings page
	 * Use normal require for manual sorting menus
	 */
	require THEME_ADMIN . 'settings/settings.php'; // Theme settings main file
	require THEME_ADMIN . 'settings/listings.php'; // Settings
	require THEME_ADMIN . 'settings/emails.php'; // Email
	require THEME_ADMIN . 'settings/advanced.php'; // Advanced
	require THEME_ADMIN . 'settings/license.php'; // License

	// Settings page, but not under 7listings top-level menu
	require THEME_ADMIN . 'settings/contact.php'; // Contact
	require THEME_ADMIN . 'settings/homepage.php'; // Homepage
	require THEME_ADMIN . 'settings/design.php'; // Design
	require THEME_ADMIN . 'settings/sidebars.php'; // Sidebars

	require THEME_ADMIN . 'taxonomy-image.php';
	require THEME_ADMIN . 'seo.php';
}
else
{
	//Lazy load
	require THEME_INC . 'lazyload/lazyload.php';

	require THEME_MODULES . 'core/featured-title.php';
	require THEME_MODULES . 'core/frontend.php';

	peace_include_dir( THEME_INC . 'frontend' );
}

// Load other modules
$modules = array( 'post', 'product', 'company', 'accommodation', 'tour', 'rental', 'attraction', 'cart', 'slideshow' );
foreach ( $modules as $module )
{
	require THEME_MODULES . "$module/$module.php";
}

require THEME_INC . 'widgets/widgets.php';

// Social buttons
require THEME_INC . 'social-buttons/social-buttons.php';

if ( ! isset( $content_width ) )
{
	$content_width = 755;
}

add_action( 'after_setup_theme', 'sl_setup' );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function sl_setup()
{
	load_theme_textdomain( '7listings', THEME_DIR . 'lang' );

	// Theme support
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'post-formats', array( 'aside', 'status', 'chat', 'image', 'gallery', 'video', 'audio', 'quote', 'link' ) );

	// Framework features
	add_theme_support( 'peace-entry-views' );

	// Image sizes
	add_image_size( 'sl_thumb_tiny', 80, 80, true );
	add_image_size( 'sl_thumb_small', 150, 150, true );        // WP & WC default - .span12 : col6 160x160
	add_image_size( 'sl_thumb_medium', 300, 300, true );    // WP & WC default - .span12 : col3 360x360
	add_image_size( 'sl_thumb_large', 600, 600, true );
	add_image_size( 'sl_thumb_huge', 1024, 1024, true );    // WP default

	add_image_size( 'sl_pano_small', 153, 85, true );        // .span12 : col6 - 160x90
	add_image_size( 'sl_pano_medium', 306, 170, true );        // .span12 : col3 - 360x202
	add_image_size( 'sl_pano_large', 612, 340, true );
	add_image_size( 'sl_pano_huge', 1260, 700, true );

	add_image_size( 'sl_feat_medium', 1280, 320, true );
	add_image_size( 'sl_feat_large', 1920, 480, true );

	// Menu
	register_nav_menu( 'primary', __( 'Primary', '7listings' ) );

	// Sidebars
	register_sidebar( array(
		'id'            => 'page',
		'name'          => __( 'Right Sidebar', '7listings' ),
		'description'   => __( 'An optional widget area for pages', '7listings' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'id'            => 'footer-1',
		'name'          => __( 'Footer Area One', '7listings' ),
		'description'   => __( 'An optional widget area for your site footer', '7listings' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'id'            => 'footer-2',
		'name'          => __( 'Footer Area Two', '7listings' ),
		'description'   => __( 'An optional widget area for your site footer', '7listings' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'id'            => 'footer-3',
		'name'          => __( 'Footer Area Three', '7listings' ),
		'description'   => __( 'An optional widget area for your site footer', '7listings' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}