<?php

/**
 * This class displays terms of a taxonomy in the frontend
 *
 * Note: when extend this class, must declare $this->taxonomy
 */
abstract class Sl_Widget_Compatibility_Taxonomy extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'title'              => '',
		'number'             => 5,
		'logo'               => 0,
		'image_size'         => 'sl_thumb_small',
		'name'               => 1,
		'description'        => 1,
		'description_length' => 25,
		'display'            => 'list',
		'columns'            => 1,
	);

	/**
	 * @var string Taxonomy
	 */
	public $taxonomy;

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
		$instance = array_merge( $this->default, self::update_params( $instance ) );

		$terms = get_terms( $this->taxonomy, array(
			'number'       => $instance['number'],
			'hierarchical' => false,
		) );

		if ( empty( $terms ) || is_wp_error( $terms ) )
			return;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		$class = 'sl-list posts taxonomies';
		if ( $instance['display'] == 'grid' )
		{
			$class .= ' grid';
			$class .= ' columns-' . $instance['columns'];
		}
		echo '<section class="' . $class . '">';

		foreach ( $terms as $term )
		{
			echo '<article class="post taxonomy">';

			if ( $instance['logo'] )
			{
				$logo = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
				if ( $logo )
				{
					list( $src ) = wp_get_attachment_image_src( $logo, $instance['image_size'] );
					$logo = '<img class="photo" src="' . $src . '"alt="' . $term->name . '">';
				}
				else
				{
					$logo = sl_image_placeholder( $instance['image_size'] );
				}
				echo '<a href="' . get_term_link( $term, $this->taxonomy ) . '" title="' . $term->name . '" rel="bookmark">';
				echo '<figure class="thumbnail">' . $logo . '</figure>';
				echo '</a>';
			}

			echo '<div class="details">';
			if ( $instance['name'] )
				echo '<h4 class="entry-title"><a class="title" href="' . get_term_link( $term, $this->taxonomy ) . '" title="' . $term->name . '" rel="bookmark">' . $term->name . '</a></h4>';
			if ( $instance['description'] )
				echo '<p class="entry-summary excerpt">' . wp_trim_words( $term->description, $instance['description_length'], sl_setting( 'excerpt_more' ) ) . '</p>';
			echo '</div>';

			echo '</article>';
		}
		echo '</section>';

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
		$instance                       = $old_instance;
		$instance['title']              = strip_tags( $new_instance['title'] );
		$instance['number']             = absint( $new_instance['number'] );
		$instance['logo']               = empty( $new_instance['logo'] ) ? 0 : 1;
		$instance['image_size']         = strip_tags( $new_instance['image_size'] );
		$instance['name']               = empty( $new_instance['name'] ) ? 0 : 1;
		$instance['description']        = empty( $new_instance['description'] ) ? 0 : 1;
		$instance['description_length'] = absint( $new_instance['description_length'] );
		$instance['display']            = strip_tags( $new_instance['display'] );
		$instance['columns']            = absint( $new_instance['columns'] );

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
		$instance = array_merge( $this->default, self::update_params( $instance ) );
		?>
		<div class="sl-admin-widget">
			<p>
				<label><?php _e( 'Title', '7listings' ) ?></label>
				<input type="text" class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</p>
			<hr class="light">
			<p>
				<label class="input-label"><?php _e( 'Number', '7listings' ) ?></label>
				<span class="input-append">
					<input type="number" class="amount" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo absint( $instance['number'] ); ?>">
					<span class="add-on"><?php _e( 'terms', '7listings' ); ?></span>
				</span>
			</p>
			<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
			<hr class="light">
			<p class="checkbox-toggle">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'logo' ), $instance['logo'] ); ?>
				<label><?php _e( 'Thumbnail', '7listings' ) ?></label>
			</p>
			<p>
				<label><?php _e( 'Image Size', '7listings' ); ?></label>
				<?php Sl_Form::image_sizes_select( $this->get_field_name( 'image_size' ), $instance['image_size'] ); ?>
			</p>
			<hr class="light">
			<p>
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'name' ), $instance['name'] ); ?>
				<label><?php _e( 'Title', '7listings' ) ?></label>
			</p>
			<p>
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'description' ), $instance['description'] ); ?>
					<label><?php _e( 'Description', '7listings' ) ?></label>
				</span>
				<span class="input-append supplementary-input">
					<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo esc_attr( $this->get_field_name( 'description_length' ) ); ?>" value="<?php echo esc_attr( $instance['description_length'] ); ?>">
					<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
				</span>
			</p>

		</div>
	<?php
	}

	/**
	 * Update parameters name of the widget
	 * In previous versions, we used `num` in some classes. Now we change to `number`
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	public static function update_params( $instance )
	{
		if ( isset( $instance['num'] ) && ! isset( $instance['number'] ) )
		{
			$instance['number'] = $instance['num'];
			unset( $instance['num'] );
		}

		return $instance;
	}
}
