<?php
add_action( 'init', 'sl_register_media' );

/**
 * Register scripts and styles
 *
 * @return void
 */
function sl_register_media()
{
	$cdn = '//cdn.jsdelivr.net/';

	wp_register_script( 'sl-utils', THEME_JS . 'utils.js', '', '', true );

	wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/base/jquery-ui.css' );
	wp_register_style( 'jquery-ui-timepicker', $cdn . 'jquery.ui.timepicker.addon/1.2/jquery-ui-timepicker-addon.css', array( 'jquery-ui' ) );

	wp_register_script( 'jquery-ui-timepicker', $cdn . 'jquery.ui.timepicker.addon/1.3.1/jquery-ui-timepicker-addon.min.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ), '', true );

	wp_register_style( 'select2', $cdn . 'select2/4.0.0/css/select2.min.css' );
	wp_register_script( 'select2', $cdn . 'select2/4.0.0/js/select2.min.js', array( 'jquery' ), '4.0.0', true );

	if ( ! is_admin() )
	{
		if ( false === strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) )
		{
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', $cdn . 'jquery/2.1.1/jquery.min.js', array(), '2.1.1', ! sl_setting( 'jquery_on_top' ) );

			wp_register_script( 'jquery-tablesorter', $cdn . 'tablesorter/2.11.1/js/jquery.tablesorter.min.js', array( 'jquery' ), '', true );
			wp_register_script( 'jquery-cycle2', $cdn . 'g/cycle2(jquery.cycle2.min.js+jquery.cycle2.carousel.min.js+jquery.cycle2.swipe.min.js)', array( 'jquery' ), '20140314', true );
			wp_register_script( 'google-js-api', 'https://www.google.com/jsapi', array(), '', true );
			wp_register_script( 'jquery-lemmon-slider', THEME_JS . 'libs/lemmon-slider.min.js', array( 'jquery' ), '0.2', true );
			wp_register_script( 'mobile-detect', $cdn . 'mobile-detect.js/0.4.3/mobile-detect.min.js', '', '0.4.3' );
		}
	}
	else
	{
		wp_register_script( 'angularjs', $cdn . 'angularjs/1.4.0/angular.min.js', '', '1.4.0', true );
		wp_register_script( 'angularjs-sanitize', $cdn . 'angularjs/1.4.0/angular-sanitize.min.js', array( 'angularjs' ), '1.4.0', true );

		wp_register_script( 'bootstrap-transition', THEME_JS . 'libs/bootstrap-transition.js', array( 'jquery' ), '2.3.2', true );

		wp_register_script( 'sl-choose-image', THEME_JS . 'admin/choose-image.js', array( 'jquery' ) );
		wp_register_script( 'sl-photos', THEME_JS . 'admin/photos.js', array( 'jquery' ) );

		wp_register_script( 'sl-meta-box', THEME_JS . 'admin/meta-box.js', array( 'sl-utils', 'jquery-ui-sortable', 'jquery-ui-autocomplete', 'jquery-ui-timepicker' ) );
		wp_localize_script( 'sl-meta-box', 'SlMetaBox', array(
			'excerptLimit' => sl_setting( 'excerpt_limit' ),
			'wordCount'    => __( 'Word count:', '7listings' ),
			'wordsLeft'    => __( 'Words left:', '7listings' ),
		) );
	}

	wp_register_script( 'sl-location-autocomplete', THEME_JS . 'location-autocomplete.js', array( 'jquery-ui-autocomplete' ), '', true );
	wp_localize_script( 'sl-location-autocomplete', 'SlLocation', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'location-autocomplete' ),
	) );
}

/**
 * Return URL of a file. Look in child theme and parent theme
 *
 * @param  string $file
 *
 * @return string
 */
function sl_locate_url( $file )
{
	if ( file_exists( CHILD_DIR . $file ) )
		return CHILD_URL . $file;
	if ( file_exists( THEME_DIR . $file ) )
		return THEME_URL . $file;

	return '';
}

/**
 * Minify Javascript code
 *
 * @param string $js
 *
 * @return string
 */
function sl_js_minify( $js )
{
	if ( ! class_exists( 'JSMin' ) )
		require THEME_DIR . 'lib/JSMin.php';

	return JSMin::minify( $js );
}

add_filter( 'image_size_names_choose', 'sl_image_size_names_choose' );

/**
 * Show custom image sizes in Media popup
 *
 * @param array $sizes
 *
 * @return array
 */
function sl_image_size_names_choose( $sizes )
{
	// Image sizes
	$sizes['sl_thumb_tiny']   = __( 'Square Tiny', '7listings' );
	$sizes['sl_thumb_small']  = __( 'Square Small', '7listings' );
	$sizes['sl_thumb_medium'] = __( 'Square Medium', '7listings' );
	$sizes['sl_thumb_large']  = __( 'Square Large', '7listings' );
	$sizes['sl_thumb_huge']   = __( 'Square Huge', '7listings' );

	$sizes['sl_pano_small']  = __( 'Panorama Small', '7listings' );
	$sizes['sl_pano_medium'] = __( 'Panorama Medium', '7listings' );
	$sizes['sl_pano_large']  = __( 'Panorama Large', '7listings' );
	$sizes['sl_pano_huge']   = __( 'Panorama Huge', '7listings' );

	$sizes['sl_feat_medium']  = __( 'Featured Medium', '7listings' );
	$sizes['sl_feat_large']   = __( 'Featured Large', '7listings' );

	return $sizes;
}
