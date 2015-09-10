<?php

/**
 * This class will hold all settings for accommodation
 */
class Sl_Core_Homepage
{
	/**
	 * @var string Post type: used for post type slug and some checks (prefix or suffix)
	 */
	public $post_type;

	/**
	 * @var array Array of widgets
	 */
	public $widgets;

	/**
	 * Constructor
	 *
	 * Add hooks
	 */
	function __construct( $post_type, $widgets )
	{
		$this->post_type = $post_type;

		// Normalize widgets
		$this->widgets = $widgets;
		foreach ( $this->widgets as $k => $v )
		{
			$this->widgets[$k] = $this->post_type . '_' . $v;
		}

		add_action( 'sl_homepage_settings_box', array( $this, 'show' ), 10, 1 );
		add_filter( 'sl_homepage_settings_sanitize', array( $this, 'sanitize' ), 10, 3 );
	}

	/**
	 * Show settings field
	 *
	 * @param $id
	 *
	 * @return void
	 */
	function show( $id )
	{
		foreach ( $this->widgets as $widget )
		{
			if ( $id != $widget )
				continue;
			$file = THEME_TABS . $this->post_type . '/homepage.php';
			if ( ! file_exists( $file ) )
				continue;
			include $file;

			return;
		}
	}

	/**
	 * Sanitize settings
	 *
	 * @param array $options_new Submitted options
	 * @param array $options     Saved options
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		// Add widgets if they're missed
		foreach ( $this->widgets as $widget )
		{
			if ( ! in_array( $widget, $options_new['homepage_order'] ) )
				$options_new['homepage_order'][] = $widget;
		}

		// Checkboxes
		$checkboxes = array(
			'homepage_' . $this->post_type . '_types_image',
			'homepage_' . $this->post_type . '_types_desc',
			'homepage_' . $this->post_type . '_listings_priority',
		);
		foreach ( $this->widgets as $widget )
		{
			$checkboxes[] = 'homepage_' . $widget . '_active';
		}

		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $checkboxes );
	}
}
