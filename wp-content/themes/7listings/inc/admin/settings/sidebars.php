<?php

class Sl_Settings_Sidebars extends Sl_Settings_Page
{
	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'sl-sidebars', THEME_JS . 'admin/sidebars.js', array( 'jquery' ) );

		$params = array(
			'nonceDelete' => wp_create_nonce( 'delete-sidebar' ),
		);
		wp_localize_script( 'sl-sidebars', 'SlSidebar', $params );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		include THEME_TABS . 'settings/sidebars.php';
	}

	/**
	 * Sanitize options
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		if ( empty( $options['new_sidebar'] ) )
			return $options_new;

		// Don't save 'new_sidebar' field
		unset( $options_new['new_sidebar'] );
		$sidebars = empty( $options_new['sidebars'] ) || ! is_array( $options_new['sidebars'] ) ? array() : $options_new['sidebars'];

		$sidebars[] = wp_strip_all_tags( $options['new_sidebar'] );
		$sidebars   = array_unique( $sidebars );

		$options_new['sidebars'] = $sidebars;

		return $options_new;
	}
}

new Sl_Settings_Sidebars( 'sidebars', __( 'Sidebars', '7listings' ), 'themes.php' );
