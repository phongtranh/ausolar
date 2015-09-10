<?php
/**
 * Add shortcodes for listings slider, list
 */
class Sl_Compatibility_Shortcodes
{
	/**
	 * Constructor
	 *
	 * @return Sl_Compatibility_Shortcodes
	 */
	function __construct()
	{
		// Register shortcodes
		$shortcodes = array(
			'reviews',
			'locations',
			'product_brands',
		);
		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( $shortcode, array( $this, $shortcode ) );
		}

		// Sliders
		$shortcodes = array(
			'accommodation_slider',
			'tour_slider',
			'rental_slider',
			'product_slider',
			'post_slider',
			'company_slider',
		);
		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( $shortcode, array( $this, 'slider' ) );
		}

		// Post list
		$shortcodes = array(
			'accommodations',
			'tours',
			'rentals',
			'posts',
			'companies',
			'sl_products',
		);
		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( $shortcode, array( $this, 'post_list' ) );
		}
	}


	/**
	 * Show reviews shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function reviews( $atts, $content )
	{
		return sl_review_list( $atts );
	}

	/**
	 * Show locations shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function locations( $atts, $content )
	{
		ob_start();
		$atts['title'] = ''; // Don't use title in shortcode
		the_widget(
			'Sl_Widget_Locations',
			$atts,
			array(
				'before_widget' => '',
				'after_widget'  => '',
			)
		);

		return ob_get_clean();
	}

	/**
	 * Show product brands shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function product_brands( $atts, $content )
	{
		if ( ! post_type_exists( 'product' ) )
			return '';

		ob_start();
		$atts['title'] = ''; // Don't use title in shortcode
		the_widget(
			'Sl_Widget_Product_Brands',
			$atts,
			array(
				'before_widget' => '',
				'after_widget'  => '',
			)
		);

		return ob_get_clean();
	}

	/**
	 * Show slider shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 * @param string $tag
	 *
	 * @return string
	 */
	function slider( $atts, $content, $tag )
	{
		$atts  = array_merge( array(
			'post_title'  => 0,
			'date'        => 0,
			'star_rating' => 0,
			'rating'      => 0,
			'price'       => 0,
			'booking'     => 0,
			'excerpt'     => 0,
			'cart'        => 0,
		), $atts );
		$class = 'Sl_' . ucfirst( str_replace( '_slider', '', $tag ) ) . '_Frontend';

		if ( ! class_exists( $class ) )
			return '';

		return $class::slider( $atts );
	}

	/**
	 * Show list posts shortcodes
	 *
	 * @param array  $atts
	 * @param string $content
	 * @param string $tag
	 *
	 * @return string
	 */
	function post_list( $atts, $content, $tag )
	{
		$atts = array_merge( array(
			'thumbnail'           => 0,
			'star_rating'         => 0,
			'rating'              => 0,
			'price'               => 0,
			'booking'             => 0,
			'excerpt'             => 0,
			'date'                => 0,
			'cart'                => 0,
			'more_listings'       => 0,
			'more_listings_text'  => '',
			'more_listings_style' => '',
		), $atts );

		$tag   = str_replace( 'sl_', '', $tag );
		$class = 'Sl_' . ucfirst( substr( $tag, 0, - 1 ) ) . '_Frontend';

		if ( ! class_exists( $class ) )
			return '';

		return $class::post_list( $atts );
	}
}
