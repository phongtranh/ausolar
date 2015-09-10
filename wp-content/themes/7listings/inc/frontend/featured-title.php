<?php

/**
 * This class handle everything for #featured title area for special pages only
 * For other pages, see featured title class in each module
 */
class Sl_Featured_Title
{
	/**
	 * Prefix for all hooks
	 * @var string
	 */
	public static $prefix = 'sl_featured_title_';

	/**
	 * Class constructor
	 * @return Sl_Featured_Title
	 */
	function __construct()
	{
		add_filter( self::$prefix . 'title', array( $this, 'home' ) );
		add_filter( self::$prefix . 'subtitle', array( $this, 'home' ) );
		add_filter( self::$prefix . 'bottom', array( $this, 'home' ) );

		// Add search form to featured title for 404 page
		add_filter( 'sl_error-404_featured_title_bottom', array( $this, 'not_found' ) );
	}

	/**
	 * Display title and subtitle for 7lstings homepage
	 * Used only when the settings "Use 7listings homepage" is ON
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	function home( $text = '' )
	{
		if ( ! is_front_page() || ! sl_setting( 'homepage_enable' ) )
			return $text;

		switch ( current_filter() )
		{
			case self::$prefix . 'title':
				return sl_setting( 'homepage_featured_area_heading' );
			case self::$prefix . 'subtitle':
				return sl_setting( 'homepage_featured_area_custom_text' );
			case self::$prefix . 'bottom':
				if ( $slideshow = sl_setting( 'homepage_featured_area_slideshow' ) )
					echo do_shortcode( '[slideshow id="' . $slideshow . '"]' );
				break;
		}

		return $text;
	}

	/**
	 * Add search form to featured title for 404 page
	 *
	 * @since 5.2.1
	 * @return void
	 */
	function not_found()
	{
		get_template_part( 'templates/searchform-404' );
	}
}

new Sl_Featured_Title;
