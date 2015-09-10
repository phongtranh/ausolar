<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Company extends Sl_Core
{
	/**
	 * Load files add hooks for this custom post type
	 * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
	 *
	 * @return void
	 */
	function hooks()
	{
		add_action( 'init', array( $this, 'add_role' ) );

		add_filter( 'rewrite_rules_array', array( $this, 'add_rewrite_rules' ) );

		// Add sidebars
		add_filter( 'init', array( $this, 'sidebars' ) );

		add_filter( 'sl_meta_key', array( $this, 'meta_key' ), 10, 2 );
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

			$type . '_menu_title'                     => __( 'Browse Companies', '7listings' ),
			$type . '_base_url'                       => $type,
			$type . '_label'                          => __( 'Company', '7listings' ),
			$type . '_menu_dropdown'                  => 'locations',


			// Page Settings

			// Archive Headings
			$type . '_archive_main_title'             => __( 'All Companies', '7listings' ),
			$type . '_location_title'                 => __( 'Companies in %TERM%', '7listings' ),
			$type . '_brand_title'                    => __( 'Companies with %TERM%', '7listings' ),
			$type . '_product_title'                  => __( 'Companies have %TERM%', '7listings' ),
			$type . '_service_title'                  => __( 'Companies have %TERM%', '7listings' ),

			// Archive Layout
			$type . '_archive_num'                    => get_option( 'posts_per_page' ),
			$type . '_archive_priority'               => 1,
			$type . '_archive_orderby'                => 'date',
			$type . '_archive_sidebar_layout'         => 'right',
			$type . '_archive_sidebar'                => 'company-archive',

			// Single Layout
			$type . '_single_title'                   => '%LISTING_NAME%',

			$type . '_single_featured_title_map_zoom' => 12,

			$type . '_single_sidebar_layout'          => 'right',
			$type . '_single_sidebar'                 => 'company-single',

			$type . '_similar_title'                  => __( 'You may also like these companies', '7listings' ),
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
	function load_files()
	{
		parent::load_files();

		$dir = THEME_MODULES . $this->post_type;

		require "$dir/date-views.php";
		require "$dir/payment.php";

		if ( is_admin() )
			require "$dir/admin.php";
	}

	/**
	 * Register custom post type
	 * Use Peace framework to do quickly
	 *
	 * @return void
	 */
	function register_post_type()
	{
		$singular     = ucwords( sl_setting( $this->post_type . '_label' ) );
		$plural       = ucwords( Inflect::pluralize( $singular ) );
		$plural_lower = strtolower( $plural );

		$labels = array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'name_admin_bar'     => $singular,
			'add_new'            => _x( 'Add New', 'tour', '7listings' ),
			'add_new_item'       => sprintf( __( 'Add New %s', '7listings' ), $singular ),
			'new_item'           => sprintf( __( 'New %s', '7listings' ), $singular ),
			'edit_item'          => sprintf( __( 'Edit %s', '7listings' ), $singular ),
			'view_item'          => sprintf( __( 'View %s', '7listings' ), $singular ),
			'all_items'          => sprintf( __( 'All %s', '7listings' ), $plural ),
			'search_items'       => sprintf( __( 'Search %s', '7listings' ), $plural ),
			'parent_item_colon'  => null,
			'not_found'          => sprintf( __( 'No %s found', '7listings' ), $plural_lower ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', '7listings' ), $plural_lower ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'           => sl_setting( $this->post_type . '_base_url' ),
				'with_front'     => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
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
		// Location
		// register_taxonomy_for_object_type( 'location', $this->post_type );

		// Brand
		if ( taxonomy_exists( 'brand' ) )
		{
			register_taxonomy_for_object_type( 'brand', $this->post_type );
		}
		else
		{
			$labels = array(
				'name'                       => _x( 'Brands', 'Taxonomy General Name', '7listings' ),
				'singular_name'              => _x( 'Brand', 'Taxonomy Singular Name', '7listings' ),
				'menu_name'                  => __( 'Brands', '7listings' ),
				'all_items'                  => __( 'All Brands', '7listings' ),
				'parent_item'                => __( 'Parent Brand', '7listings' ),
				'parent_item_colon'          => __( 'Parent Brand:', '7listings' ),
				'new_item_name'              => __( 'New Brand Name', '7listings' ),
				'add_new_item'               => __( 'Add New Brand', '7listings' ),
				'edit_item'                  => __( 'Edit Brand', '7listings' ),
				'update_item'                => __( 'Update Brand', '7listings' ),
				'separate_items_with_commas' => __( 'Separate brands with commas', '7listings' ),
				'search_items'               => __( 'Search brands', '7listings' ),
				'add_or_remove_items'        => __( 'Add or remove brands', '7listings' ),
				'choose_from_most_used'      => __( 'Choose from the most used brands', '7listings' ),
			);

			$args = array(
				'labels'       => $labels,
				'hierarchical' => true,
				'public'       => true,
			);

			register_taxonomy( 'brand', $this->post_type, $args );
		}

		$labels = array(
			'name'                       => _x( 'Products', 'Taxonomy General Name', '7listings' ),
			'singular_name'              => _x( 'Product', 'Taxonomy Singular Name', '7listings' ),
			'menu_name'                  => __( 'Products', '7listings' ),
			'all_items'                  => __( 'All Products', '7listings' ),
			'parent_item'                => __( 'Parent Product', '7listings' ),
			'parent_item_colon'          => __( 'Parent Product:', '7listings' ),
			'new_item_name'              => __( 'New Product Name', '7listings' ),
			'add_new_item'               => __( 'Add New Product', '7listings' ),
			'edit_item'                  => __( 'Edit Product', '7listings' ),
			'update_item'                => __( 'Update Product', '7listings' ),
			'separate_items_with_commas' => __( 'Separate products with commas', '7listings' ),
			'search_items'               => __( 'Search products', '7listings' ),
			'add_or_remove_items'        => __( 'Add or remove products', '7listings' ),
			'choose_from_most_used'      => __( 'Choose from the most used products', '7listings' ),
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug' => $this->post_type . '-product',
			)
		);

		register_taxonomy( $this->post_type . '_product', $this->post_type, $args );

		// Services
		$labels = array(
			'name'                       => _x( 'Services', 'Taxonomy General Name', '7listings' ),
			'singular_name'              => _x( 'Service', 'Taxonomy Singular Name', '7listings' ),
			'menu_name'                  => __( 'Services', '7listings' ),
			'all_items'                  => __( 'All Services', '7listings' ),
			'parent_item'                => __( 'Parent Service', '7listings' ),
			'parent_item_colon'          => __( 'Parent Service:', '7listings' ),
			'new_item_name'              => __( 'New Service Name', '7listings' ),
			'add_new_item'               => __( 'Add New Service', '7listings' ),
			'edit_item'                  => __( 'Edit Service', '7listings' ),
			'update_item'                => __( 'Update Service', '7listings' ),
			'separate_items_with_commas' => __( 'Separate services with commas', '7listings' ),
			'search_items'               => __( 'Search services', '7listings' ),
			'add_or_remove_items'        => __( 'Add or remove services', '7listings' ),
			'choose_from_most_used'      => __( 'Choose from the most used services', '7listings' ),
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug' => $this->post_type . '-service',
			)
		);

		register_taxonomy( $this->post_type . '_service', $this->post_type, $args );

		// Type
		$labels = array(
			'name'                       => _x( 'Types', 'Taxonomy General Name', '7listings' ),
			'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', '7listings' ),
			'menu_name'                  => __( 'Types', '7listings' ),
			'all_items'                  => __( 'All Types', '7listings' ),
			'parent_item'                => __( 'Parent Type', '7listings' ),
			'parent_item_colon'          => __( 'Parent Type:', '7listings' ),
			'new_item_name'              => __( 'New Type Name', '7listings' ),
			'add_new_item'               => __( 'Add New Type', '7listings' ),
			'edit_item'                  => __( 'Edit Type', '7listings' ),
			'update_item'                => __( 'Update Type', '7listings' ),
			'separate_items_with_commas' => __( 'Separate types with commas', '7listings' ),
			'search_items'               => __( 'Search types', '7listings' ),
			'add_or_remove_items'        => __( 'Add or remove types', '7listings' ),
			'choose_from_most_used'      => __( 'Choose from the most used types', '7listings' ),
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array(
				'slug' => $this->post_type . '-types',
			)
		);
		
		register_taxonomy( $this->post_type . '_types', $this->post_type, $args );
	}

	/**
	 * Add company owner role
	 *
	 * @return void
	 * @since  1.0
	 */
	function add_role()
	{
		add_role(
			'company_owner',
			__( 'Company Owner', '7listings' ),
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
		$new["$base/area/([^/]+)/?$"]                  = 'index.php?post_type=' . $this->post_type . '&place=$matches[1]';
		$new["$base/area/([^/]+)/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&place=$matches[1]&paged=$matches[2]';

		// City
		$new["$base/city/([^/]+)/?$"]                  = 'index.php?post_type=' . $this->post_type . '&place=$matches[1]';
		$new["$base/city/([^/]+)/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&place=$matches[1]&paged=$matches[2]';

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
			'id'            => 'company-archive',
			'name'          => __( 'Company Archive', '7listings' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
		register_sidebar( array(
			'id'            => 'company-single',
			'name'          => __( 'Company Single', '7listings' ),
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
	function meta_key( $key, $post_type )
	{
		if ( $post_type != $this->post_type )
			return $key;
		switch ( $key )
		{
			case 'logo':
				return 'company_logo';
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
		$prefix = THEME_DIR . 'inc/widgets/' . $this->post_type;
		require $prefix . '-search.php';
		//require $prefix . '-alphabet.php';
		require $prefix . '-news.php';

		register_widget( 'SL_Widget_Company_Search' );
		//register_widget( 'Sl_Widget_Company_Alphabet' );
		register_widget( 'Sl_Widget_Company_News' );
	}
}

new Sl_Company( 'company' );
