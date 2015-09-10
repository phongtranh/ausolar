<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Accommodation extends Sl_Core
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

		// Auto update permanent price
		add_action( 'init', array( $this, 'update_permanent_price' ) );
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

		$settings = array_merge( $this->atr_default_settings(), $settings );

		// For homepage
		$settings = array_merge( array(
			'homepage_' . $type . '_featured_star_rating' => 1,

			'homepage_' . $type . '_amenities_display'    => 77,
		), $settings );

		// Add all widgets if they're missed
		$widgets = array(
			$type . '_featured',
			$type . '_types',
			$type . '_listings',
			$type . '_amenities',
		);
		foreach ( $widgets as $widget )
		{
			if ( ! in_array( $widget, $settings['homepage_order'] ) )
				$settings['homepage_order'][] = $widget;
		}

		// Check if panels are active
		$fields = array(
			'homepage_' . $type . '_featured_active'  => 0,
			'homepage_' . $type . '_listings_active'  => 0,
			'homepage_' . $type . '_types_active'     => 0,
			'homepage_' . $type . '_amenities_active' => 0,
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
			$type . '_menu_title'               => __( 'Browse Accommodations', '7listings' ),
			$type . '_label'                    => __( 'Accommodation', '7listings' ),
			$type . '_menu_dropdown'            => 'types',

			// Page Settings

			// Archive Headings
			$type . '_archive_main_title'       => __( 'All Accommodations', '7listings' ),
			$type . '_amenity_title'            => __( 'Accommodations with %TERM%', '7listings' ),
			$type . '_type_title'               => __( '%TERM% accommodations', '7listings' ),
			$type . '_location_title'           => __( 'Accommodations in %TERM%', '7listings' ),
			$type . '_archive_main_description' => __( 'Accommodation Archive Description', '7listings' ),

			// Single Similar Listings
			$type . '_similar_title'            => __( 'You may also like these accommodations', '7listings' ),
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
			'add_new'            => _x( 'Add New', 'accommodation', '7listings' ),
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
		if ( taxonomy_exists( 'type' ) )
		{
			register_taxonomy_for_object_type( 'type', $this->post_type );
		}
		else
		{
			$labels = array(
				'name'                       => _x( 'Property Types', 'Taxonomy General Name', '7listings' ),
				'singular_name'              => _x( 'Property Type', 'Taxonomy Singular Name', '7listings' ),
				'menu_name'                  => __( 'Property Types', '7listings' ),
				'all_items'                  => __( 'All Property Types', '7listings' ),
				'parent_item'                => __( 'Parent Property Type', '7listings' ),
				'parent_item_colon'          => __( 'Parent Property Type:', '7listings' ),
				'new_item_name'              => __( 'New Property Type Name', '7listings' ),
				'add_new_item'               => __( 'Add New Property Type', '7listings' ),
				'edit_item'                  => __( 'Edit Property Type', '7listings' ),
				'update_item'                => __( 'Update Property Type', '7listings' ),
				'separate_items_with_commas' => __( 'Separate property types with commas', '7listings' ),
				'search_items'               => __( 'Search property types', '7listings' ),
				'add_or_remove_items'        => __( 'Add or remove property types', '7listings' ),
				'choose_from_most_used'      => __( 'Choose from the most used property types', '7listings' ),
			);
			$args   = array(
				'labels'       => $labels,
				'hierarchical' => true,
				'rewrite'      => array(
					'slug' => sl_setting( $this->post_type . '_base_url' )
				)
			);
			register_taxonomy( 'type', $this->post_type, $args );
		}

		// Stars
		$labels = array(
			'name'                       => _x( 'Stars', 'Taxonomy General Name', '7listings' ),
			'singular_name'              => _x( 'Star', 'Taxonomy Singular Name', '7listings' ),
			'menu_name'                  => __( 'Stars', '7listings' ),
			'all_items'                  => __( 'All Stars', '7listings' ),
			'parent_item'                => __( 'Parent Star', '7listings' ),
			'parent_item_colon'          => __( 'Parent Star:', '7listings' ),
			'new_item_name'              => __( 'New Star Name', '7listings' ),
			'add_new_item'               => __( 'Add New Star', '7listings' ),
			'edit_item'                  => __( 'Edit Star', '7listings' ),
			'update_item'                => __( 'Update Star', '7listings' ),
			'separate_items_with_commas' => __( 'Separate stars with commas', '7listings' ),
			'search_items'               => __( 'Search stars', '7listings' ),
			'add_or_remove_items'        => __( 'Add or remove stars', '7listings' ),
			'choose_from_most_used'      => __( 'Choose from the most used stars', '7listings' ),
		);
		$args   = array(
			'labels'  		=> $labels,
			'hierarchical' 	=> true,
			'rewrite' 		=> array(
				'slug' => sl_setting( $this->post_type . '_base_url' ) . 's'
			)
		);
		register_taxonomy( 'stars', $this->post_type, $args );

		// Amenity
		$labels = array(
			'name'                       => _x( 'Amenities', 'Taxonomy General Name', '7listings' ),
			'singular_name'              => _x( 'Amenity', 'Taxonomy Singular Name', '7listings' ),
			'menu_name'                  => __( 'Amenities', '7listings' ),
			'all_items'                  => __( 'All Amenities', '7listings' ),
			'parent_item'                => __( 'Parent Amenity', '7listings' ),
			'parent_item_colon'          => __( 'Parent Amenity:', '7listings' ),
			'new_item_name'              => __( 'New Amenity Name', '7listings' ),
			'add_new_item'               => __( 'Add New Amenity', '7listings' ),
			'edit_item'                  => __( 'Edit Amenity', '7listings' ),
			'update_item'                => __( 'Update Amenity', '7listings' ),
			'separate_items_with_commas' => __( 'Separate amenities with commas', '7listings' ),
			'search_items'               => __( 'Search amenities', '7listings' ),
			'add_or_remove_items'        => __( 'Add or remove amenities', '7listings' ),
			'choose_from_most_used'      => __( 'Choose from the most used amenities', '7listings' ),
		);
		$args   = array(
			'labels'       => $labels,
			'hierarchical' => true,
		);
		register_taxonomy( 'amenity', $this->post_type, $args );
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
		$base                               = sl_setting( $this->post_type . '_base_url' );
		$rules["$base/?$"]                  = 'index.php?post_type=' . $this->post_type;
		$rules["$base/page/([0-9]{1,})/?$"] = 'index.php?post_type=' . $this->post_type . '&paged=$matches[1]';

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
		$base = sl_setting( $this->post_type . '_base_url' );
		add_permastruct( $this->post_type, "/$base/%" . $this->post_type . '_type%/%' . $this->post_type . '%' );
	}

	/**
	 * Replace rewrite tags
	 *
	 * @param string $permalink
	 * @param object $post
	 * @param        $leavename
	 * @param        $sample
	 *
	 * @return string
	 */
	function rewrite_replace_tags( $permalink, $post, $leavename, $sample )
	{
		if (
			'' == $permalink
			|| in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) )
			|| $this->post_type != $post->post_type
			|| false === strpos( $permalink, '%' . $this->post_type . '_type%' )
		)
			return $permalink;

		$type  = '';
		$types = get_the_terms( $post->ID, 'type' );
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
		$taxonomies[] = 'amenity';

		return $taxonomies;
	}

	/**
	 * Auto update permanent price
	 */
	function update_permanent_price()
	{
		$listings = get_posts( array(
			'post_type'      => $this->post_type,
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );
		if ( empty( $listings ) )
			return;

		foreach ( $listings as $listing )
		{
			$this->update_permanent_price_single( $listing );
		}

		wp_reset_postdata();
	}

	/**
	 * Update permanent price of single listing
	 *
	 * @param WP_Post $listing
	 *
	 * @return void
	 */
	function update_permanent_price_single( $listing )
	{
		$meta_key  = sl_meta_key( 'booking', $this->post_type );
		$resources = get_post_meta( $listing->ID, $meta_key, true );
		if ( empty( $resources ) )
			return;
		$schedule = get_post_meta( $listing->ID, 'schedule', true );

		$now = current_time( 'timestamp', 1 );

		foreach ( $resources as $index => $resource )
		{
			if ( empty( $resource['price'] ) )
				continue;

			$value = isset( $schedule[$index] ) ? $schedule[$index] : array();
			$value = array_merge( array(
				'date'   => '',
				'price'  => '',
				'enable' => 0,
			), $value );

			$date = strtotime( str_replace( '/', '-', $value['date'] ) );

			if ( ! $value['enable'] || $date > $now )
				continue;

			$resources[$index]['price'] = $value['price'];
			unset( $schedule[$index] );
		}

		update_post_meta( $listing->ID, $meta_key, $resources );
		update_post_meta( $listing->ID, 'schedule', $schedule );
	}
}

new Sl_Accommodation( 'accommodation' );
