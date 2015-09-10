<?php

/**
 * This class creates a listing search widget that allows user to search for listings with type, location, date, etc.
 */
class Sl_Widget_Search extends WP_Widget
{
	/**
	 * Default settings for widget.
	 * @var array
	 */
	public $default = array(
		'title'       => '',
		'post_type'   => 'accommodation',
		'keyword'     => 0,
		'location'    => 0,
		'type'        => 0,
		'star_rating' => 0,
	);

	/**
	 * List of checkboxes of search fields
	 * @var array
	 */
	public $checkboxes;

	/**
	 * Widget constructor
	 */
	public function __construct()
	{
		$this->checkboxes = array(
			'keyword'  => __( 'Keyword', '7listings' ),
			'location' => __( 'Location', '7listings' ),
			'type'     => __( 'Type', '7listings' ),
		);
		parent::__construct(
			'sl-listing-search',
			__( '7 - Listing Search', '7listings' ),
			array(
				'classname'   => 'listing-search',
				'description' => __( 'Advanced listing search form with settings and filters.', '7listings' ),
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

		// Get previous params from $_GET to show in the widget
		$params = array(
			's'                                     => '',
			'sl_location_' . $instance['post_type'] => '',
			'sl_type_' . $instance['post_type']     => '',
			'star_rating'                           => '',
		);
		$params = array_merge( $params, $_GET );

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];
		?>
		<form action="<?php echo HOME_URL; ?>" method="get" role="form">
			<?php wp_nonce_field( 'widget-search', 'sl_widget_search', false ); ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr( $instance['post_type'] ); ?>">
			<?php if ( $instance['keyword'] ) : ?>
				<div class="control-group keywords">
					<div class="controls">
						<input type="search" name="s" placeholder="<?php esc_attr_e( 'Keywords...', '7listings' ); ?>" value="<?php echo esc_attr( $params['s'] ); ?>" class="keyword">
					</div>
				</div>
			<?php endif; ?>
			<?php if ( $instance['location'] ) : ?>
				<div class="control-group locations">
					<div class="controls">
						<?php
						global $wpdb;

						/**
						 * Get all location terms for current post type only
						 * We know that terms are saved as post meta, so we query from post meta
						 */
						$term_ids = $wpdb->get_col( $wpdb->prepare(
							"SELECT DISTINCT meta_value FROM $wpdb->postmeta AS m
							INNER JOIN $wpdb->posts AS p ON p.ID = m.post_id
							WHERE p.post_type='%s' AND m.meta_key IN ('state', 'city', 'area')",
							$instance['post_type']
						) );
						wp_dropdown_categories( array(
							'taxonomy'        => 'location',
							'orderby'         => 'name',
							'hierarchical'    => true,
							'name'            => "sl_location_{$instance['post_type']}",
							'show_option_all' => __( 'All locations', '7listings' ),
							'include'         => $term_ids,
							'id'              => "location-{$instance['post_type']}",
							'class'           => 'sl-location-widget',
							'selected'        => $params["sl_location_{$instance['post_type']}"],
						) );
						?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( $instance['type'] ) : ?>
				<div class="control-group types">
					<div class="controls">
						<?php
						wp_dropdown_categories( array(
							'taxonomy'        => sl_meta_key( 'tax_type', $instance['post_type'] ),
							'show_option_all' => __( 'All Types', '7listings' ),
							'orderby'         => 'NAME',
							'name'            => "sl_type_{$instance['post_type']}",
							'hierarchical'    => true,
							'selected'        => $params["sl_type_{$instance['post_type']}"],
						) );
						?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( 'accommodation' == $instance['post_type'] && $instance['star_rating'] ) : ?>
				<div class="control-group accomm-rating">
					<label class="control-label"><?php _e( 'Star Rating', '7listings' ); ?></label>
					<div class="controls">
						<select name="star_rating">
							<?php
							Sl_Form::options( $params['star_rating'], array(
								0 => '0',
								1 => '1',
								2 => '2',
								3 => '3',
								4 => '4',
								5 => '5',
							) );
							?>
						</select>
					</div>
				</div>
			<?php endif; ?>
			<input type="submit" class="button search" value="<?php esc_attr_e( 'Search', '7listings' ); ?>">
		</form>
		<?php
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
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title'] );
		$instance['post_type'] = strip_tags( $new_instance['post_type'] );

		foreach ( $this->checkboxes as $k => $v )
		{
			$instance[$k] = intval( isset( $new_instance[$k] ) );
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
	public function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		$post_types = sl_setting( 'listing_types' );

		// Ignore 'product' and 'company' post types
		$post_types = array_diff( $post_types, array( 'product', 'company' ) );

		/**
		 * If license has only 1 listing type or users activate only 1 listing types
		 * Then set the value here and hide select dropdown
		 */
		if ( 2 > count( $post_types ) )
		{
			$instance['post_type'] = $post_types[0];
		}
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<?php if ( 1 < count( $post_types ) ) : ?>
			<div class="sl-settings toggle-choices">
				<div class="sl-label">
					<label><?php _e( 'Post Type', '7listings' ); ?></label>
				</div>
				<div class="sl-input post-type-input">
					<?php
					foreach ( $post_types as $post_type )
					{
						printf('<input type="radio" id="%ss" name="%s" value="%s" %s><label for="%ss" class="%ss icon" title="Select Wordpress %s">%s</label>',
							$post_type,
							$this->get_field_name( 'post_type' ),
							$post_type,
							$post_type == $instance['post_type'] ? 'checked="checked"' : '',
							$post_type,
							$post_type,
							ucfirst( $post_type ),
							ucfirst( $post_type )
						);
					}
					?>
				</div>
			</div>
		<?php endif; ?>
		<?php
		$checkboxes = $this->checkboxes;
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<?php if ( in_array( 'accommodation', $post_types ) ) : ?>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Star Rating', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'star_rating' ), $instance['star_rating'] ); ?>
				</div>
			</div>
		<?php endif; ?>
	<?php
	}
}
