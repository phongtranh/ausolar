<?php
/**
 * This class registers new listing post type and load files needed for this module
 */
class Solar_Wholesale extends Sl_Core
{
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

	    if ( isset( $_GET['csv_export'] ) && $_GET['csv_export'] == 1 )
	    {
		    include get_stylesheet_directory() . '/inc/leads-csv.php';
		    exit;
	    }

        $settings_file = THEME_MODULES . $this->post_type . '/settings.php';
        if ( is_admin() && file_exists( $settings_file ) )
            require_once $settings_file;

        $this->load_files();

        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ), 20 );

        // Register widgets
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
        add_filter( 'template_include', array( $this, 'solar_supplier_page' ), 99 );

        $this->hooks();
    }

    public function solar_supplier_page( $template )
    {
        $object = get_queried_object();
        if ( ! empty( $object ) && $object->post_name === 'affiliates' )
            $template = locate_template( 'templates/wholesale/template-supplier.php' );

        return $template;
    }

    /**
     * Load files add hooks for this custom post type
     * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
     *
     * @return void
     */
    public function hooks()
    {
	    // Role has added. Comment out
        //add_action( 'init', array( $this, 'add_role' ) );

        add_action( 'after_setup_theme', array( $this, 'load_ajax' ), 10 );

        add_filter( 'rewrite_rules_array', array( $this, 'add_rewrite_rules' ) );

        // Add sidebars
        add_filter( 'init', array( $this, 'sidebars' ) );

        add_filter( 'sl_meta_key', array( $this, 'meta_key' ), 10, 2 );

        add_action( 'template_redirect', array( $this, 'prevent_view_company' ) );
    }

    public function prevent_view_company()
    {
        if ( current_user_can( 'wholesale_owner' ) )
        {
            $object = get_queried_object();

            $company_pages = array('signup', 'edit', 'dashboard', 'profile', 'posts', 'account');
            foreach ($company_pages as $page) {
                if ( is_page() && $object->ID == sl_setting("company_page_{$page}") ) {
                    wp_redirect('/affiliates');
                    exit;
                }
            }
        }
    }

    /**
     * Set default settings for this custom post type
     *
     * @param array $settings
     *
     * @return array
     */
    public function default_settings( $settings )
    {
        $type = $this->post_type;

        // For homepage
        $settings = array_merge( array(
            'homepage_' . $type . '_logos_number' => 5,
            'homepage_' . $type . '_logos_amount' => 5,
            'homepage_' . $type . '_logos_scroll' => 1,
            'homepage_' . $type . '_logos_speed'  => 2000,
            'homepage_' . $type . '_logos_total'  => 10,
            'homepage_' . $type . '_logos_height' => 80,
        ), $settings );

        // Add all widgets if they're missed
        $widgets = array(
            $type . '_logos',
        );
        foreach ( $widgets as $widget )
        {
            if ( ! in_array( $widget, $settings['homepage_order'] ) )
                $settings['homepage_order'][] = $widget;
        }

        // Check if panels are active
        $fields = array(
            'homepage_' . $type . '_logos_active' => 0,
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

        $settings = array_merge( array(
            $type . '_featured_graphics'              => 1,

            $type . '_menu_title'                     => __( 'Browse Wholesales', '7listings' ),
            $type . '_base_url'                       => $type,
            $type . '_label'                          => __( 'Wholesale', '7listings' ),
            $type . '_menu_dropdown'                  => 'locations',


            // Page Settings

            // Archive Headings
            $type . '_archive_main_title'             => __( 'All Wholesales', '7listings' ),
            $type . '_location_title'                 => __( 'Wholesales in %TERM%', '7listings' ),
            $type . '_brand_title'                    => __( 'Wholesales with %TERM%', '7listings' ),
            $type . '_product_title'                  => __( 'Wholesales have %TERM%', '7listings' ),
            $type . '_service_title'                  => __( 'Wholesales have %TERM%', '7listings' ),

            // Archive Layout
            $type . '_archive_num'                    => get_option( 'posts_per_page' ),
            $type . '_archive_priority'               => 1,
            $type . '_archive_orderby'                => 'date',
            $type . '_archive_sidebar_layout'         => 'right',
            $type . '_archive_sidebar'                => 'wholesale-archive',

            // Single Layout
            $type . '_single_title'                   => '%LISTING_NAME%',

            $type . '_single_featured_title_map_zoom' => 12,

            $type . '_single_sidebar_layout'          => 'right',
            $type . '_single_sidebar'                 => 'wholesale-single',

            $type . '_similar_title'                  => __( 'You may also like these wholesales', '7listings' ),
            $type . '_similar_by'                     => 'location',
            $type . '_similar_columns'                => 3,
            $type . '_similar_display'                => 3,
            $type . '_similar_excerpt_length'         => 25,
        ), $settings );

        return $settings;
    }

    /**
     * Load files
     *
     * @return void
     */
    public function load_files()
    {
        parent::load_files();

        $dir = __DIR__;

        require "$dir/helper.php";
        require "$dir/edit.php";
        require "$dir/ajax.php";

        if ( is_admin() )
        {
            require "$dir/admin.php";
            require "$dir/management.php";
        }
        else
        {
            require "$dir/frontend.php";
        }
    }

    public function load_ajax()
    {

    }

    /**
     * Register custom post type
     * Use Peace framework to do quickly
     *
     * @return void
     */
    function register_post_type()
    {
        $labels = array(
            'name'               => _x( 'Wholesales', 'Post Type General Name', '7listings' ),
            'singular_name'      => _x( 'Wholesale', 'Post Type Singular Name', '7listings' ),
            'menu_name'          => __( 'Wholesale', '7listings' ),
            'parent_item_colon'  => __( 'Parent Wholesale:', '7listings' ),
            'all_items'          => __( 'All Wholesales', '7listings' ),
            'view_item'          => __( 'View Wholesales', '7listings' ),
            'add_new_item'       => __( 'Add New Wholesale', '7listings' ),
            'add_new'            => __( 'Add New', '7listings' ),
            'edit_item'          => __( 'Edit Wholesale', '7listings' ),
            'update_item'        => __( 'Update Wholesale', '7listings' ),
            'search_items'       => __( 'Search wholesales', '7listings' ),
            'not_found'          => __( 'No wholesale found', '7listings' ),
            'not_found_in_trash' => __( 'No wholesale found in Trash', '7listings' ),
        );

        $args = array(
            'label'       => __( 'Wholesale', '7listings' ),
            'labels'      => $labels,
            'supports'    => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array( 'slug' => sl_setting( $this->post_type . '_base_url' ), 'with_front' => false ),
        );

        register_post_type( $this->post_type, $args );
    }

    /**
     * Register custom taxonomies for our custom post type
     * Use Peace framework to do quickly
     *
     * @return void
     */
    function register_taxonomies()
    {
        //
    }

    /**
     * Add wholesale owner role
     *
     * @return void
     * @since  1.0
     */
    function add_role()
    {
        add_role(
            'wholesale_owner',
            __( 'Wholesale Owner', '7listings' ),
            array(
                'read'         => true,
                'edit_posts'   => true,
                'delete_posts' => false,
            )
        );
    }

    /**
     * Add rewrite rules for custom post type
     *
     * @param array $rules
     *
     * @return array
     */
    function add_rewrite_rules( $rules )
    {
        $base = sl_setting( $this->post_type . '_base_url' );
        $new  = array();

        // State
        $new["$base/area/([^/]+)/?$"]                  = 'index.php?post_type=' . $this->post_type . '&location=$matches[1]';
        $new["$base/area/([^/]+)/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&location=$matches[1]&paged=$matches[2]';

        // City
        $new["$base/city/([^/]+)/?$"]                  = 'index.php?post_type=' . $this->post_type . '&location=$matches[1]';
        $new["$base/city/([^/]+)/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&location=$matches[1]&paged=$matches[2]';

        return array_merge( $new, $rules );
    }

    /**
     * Add new sidebars for company
     *
     * @return void
     */
    function sidebars()
    {
        register_sidebar( array(
            'id'            => 'wholesale-archive',
            'name'          => __( 'Wholesale Archive', '7listings' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'id'            => 'wholesale-single',
            'name'          => __( 'Wholesale Single', '7listings' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );
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
        $taxonomies[] = 'brand';

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
        $taxonomies[] = 'brand';

        return $taxonomies;
    }

    /**
     * Change meta key
     *
     * @param  string $key
     * @param  string $post_type
     *
     * @return string
     */
    public function meta_key( $key, $post_type )
    {
        if ( $post_type != $this->post_type )
            return $key;
        switch ( $key )
        {
            case 'logo':
                return 'wholesale_logo';
        }

        return $key;
    }

    /**
     * Register widgets
     *
     * @return void
     */
    function register_widgets()
    {
        //
    }
}

new Solar_Wholesale( 'wholesale' );