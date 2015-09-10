<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Product extends Sl_Core
{
	/**
	 * Load files add hooks for this custom post type
	 * Those hooks are added in 'after_setup_theme' to allow some functions run before like license check
	 *
	 * @return void
	 */
	function hooks()
	{
		add_filter( 'rewrite_rules_array', array( $this, 'add_rewrite_rules' ) );
		add_action( 'do_feed_products', array( $this, 'feed_products' ) );

		// Need an early hook to ajaxify update shop cart icon
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragment' ) );
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
			'homepage_' . $type . '_featured_orderby'             => 'date',
			'homepage_' . $type . '_featured_columns'             => 5,

			'homepage_' . $type . '_listings_display'             => 10,
			'homepage_' . $type . '_listings_orderby'             => 'date',
			'homepage_' . $type . '_listings_columns'             => 5,
			'homepage_' . $type . '_listings_image_size'          => 'sl_pano_small',

			'homepage_' . $type . '_listings_more_listings_text'  => __( 'See more products', '7listings' ),
			'homepage_' . $type . '_listings_more_listings_style' => 'button',

			'homepage_' . $type . '_categories_display'           => 10,
			'homepage_' . $type . '_categories_orderby'           => 'none',
			'homepage_' . $type . '_categories_columns'           => 5,
			'homepage_' . $type . '_categories_thumb'             => 1,
			'homepage_' . $type . '_categories_image_size'        => 'sl_pano_small',
			'homepage_' . $type . '_categories_category_title'    => 1,
			'homepage_' . $type . '_categories_count'             => 1,
		), $settings );

		// Add all widgets if they're missed
		$widgets = array(
			$type . '_featured',
			$type . '_categories',
			$type . '_listings',
		);
		foreach ( $widgets as $widget )
		{
			if ( ! in_array( $widget, $settings['homepage_order'] ) )
				$settings['homepage_order'][] = $widget;
		}

		// Check if panels are active
		$fields = array(
			'homepage_' . $type . '_featured_active'   => 0,
			'homepage_' . $type . '_listings_active'   => 0,
			'homepage_' . $type . '_categories_active' => 0,
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
			$type . '_label'                        => __( 'Products', '7listings' ),
			$type . '_feed_condition'               => 'new',
			$type . '_feed_brand'                   => get_bloginfo( 'name' ),


			// Page Settings

			// Archive (tabs)
			$type . '_archive_seo_title_main'       => __( 'All Products', '7listings' ),
			$type . '_archive_seo_title_child'      => __( '%CAT_NAME% Products', '7listings' ),
			$type . '_archive_num'                  => get_option( 'posts_per_page' ),
			$type . '_tag_title'                    => __( '%TERM%', '7listings' ),
			$type . '_brand_title'                  => __( '%BRAND_NAME%', '7listings' ),
			$type . '_attribute_title'              => __( '%TERM%', '7listings' ),

			// Archive Listings
			$type . '_featured_graphics'            => 1,
			$type . '_archive_price'                => 1,
			$type . '_archive_button'               => 1,
			$type . '_archive_rating'               => 1,
			$type . '_archive_excerpt'              => 1,
			$type . '_archive_excerpt_length'       => 25,
			$type . '_archive_image_size'           => 'sl_thumb_small',

			// Archive Layout
			$type . '_archive_main_num'             => get_option( 'posts_per_page' ),
			$type . '_archive_sort_display'         => 'above',
			$type . '_archive_order_by'             => 'menu_order',
			$type . '_archive_layout'               => 'grid',
			$type . '_archive_columns'              => 3,
			$type . '_archive_sidebar_layout'       => 'none',
			$type . '_archive_cat_desc'             => 1,

			// Single Listings
			$type . '_single_title'                 => __( '%PRODUCT_NAME%', '7listings' ),
			$type . '_single_brand_logo_image_size' => 'sl_thumb_small',
			$type . '_slider_image_size'            => 'sl_pano_large',
			$type . '_attributes'                   => 1,
			$type . '_comment_status'               => 1,
			$type . '_ping_status'                  => 1,

			// Single Similar Listings
			$type . '_upsells'                      => 1,
			$type . '_related'                      => 'type',
			$type . '_similar_image_size'           => 'sl_thumb_small',
			$type . '_similar_columns'              => 3,

			$type . '_upsells_title'                => __( 'You may also like', '7listings' ),
			$type . '_related_title'                => __( 'Related Products', '7listings' ),
			$type . '_sells_amount'                 => 4,
			$type . '_sells_excerpt'                => 25,
			$type . '_sells_rating'                 => 1,
			$type . '_sells_price'                  => 1,
			$type . '_sells_button'                 => 1,
			$type . '_sells_excerpt_enable'         => 1,

			// Single Layout
			$type . '_single_sidebar_layout'        => 'none',
		), $settings );

		return $settings;
	}

	/**
	 * Register taxonomies
	 *
	 * @return void
	 */
	function register_taxonomies()
	{
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
			$args   = array(
				'labels'       => $labels,
				'hierarchical' => true,
				'public'       => true,
			);
			register_taxonomy( 'brand', $this->post_type, $args );
		}
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
	 * Add rewrite rules for custom post type
	 *
	 * @param array $rules
	 *
	 * @return array
	 */
	function add_rewrite_rules( $rules )
	{
		$new['products.xml$'] = 'index.php?feed=products';

		return array_merge( $new, $rules );
	}

	/**
	 * Show product feed for Google Merchant
	 *
	 * @return void
	 * @since  4.12
	 */
	function feed_products()
	{
		locate_template( 'templates/product/feed.php', true );
	}

	/**
	 * Ajaxify your cart viewer
	 *
	 * @since 1.0
	 *
	 * @param array $fragments
	 *
	 * @return array
	 */
	function add_to_cart_fragment( $fragments )
	{
		if ( ! Sl_License::is_module_enabled( 'product' ) || ! sl_setting( 'design_mini_cart_enable' ) )
			return $fragments;

		if ( WC()->cart->cart_contents_count )
		{
			$fragments['a.shop'] = sprintf( '
				<a href="%s" class="button shop hidden">%s</a> ',
				apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
				__( 'Shop', '7listings' )
			);

			$fragments['span.button.total.hidden'] = sprintf( '
				<span class="button total">%s</span>',
				WC()->cart->get_cart_total()
			);
		}

		$fragments['span.button.amount'] = sprintf( '
			<span class="button amount">
				<i class="icon-shopping-cart"></i><span class="title">%s</span>
				<span class="mini-cart-counter">%s <span>%s</span></span>
			</span>',
			__( 'Cart', '7listings' ),
			WC()->cart->cart_contents_count,
			__( 'items', '7listings' )
		);

		return $fragments;
	}
}

new Sl_Product( 'product' );
