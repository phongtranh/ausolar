<?php
add_action( 'widgets_init', 'sl_compatibility_widgets' );

/**
 * Register custom sidebar and widgets
 *
 * @return void
 */
function sl_compatibility_widgets()
{
	require SL_COMPATIBILITY_DIR . 'widgets/widget.php';
	require SL_COMPATIBILITY_DIR . 'widgets/taxonomy.php';
	require SL_COMPATIBILITY_DIR . 'widgets/single.php';
	require SL_COMPATIBILITY_DIR . 'widgets/slider.php';
	require SL_COMPATIBILITY_DIR . 'widgets/list.php';

	require SL_COMPATIBILITY_DIR . 'widgets/locations.php';
	require SL_COMPATIBILITY_DIR . 'widgets/post-slider.php';
	require SL_COMPATIBILITY_DIR . 'widgets/posts.php';

	register_widget( 'Sl_Widget_Locations' );
	register_widget( 'Sl_Widget_Post_Slider' );
	register_widget( 'Sl_Widget_Posts' );

	if ( post_type_exists( 'tour' ) )
	{
		require SL_COMPATIBILITY_DIR . 'widgets/tour-single.php';
		require SL_COMPATIBILITY_DIR . 'widgets/tour-slider.php';
		require SL_COMPATIBILITY_DIR . 'widgets/tour-types.php';
		require SL_COMPATIBILITY_DIR . 'widgets/tours.php';
		register_widget( 'Sl_Widget_Tour_Single' );
		register_widget( 'Sl_Widget_Tour_Slider' );
		register_widget( 'Sl_Widget_Tour_Types' );
		register_widget( 'Sl_Widget_Tours' );
	}
}
