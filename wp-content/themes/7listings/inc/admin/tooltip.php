<?php

/**
 * This class adds tooltip shortcode and make it available everywhere in the admin
 *
 * @package Sl
 * @author  Tran Ngoc Tuan Anh <anh@7listings.net>
 */
class Sl_Tooltip
{
	/**
	 * Add hooks when class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_shortcode( 'tooltip', array( __CLASS__, 'render' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public static function enqueue()
	{
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
			wp_enqueue_style( 'sl-tooltip', THEME_LESS . 'admin/tooltip.less' );
		else
			wp_enqueue_style( 'sl-tooltip', THEME_CSS . 'admin/tooltip.css' );
		wp_enqueue_script( 'bootstrap-tooltip', THEME_JS . 'libs/bootstrap-tooltip.js', array( 'jquery' ), '2.3.2', true );
		wp_enqueue_script( 'sl-tooltip', THEME_JS . 'admin/tooltip.js', array( 'bootstrap-tooltip' ), '', true );
	}

	/**
	 * Show tooltip shortcode
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public static function render( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'content'   => '',
			'type'      => '',
			'placement' => '',
		), $atts );
		if ( ! $atts['content'] || ! $content )
			return '';

		return sprintf(
			'<a href="#" data-toggle="tooltip" data-html="true" class="sl-tooltip %s" data-placement="%s" title="%s">%s</a>',
			$atts['type'],
			$atts['placement'] ? $atts['placement'] : 'top',
			esc_attr( $atts['content'] ),
			do_shortcode( $content )
		);
	}
}
