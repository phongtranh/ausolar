<?php

/**
 * Main class for customizer
 *
 * @author Rilwis <anh@7listings.net>
 * @since  4.12.4
 */
class Sl_Customizer
{
	/**
	 * Class constructore
	 * @return Sl_Customizer
	 */
	function __construct()
	{
		add_action( 'customize_register', array( $this, 'register' ) );
	}

	/**
	 * Register customizer sections, controls, etc.
	 * @return void
	 */
	function register( $wp_customize )
	{
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
		$wp_customize->get_setting( 'background_color' )->transport = 'postMessage';

		$wp_customize->add_section( $this->section_id( 'background' ), array(
			'title'    => __( 'Background', '7listings' ),
			'priority' => 100,
		) );
		$wp_customize->add_setting( $this->settings_id( 'design_body_background' ), array(
			'default'    => '',
			'type'       => 'option',
			'capability' => 'edit_theme_options',
			'transport'  => 'postMessage',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'body_background', array(
			'label'    => __( 'Color', '7listings' ),
			'section'  => $this->section_id( 'background' ),
			'settings' => $this->settings_id( 'design_body_background' ),
		) ) );

		add_action( 'customize_preview_init', array( $this, 'script' ) );
	}

	/**
	 * Javascript for live preview
	 * @return void
	 */
	function script()
	{
		wp_enqueue_script( 'sl-customizer', THEME_URL . 'inc/customizer/live-preview.js', array( 'jquery', 'customize-preview' ), '', true );
	}

	/**
	 * Get section id
	 *
	 * @param  string $id Section ID
	 *
	 * @return string     Section ID with theme slug prefixed
	 */
	function section_id( $id )
	{
		return "7listings_$id";
	}

	/**
	 * Get settings id
	 *
	 * @param  string $id Settings ID
	 *
	 * @return string     Settings ID with theme slug prefixed
	 */
	function settings_id( $id )
	{
		return THEME_SETTINGS . "[$id]";
	}
}

new Sl_Customizer;
