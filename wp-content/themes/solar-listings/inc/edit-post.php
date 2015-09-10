<?php
add_action( 'post_edit_single_meta_after', 'solar_edit_single_meta_after' );

/**
 *
 * Display option fill feature image into header
 *
 * @return void
 */

function solar_edit_single_meta_after()
{
	$post_type = 'post';
?>
	<p>
		<label><?php _e( 'Fill Featured Image into Header', '7listings' ); ?></label>
		<?php Sl_Form::checkbox( "{$post_type}_fill_featured_image" ); ?>
	</p>
<?php
}

add_filter( 'sl_settings_sanitize', 'solar_post_sanitize_filter', 10, 3 );

/**
 * Sanitize options
 *
 * @param array  $options_new
 * @param array  $options
 * @param string $page
 *
 * @return array
 */

function solar_post_sanitize_filter( $options_new, $options, $page = '' )
{
	$type = 'post';
	if ( $page == 'page_post' )
	{
		$options_new = Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array(
			"{$type}_fill_featured_image",
		) );
	}
	return $options_new;
}

add_filter( 'sl_featured_title_style', 'solar_post_fill_background_header' );

/**
 * Add background style for featured title area
 *
 * @param string $style
 *
 * @return string
 *
 */
function solar_post_fill_background_header( $style )
{
	if ( is_single() && sl_setting( 'post_fill_featured_image' ) )
	{
		if ( has_post_thumbnail() )
		{
			list( $image_url ) = wp_get_attachment_image_src( get_post_thumbnail_id(), 'sl_feat_large' );
			$style .= 'background-image: url(' . $image_url . ')';
		}
	}

	return $style;
}