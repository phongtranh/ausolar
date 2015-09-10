<?php

/**
 * This class display list of posts in the frontend
 */
class Sl_Widget_Compatibility_List extends Sl_Compatibility_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'title'               => '',
		'number'              => 5,

		'type'                => '',
		'location'            => '',

		'hierarchy'           => 0,
		'display_order'       => 'all',
		'orderby'             => 'date',

		'thumbnail'           => 1,
		'image_size'          => 'sl_thumb_tiny',
		'rating'              => 1,
		'price'               => 1,
		'booking'             => 1,
		'excerpt'             => 1,
		'excerpt_length'      => 25,

		'display'             => 'list',
		'columns'             => 1,

		'more_listings'       => 1,
		'more_listings_style' => 'button',
	);

	/**
	 * Post type, used in form() method
	 * @var string
	 */
	public $post_type;

	/**
	 * List of checkboxes used in form() method
	 * @var array
	 */
	public $checkboxes;

	/**
	 * List of order options for sorting
	 * Used in 'company' post type (no sort by price)
	 * @var array
	 */
	public $orderby = null;

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
		$html     = call_user_func( array( 'Sl_' . ucfirst( $this->post_type ) . '_Frontend', 'post_list' ), $instance );
		if ( ! $html )
			return;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		echo $html;

		echo $args['after_widget'];
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
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );

		$instance['type']     = strip_tags( $new_instance['type'] );
		$instance['location'] = strip_tags( $new_instance['location'] );

		$instance['hierarchy']     = empty( $new_instance['hierarchy'] ) ? 0 : 1;
		$instance['display_order'] = strip_tags( $new_instance['display_order'] );
		$instance['orderby']       = strip_tags( $new_instance['orderby'] );

		$instance['thumbnail']      = empty( $new_instance['thumbnail'] ) ? 0 : 1;
		$instance['image_size']     = strip_tags( $new_instance['image_size'] );
		$instance['price']          = empty( $new_instance['price'] ) ? 0 : 1;
		$instance['rating']         = empty( $new_instance['rating'] ) ? 0 : 1;
		$instance['booking']        = empty( $new_instance['booking'] ) ? 0 : 1;
		$instance['excerpt']        = empty( $new_instance['excerpt'] ) ? 0 : 1;
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

		$instance['display'] = strip_tags( $new_instance['display'] );
		$instance['columns'] = absint( $new_instance['columns'] );

		$instance['more_listings']       = empty( $new_instance['more_listings'] ) ? 0 : 1;
		$instance['more_listings_text']  = strip_tags( $new_instance['more_listings_text'] );
		$instance['more_listings_style'] = strip_tags( $new_instance['more_listings_style'] );

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

		if ( null === $this->orderby )
		{
			$this->orderby = array(
				'date'       => __( 'Recent', '7listings' ),
				'views'      => __( 'Popular', '7listings' ),
				'price-asc'  => __( 'Price (low-high)', '7listings' ),
				'price-desc' => __( 'Price (high-low)', '7listings' ),
			);
		}
		?>
		<div class="sl-admin-widget">
			<p>
				<label><?php _e( 'Title', '7listings' ); ?></label>
				<input class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>">
			</p>
			<hr class="light">
			<?php if ( isset( $this->default['type'] ) ) : ?>
				<p>
					<label class="input-label"><?php _e( 'Type', '7listings' ); ?></label>
					<?php
					wp_dropdown_categories( array(
						'show_option_all' => __( 'All', '7listings' ),
						'taxonomy'        => sl_meta_key( 'tax_type', $this->post_type ),
						'hide_empty'      => 1,
						'name'            => $this->get_field_name( 'type' ),
						'selected'        => $instance['type'],
						'id'              => $this->get_field_id( 'type' ),
						'orderby'         => 'NAME',
						'order'           => 'ASC',
					) );
					?>
				</p>
			<?php endif; ?>
			<p>
				<label class="input-label"><?php _e( 'Location', '7listings' ); ?></label>
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'location',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'location' ),
					'selected'        => $instance['location'],
					'id'              => $this->get_field_id( 'location' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</p>
			<p>
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'hierarchy' ), $instance['hierarchy'] ); ?>
				<label>
					<?php _e( 'Hierarchy', '7listings' ); ?>
					<?php echo do_shortcode( '[tooltip content="' . __( 'Display listings based on their hierarchy (Star &gt; Featured &gt; Normal)', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				</label>
			</p>
			<p>
				<label>
					<?php _e( 'Display', '7listings' ); ?>
					<?php echo do_shortcode( '[tooltip content="' . __( 'Select what type and the order of listings you want to display', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				</label>
				<select name="<?php echo $this->get_field_name( 'display_order' ); ?>">
					<?php
					Sl_Form::options( $instance['display_order'], array(
						'star'            => __( 'Star', '7listings' ),
						'star-featured'   => __( 'Star &gt; Featured', '7listings' ),
						'featured'        => __( 'Featured', '7listings' ),
						'featured-normal' => __( 'Featured &gt; Normal', '7listings' ),
						'all'             => __( 'All', '7listings' ),
					) );
					?>
				</select>
			</p>
			<p>
				<label class="input-label"><?php _e( 'Sort By', '7listings' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
					<?php Sl_Form::options( $instance['orderby'], $this->orderby ); ?>
				</select>
			</p>
			<?php include THEME_INC . 'widgets/tpl/list.php'; ?>
		</div>
	<?php
	}
}
