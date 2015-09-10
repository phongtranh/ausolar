<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Tour extends Sl_Core
{
	/**
	 * Load files add hooks for this custom post type
	 * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
	 *
	 * @return void
	 */
	function hooks()
	{
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
			$type . '_menu_title'               => __( 'Browse Tours', '7listings' ),
			$type . '_label'                    => __( 'Tour', '7listings' ),
			$type . '_multiplier'               => 1,
			$type . '_menu_dropdown'            => 'types',


			// Page Settings

			// Archive Headings
			$type . '_archive_main_title'       => __( 'All Tours', '7listings' ),
			$type . '_features_title'           => __( 'Tours with %TERM%', '7listings' ),
			$type . '_type_title'               => __( '%TERM% tours', '7listings' ),
			$type . '_location_title'           => __( 'Tours in %TERM%', '7listings' ),
			$type . '_archive_main_description' => __( 'Tour Archive Description', '7listings' ),

			// Single Similar Listings
			$type . '_similar_title'            => __( 'You may also like these tours', '7listings' ),
		), $settings );

		return $settings;
	}

	/**
	 * Register tour post type
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
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
		$args   = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => sl_setting( $this->post_type . '_base_url' ),
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => sl_setting( $this->post_type . '_base_url' ) . 's',
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
			$args   = array(
				'labels'       => $labels,
				'hierarchical' => true,
				'rewrite'      => array(
					'slug' => sl_setting( $this->post_type . '_base_url' ) . 's'
				)
			);
			register_taxonomy( $this->post_type . '_type', $this->post_type, $args );
		}

		// Feature
		if ( taxonomy_exists( 'features' ) )
		{
			register_taxonomy_for_object_type( 'features', $this->post_type );
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
			register_taxonomy( 'features', $this->post_type, $args );
		}
	}

	/**
	 * Auto update permanent price
	 *
	 * @return void
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
		if ( empty( $schedule ) )
			return;

		$prices = array(
			'adult'  => 'Adults',
			'child'  => 'Children',
			'senior' => 'Seniors',
			'family' => 'Families',
			'infant' => 'Infants',
		);
		$now    = current_time( 'timestamp', 1 );

		foreach ( $resources as $index => $resource )
		{
			foreach ( $prices as $type => $label )
			{
				$price_type = "price_{$type}";
				if ( empty( $resource[$price_type] ) )
					continue;

				$value = isset( $schedule[$index] ) && isset( $schedule[$index][$type] ) ? $schedule[$index][$type] : array();
				$value = array_merge( array(
					'date'   => '',
					'price'  => '',
					'enable' => 0,
				), $value );

				$date = strtotime( str_replace( '/', '-', $value['date'] ) );

				if ( ! $value['enable'] || $date > $now )
					continue;

				$resources[$index][$price_type] = $value['price'];
				unset( $schedule[$index][$type] );
			}

			if (
				empty( $resource['upsell_items'] )
				|| empty( $resource['upsell_prices'] )
				|| empty( $schedule[$index] )
				|| empty( $schedule[$index]['upsells'] )
			)
			{
				continue;
			}

			foreach ( $schedule[$index]['upsells'] as $key => $upsell )
			{
				$value_upsell = isset( $upsell ) ? $upsell : array();
				$value_upsell = array_merge( array(
					'date'   => '',
					'price'  => '',
					'enable' => 0,
				), $value_upsell );

				$date = strtotime( str_replace( '/', '-', $value_upsell['date'] ) );

				if ( ! $value_upsell['enable'] || $date > $now )
					continue;

				$resources[$index]['upsell_prices'][$key] = $value_upsell['price'];
				unset( $schedule[$index]['upsells'][$key] );
			}
		}

		update_post_meta( $listing->ID, $meta_key, $resources );
		update_post_meta( $listing->ID, 'schedule', $schedule );
	}
}

new Sl_Tour( 'tour' );
