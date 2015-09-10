<?php

/**
 * This class handles common things for module for #featured title area
 * such as output logo, rating, map
 */
class Sl_Core_Featured_Title
{
	/**
	 * Prefix for all hooks
	 * @var string
	 */
	public static $prefix = 'sl_featured_title_';

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
	 * @return Sl_Core_Featured_Title
	 * @since 5.0.8
	 */
	public function __construct( $post_type )
	{
		$this->post_type = $post_type;

		// Hook to 'template_redirect' to make sure all WordPress functions ready
		add_action( 'template_redirect', array( $this, 'run' ) );
	}

	/**
	 * Add hooks for featured title
	 * Must be hooked in 'template_redirect' to make sure all WordPress functions ready
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function run()
	{
		if ( ! is_singular( $this->post_type ) && $this->post_type != sl_is_listing_archive() )
			return;

		if ( is_singular() )
			$this->singular();
		else
			$this->archive();
	}

	/**
	 * Add hooks for featured title for singular pages
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function singular()
	{
		// Show listing logo only if map is off
		if ( ! sl_setting( $this->post_type . '_single_featured_title_map' ) )
			add_action( self::$prefix . 'top', array( $this, 'single_logo' ) );

		// Show div.featured and div.featured.star for ribbon
		add_action( self::$prefix . 'top', array( $this, 'single_ribbon' ) );

		// Show overall rating
		add_action( self::$prefix . 'bottom', array( $this, 'single_overall_rating' ) );

		// Show location in featured title area
		add_action( self::$prefix . 'bottom', array( $this, 'single_location' ) );

		// Show listing price if there's only 1 booking resource
		// Because we hide booking resources if there's only one, and there's no places to show the price
		add_action( self::$prefix . 'bottom', array( $this, 'single_price' ) );

		// Show booking button
		add_action( self::$prefix . 'bottom', array( $this, 'single_booking_button' ) );

		// Show single listing featured image as background
		if ( sl_setting( $this->post_type . '_single_featured_title_image' ) )
			add_filter( self::$prefix . 'style', array( $this, 'single_background' ) );

		// Show map for single page
		if ( sl_setting( $this->post_type . '_single_featured_title_map' ) )
		{
			add_filter( self::$prefix . 'after', array( $this, 'single_map' ) );
			add_filter( self::$prefix . 'class', array( $this, 'single_map' ) );
		}

		// Add more classes to #featured title area
		add_filter( self::$prefix . 'class', array( $this, 'single_class' ) );

		// Add div wrapper
		add_action( self::$prefix . 'top', array( $this, 'single_wrapper' ), 1 );
		add_action( self::$prefix . 'bottom', array( $this, 'single_wrapper' ), 100 );
	}

	/**
	 * Add hooks for featured title for archive pages
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function archive()
	{
		// Show term image
		if ( is_tax() && sl_setting( $this->post_type . '_archive_cat_image' ) && ! sl_setting( $this->post_type . '_archive_map' ) )
		{
			$image_type = sl_setting( $this->post_type . '_archive_cat_image_type' );
			if ( 'background' == $image_type )
				add_filter( self::$prefix . 'style', array( $this, 'archive_background' ) );
			elseif ( 'thumbnail' == $image_type )
				add_action( self::$prefix . 'top', array( $this, 'archive_logo' ) );
		}

		// Show map for archive page
		if ( sl_setting( $this->post_type . '_archive_map' ) )
		{
			add_filter( self::$prefix . 'after', array( $this, 'archive_map' ) );
			add_filter( self::$prefix . 'class', array( $this, 'archive_map' ) );
		}

		// Add more classes to #featured title area
		add_filter( self::$prefix . 'class', array( $this, 'archive_class' ) );

		// Show search widget
		if ( sl_setting( $this->post_type . '_archive_search_widget' ) )
		{
			add_filter( self::$prefix . 'bottom', array( $this, 'archive_search_widget' ) );
			add_filter( self::$prefix . 'class', array( $this, 'archive_search_widget' ) );
		}
	}

	/**
	 * Show listing logo
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function single_logo()
	{
		get_template_part( 'templates/parts/logo' );
	}

	/**
	 * Show div.featured and div.featured.star
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function single_ribbon()
	{
		get_template_part( 'templates/parts/featured-ribbon' );
	}

	/**
	 * Show  min price  of the booking resources.
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function single_price()
	{
		echo sl_listing_element( 'price' );
	}

	/**
	 * Show  booking button.
	 *
	 * @return void
	 * @since 5.5.2
	 */
	public function single_booking_button()
	{
		echo sl_listing_element( 'booking', array( 'post_type' => $this->post_type ) );
	}

	/**
	 * Show overall rating
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function single_overall_rating()
	{
		list( $count, $average ) = sl_get_average_rating();
		if ( ! $count )
			return;

		sl_star_rating( $average, array(
			'count' => $count,
			'item'  => get_the_title(),
		) );
	}

	/**
	 * Show company meta in featured title area
	 *
	 * @return void
	 */
	public function single_location()
	{
		$city  = get_post_meta( get_the_ID(), 'city', true );
		$state = get_post_meta( get_the_ID(), 'state', true );
		if ( is_numeric( $city ) )
		{
			$city = get_term( $city, 'location' );
			$city = is_wp_error( $city ) || empty( $city ) ? '' : $city->name;
		}
		if ( is_numeric( $state ) )
		{
			$state = get_term( $state, 'location' );
			$state = is_wp_error( $state ) || empty( $state ) ? '' : $state->name;
		}
		if ( $city && $state )
			echo "<h3 class='location'>$city, $state</h3>";
	}

	/**
	 * Add more classes to featured title area
	 *
	 * @param array $class
	 *
	 * @return array
	 * @since 5.0.8
	 */
	public function single_class( $class )
	{
		// Add .featured or .star
		if ( $featured = intval( get_post_meta( get_the_ID(), 'featured', true ) ) )
			$class[] = 1 == $featured ? 'featured' : 'star';

		// Add height class
		$height = sl_setting( $this->post_type . '_single_featured_title_height' );
		if ( $height && 'medium' != $height )
			$class[] = $height;

		return $class;
	}

	/**
	 * Add background style for featured title area
	 *
	 * @param string $style Inline styles for featured title area
	 *
	 * @return string
	 * @since 5.0.9
	 */
	public function single_background( $style )
	{
		if ( $image = sl_broadcasted_image_src( '_thumbnail_id', sl_setting( get_post_type() . '_single_featured_title_image_size' ) ) )
			$style .= 'background-image: url(' . $image . ');';

		return $style;
	}

	/**
	 * Show map in featured title area for single page
	 * This public function also adds class .map to featured title area
	 *
	 * The behaviour depends on current hook
	 *
	 * @param array $class
	 *
	 * @return void|array
	 * @since 5.0.8
	 */
	public function single_map( $class = array() )
	{
		if ( current_filter() == self::$prefix . 'class' )
		{
			$class[] = 'map';
		}
		else
		{
			get_template_part( 'templates/parts/map' );
		}
		return $class;
	}

	/**
	 * Add div wrapper for company info in featured title area
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function single_wrapper()
	{
		echo current_filter() == self::$prefix . 'top' ? '<div class="listing-badge">' : '</div>';
	}

	/**
	 * Show map in featured title area for archive page
	 * This public function also adds class .map to featured title area
	 *
	 * The behaviour depends on current hook
	 *
	 * @param array $class
	 *
	 * @return array
	 * @since 5.0.8
	 */
	public function archive_map( $class = array() )
	{
		if ( current_filter() == self::$prefix . 'after' )
		{
			echo "<div class='google-map' data-connect_to='#option_map'></div>";
		}
		else
		{
			$class[] = 'map';
		}

		return $class;
	}

	/**
	 * Add background style for featured title area
	 *
	 * @param string $style
	 *
	 * @return string
	 * @since 5.0.8
	 */
	public function archive_background( $style )
	{
		$term = get_queried_object();
		$logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
		if ( $logo )
		{
			list( $src ) = wp_get_attachment_image_src( $logo, sl_setting( get_post_type() . '_archive_cat_image_size' ) );
			$style .= 'background-image: url(' . $src . ');';
		}

		return $style;
	}

	/**
	 * Add term logo for featured title area
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function archive_logo()
	{
		$term = get_queried_object();
		$logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
		if ( $logo )
		{
			list( $src ) = wp_get_attachment_image_src( $logo, sl_setting( get_post_type() . '_archive_cat_image_size' ) );
			echo '<img src="' . $src . '" class="brand-logo ">';
		}
	}

	/**
	 * Add 'height' class to featured title area in archive page
	 *
	 * @param array $class
	 *
	 * @return array
	 * @since 5.0.8
	 */
	public function archive_class( $class )
	{
		$height = sl_setting( $this->post_type . '_archive_featured_title_height' );
		if ( $height && 'medium' != $height )
			$class[] = $height;

		return $class;
	}

	/**
	 * Show search widget in featured title area for archive page
	 * This public function also adds class .search-widget to featured title area
	 * The behaviour depends on current hook
	 *
	 * The function uses similar code as in FitWP Widget Shortcode plugin to output the widget
	 *
	 * @see   plugin FitWP Widget Shortcode
	 *
	 * @param array $class
	 *
	 * @return array|void
	 * @since 5.3
	 */
	public function archive_search_widget( $class = array() )
	{
		if ( current_filter() == self::$prefix . 'class' )
		{
			$class[] = 'search-widget';
			return $class;
		}

		$prefix   = $this->post_type . '_archive_search_widget_';
		$instance = array(
			'title'       => sl_setting( $prefix . 'title' ),
			'post_type'   => $this->post_type,
			'keyword'     => sl_setting( $prefix . 'keyword' ),
			'location'    => sl_setting( $prefix . 'location' ),
			'type'        => sl_setting( $prefix . 'type' ),
			'feature'     => sl_setting( $prefix . 'feature' ),
			'date'        => sl_setting( $prefix . 'date' ),
			'rating'      => sl_setting( $prefix . 'rating' ),
			'star_rating' => sl_setting( $prefix . 'star_rating' ),
		);

		// Setup widget parameters to display in the frontend
		$args = array(
			'before_widget' => '<aside class="widget listing-search">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		the_widget( 'Sl_Widget_Search', $instance, $args );

		return $class;
	}
}
