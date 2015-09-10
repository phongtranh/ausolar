<?php
// Remove junk headers
add_action( 'wp_head', 'sl_remove_junk_headers', 0 );

/**
 * Remove junk headers
 *
 * @return void
 */
function sl_remove_junk_headers()
{
	remove_action( 'wp_head', 'feed_links', 2 );       // Links to the general feeds: Post and Comment Feed
	remove_action( 'wp_head', 'feed_links_extra', 3 ); // Links to the extra feeds such as category feeds
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
}

add_filter( 'wp_title', 'sl_meta_title', 10, 3 );

/**
 * Get home page title
 *
 * @param string $title              Default WordPress title
 * @param string $separator          Separator determined in theme
 * @param string $separator_location Whether the separator should be left or right
 *
 * @return string Title
 */
function sl_meta_title( $title, $separator, $separator_location )
{
	global $page, $paged;

	// Don't affect in feeds.
	if ( is_feed() )
		return $title;

	// Allow pages, modules to change title before processing
	// This will prevent all hooks to same "wp_title" and handling priority is hard
	$custom_title = peace_filters( 'meta_title', '', $separator, $separator_location );

	// Better title for search page
	if ( is_search() )
		$custom_title = esc_html( sprintf( __( 'Search results for "%s"', '7listings' ), get_query_var( 's' ) ) );

	if ( $custom_title )
	{
		if ( 'right' == $separator_location )
			$title = "$custom_title $separator ";
		else
			$title = " $separator $custom_title";
	}

	// Add the blog name
	if ( 'right' == $separator_location )
		$title .= get_bloginfo( 'name' );
	else
		$title = get_bloginfo( 'name' ) . $title;

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && is_front_page() )
		$title .= " $separator $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', '7listings' ), max( $paged, $page ) );

	return $title;
}

add_filter( 'sl_meta_title', 'sl_meta_title_special_pages' );

/**
 * Meta title for special pages
 *
 * @param string $title
 *
 * @return string
 */
function sl_meta_title_special_pages( $title )
{
	if ( get_query_var( 'book' ) )
		$title = sprintf( __( 'Booking %s', '7listings' ), get_the_title() );

	if ( get_query_var( 'book_bundle' ) )
		$title = sprintf( __( 'Book %s', '7listings' ), get_the_title() );

	return peace_filters( 'meta_title_special_pages', $title );
}

add_filter( 'wpseo_title', 'sl_wpseo_meta_title' );

/**
 * Set meta title for special pages to make it work with WordPress SEO plugin
 *
 * @param string $title Meta title set in WordPress SEO settings page
 *
 * @return string Title
 */
function sl_wpseo_meta_title( $title )
{
	global $page, $paged, $sep;

	$custom_title = sl_meta_title_special_pages( '' );
	if ( ! $custom_title )
		return $title;

	$separator = $sep;
	if ( function_exists( 'wpseo_replace_vars' ) )
		$separator = wpseo_replace_vars( '%%sep%%', array() );

	$separator_location = 'right';

	if ( 'right' == $separator_location )
		$title = "$custom_title $separator ";
	else
		$title = " $separator $custom_title";

	// Add the blog name
	if ( 'right' == $separator_location )
		$title .= get_bloginfo( 'name' );
	else
		$title = get_bloginfo( 'name' ) . $title;

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $separator " . sprintf( __( 'Page %s', '7listings' ), max( $paged, $page ) );

	return $title;
}

add_action( 'wp_head', 'sl_meta' );

/**
 * Display meta tags for rich snippet
 *
 * @return void
 */
function sl_meta()
{
	if ( ! is_single() )
		return;

	printf( '
		<meta property="og:type" content="%s">
		<meta property="og:url" content="%s">
		<meta property="og:title" content="%s">
		<meta property="og:description" content="%s">
		<meta property="og:image" content="%s">
		<meta property="og:site_name" content="%s">',
		'product' == get_post_type() ? 'product' : 'article',
		get_permalink(),
		get_the_title(),
		sl_excerpt(),
		sl_broadcasted_image_src( '_thumbnail_id', 'full' ),
		get_bloginfo( 'name' )
	);
}

add_action( 'wp_head', 'sl_favicon' );

/**
 * Display favicon
 *
 * @return void
 */
function sl_favicon()
{
	if ( $favicon = sl_setting( 'favicon' ) )
		echo '<link rel="shortcut icon" href="' . wp_get_attachment_url( $favicon ) . '">';
}

add_action( 'wp_footer', 'sl_google_analytics' );

/**
 * Show GA code in header
 *
 * @return void
 */
function sl_google_analytics()
{
	$ga = sl_setting( 'ga' );
	if ( ! $ga )
		return;
	?>
	<script>var _gaq = [['_setAccount', '<?php echo $ga; ?>'], ['_trackPageview']];
		(function ( d, t )
		{
			var g = d.createElement( t ), s = d.getElementsByTagName( t )[1];
			g.async = true;
			g.src = ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js';
			s.parentNode.insertBefore( g, s )
		}( document, 'script' ));</script>
<?php
}
