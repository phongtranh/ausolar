<?php

/**
 * This class outputs social buttons, social links as well as enqueue needed Javascript
 */
class Sl_Social_Buttons_Frontend
{
	/**
	 * @var bool Check if JS is enqueued
	 */
	public $enqueued = false;

	/**
	 * Add hooks to frontend to output social buttons and links
	 * Must use 'template_redirect' to make sure we can use theme function (sl_setting) and other conditions
	 *
	 * @return Sl_Social_Buttons_Frontend
	 */
	function __construct()
	{
		add_action( 'template_redirect', array( $this, 'output' ) );
	}

	/**
	 * Add hooks to show social buttons for posts and social links for header and contact us page
	 *
	 * @return void
	 */
	function output()
	{
		// Show social buttons for posts
		$hooks = array( 'the_content', 'sl_archive_page', 'sl_archive_post' );
		foreach ( $hooks as $hook )
		{
			add_filter( $hook, array( $this, 'buttons' ) );
		}

		// Social links for header
		if ( sl_setting( 'design_header_social_display' ) )
		{
			add_action( 'sl_header_bottom', array( $this, 'header_social_links' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );
		}

		// Social links in contact page
		if ( sl_setting( 'contact_social_links' ) )
			add_action( 'sl_contact_page_before_map', array( $this, 'contact_page_social_links' ) );

		// Author details box
		if ( is_single() )
			add_action( 'sl_author_details', array( $this, 'author_social_links' ) );

		// Social type
		add_action( 'wp_footer', array( $this, 'knowledge_graph' ) );

	}

	/**
	 * Add Social Buttons to post content
	 * Adds a like button above the post, below the post, or both above and below the post depending on stored preferences.
	 *
	 * @param string $content Existing content
	 *
	 * @return string Passed content with Social Buttons markup prepended, appended, or both.
	 */
	function buttons( $content )
	{
		global $post;

		if ( class_exists( 'Sl_Product_Frontend' ) && in_array( $post->ID, Sl_Product_Frontend::woocommerce_page_ids() ) )
			return $content;

		if ( is_feed() )
			return $content;

		if ( ! $this->show() )
			return $content;

		if ( sl_setting( 'design_social_icon_counter' ) )
			$this->enqueue_scripts();

		$link     = is_home() ? HOME_URL : get_permalink();
		$img_link = wp_get_attachment_url( get_post_thumbnail_id() );
		$text     = is_home() ? get_bloginfo( 'name' ) : the_title_attribute( 'echo=0' );

		if ( 'sl_archive_page' == current_filter() )
		{
			global $wp;
			$link = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
			$text = wp_title( '|', false );
		}

		$classes = 'social-media share buttons';
		if ( sl_setting( 'design_social_icon_counter' ) )
			$classes .= ' counters';
		if ( sl_setting( 'design_social_icon_color_scheme' ) )
			$classes .= ' ' . sl_setting( 'design_social_icon_color_scheme' );
		$buttons = '<div class="' . $classes . '" data-url="' . $link . '">';

		$options = sl_setting( 'social_buttons' );
		foreach ( $options['buttons'] as $button )
		{
			$buttons .= call_user_func( array( 'Sl_Social_Buttons_Share_Buttons', $button ), $link, $text, $img_link );
		}

		$buttons .= '</div>';

		switch ( $options['position'] )
		{
			case 'top':
				return $buttons . $content;
			case 'bottom':
				return $content . $buttons;
			case 'both':
				if ( 'the_content' == current_filter() )
					return $buttons . $content . $buttons;

				return $buttons;
		}

		return $content;
	}

	/**
	 * Check if social buttons are needed
	 *
	 * @return bool
	 */
	function show()
	{
		$show    = true;
		$options = sl_setting( 'social_buttons' );

		// No options
		if ( empty( $options['buttons'] ) || empty( $options['show_on'] ) || empty( $options['position'] ) )
		{
			$show = false;
		}
		// Front page
		elseif ( is_front_page() && ! in_array( 'home', $options['show_on'] ) )
		{
			$show = false;
		}

		else
		{
			switch ( current_filter() )
			{
				// Singular page
				case 'the_content':
					$show = ( is_front_page() && in_array( 'home', $options['show_on'] ) ) || ( is_singular() && in_array( get_post_type(), $options['show_on'] ) );
					break;

				// Show in featured title area for archive page
				case 'sl_archive_page':
					$show = is_archive() && in_array( 'archive_page', $options['show_on'] );
					break;

				// Post archive page: show social buttons after post content
				case 'sl_archive_post':
					$show = is_archive() && in_array( 'archive', $options['show_on'] );
			}
		}

		return apply_filters( 'sl_social_buttons', $show );
	}

	/**
	 * Enqueue scripts for social buttons
	 *
	 * @return void
	 */
	function enqueue_scripts()
	{
		if ( $this->enqueued )
			return;

		$options = sl_setting( 'social_buttons' );
		wp_enqueue_script( 'sl-social-buttons', THEME_JS . 'social-buttons.js', '', '', true );
		$params = array(
			'buttons' => $options['buttons'],
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'get-counter' ),
		);
		wp_localize_script( 'sl-social-buttons', 'SlSocialButtons', $params );
		$this->enqueued = true;
	}

	/**
	 * Show social links in header
	 *
	 * @return void
	 */
	function header_social_links()
	{
		$counter = intval( sl_setting( 'design_header_social_counter' ) );
		$class   = 'header';
		if ( sl_setting( 'design_header_social_color_scheme' ) )
			$class .= ' ' . sl_setting( 'design_header_social_color_scheme' );
		echo do_shortcode( '[social_links counter="' . $counter . '" class="' . $class . '"]' );
	}

	/**
	 * Add CSS class for social links in body tag
	 *
	 * @param string $classes
	 *
	 * @return array Array of classes
	 */
	function body_class( $classes )
	{
		$classes[] = 'social';

		return $classes;
	}

	/**
	 * Show social links in contact page
	 *
	 * @return void
	 */
	function contact_page_social_links()
	{
		$counter = intval( sl_setting( 'contact_social_counter' ) );
		$class   = 'contact';
		if ( sl_setting( 'design_contact_social_color_scheme' ) )
			$class .= ' ' . sl_setting( 'design_contact_social_color_scheme' );

		$output = do_shortcode( '[social_links counter="' . $counter . '" class="' . $class . '"]' );
		if ( ! $output )
			return;

		echo '<h2>' . __( 'Connect With Us', '7listings' ) . '</h2>';
		echo $output;
	}

	/**
	 * Show social links in author details box
	 *
	 * @return void
	 */
	function author_social_links()
	{
		$classes = 'author';
		if ( sl_setting( 'design_social_icon_color_scheme' ) )
			$classes .= ' ' . sl_setting( 'design_social_icon_color_scheme' );
		$shortcode = '[social_links class="' . $classes . '"';
		$networks  = array(
			'facebook',
			'twitter',
			'googleplus',
			'pinterest',
			'linkedin',
			'instagram',
			'rss',
		);
		$has_link  = false;
		foreach ( $networks as $network )
		{
			if ( $link = get_the_author_meta( $network ) )
			{
				$shortcode .= ' ' . $network . '="' . $link . '"';
				$has_link = true;
			}
		}
		$shortcode .= ']';
		if ( $has_link )
			echo do_shortcode( $shortcode );
	}

	/**
	 * Output js for knowledge graph
	 *
	 * @return void
	 */
	function knowledge_graph()
	{
		$knowledge_graph = array(
			'@context' => 'http://schema.org',
			'@type'    => 'Organization',
			'name'     => get_bloginfo( 'name' ),
			'url'      => HOME_URL,
		);

		// Type
		if ( 'person' == sl_setting( 'knowledge_graph_type' ) )
		{
			$knowledge_graph['@type'] = 'Person';
		}

		// Logo
		if ( sl_setting( 'knowledge_graph_logo' ) && 'Organization' == $knowledge_graph['@type'] )
		{
			list( $logo_url ) = wp_get_attachment_image_src( sl_setting( 'knowledge_graph_logo' ), 'full' );
			$knowledge_graph['logo'] = $logo_url;
		}

		// sameAs
		$socials = array( 'facebook', 'twitter', 'googleplus', 'pinterest', 'linkedin', 'instagram', 'rss' );
		$same_as = array();
		foreach ( $socials as $social )
		{
			if ( sl_setting( $social ) )
			{
				$same_as[] = esc_url( self::sanitize_url( sl_setting( $social ), $social ) );
			}
		}
		if ( $same_as )
		{
			$knowledge_graph['sameAs'] = $same_as;
		}

		// Contact point
		$contact_points = array();
		foreach ( (array) sl_setting( 'knowledge_graph_contact_points' ) as $data )
		{
			if ( empty( $data['phone'] ) )
				continue;

			$contact_point = array(
				'@type'       => 'ContactPoint',
				'telephone'   => $data['phone'],
				'contactType' => $data['type'] ? $data['type'] : 'customer support',
			);

			// Option
			if ( ! empty( $data['option'] ) )
			{
				$contact_point['contactOption'] = $data['option'];
			}

			// Area
			if ( ! empty( $data['area'] ) )
			{
				$contact_point['areaServed'] = $data['area'];
			}

			// Language
			if ( ! empty( $data['language'] ) )
			{
				$contact_point['availableLanguage'] = array_map( 'trim', explode( ',', $data['language'] ) );
			}

			$contact_points[] = $contact_point;
		}
		if ( $contact_points )
		{
			$knowledge_graph['contactPoint'] = $contact_points;
		}

		echo '<script type="application/ld+json">', json_encode( $knowledge_graph ), '</script>';
	}

	/**
	 * Sanitize URL, making them always correct even if user entered only username (like for Twitter)
	 *
	 * @param string $url     URL needs to sanitize
	 * @param string $network Social network, need to provided to get URL prefix (http://twitter.com for example)
	 *
	 * @return string Sanitized URL
	 */
	public static function sanitize_url( $url, $network )
	{
		// If param is a correct URL, do nothing
		if ( filter_var( $url, FILTER_VALIDATE_URL ) )
			return $url;

		switch ( $network )
		{
			case 'googleplus':
				$prefix = 'https://plus.google.com';
				break;
			case 'twitter':
				$prefix = 'https://' . $network . '.com';
				$url    = str_replace( '@', '', $url );
				break;
			case 'instagram':
			case 'rss':
				$prefix = 'http://' . $network . '.com';
				break;
			default:
				$prefix = 'https://' . $network . '.com';
		}

		return $prefix . '/' . ltrim( $url, '/' );
	}
}
