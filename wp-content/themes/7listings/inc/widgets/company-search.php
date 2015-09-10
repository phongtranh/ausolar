<?php

class Sl_Widget_Company_Search extends WP_Widget
{
	/**
	 * @var array Default settings for widget
	 */
	public $default = array(
		'title' => '',
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Company_Search
	 */
	function __construct()
	{
		parent::__construct(
			'sl-company-search',
			__( '7 - Company Search', '7listings' ),
			array(
				'description' => __( 'Simple company search form.', '7listings' ),
			)
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance )
	{
		extract( $args, EXTR_SKIP );

		$instance = array_merge( $this->default, $instance );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $before_title . $title . $after_title;

		$base_url = 'company' == sl_is_listing_archive() ? remove_query_arg( 'start' ) : get_post_type_archive_link( 'company' );
		$base_url = remove_query_arg( 'state', $base_url );
		echo '<form action="' . $base_url . '" method="get">
				<input type="search" placeholder="' . __( 'Search Companies...', '7listings' ) . '" name="s" required><button type="submit" class="button search" title="' . __( 'Search', '7listings' ) . '">' . __( 'Search', '7listings' ) . '</button>
			</form>';

		echo $after_widget;

		wp_reset_postdata();
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @internal param array $old_instance
	 *
	 * @return array
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );
		include THEME_INC . 'widgets/tpl/title.php';
	}
}
