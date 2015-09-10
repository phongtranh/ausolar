<?php

/**
 * This class displays single listing in the frontend
 */
class Sl_Widget_Single extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'post_type'      => 'accommodation',
		'title'          => '',
		'post_id'        => 0,
		'post_title'     => 1,
		'thumbnail'      => 1,
		'image_size'     => 'sl_pano_medium',
		'price'          => 0,
		'star_rating'    => 0,
		'rating'         => 0,
		'booking'        => 0,
		'excerpt'        => 1,
		'excerpt_length' => 25,
		'more_link'      => 0,
		'more_link_type' => 'text',
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Single
	 */
	function __construct()
	{
		$this->default['more_link_text'] = __( 'Read more', '7listings' );
		parent::__construct(
			'sl-single',
			__( '7 - Single', '7listings' ),
			array(
				'classname'   => 'sl-list single',
				'description' => __( 'Add 1 post or listing.', '7listings' ),
			)
		);

		/**
		 * Ajax callback to get all posts with specific post types
		 * Use static method means 1 callback for all widget instances
		 */
		add_action( 'wp_ajax_sl_widget_single_get_posts', array( __CLASS__, 'get_posts' ) );
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

		/**
		 * Set all available elements. For post type which doesn't have elements, they won't show up in the frontend
		 * because the widget settings always set its value to '0'
		 */
		$instance['elements'] = array( 'post_title', 'star_rating', 'rating', 'excerpt', 'price', 'booking', 'more_link' );
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
		$instance['post_type']      = strip_tags( $new_instance['post_type'] );
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['post_id']        = absint( $new_instance['post_id'] );
		$instance['image_size']     = strip_tags( $new_instance['image_size'] );
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );
		$instance['more_link_text'] = strip_tags( $new_instance['more_link_text'] );
		$instance['more_link_type'] = strip_tags( $new_instance['more_link_type'] );

		$checkboxes = array(
			'post_title',
			'thumbnail',
			'price',
			'star_rating',
			'rating',
			'booking',
			'excerpt',
			'more_link',
		);
		foreach ( $checkboxes as $checkbox )
		{
			$instance[$checkbox] = intval( ! empty( $new_instance[$checkbox] ) );
		}

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

		// Get available post types, but not product
		$post_types = array_diff( sl_setting( 'listing_types' ), array( 'product' ) );

		/**
		 * If license has only 1 listing type or users activate only 1 listing types
		 * Then set the value here and hide select dropdown
		 */
		if ( 2 > count( $post_types ) )
		{
			$instance['post_type'] = current( $post_types );
		}
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings toggle-choices<?php echo 1 < count( $post_types ) ? '' : ' hidden'; ?>">
			<div class="sl-label">
				<div class="sl-label">
					<label><?php _e( 'Post Type', '7listings' ); ?></label>
				</div>
			</div>
			<div class="sl-input post-type-input">
				<?php
				foreach ( $post_types as $post_type )
				{
					$class ='';
					if( 'company' == $post_type )
						$class = 'companie';
					elseif( 'post' == $post_type )
						$class = 'wp-posts';

					printf('<input type="radio" class="sl-widget-single-post-type" id="%s" name="%s" value="%s" %s><label for="%s" class="%ss icon" title="Select Wordpress %s">%s</label>',
						$post_type,
						esc_attr( $this->get_field_name( 'post_type' ) ),
						$post_type,
						$post_type == $instance['post_type'] ? 'checked="checked"' : '',
						$post_type,
						$class ? $class  : $post_type,
						ucfirst( $post_type ),
						ucfirst( $post_type )
					);
				}
				?>
			</div>
		</div>
		<div class="sl-settings single-listing">
			<div class="sl-label">
				<label><?php _e( 'Post', '7listings' ); ?><span class="spinner sl-hidden"></span></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>">
					<?php
					/**
					 * Get list of posts with current post type
					 * If current post type is not set, get the first post type from the list of post types above
					 */
					$post_type = $instance['post_type'] ? $instance['post_type'] : current( $options );
					$list      = get_posts( array(
						'posts_per_page' => - 1,
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'orderby'        => 'title',
						'order'          => 'ASC',
					) );
					foreach ( $list as $post )
					{
						$selected = selected( $post->ID, $instance['post_id'], false );
						echo "<option value='$post->ID' $selected>$post->post_title</option>";
					}
					?>
				</select>
			</div>
		</div>

		<br>

		<?php
		include THEME_INC . 'widgets/tpl/thumbnail.php';
		$checkboxes = array(
			'post_title' => __( 'Title', '7listings' ),
			'rating'     => __( 'Rating', '7listings' ),
		);
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<div class="sl-settings" data-name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" data-value="accommodation">
			<div class="sl-label">
				<label><?php _e( 'Star Rating', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'star_rating' ), $instance['star_rating'] ); ?>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" data-value="accommodation,tour,rental,attraction">
			<div class="sl-label">
				<label><?php _e( 'Price', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'price' ), $instance['price'] ); ?>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" data-value="accommodation,tour,rental,attraction">
			<div class="sl-label">
				<label><?php _e( 'Booking Button', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'booking' ), $instance['booking'] ); ?>
			</div>
		</div>
		<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Read more', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'more_link' ), $instance['more_link'] ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Text', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" name="<?php echo $this->get_field_name( 'more_link_text' ); ?>" value="<?php echo $instance['more_link_text']; ?>">
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Style', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo $this->get_field_name( 'more_link_type' ); ?>" class="sl-input-small">
						<option value="button"<?php selected( 'button', $instance['more_link_type'] ); ?>><?php _e( 'Button', '7listings' ); ?></option>
						<option value="text"<?php selected( 'text', $instance['more_link_type'] ); ?>><?php _e( 'Text', '7listings' ); ?></option>
					</select>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Ajax callback to get all posts with specific post types
	 * Use static method means 1 callback for all widget instances
	 *
	 * @return void
	 */
	public static function get_posts()
	{
		check_ajax_referer( 'get-posts' );

		if ( empty( $_GET['post_type'] ) )
		{
			wp_send_json_error();
		}

		$html = '';
		$list = get_posts( array(
			'posts_per_page' => - 1,
			'post_type'      => $_GET['post_type'],
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );
		foreach ( $list as $post )
		{
			$html .= sprintf( '<option value="%s">%s</option>', $post->ID, $post->post_title );
		}
		wp_send_json_success( $html );
	}
}