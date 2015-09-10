<?php

/**
 * This class will hold all things for frontend
 */
abstract class Sl_Core_Frontend
{
	/**
	 * Post type
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Constructor
	 *
	 * @param string $post_type
	 *
	 * @return Sl_Core_Frontend
	 */
	function __construct( $post_type )
	{
		$this->post_type = $post_type;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'sl_js_params', array( $this, 'js_params' ) );

		add_filter( 'booking_button', array( $this, 'booking_button' ), 10, 3 );
		add_action( 'template_redirect', array( $this, 'show_homepage_modules' ) );
		foreach ( array( 'single', 'post_type_archive', 'tax', 'general_text' ) as $hook )
		{
			add_filter( 'sl_breadcrumbs_' . $hook, array( $this, 'breadcrumbs' ), 10, 2 );
		}

		// Filter menu
		add_filter( "sl_menu_dropdown_$post_type", array( $this, 'menu_dropdown' ) );

		// Set meta title tag
		add_filter( 'sl_meta_title', array( $this, 'meta_title' ), 10, 2 );

		// Set meta title as heading in featured title area
		add_filter( 'sl_featured_title_title', array( $this, 'meta_title' ) );

		// Set sub title in featured title area
		add_filter( 'sl_featured_title_subtitle', array( $this, 'sub_title' ) );

		// Show hidden entry meta
		add_action( "sl_singular-{$post_type}_entry_top", array( $this, 'hidden_entry_meta' ) );

		// Show booking button at the bottom of content when there's only 1 booking resource
		add_action( "sl_singular-{$post_type}_entry_content_bottom", array( $this, 'single_booking_button' ) );

		/**
		 * Handle things in featured title area
		 *
		 * @since 5.0.8
		 */
		$class = 'Sl_' . ucfirst( $post_type ) . '_Featured_Title';
		if ( ! class_exists( $class ) )
		{
			$class = 'Sl_Core_Featured_Title';
		}
		new $class( $post_type );
	}

	/**
	 * Enqueue scripts for homepage
	 *
	 * @return array
	 */
	function enqueue_scripts()
	{
		if ( is_front_page() && 'posts' == get_option( 'show_on_front' ) && sl_setting( "homepage_{$this->post_type}_featured_active" ) )
		{
			wp_enqueue_script( 'jquery-cycle2' );
		}
	}

	/**
	 * Add more param to JS object in frontend
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function js_params( $params )
	{
		return $params;
	}

	/**
	 * Show homepage modules
	 *
	 * @return void
	 */
	function show_homepage_modules()
	{
		if ( ! is_home() && ! is_front_page() )
		{
			return;
		}

		locate_template( "templates/{$this->post_type}/home-modules.php", true );
		if ( function_exists( "sl_homepage_show_{$this->post_type}_modules" ) )
		{
			add_action( 'sl_homepage_show_module', "sl_homepage_show_{$this->post_type}_modules", 10, 1 );
		}
	}

	/**
	 * Show booking button for a booking resource
	 *
	 * @param string $output   Button markup
	 * @param array  $resource Resource params
	 *
	 * @return string
	 */
	function booking_button( $output, $resource )
	{
		global $post;

		if ( $this->post_type != $post->post_type || ! sl_setting( $post->post_type . '_booking' ) )
		{
			return $output;
		}

		$resource_slug = sanitize_title( $resource['title'] );
		$booking_url   = home_url( "book/{$post->post_name}/{$resource_slug}/" );

		$output = sprintf(
			'<a class="button booking" href="%s">%s</a>',
			$booking_url, __( 'Book', '7listings' )
		);

		return $output;
	}

	/**
	 * Set meta title tag
	 * Also used to set heading (title) in featured title area
	 *
	 * @param string $title
	 * @param string $sep
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	function meta_title( $title = '', $sep = '' )
	{
		if (
			( ! is_singular( $this->post_type ) )
			&& ! is_post_type_archive( $this->post_type )
			&& ! is_tax( sl_meta_key( 'tax_type', $this->post_type ) )
		)
		{
			return $title;
		}

		$replacement = array(
			'%SEP%'     => $sep,
			'%LABEL%'   => sl_setting( $this->post_type . '_label' ),
			'%CITY%'    => sl_setting( 'general_city' ),
			'%STATE%'   => sl_setting( 'state' ),
			'%COUNTRY%' => sl_setting( 'country' ),
		);

		if ( is_post_type_archive( $this->post_type ) )
		{
			$title = sl_setting( $this->post_type . '_archive_main_title' );
		}
		elseif ( is_tax( sl_meta_key( 'tax_type', $this->post_type ) ) )
		{
			$title                     = sl_setting( $this->post_type . '_archive_cat_title' );
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
		elseif ( is_singular( $this->post_type ) )
		{
			$title                         = sl_setting( $this->post_type . '_single_title' );
			$post_title                    = get_the_title();
			$replacement['%LISTING_NAME%'] = ( get_query_var( 'book', false ) || get_query_var( 'book_bundle', false ) ) ? sprintf( __( 'Book %s', '7listings' ), $post_title ) : $post_title;
		}

		$title = strtr( $title, $replacement );

		return $title;
	}

	/**
	 * Breadcrumb item for single page
	 *
	 * @param  array  $item Array of item URL and title
	 * @param  string $post_type
	 *
	 * @return string
	 */
	public function breadcrumbs( $item, $post_type )
	{
		if ( $post_type != $this->post_type )
		{
			return $item;
		}

		$link = get_post_type_archive_link( $post_type );
		if ( strpos( $link, '?' ) )
		{
			$link = home_url( sl_setting( $post_type . '_base_url' ) . '/' );
		}

		$post_type_object = get_post_type_object( $post_type );
		return array( $link, $post_type_object->label );
	}

	/**
	 * Filter posts by alphabet
	 *
	 * @param string $where
	 *
	 * @return string
	 */
	static function filter_by_alphabet( $where )
	{
		if ( ! isset( $_GET['start'] ) || ! preg_match( '#^[a-z]$#', $_GET['start'] ) )
			return $where;

		$start = $_GET['start'];
		$where .= " AND LOWER(SUBSTR(post_title,1,1)) = '$start' ";

		return $where;
	}

	/**
	 * Display hidden entry meta for listings
	 * @return void
	 */
	public function hidden_entry_meta()
	{
		// Entry title
		echo '<span class="hidden entry-title">', get_the_title(), '</span>';

		// Offer price
		$price = get_post_meta( get_the_ID(), 'price_from', true );
		if ( $price = peace_filters( 'entry_price', $price ) )
		{
			echo '<span class="hidden" itemprop="offers" itemscope itemtype="http://schema.org/Offer"><span itemprop="price" content="$', $price, '"></span></span>';
		}
	}

	/**
	 * Show booking button at the bottom of content when there's only 1 booking resource
	 *
	 * @return void
	 */
	function single_booking_button()
	{
		$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', $this->post_type ), true );

		// Show price only if there's only one booking resource
		if ( empty( $resources ) || 1 != count( $resources ) )
		{
			return;
		}

		$resource    = current( $resources );
		$book_button = apply_filters( 'booking_button', '', $resource );

		if ( $book_button )
		{
			echo "<p>$book_button</p>";
		}
	}

	/**
	 * Add filter menu dropdown for modules
	 * This need to be overwritten in each module to display its taxonomy items
	 *
	 * @param string $ul HTML markup for dropdown menu for this module
	 *
	 * @return string
	 */
	function menu_dropdown( $ul )
	{
		return $ul;
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
		if ( is_post_type_archive( $this->post_type ) && sl_setting( $this->post_type . '_archive_desc_enable' ) )
		{
			$subtitle = sl_setting( $this->post_type . '_archive_main_description' );
		}

		return $subtitle;
	}

	/**
	 * Get parameters for booking page
	 *
	 * @return array
	 */
	public static function get_booked_params()
	{
		// Booked data. Empty for normal booking but will be set for cart booking as following
		return array(
			'guests'           => array(),
			'upsells'          => array(),
			'customer_message' => '',
			'payment_gateway'  => '',
			'amount'           => '',
		);
	}

	/**
	 * Get parameters for booking page
	 *
	 * @return array
	 */
	public static function get_booking_params()
	{
		$params = array(
			'resource'  => $GLOBALS['resource'],
			'resources' => $GLOBALS['resources'],
			'is_cart'   => false,
		);

		// Saved booking data. Empty for normal booking but will be set for cart booking as following
		$data = call_user_func( array( 'Sl_' . ucfirst( get_post_type() ) . '_Frontend', 'get_booked_params' ) );

		// Additional variables for cart booking page
		if ( isset( $_GET['cart'] ) )
		{
			$params['is_cart'] = true;

			// Get booked information
			$cart  = Sl_Cart::get_instance();
			$index = $cart->find_product_in_cart( get_the_ID(), $params['resource']['resource_id'] );

			if ( - 1 != $index )
			{
				$cart_content = $cart->get_cart();
				if ( isset( $cart_content[$index]['data'] ) )
				{
					$data = $cart_content[$index]['data'];
				}
			}
		}

		// Set booking data
		$params['data'] = $data;

		return $params;
	}

	/**
	 * Reset tax query
	 *
	 * @param $query
	 */
	public static function  set_tax_query( $query )
	{
		sl_build_query_args( $query_args, $GLOBALS['sl_list_args'] );

		$query->set( 'tax_query', $query_args['tax_query'] );
	}
}
