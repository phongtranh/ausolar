<?php
/**
 * Register custom sidebar and widgets
 * Priority = 5 to make sure some base classes of theme widgets are loaded for other modules to extend
 */
add_action( 'widgets_init', 'sl_register_sidebar', 5 );

/**
 * Register custom sidebar and widgets
 *
 * @return void
 */
function sl_register_sidebar()
{
	if ( $sidebars = sl_setting( 'sidebars' ) )
	{
		foreach ( $sidebars as $sidebar )
		{
			register_sidebar( array(
				'name'          => $sidebar,
				'id'            => sanitize_title( $sidebar ),
				'description'   => __( 'An optional widget area for pages', '7listings' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			) );
		}
	}

	// Load widgets
	$widgets = array( 'contact-form', 'contact-info', 'reviews', 'twitter','filter' );
	foreach ( $widgets as $widget )
	{
		require THEME_INC . "widgets/$widget.php";
		$class_name = 'Sl_Widget_' . str_replace( '-', '_', ucwords( $widget ) );
		register_widget( $class_name );
	}

	if ( sl_setting( 'listing_types' ) )
	{
		// require THEME_INC . 'widgets/single.php';
		// register_widget( 'Sl_Widget_Single' );

		require THEME_INC . 'widgets/slider.php';
		register_widget( 'Sl_Widget_Slider' );

		require THEME_INC . 'widgets/list.php';
		register_widget( 'Sl_Widget_List' );

		require THEME_INC . 'widgets/taxonomy.php';
		register_widget( 'Sl_Widget_Taxonomy' );

		require THEME_INC . 'widgets/search.php';
		register_widget( 'Sl_Widget_Search' );

		require THEME_INC . 'widgets/active-filter.php';
		register_widget( 'Sl_Active_Filters' );

		require THEME_INC . 'widgets/recently-viewed.php';
		register_widget( 'Sl_Recently_Viewed' );
	}
}

add_action( 'admin_enqueue_scripts', 'sl_admin_widgets_script' );

/**
 * Enqueue Javascript code for admin 'widgets.php' page
 *
 * @return void
 */
function sl_admin_widgets_script()
{
	wp_enqueue_script( 'sl-admin-widgets', THEME_JS . 'admin/widgets.js', array( 'jquery' ), '', true );
	wp_localize_script( 'sl-admin-widgets', 'SlWidgets', array(
		'nonceGetPosts' => wp_create_nonce( 'get-posts' ),
	) );
}
