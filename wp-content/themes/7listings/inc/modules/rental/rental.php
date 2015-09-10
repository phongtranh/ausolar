<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Rental extends Sl_Core
{
	/**
	 * Load files add hooks for this custom post type
	 * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
	 *
	 * @return void
	 */
	function hooks()
	{
		// Custom rewrite rule
		add_filter( 'generate_rewrite_rules', array( $this, 'add_rewrite_rules' ) );
		add_action( 'init', array( $this, 'add_rewrite_tag' ) );
		add_action( 'init', array( $this, 'add_permastruct' ) );
		add_filter( 'post_type_link', array( $this, 'rewrite_replace_tags' ), 10, 4 );
	}

	/**
	 * Set default settings for this custom post type
	 *
	 * @param array $settings
	 *
	 * @return array void
	 */
	function default_settings( $settings )
	{
		$type = $this->post_type;

		$settings = array_merge( $this->atr_default_settings(), $settings );

		// For homepage
		$settings = array_merge( array(
			'homepage_' . $type . '_features_display' => 77,
		), $settings );

		// Add all widgets if they're missed
		$widgets = array(
			$type . '_featured',
			$type . '_types',
			$type . '_listings',
			$type . '_features',
		);
		foreach ( $widgets as $widget )
		{
			if ( ! in_array( $widget, $settings['homepage_order'] ) )
				$settings['homepage_order'][] = $widget;
		}
		// Check if panels are active
		$fields = array(
			'homepage_' . $type . '_featured_active' => 0,
			'homepage_' . $type . '_listings_active' => 0,
			'homepage_' . $type . '_types_active'    => 0,
			'homepage_' . $type . '_features_active' => 0,
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

		// For listings
		$settings = array_merge( array(
			$type . '_menu_title'               => __( 'Browse Rentals', '7listings' ),
			$type . '_label'                    => __( 'Rental', '7listings' ),
			$type . '_menu_dropdown'            => 'types',


			// Page Settings

			// Archive Headings
			$type . '_archive_main_title'       => __( 'All Rentals', '7listings' ),
			$type . '_feature_title'            => __( 'Rentals with %TERM%', '7listings' ),
			$type . '_type_title'               => __( '%TERM% rentals', '7listings' ),
			$type . '_location_title'           => __( 'Rentals in %TERM%', '7listings' ),
			$type . '_archive_main_description' => __( 'Rental Archive Description', '7listings' ),

			// Single Similar Listings
			$type . '_similar_title'            => __( 'You may also like these rentals', '7listings' ),
		), $settings );

		return $settings;
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
			'add_new'            => _x( 'Add New', 'rental', '7listings' ),
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
			'rewrite'            => false,
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
		register_taxonomy_for_object_type( 'location', $this->post_type );

		// Type
		if ( taxonomy_exists( $this->post_type . '_type' ) )
		{
			register_taxonomy_for_object_type( $this->post_type . '_type', $this->post_type );
		}
		else
		{
			$labels = array(
				'name'                       => _x( 'Rental Types', 'Taxonomy General Name', '7listings' ),
				'singular_name'              => _x( 'Rental Type', 'Taxonomy Singular Name', '7listings' ),
				'menu_name'                  => __( 'Rental Types', '7listings' ),
				'all_items'                  => __( 'All Rental Types', '7listings' ),
				'parent_item'                => __( 'Parent Rental Type', '7listings' ),
				'parent_item_colon'          => __( 'Parent Rental Type:', '7listings' ),
				'new_item_name'              => __( 'New Rental Type Name', '7listings' ),
				'add_new_item'               => __( 'Add New Rental Type', '7listings' ),
				'edit_item'                  => __( 'Edit Rental Type', '7listings' ),
				'update_item'                => __( 'Update Rental Type', '7listings' ),
				'separate_items_with_commas' => __( 'Separate rental types with commas', '7listings' ),
				'search_items'               => __( 'Search rental types', '7listings' ),
				'add_or_remove_items'        => __( 'Add or remove rental types', '7listings' ),
				'choose_from_most_used'      => __( 'Choose from the most used rental types', '7listings' ),
			);
			$args   = array(
				'labels'       => $labels,
				'hierarchical' => true,
				'rewrite'      => array(
					'slug' => sl_setting( $this->post_type . '_base_url' )
				)
			);
			register_taxonomy( $this->post_type . '_type', $this->post_type, $args );
		}

		// Feature
		if ( taxonomy_exists( 'feature' ) )
		{
			register_taxonomy_for_object_type( 'feature', $this->post_type );
		}
		else
		{
			$labels = array(
				'name'                       => _x( 'Features', 'Taxonomy General Name', '7listings' ),
				'singular_name'              => _x( 'Feature', 'Taxonomy Singular Name', '7listings' ),
				'menu_name'                  => __( 'Features', '7listings' ),
				'all_items'                  => __( 'All Features', '7listings' ),
				'parent_item'                => __( 'Parent Feature', '7listings' ),
				'parent_item_colon'          => __( 'Parent Feature:', '7listings' ),
				'new_item_name'              => __( 'New Feature Name', '7listings' ),
				'add_new_item'               => __( 'Add New Feature', '7listings' ),
				'edit_item'                  => __( 'Edit Feature', '7listings' ),
				'update_item'                => __( 'Update Feature', '7listings' ),
				'separate_items_with_commas' => __( 'Separate features with commas', '7listings' ),
				'search_items'               => __( 'Search features', '7listings' ),
				'add_or_remove_items'        => __( 'Add or remove features', '7listings' ),
				'choose_from_most_used'      => __( 'Choose from the most used features', '7listings' ),
			);
			$args   = array(
				'labels'       => $labels,
				'hierarchical' => true,
			);
			register_taxonomy( 'feature', $this->post_type, $args );
		}
	}

	/**
	 * Add rewrite rules for custom post type
	 *
	 * @param $wp_rewrite
	 *
	 * @return void
	 */
	function add_rewrite_rules( $wp_rewrite )
	{
		$base_url                                 = sl_setting( $this->post_type . '_base_url' );
		$rules["{$base_url}/?$"]                  = 'index.php?post_type=' . $this->post_type;
		$rules["{$base_url}/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&paged=$matches[1]';

		$wp_rewrite->rules = array_merge( $rules, $wp_rewrite->rules );
	}

	/**
	 * Add rewrite tag
	 *
	 * @return void
	 */
	function add_rewrite_tag()
	{
		add_rewrite_tag( '%' . $this->post_type . '_type%', '([^/]+)' );
		add_rewrite_tag( '%' . $this->post_type . '%', '([^/]+)', $this->post_type . '=' );
	}

	/**
	 * Add permalink structure
	 *
	 * @return void
	 */
	function add_permastruct()
	{
		$base_url = sl_setting( $this->post_type . '_base_url' );
		add_permastruct( $this->post_type, "/{$base_url}/%" . $this->post_type . '_type%/%' . $this->post_type . '%', false );
	}

	/**
	 * Replace rewrite tags
	 *
	 * @param string  $permalink
	 * @param WP_Post $post
	 * @param         $leavename
	 * @param         $sample
	 *
	 * @return string
	 */
	function rewrite_replace_tags( $permalink, $post, $leavename, $sample )
	{
		if (
			'' == $permalink
			|| 'publish' != $post->post_status
			|| $this->post_type != $post->post_type
			|| false === strpos( $permalink, '%' . $this->post_type . '_type%' )
		)
			return $permalink;

		$type  = '';
		$types = get_the_terms( $post->ID, $this->post_type . '_type' );
		if ( ! empty( $types ) )
		{
			usort( $types, '_usort_terms_by_ID' ); // Order by ID
			$type = $types[0]->slug;
		}

		if ( empty( $type ) )
			return $permalink;

		$permalink = str_replace( '%' . $this->post_type . '_type%', $type, $permalink );

		return $permalink;
	}
}

new Sl_Rental( 'rental' );
