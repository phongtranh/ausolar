<?php

class SL_Widget_Post_Slider extends Sl_Widget_Compatibility_Slider
{
	/**
	 * Constructor
	 *
	 * @return SL_Widget_Post_Slider
	 */
	function __construct()
	{
		self::remove_atts( $this->default, array( 'type', 'location', 'priority', 'rating', 'price', 'booking' ) );
		$this->default['cat']  = '';
		$this->default['date'] = 1;
		$this->post_type       = 'post';
		$this->checkboxes      = array(
			'post_title' => __( 'Title', '7listings' ),
			'date'       => __( 'Date', '7listings' ),
		);
		parent::__construct(
			'sl-post-slider',
			__( '7 - Post Slider', '7listings' ),
			array(
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
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
		$instance         = parent::update( $new_instance, $old_instance );
		$instance['cat']  = strip_tags( $new_instance['cat'] );
		$instance['date'] = empty( $new_instance['date'] ) ? 0 : 1;
		self::remove_atts( $instance, array( 'type', 'location', 'priority', 'rating', 'price', 'booking' ) );

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
		$checkboxes = $this->checkboxes;
		$instance   = array_merge( $this->default, $instance );
		?>
		<div class="sl-admin-widget">
			<p>
				<label><?php _e( 'Title', '7listings' ); ?></label>
				<input class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>">
			</p>
			<hr class="light">
			<p>
				<label class="input-label"><?php _e( 'Category', '7listings' ); ?></label>
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'cat' ),
					'selected'        => $instance['cat'],
					'id'              => $this->get_field_id( 'cat' ),
				) );
				?>
			</p>
			<p>
				<label class="input-label"><?php _e( 'Sort By', '7listings' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
					<?php
					Sl_Form::options( $instance['orderby'], array(
						'date'  => __( 'Recent', '7listings' ),
						'views' => __( 'Popular', '7listings' ),
					) );
					?>
				</select>
			</p>
			<?php include THEME_INC . 'widgets/tpl/slider.php'; ?>
		</div>
	<?php
	}
}
