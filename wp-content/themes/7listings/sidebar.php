<?php
// Default sidebar for whole website
$sidebar = sl_setting( 'post_archive_sidebar' );

// Singular pages, including: page, post and custom post types
if ( is_singular() )
{
	// Default sidebar for pages
	if ( is_page() )
		$sidebar = sl_setting( 'design_sidebar_default_page' );

	// Sidebar set in custom post type settings page
	if ( $setting = sl_setting( get_post_type() . '_single_sidebar' ) )
		$sidebar = $setting;

	// Sidebar set in edit page
	if ( $meta = get_post_meta( get_the_ID(), 'custom_sidebar', true ) )
		$sidebar = $meta;
}

// Listing archive
if ( $post_type = sl_is_listing_archive() )
{
	if ( $setting = sl_setting( $post_type . '_archive_sidebar' ) )
		$sidebar = $setting;
}


$sidebar = peace_filters( 'sidebar', $sidebar );
$sidebar = sanitize_title( $sidebar );
?>

<?php if ( ! dynamic_sidebar( $sidebar ) ) : ?>

	<aside id="archives" class="widget">
		<h3 class="widget-title"><?php _e( 'Sidebar', '7listings' ); ?></h3>
		<p class="description"><?php _e( 'You can completely customise this default sidebar.', '7listings' ); ?></p>
		<ul>
			<li><?php printf( __( '<a href="%s">Widgets</a>', '7listings' ), admin_url( 'widgets.php' ) ); ?></li>
			<li><?php printf( __( '<a href="%s">Sidebars</a>', '7listings' ), admin_url( 'themes.php?page=sidebars' ) ); ?></li>
			<li><?php printf( __( '<a href="%s">Design</a>', '7listings' ), admin_url( 'themes.php?page=design#sidebar' ) ); ?></li>
		</ul>
		
	</aside>

	<aside id="support" class="widget">
		<h3 class="widget-title"><?php _e( 'Support', '7listings' ); ?></h3>
		<p class="description"><?php _e( 'You have tooltips next to inputs, and a help articles on the top right in the admin area.', '7listings' ); ?></p>
		<ul>
			<li><a href="http://www.7listings.net/docs/" target="_blank">Documentation</a></li>
		</ul>
	</aside>

<?php endif; ?>
