<?php

class Sl_Widget_Slideshow extends WP_Widget
{
	/**
	 * @var array Default settings for widget
	 */
	public $default = array(
		'title' => '',
		'id'    => 0,
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Slideshow
	 */
	function __construct()
	{
		parent::__construct(
			'sl-slideshow',
			__( '7 - Slideshow', '7listings' ),
			array(
				'classname'   => 'widget_slideshow',
				'description' => __( 'Add a slideshow.', '7listings' ),
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
		$instance = array_merge( $this->default, $instance );

		extract( $args );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode( '[slideshow id="' . $instance['id'] . '"]' );

		echo $after_widget;
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
		$instance['id']    = absint( $new_instance['id'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label class="input-label"><?php _e( 'Slideshow', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'id' ); ?>">
					<?php
					$posts  = get_posts( 'post_type=slideshow&numberposts=-1' );
					$folded = array_combine( wp_list_pluck( $posts, 'ID' ), wp_list_pluck( $posts, 'post_title' ) );
					Sl_Form::options( $instance['id'], $folded );
					?>
				</select>
			</div>
		</div>
	<?php
	}
}
