<?php

/**
 * This class will hold all things for product management page
 */
class Sl_Product_Frontend
{
	/**
	 * @var string Post type
	 */
	public $post_type = 'product';

	/**
	 * @var bool Detect if we're on the single page
	 */
	public $is_single = false;

	/**
	 * @var bool Detect if we're on the archive page
	 */
	public $is_archive = false;

	/**
	 * Check if we in right page in admin area
	 * Use a separated function allow child class to rewrite the conditions
	 *
	 * @return Sl_Product_Frontend
	 */
	function __construct()
	{
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_filter( 'template_include', array( $this, 'archive_template' ), 30 );
		add_action( 'pre_get_posts', array( $this, 'change_num_products' ) );

		add_filter( 'sl_js_params', array( $this, 'js_params' ) );

		add_filter( 'sl_meta_title', array( $this, 'meta_title' ), 10, 2 );

		add_action( 'comment_post', array( $this, 'comment_post' ), 1 );
		foreach ( array( 'single', 'post_type_archive', 'tax' ) as $hook )
		{
			add_filter( 'sl_breadcrumbs_' . $hook, array( $this, 'breadcrumbs' ), 10, 2 );
		}

		add_filter( 'sl_archive_page', array( $this, 'brand_description' ), 20 );

		add_action( 'template_redirect', array( $this, 'featured_title' ) );

		add_filter( 'sl_singular-product_featured_class', array( $this, 'featured_class' ) );

		// Featured graphics
		if ( sl_setting( "{$this->post_type}_featured_graphics" ) )
		{
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'featured_graphics' ) );
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'featured_graphics' ) );
		}

		add_filter( 'sl_listing_element', array( __CLASS__, 'listing_element' ), 10, 3 );

		// Disable WooCommerce style
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );

		add_action( 'sl_show_mini_cart', array( $this, 'header_mini_cart' ) );

		// Change default WooCommerce placeholder image
		add_filter( 'woocommerce_placeholder_img', array( $this, 'placeholder_img' ), 10, 3 );

		// Set sub title in featured title area
		add_filter( 'sl_featured_title_subtitle', array( $this, 'sub_title' ) );
	}

	/**
	 * Remove WooCommerce hooks
	 *
	 * @return void
	 */
	function template_redirect()
	{
		// Setup template checks
		$this->is_single  = is_single() && $this->post_type == get_post_type();
		$this->is_archive = is_post_type_archive( $this->post_type ) || is_tax( get_object_taxonomies( $this->post_type ) );

		$this->woocommerce_hooks();
		$this->show_homepage_modules();
	}

	/**
	 * Show homepage modules
	 *
	 * @return void
	 */
	function show_homepage_modules()
	{
		if ( ! is_home() && ! is_front_page() )
			return;
		require THEME_TPL . $this->post_type . '/home-modules.php';
		add_action( 'sl_homepage_show_module', 'sl_homepage_show_product_modules', 10, 1 );
	}

	/**
	 * Hooks for woocommerce
	 *
	 * @return void
	 */
	function woocommerce_hooks()
	{
		global $woocommerce;

		remove_action( 'wp_head', array( $woocommerce, 'generator' ) );

		// Archive page
		if ( $this->is_archive )
		{
			// Change loop columns
			add_filter( 'loop_shop_columns', array( $this, 'archive_columns' ) );

			// Don't use WooCommerce pagination, use our Bootstrap one
			remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
			add_action( 'woocommerce_after_shop_loop', 'peace_numeric_pagination', 5 );

			// Change position of order box
			if ( ! sl_setting( "{$this->post_type}_archive_sort" ) )
			{
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}
			elseif ( 'below' == sl_setting( "{$this->post_type}_archive_sort_display" ) )
			{
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering' );
			}
			elseif ( 'both' == sl_setting( "{$this->post_type}_archive_sort_display" ) )
			{
				add_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering' );
			}

			// Default sort by
			add_filter( 'woocommerce_default_catalog_orderby', array( $this, 'default_catalog_orderby' ) );

			// Conditionally show product elements base on product settings
			add_action( 'woocommerce_before_shop_loop', array( $this, 'begin_archive_elements' ) );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'end_archive_elements' ) );
		}

		// Single page
		if ( $this->is_single )
		{
			// No duplicated title
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );

			// Move sale flash to featured title area
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
			add_action( 'sl_singular-product_featured_title_bottom', 'woocommerce_show_product_sale_flash' );

			// Add price to featured title area
			//			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );

			// Check price
			if ( get_post_meta( get_the_ID(), '_regular_price', true ) )
				add_action( 'sl_singular-product_featured_title_bottom', 'woocommerce_template_single_price' );

			// Move add to cart button to featured title area
			//			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			//			add_action( 'sl_singular-product_featured_title_bottom', 'woocommerce_template_single_add_to_cart' );

			// No tabs
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

			// Wrap description in H4
			add_filter( 'woocommerce_short_description', array( $this, 'change_desciption' ) );

			// No auto-related products & upsells
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

			// No image & thumbnails
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

			// Show brands
			add_action( 'woocommerce_product_meta_start', array( $this, 'show_brands' ) );

			if ( ! sl_setting( $this->post_type . '_meta' ) )
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

			// Show overall rating below product price and options
			add_action( 'woocommerce_single_product_summary', array( $this, 'overall_rating' ), 35 );
		}

		// No cart
		if ( ! sl_setting( $this->post_type . '_cart' ) )
		{
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
	}

	/**
	 * Display category description
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	function brand_description( $text )
	{
		if ( ! is_tax( 'brand' ) || ! sl_setting( $this->post_type . '_brand_desc' ) )
			return $text;

		$desc = term_description();

		return $desc ? '<h3>' . $desc . '</h3>' . $text : $text;
	}

	/**
	 * Wrap description in H6
	 *
	 * @param string $desc
	 *
	 * @return string
	 */
	function change_desciption( $desc )
	{
		return '<h4>' . strip_tags( $desc ) . '</h4>';
	}

	/**
	 * Change number of columns in archive page
	 *
	 * @param int $cols
	 *
	 * @return int
	 */
	function archive_columns( $cols )
	{
		return sl_setting( $this->post_type . '_archive_columns' );
	}

	/**
	 * Change number of products per page
	 *
	 * @param WP_Query $query
	 *
	 * @return void
	 */
	function change_num_products( $query )
	{
		if ( ! $query->is_main_query() )
			return;

		if ( is_post_type_archive( $this->post_type ) )
			set_query_var( 'posts_per_page', sl_setting( $this->post_type . '_archive_main_num' ) );
		if ( is_tax( get_object_taxonomies( $this->post_type ) ) )
			set_query_var( 'posts_per_page', sl_setting( $this->post_type . '_archive_num' ) );
	}

	/**
	 * Add more param to JS object on the homepage
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function js_params( $params )
	{
		if ( ! $this->is_single )
			return $params;
		$params['post_type'] = $this->post_type;

		return $params;
	}

	/**
	 * Change title for archive page
	 *
	 * @param string $title
	 * @param string $sep
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	function meta_title( $title, $sep = '' )
	{
		if ( ! $this->is_single && ! $this->is_archive )
			return $title;

		$replacement = array(
			'%SEP%'   => $sep,
			'%LABEL%' => sl_setting( $this->post_type . '_label' ),
		);

		if ( is_post_type_archive( $this->post_type ) )
		{
			$title = sl_setting( $this->post_type . '_archive_seo_title_main' );
		}
		elseif ( is_tax( $this->post_type . '_cat' ) )
		{
			$title                     = sl_setting( $this->post_type . '_archive_seo_title_child' );
			$cat                       = get_queried_object();
			$replacement['%CAT_NAME%'] = $cat->name;
			if ( $cat->parent )
			{
				$parent                           = get_term( $cat->parent, $this->post_type . '_cat' );
				$replacement['%PARENT_CAT_NAME%'] = $parent->name;
			}
			else
			{
				$replacement['%PARENT_CAT_NAME%'] = '';
			}
		}
		elseif ( is_tax( 'brand' ) )
		{
			$title                       = sl_setting( $this->post_type . '_brand_title' );
			$cat                         = get_queried_object();
			$replacement['%BRAND_NAME%'] = $cat->name;
		}
		elseif ( $this->is_single )
		{
			$title                         = sl_setting( $this->post_type . '_single_title' );
			$replacement['%PRODUCT_NAME%'] = get_the_title();
		}

		$title = strtr( $title, $replacement );

		return $title;
	}

	/**
	 * Get correct archive template
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	function archive_template( $template )
	{
		if (
			is_post_type_archive( $this->post_type )
			|| is_tax( get_object_taxonomies( $this->post_type ) )
		)
		{
			$template = locate_template( array(
				'templates/' . $this->post_type . '/archive-' . sl_setting( $this->post_type . '_archive_layout' ) . '.php',
				'templates/' . $this->post_type . '/archive.php',
			) );
		}

		return $template;
	}

	/**
	 * Don't process rating for product, let WooCommerce do
	 *
	 * @return void
	 **/
	function comment_post()
	{
		global $post;
		if ( $this->post_type != $post->post_type )
			return;

		remove_action( 'comment_post', 'sl_add_comment_rating', 5 );
	}

	/**
	 * Show product brands
	 *
	 * @return void
	 */
	function show_brands()
	{
		$count = count( get_the_terms( get_the_ID(), 'brand' ) );
		echo get_the_term_list( get_the_ID(), 'brand', '<span class="posted_in">' . _n( 'Brand:', 'Brands:', $count, '7listings' ) . ' ', ', ', '.</span>' );
	}

	/**
	 * Breadcrumb item for single page
	 *
	 * @param  string $item
	 * @param  string $post_type
	 *
	 * @return string
	 */
	function breadcrumbs( $item, $post_type )
	{
		if ( $post_type != $this->post_type )
			return $item;

		$link = get_post_type_archive_link( $post_type );
		if ( strpos( $link, '?' ) )
			$link = home_url( sl_setting( $post_type . '_base_url' ) . '/' );

		return array( $link, __( 'Products', '7listings' ) );
	}

	/**
	 * Set default catalog orderby
	 *
	 * @param string $orderby
	 *
	 * @return string
	 */
	function default_catalog_orderby( $orderby )
	{
		return sl_setting( "{$this->post_type}_archive_order_by" );
	}

	/**
	 * Display slider
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	static function slider( $args )
	{
		global $woocommerce;

		$args = array_merge( array(
			'title'      => '',
			'type'       => 'all',
			'cat'        => '',
			'brand'      => '',
			'orderby'    => 'date',
			'number'     => 5,
			'cart'       => 1,
			'transition' => 'fade',
			'delay'      => 0,
			'speed'      => 1000,

			'container'  => 'div', // Container tag
		), $args );

		$query_args = array(
			'post_type'  => 'product',
			'meta_query' => array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'visible' ),
					'compare' => 'IN',
				),
			),
		);
		sl_build_query_args( $query_args, $args );
		switch ( $args['orderby'] )
		{
			case 'price-asc':
				$query_args['meta_key'] = '_regular_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'ASC';
				break;
			case 'price-desc':
				$query_args['meta_key'] = '_regular_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;
			case 'date':
			default:
		}
		switch ( $args['type'] )
		{
			case 'featured':
				$query_args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes',
				);
				break;
			case 'on-sale':
				$query_args['meta_query'][] = array(
					'key'     => '_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				);
				break;
			case 'top-rated':
				add_filter( 'posts_clauses', array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
				break;
			case 'best-sellers':
				$query_args['meta_key']      = 'total_sales';
				$query_args['orderby']       = 'meta_value_num';
				$query_args['order']         = 'DESC';
				$query_args['no_found_rows'] = 1;
				break;
			default:
		}
		if ( $args['cat'] )
		{
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'id',
				'terms'    => $args['cat'],
			);
		}
		if ( $args['brand'] )
		{
			$query_args['tax_query'][] = array(
				'taxonomy' => 'brand',
				'field'    => 'id',
				'terms'    => $args['brand'],
			);
		}

		$query = new WP_Query( $query_args );

		if ( 'top-rated' == $args['type'] )
			remove_filter( 'posts_clauses', array( $woocommerce->query, 'order_by_rating_post_clauses' ) );

		if ( ! $query->have_posts() )
			return '';

		$html = '';

		$args['class']      = 'slide';
		$args['image_size'] = 'sl_pano_medium';
		$args['elements']   = array( 'post_title', 'rating', 'excerpt', 'price', 'cart' );
		while ( $query->have_posts() )
		{
			$query->the_post();
			$html .= sl_post_list_single( $args );
		}

		wp_reset_postdata();
		wp_enqueue_script( 'jquery-cycle2' );

		return sprintf(
			'<%s class="sl-list posts tours cycle-slideshow" data-cycle-slides="> article" data-cycle-fx="%s" data-cycle-delay="%s" data-cycle-speed="%s">%s</%s>',
			$args['container'],
			$args['transition'], $args['delay'], $args['speed'], $html,
			$args['container']
		);
	}

	/**
	 * Display post list
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	static function post_list( $args )
	{
		global $woocommerce;

		$args = array_merge( array(
			'title'               => '',
			'type'                => 'all',
			'cat'                 => '',
			'brand'               => '',
			'orderby'             => 'date',
			'number'              => 5,
			'display'             => 'list',
			'columns'             => 1,
			'cart'                => 1,
			'more_listings'       => 1,
			'more_listings_text'  => __( 'See more listings', '7listings' ),
			'more_listings_style' => 'button',

			'container'           => 'aside', // Container tag
		), $args );

		$query_args = array(
			'post_type'  => 'product',
			'meta_query' => array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'visible' ),
					'compare' => 'IN',
				),
			),
		);
		sl_build_query_args( $query_args, $args );
		switch ( $args['orderby'] )
		{
			case 'price-asc':
				$query_args['meta_key'] = '_regular_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'ASC';
				break;
			case 'price-desc':
				$query_args['meta_key'] = '_regular_price';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['order']    = 'DESC';
				break;
		}
		switch ( $args['type'] )
		{
			case 'featured':
				$query_args['meta_query'][] = array(
					'key'   => '_featured',
					'value' => 'yes',
				);
				break;
			case 'on-sale':
				$query_args['meta_query'][] = array(
					'key'     => '_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'NUMERIC',
				);
				break;
			case 'top-rated':
				add_filter( 'posts_clauses', array( $woocommerce->query, 'order_by_rating_post_clauses' ) );
				break;
			case 'best-sellers':
				$query_args['meta_key']      = 'total_sales';
				$query_args['orderby']       = 'meta_value_num';
				$query_args['order']         = 'DESC';
				$query_args['no_found_rows'] = 1;
				break;
			default:
		}
		if ( $args['cat'] )
		{
			$query_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'id',
				'terms'    => $args['cat'],
			);
		}
		if ( $args['brand'] )
		{
			$query_args['tax_query'][] = array(
				'taxonomy' => 'brand',
				'field'    => 'id',
				'terms'    => $args['brand'],
			);
		}

		$query = new WP_Query( $query_args );

		if ( 'top-rated' == $args['type'] )
			remove_filter( 'posts_clauses', array( $woocommerce->query, 'order_by_rating_post_clauses' ) );

		if ( ! $query->have_posts() )
			return '';

		$html = '';

		$args['elements'] = array( 'post_title', 'rating', 'excerpt', 'price', 'cart' );
		while ( $query->have_posts() )
		{
			$query->the_post();
			$html .= sl_post_list_single( $args );
		}

		wp_reset_postdata();

		$class = 'sl-list posts products';
		$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

		$html = "<{$args['container']} class='$class'>$html</{$args['container']}>";

		/**
		 * Add 'View more listings' links
		 * Link to term archive page and fallback to post type archive page
		 * If the archive page does not have more listing, then don't show this link
		 */
		if ( $args['more_listings'] )
		{
			$show = true;

			$link = get_post_type_archive_link( 'product' );

			// If set 'product_cat' taxonomy, get link to that taxonomy page
			if ( $args['cat'] )
			{
				$term = get_term( absint( $args['cat'] ), 'product_cat' );
				if ( ! is_wp_error( $term ) )
				{
					// Don't show view more listings if the term doesn't have more listings
					if ( $term->count <= $args['number'] )
						$show = false;

					$term_link = get_term_link( $term, 'product_cat' );
					if ( ! is_wp_error( $term_link ) )
						$link = $term_link;
				}
			}

			if ( $show )
			{
				$html .= sprintf(
					'<a%s href="%s">%s</a>',
					'button' == $args['more_listings_style'] ? ' class="button"' : '',
					$link,
					$args['more_listings_text']
				);
			}
		}

		return $html;
	}

	/**
	 * Add hooks for featured title
	 *
	 * @return string
	 */
	function featured_title()
	{
		$type                 = $this->post_type;
		$featured_title_hooks = array( "singular-$type", "archive-$type" );

		$taxonomies = get_object_taxonomies( $type );
		foreach ( $taxonomies as &$tax )
		{
			$tax = "taxonomy-$tax";
		}
		$featured_title_hooks = array_merge( $featured_title_hooks, $taxonomies );

		foreach ( $featured_title_hooks as $hook )
		{
			add_filter( "sl_{$hook}_featured_title_title", array( $this, 'meta_title' ) );
		}

		if ( is_tax() && function_exists( 'is_woocommerce' ) && is_woocommerce() )
		{
			$image_type = sl_setting( 'product_archive_cat_image_type' );

			if ( 'background' == $image_type )
				add_filter( 'sl_featured_title_style', array( $this, 'archive_background' ) );
			elseif ( 'thumbnail' == $image_type )
				add_action( 'sl_featured_title_top', array( $this, 'archive_logo' ) );

			add_filter( 'sl_featured_title_class', array( $this, 'archive_class' ) );
		}

		add_action( 'sl_singular-product_featured_title_top', array( $this, 'featured_title_single_top' ) );
	}

	/**
	 * Add background style for featured title area
	 *
	 * @param string $style
	 *
	 * @return string
	 */
	function archive_background( $style )
	{
		if ( $src = $this->get_archive_thumbnail_src() )
			$style .= 'background-image: url(' . $src . ');';

		return $style;
	}

	/**
	 * Add term logo for featured title area
	 *
	 * @return void
	 */
	function archive_logo()
	{
		if ( $src = $this->get_archive_thumbnail_src() )
			echo '<figure class="thumbnail"><img src="' . esc_url( $src ) . '" class="brand-logo photo"></figure>';
	}

	/**
	 * Get image source of archive thumbnail
	 *
	 * @return string or null
	 */
	function get_archive_thumbnail_src()
	{
		if ( sl_setting( 'product_archive_cat_image' ) )
		{
			$term = get_queried_object();
			$logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
			if ( function_exists( 'get_woocommerce_term_meta' ) && empty( $logo ) )
				$logo = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id' );
			if ( $logo )
			{
				$size = sl_setting( 'product_archive_cat_image_size' );
				list( $src ) = wp_get_attachment_image_src( $logo, $size );
				return $src;
			}
			return null;
		}
		return null;
	}

	/**
	 * Display product image
	 *
	 * @return void
	 */
	function featured_title_single_top()
	{
		if ( ! sl_setting( $this->post_type . '_single_brand_logo' ) )
		{
			return;
		}

		$terms = wp_get_post_terms( get_the_ID(), 'brand' );
		if ( is_wp_error( $terms ) || ! $terms )
		{
			return;
		}
		$term = current( $terms );
		$size = sl_setting( get_post_type() . '_brand_logo_image_size' );

		if ( $logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' ) )
		{
			list( $src ) = wp_get_attachment_image_src( $logo, $size );
		}
		else
		{
			$src = sl_image_placeholder( $size, 'src' );
		}

		$image = '<figure class="thumbnail"><img src="' . esc_url( $src ) . '" class="brand-logo photo" alt="' . esc_attr( $term->name ) . '"></figure>';

		if ( sl_setting( $this->post_type . '_single_brand_logo_link' ) )
		{
			$link = get_term_link( $term, 'brand' );
			echo '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $term->name ) . '">' . $image . '</a>';
		}
		else
		{
			echo $image;
		}
	}

	/**
	 * Show overall rating below product price and options
	 *
	 * @return void
	 */
	function overall_rating()
	{
		list( $count, $average ) = sl_get_average_rating();
		echo '<div class="overall-rating">';
		echo '<span class="rating-title"><a href="#comments">' . sprintf( _n( '%s Review', '%s Reviews', $count, '7listings' ), $count ) . '</a></span>';
		sl_star_rating( $average, array(
			'count' => $count,
			'item'  => get_the_title(),
		) );
		echo '</div>';
	}

	/**
	 * Display thumbnail for related products
	 * This function will apply filters 'post_thumbnail_html' which will show correct placeholder image set
	 * by the theme if the product doesn't have featured image
	 *
	 * @see sl_thumbnail_html()
	 *
	 * @return void
	 */
	static function related_thumbnail()
	{
		$size = sl_setting( 'product_similar_image_size' );
		the_post_thumbnail( $size, array(
			'alt'   => the_title_attribute( 'echo=0' ),
			'title' => the_title_attribute( 'echo=0' ),
		) );
	}

	/**
	 * Start adding hooks to conditionally display product elements
	 *
	 * @return void
	 */
	function begin_archive_elements()
	{
		// Price
		if ( ! sl_setting( "{$this->post_type}_archive_price" ) )
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

		// Add to cart button
		if ( ! sl_setting( "{$this->post_type}_archive_button" ) )
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

		// Rating
		if ( ! sl_setting( "{$this->post_type}_archive_rating" ) )
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

		// Thumbnail
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_thumbnail' ) );

		if ( sl_setting( "{$this->post_type}_archive_excerpt" ) )
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'archive_excerpt' ) );
	}

	/**
	 * Finish adding hooks to conditionally display product elements
	 *
	 * @return void
	 */
	function end_archive_elements()
	{
		// Price
		if ( ! sl_setting( "{$this->post_type}_archive_price" ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );

		// Add to cart button
		if ( ! sl_setting( "{$this->post_type}_archive_button" ) )
			add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

		// Rating
		if ( ! sl_setting( "{$this->post_type}_archive_rating" ) )
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

		// Thumbnail
		remove_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'archive_thumbnail' ) );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
	}

	/**
	 * Display product featured graphics
	 *
	 * @return void
	 */
	function featured_graphics()
	{
		get_template_part( 'templates/product/featured-ribbon' );
	}

	/**
	 * Show product thumbnail on archive page
	 * This function will apply filters 'post_thumbnail_html' which will show correct placeholder image set
	 * by the theme if the product doesn't have featured image
	 *
	 * @see sl_thumbnail_html()
	 *
	 * @return void
	 */
	function archive_thumbnail()
	{
		if ( sl_setting( 'product_archive_cat_thumb' ) )
		{
			global $post;
			$size   = sl_setting( 'product_archive_image_size' );
			$image  = get_the_post_thumbnail( $post->ID, $size, array(
				'alt'   => the_title_attribute( 'echo=0' ),
				'title' => the_title_attribute( 'echo=0' ),
			) );
			echo $image;
		}
	}

	/**
	 * Show product excerpt on archive page
	 *
	 * @return void
	 */
	function archive_excerpt()
	{
		echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( get_post_type() . '_archive_excerpt_length' ) ) );
	}

	/**
	 * Add more classes to featured title area
	 *
	 * @param array $class
	 *
	 * @return array
	 */
	function featured_class( $class )
	{
		global $product;

		if ( $product->is_on_sale() )
			$class[] = 'sale';
		if ( $product->is_featured() )
			$class[] = 'featured';

		return $class;
	}

	/**
	 * Change and add some listing element for product
	 *
	 * @param string $output  Output
	 * @param string $element Element name, must be 'star_rating'
	 * @param array  $args    Argument
	 *
	 * @return string
	 */
	public static function listing_element( $output, $element, $args )
	{
		if ( 'product' != $args['post_type'] )
			return $output;

		switch ( $element )
		{
			case 'price':
				global $product;
				$product = wc_get_product( get_the_ID() );

				return '<span class="entry-meta price">' . $product->get_price_html() . '</span>';
			case 'cart':
				ob_start();
				woocommerce_template_loop_add_to_cart();

				return ob_get_clean();
		}

		return $output;
	}


	/**
	 * Display mini cart
	 *
	 * @return string
	 */
	function header_mini_cart()
	{
		if ( ! class_exists( 'WC_Cart' ) || ! Sl_License::is_module_enabled( 'product' ) || ! sl_setting( 'design_mini_cart_enable' ) )
			return;

		printf( '
			<aside id="cart-header">
				<a href="%s" class="cart-contents" title="%s">
					<span class="button amount">
						<i class="icon-shopping-cart"></i><span class="title">%s</span>
						<span class="mini-cart-counter">%s <span>%s</span></span>
					</span>
					<span class="button total hidden">
						%s
					</span>
				</a>
				<a href="%s" class="button shop" >%s</a>
				<div id="mini-cart-content" class="mini-cart widget_shopping_cart_content"></div>
			</aside>',
			WC()->cart->get_cart_url(),
			__( 'View your shopping cart', '7listings' ),
			__( 'Cart', '7listings' ),
			WC()->cart->cart_contents_count,
			__( 'items', '7listings' ),
			WC()->cart->get_cart_total(),
			apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ),
			__( 'Shop', '7listings' )

		);
	}

	/**
	 * List woocommerce pages id
	 *
	 * @return array
	 */
	public static function woocommerce_page_ids()
	{
		if ( function_exists( 'wc_get_page_id' ) )
			return array(
				wc_get_page_id( 'checkout' ),
				wc_get_page_id( 'cart' ),
				wc_get_page_id( 'myaccount' ),
				wc_get_page_id( 'view_order' ),
				wc_get_page_id( 'terms' )
			);
		else
			return array();
	}

	/**
	 * Change the markup of placeholder image tag to match theme's markup
	 * @param string       $image      Image tag markup
	 * @param string|array $size       Image size
	 * @param array        $dimensions Array of image width and height
	 *
	 * @return string
	 */
	function placeholder_img( $image, $size, $dimensions )
	{
		return '<figure class="thumbnail">' . sl_image_placeholder( $size ) . '</figure>';
	}

	/**
	 * Set archive subtitle in featured title area
	 *
	 * @param string $subtitle
	 *
	 * @return string
	 */
	function sub_title( $subtitle )
	{
		if ( sl_setting( 'product_archive_cat_desc' ) )
		{
			if ( is_archive() )
				$subtitle = term_description();

			if ( is_post_type_archive( $this->post_type ) )
			{
				$subtitle = sl_setting( $this->post_type . '_archive_main_description' );
			}
		}

		return $subtitle;
	}

	/**
	 * Add 'height' class to featured title area in archive page
	 *
	 * @param array $class
	 *
	 * @return array
	 */
	function archive_class( $class )
	{
		$height = sl_setting( 'product_archive_featured_title_height' );
		if ( $height && 'medium' != $height )
			$class[] = $height;

		return $class;
	}

	/**
	 * Show product excerpt on cross and up sells
	 *
	 * @return void
	 */
	static function product_excerpt()
	{
		echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( 'product_sells_excerpt' ) ) );
	}
}

new Sl_Product_Frontend;
