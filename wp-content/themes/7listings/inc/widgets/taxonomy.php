<?php

/**
 * This class displays terms of a taxonomy in the frontend
 *
 * Note: when extend this class, must declare $instance['taxonomy']
 */
class Sl_Widget_Taxonomy extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'taxonomy'           => '',
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
	 * Constructor
	 *
	 * @return Sl_Widget_Taxonomy
	 */
	public function __construct()
	{
		parent::__construct(
			'sl-taxonomies',
			__( '7 - Taxonomies', '7listings' ),
			array(
				'description' => __( 'List of taxonomies.', '7listings' ),
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
	public function widget( $args, $instance )
	{
		$instance = array_merge( $this->default, $instance );

		$terms = get_terms( $instance['taxonomy'], array(
			'number'       => $instance['number'],
			'hierarchical' => false,
		) );

		if ( empty( $terms ) || is_wp_error( $terms ) )
			return;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		$class = 'sl-list taxonomies';
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
				echo '<a href="' . get_term_link( $term, $instance['taxonomy'] ) . '" title="' . $term->name . '" rel="bookmark">';
				echo '<figure class="thumbnail">' . $logo . '</figure>';
				echo '</a>';
			}

			echo '<div class="details">';
			if ( $instance['name'] )
				echo '<h4 class="entry-title"><a class="title" href="' . get_term_link( $term, $instance['taxonomy'] ) . '" title="' . $term->name . '" rel="bookmark">' . $term->name . '</a></h4>';
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
	public function update( $new_instance, $old_instance )
	{
		$instance                       = $old_instance;
		$instance['taxonomy']           = strip_tags( $new_instance['taxonomy'] );
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
	public function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		// Get all available taxonomies for active modules
		$taxonomies = array();
		$post_types = sl_setting( 'listing_types' );
		if ( in_array( 'tour', $post_types ) )
		{
			$taxonomies[] = sl_meta_key( 'tax_type', 'tour' );
			$taxonomies[] = 'location';
		}
		if ( in_array( 'accommodation', $post_types ) )
		{
			$taxonomies[] = sl_meta_key( 'tax_type', 'accommodation' );
			$taxonomies[] = 'location';
		}
		if ( in_array( 'rental', $post_types ) )
		{
			$taxonomies[] = sl_meta_key( 'tax_type', 'rental' );
			$taxonomies[] = 'location';
		}
		if ( in_array( 'product', $post_types ) )
		{
			$taxonomies[] = 'brand';
		}
		$taxonomies = array_unique( $taxonomies );
		$all_taxonomies = get_taxonomies( array(), 'objects' );

		// Get taxonomy names to put it the dropdown
		$tax_list = array();
		foreach ( $all_taxonomies as $taxonomy => $tax_object )
		{
			if ( in_array( $taxonomy, $taxonomies ) )
			{
				$tax_list[$taxonomy] = $tax_object->labels->singular_name;
			}
		}
		asort( $tax_list );
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="widefat widget-title" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Taxonomy', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>">
					<?php Sl_Form::options( $instance['taxonomy'], $tax_list ); ?>
				</select>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Number', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="small-text" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo absint( $instance['number'] ); ?>">
					<span class="add-on"><?php _e( 'terms', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Thumbnail', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'logo' ), $instance['logo'] ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image Size', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( $this->get_field_name( 'image_size' ), $instance['image_size'] ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'name' ), $instance['name'] ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Description', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'description' ), $instance['description'] ); ?>
				</span>
				<span class="input-append supplementary-input">
					<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo esc_attr( $this->get_field_name( 'description_length' ) ); ?>" value="<?php echo esc_attr( $instance['description_length'] ); ?>">
					<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
				</span>
			</div>
		</div>
	<?php
	}
}
