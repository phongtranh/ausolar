<?php
/**
 * Get sidebar layout for current page
 *
 * @return string
 */
function sl_sidebar_layout()
{
	return peace_filters( 'sidebar_layout', sl_setting( 'post_archive_sidebar_layout' ) );
}

add_filter( 'sl_singular_sidebar_layout', 'sl_singular_sidebar_layout' );

/**
 * Get sidebar layout for singular page
 *
 * @param  string $layout
 *
 * @return string
 */
function sl_singular_sidebar_layout( $layout )
{
	// If post has custom sidebar layout. Used for post, page
	if ( get_post_meta( get_the_ID(), 'custom_layout', true ) && ( $meta = get_post_meta( get_the_ID(), 'layout', true ) ) )
		return $meta;

	// If post has default layout in settings. Used for all listings types
	if ( $setting = sl_setting( get_post_type() . '_single_sidebar_layout' ) )
		return $setting;

	// Default sidebar layout for pages
	if ( is_page() )
		$layout = sl_setting( 'design_layout_default_page' );

	return $layout;

}

add_filter( 'sl_sidebar_layout', 'sl_listings_archive_sidebar_layout' );

/**
 * Get sidebar layout for listings archive page
 *
 * @param  string $layout
 *
 * @return string
 */
function sl_listings_archive_sidebar_layout( $layout )
{
	if ( $post_type = sl_is_listing_archive() )
	{
		if ( $setting = sl_setting( $post_type . '_archive_sidebar_layout' ) )
			$layout = $setting;
	}

	return $layout;
}
