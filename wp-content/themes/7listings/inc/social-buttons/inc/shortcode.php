<?php

/**
 * This class adds [social_links] shortcode
 * The shortcode can take settings from theme settings page 7Listings > Media
 * or use custom settings
 */
class Sl_Social_Buttons_Shortcode
{
	/**
	 * Add hooks to frontend to output social buttons and links
	 * Must use 'template_redirect' to make sure we can use theme function (sl_setting) and other conditions
	 *
	 * @return Sl_Social_Buttons_Shortcode
	 */
	public function __construct()
	{
		add_shortcode( 'social_links', array( $this, 'shortcode' ) );
	}

	/**
	 * Render shortcode [social_links]
	 *
	 * @param array  $atts    Shortcode attributes, can take the following attributes
	 *                        - `counter`: Show counter for fans, followers, etc.
	 *                        - `class`: Additional CSS classes for custom styling
	 *                        - `size`: Icon size
	 *                        - List of custom social profile URLs
	 *
	 * @param string $content Shortcode content. Never use it
	 *
	 * @return string
	 */
	public function shortcode( $atts = array(), $content = '' )
	{
		$atts = shortcode_atts( array(
			'counter'    => 0, // Whether show counter for likes, followers
			'class'      => '', // Additional CSS classes for custom styling
			'size'       => '', // Icon size, can be 'small', 'medium' (or empty - default), 'large'

			// Social networks supported
			'facebook'   => '',
			'twitter'    => '',
			'googleplus' => '',
			'pinterest'  => '',
			'linkedin'   => '',
			'instagram'  => '',
			'rss'        => '',
		), $atts );

		// Generate links to social profiles
		$output = $this->social_links( $atts['counter'], $atts );

		if ( ! $output )
			return '';

		$classes = 'social-media links';
		if ( $atts['counter'] )
			$classes .= ' counters';
		if ( $atts['size'] )
			$classes .= ' ' . $atts['size'];
		if ( $atts['class'] )
			$classes .= ' ' . $atts['class'];

		return '<div class="' . esc_attr( $classes ) . '">' . $output . '</div>';
	}

	/**
	 * Output social links
	 *
	 * @param bool  $counter Whether display counter of followers, likes for social profile
	 * @param array $links   List of profile links. If omitted, use profile links from settings page 7Listings -> Social Media
	 *
	 * @return string
	 */
	public function social_links( $counter = false, $links = array() )
	{
		$networks = array(
			'facebook'   => __( 'Like us on Facebook', '7listings' ),
			'twitter'    => __( 'Follow us on Twitter', '7listings' ),
			'googleplus' => __( 'Join our Google+ Circle', '7listings' ),
			'pinterest'  => __( 'Pinterest', '7listings' ),
			'linkedin'   => __( 'Visit us on LinkedIn', '7listings' ),
			'instagram'  => __( 'Instagram', '7listings' ),
			'rss'        => __( 'Subscribe to our RSS', '7listings' ),
		);

		// Sanitize links, make sure it contains supported networks only with not empty values
		foreach ( $links as $k => $v )
		{
			if ( ! isset( $networks[$k] ) || ! $v )
				unset( $links[$k] );
		}

		$output = '';
		foreach ( $networks as $network => $title )
		{
			// Get custom link if it presents
			if ( $links )
			{
				if ( empty( $links[$network] ) )
					continue;
				$url = $links[$network];
			}
			// If no custom links are provided, use links from settings page
			else
			{
				if ( ! sl_setting( $network ) )
					continue;
				$url = sl_setting( $network );
			}

			$counter_text = '';
			$total        = 0;
			if ( $counter )
			{
				// List of reference to method name and title attribute of span tag for each network
				$ref = array(
					'facebook'   => array( 'facebook_page', __( '%d Facebook likes', '7listings' ) ),
					'twitter'    => array( 'twitter_followers', __( '%d Twitter followers', '7listings' ) ),
					'googleplus' => array( 'google_followers', __( '%d Google +1s', '7listings' ) ),
					'pinterest'  => array( 'pinterest_followers', __( '%d Pinterest followers', '7listings' ) ),
					'instagram'  => array( 'instagram_followers', __( '%d Instagram followers', '7listings' ) ),
				);
				if ( isset( $ref[$network] ) )
				{
					$total = Sl_Social_Buttons_Counter::get( $ref[$network][0], $url );

					if ( 0 == $total )
						$counter_text = '<span class="number">-</span>';
					else
						$counter_text = '<span class="number" title="' . sprintf( $ref[$network][1], $total ) . '">' . $this->format_number( $total ) . '</span> ';
				}
				else
				{

					// Just display '0' in case there is no counter callback, to make it display consistently
					$counter_text = '<span class="number">-</span>';
				}
				$counter_text = '<span class="counter">' . $counter_text . '</span>';
			}
			// Title in Social Media
			if ( 'facebook' == $network )
				$title = $this->format_number( $total ) . __( ' people like us on Facebook', '7lisitings' );
			elseif ( 'twitter' == $network )
				$title = $this->format_number( $total ) . __( ' followers on Twitter', '7lisitings' );
			elseif ( 'googleplus' == $network )
				$title = $this->format_number( $total ) . __( ' followers on Google+', '7lisitings' );
			elseif ( 'pinterest' == $network )
				$title = $this->format_number( $total ) . __( ' followers on Pinterest', '7lisitings' );
			elseif ( 'linkedin' == $network )
				$title = $this->format_number( $total ) . __( ' followers on LinkedIn', '7lisitings' );
			elseif ( 'instagram' == $network )
				$title = $this->format_number( $total ) . __( ' followers on Instagram', '7lisitings' );
			elseif ( 'rss' == $network )
				$title = $this->format_number( $total ) . __( ' subscribers to RSS feed', '7lisitings' );

			$output .= sprintf(
				'<a class="%s" href="%s" rel="%s" target="_blank" title="%s">%s</a>',
				esc_attr( $network ),
				esc_url( Sl_Social_Buttons_Frontend::sanitize_url( $url, $network ) ),
				'googleplus' == $network ? 'publisher' : 'nofollow',
				esc_attr( $title ),
				$counter_text
			);
		}

		return $output;
	}

	/**
	 * Format big counter number
	 * - If 9999 < number < 1m, then show in format 20k or 10.5k with 1 decimal if possible
	 * - If 1m < number, show 2m or 1.2m with 1 decimal if possible
	 *
	 * @param int $number
	 *
	 * @return string
	 */
	public function format_number( $number )
	{
		$number = absint( $number );
		if ( $number < 10000 )
			return $number;

		// Adding 'k'
		if ( $number < 1000000 )
			return number_format_i18n( $number / 1000, 1 ) . 'k';

		// Adding 'm'
		return number_format_i18n( $number / 1000000, 1 ) . 'm';
	}
}
