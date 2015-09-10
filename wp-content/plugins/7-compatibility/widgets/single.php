<?php

/**
 * This class displays single listing in the frontend
 */
abstract class Sl_Widget_Compatibility_Single extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'title'          => '',
		'post_id'        => 0,
		'post_title'     => 1,
		'thumbnail'      => 1,
		'image_size'     => 'sl_pano_medium',
		'price'          => 0,
		'rating'         => 0,
		'booking'        => 0,
		'excerpt'        => 1,
		'excerpt_length' => 50,
		'more_link'      => 0,
		'more_link_type' => 'text',
	);

	/**
	 * List of listing elements which will be displayed
	 * @var array
	 */
	public $elements = array( 'post_title', 'rating', 'excerpt', 'price', 'booking', 'more_link' );

	/**
	 * Post type, used in form() method
	 * @var string
	 */
	public $post_type;

	/**
	 * List of checkboxes used in form() method
	 * @var array()
	 */
	public $checkboxes;

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

		if ( ! $instance['post_id'] )
			return;

		global $post;
		$post = get_post( $instance['post_id'] );

		if ( empty( $post ) )
			return;

		setup_postdata( $post );

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		unset( $instance['title'], $instance['post_id'] );
		$instance['elements'] = $this->elements;
		echo sl_post_list_single( $instance );

		echo $args['after_widget'];

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
		$instance                   = $old_instance;
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['post_id']        = absint( $new_instance['post_id'] );
		$instance['post_title']     = empty( $new_instance['post_title'] ) ? 0 : 1;
		$instance['thumbnail']      = empty( $new_instance['thumbnail'] ) ? 0 : 1;
		$instance['image_size']     = strip_tags( $new_instance['image_size'] );
		$instance['price']          = empty( $new_instance['price'] ) ? 0 : 1;
		$instance['rating']         = empty( $new_instance['rating'] ) ? 0 : 1;
		$instance['booking']        = empty( $new_instance['booking'] ) ? 0 : 1;
		$instance['excerpt']        = empty( $new_instance['excerpt'] ) ? 0 : 1;
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

		$instance['more_link']      = empty( $new_instance['more_link'] ) ? 0 : 1;
		$instance['more_link_text'] = strip_tags( $new_instance['more_link_text'] );
		$instance['more_link_type'] = strip_tags( $new_instance['more_link_type'] );

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
				<label><?php _e( 'Select Listing', '7listings' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'post_id' ); ?>">
					<?php
					$list = get_posts( array(
						'posts_per_page' => - 1,
						'post_type'      => $this->post_type,
						'post_status'    => 'publish',
						'orderby'        => 'title',
						'order'          => 'ASC',
					) );

					foreach ( $list as $post )
					{
						$selected = selected( $post->ID, $instance['post_id'], false );
						echo "<option value='{$post->ID}' {$selected}>{$post->post_title}</option>";
					}
					?>
				</select>
			</p>
			<hr class="light">
			<?php include THEME_INC . 'widgets/tpl/thumbnail.php'; ?>
			<?php include THEME_INC . 'widgets/tpl/checkboxes.php'; ?>
			<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
			<hr class="light">
			<p class="checkbox-toggle">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'more_link' ), $instance['more_link'] ); ?>
				<label><?php _e( 'Read more', '7listings' ); ?></label>
			</p>
			<div>
				<p>
					<label><?php _e( 'Text', '7listings' ); ?></label><br>
					<input type="text" name="<?php echo $this->get_field_name( 'more_link_text' ); ?>" value="<?php echo $instance['more_link_text']; ?>">
				</p>
				<p>
					<label><?php _e( 'Style', '7listings' ); ?></label>
					<select class="input-small" name="<?php echo $this->get_field_name( 'more_link_type' ); ?>">
						<option value="button"<?php selected( 'button', $instance['more_link_type'] ); ?>><?php _e( 'Button', '7listings' ); ?></option>
						<option value="text"<?php selected( 'text', $instance['more_link_type'] ); ?>><?php _e( 'Text', '7listings' ); ?></option>
					</select>
				</p>
			</div>

		</div>
	<?php
	}
}
