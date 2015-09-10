<?php

class Sl_Widget_Reviews extends WP_Widget
{
	/**
	 * @var array Default settings for widget
	 */
	public $default = array(
		'title'          => '',
		'number'         => 3,
		'type'           => array( - 1 ),
		'avatar'         => 1,
		'avatar_size'    => 80,
		'name'           => 1,
		'post_title'     => 1,
		'rating'         => 1,
		'date'           => 1,
		'excerpt'        => 1,
		'excerpt_length' => 20,
		'display'        => 'list',
		'columns'        => 1,
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Reviews
	 */
	function __construct()
	{
		parent::__construct(
			'sl-reviews',
			__( '7 - Reviews', '7listings' ),
			array(
				'description' => __( 'List of comments or reviews.', '7listings' ),
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
		$instance   = array_merge( $this->default, $instance );
		$reviews    = sl_review_list( $instance );
		if ( ! $reviews['nums'] )
			return;

		extract( $args, EXTR_SKIP );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $before_title . $title . $after_title;

		echo $reviews['html'];

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
		$instance                   = $old_instance;
		$instance['title']          = strip_tags( $new_instance['title'] );
		$instance['number']         = intval( $new_instance['number'] );
		$instance['type']           = $new_instance['type'];
		$instance['avatar']         = empty( $new_instance['avatar'] ) ? 0 : 1;
		$instance['avatar_size']    = intval( $new_instance['avatar_size'] );
		$instance['name']           = empty( $new_instance['name'] ) ? 0 : 1;
		$instance['post_title']     = empty( $new_instance['post_title'] ) ? 0 : 1;
		$instance['rating']         = empty( $new_instance['rating'] ) ? 0 : 1;
		$instance['date']           = empty( $new_instance['date'] ) ? 0 : 1;
		$instance['excerpt']        = empty( $new_instance['excerpt'] ) ? 0 : 1;
		$instance['excerpt_length'] = intval( $new_instance['excerpt_length'] );
		$instance['display']        = strip_tags( $new_instance['display'] );
		$instance['columns']        = absint( $new_instance['columns'] );

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
				<label><?php _e( 'Post Type', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<?php
				$types = array(
					'-1' => __( 'All', '7listings' ),
				);
				switch ( Sl_License::license_type() )
				{
					case '7Basic':
						$types['post'] = __( 'News', '7listings' );
						break;
					case '7Comp':
						$types['post']    = __( 'News', '7listings' );
						$types['company'] = __( 'Company', '7listings' );
						break;
					case '7Accommodation':
						$types['post']          = __( 'News', '7listings' );
						$types['accommodation'] = __( 'Accommodation', '7listings' );
						break;
					case '7Tours':
						$types['post'] = __( 'News', '7listings' );
						$types['tour'] = __( 'Tour', '7listings' );
						break;
					case '7Rental':
						$types['post']   = __( 'News', '7listings' );
						$types['rental'] = __( 'Rental', '7listings' );
						break;
					case '7Products':
						$types['post']    = __( 'News', '7listings' );
						$types['product'] = __( 'Product', '7listings' );
						break;
					case '7Pro':
					case '7Network':
						$types['post']          = __( 'News', '7listings' );
						$types['company']       = __( 'Company', '7listings' );
						$types['accommodation'] = __( 'Accommodation', '7listings' );
						$types['tour']          = __( 'Tour', '7listings' );
						$types['rental']        = __( 'Rental', '7listings' );
						$types['product']       = __( 'Product', '7listings' );
						break;
				}
				$html = array();
				foreach ( $types as $k => $v )
				{
					$html[] = sprintf(
						'<label><input type="checkbox" name="%s[]" value="%s"%s> %s</label>',
						$this->get_field_name( 'type' ),
						$k,
						checked( in_array( $k, $instance['type'] ), 1, false ),
						$v
					);
				}
				echo implode( '', $html );
				?>
			</div>
		</div>
		
		<br>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Number', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="amount" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" value="<?php echo esc_attr( $instance['number'] ); ?>">
					<span class="add-on"><?php _e( 'Reviews', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
		
		<br>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Avatar', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle">
					<?php Sl_Form::checkbox_general( $this->get_field_name( 'avatar' ), $instance['avatar'] ); ?>
				</span>
				<span class="input-append supplementary-input">
					<input type="text" class="avatar-size" name="<?php echo esc_attr( $this->get_field_name( 'avatar_size' ) ); ?>" value="<?php echo esc_attr( $instance['avatar_size'] ); ?>">
					<span class="add-on">px</span>
				</span>
			</div>
		</div>
		<?php
		$checkboxes = array(
			'name'       => __( 'Name', '7listings' ),
			'rating'     => __( 'Rating', '7listings' ),
			'post_title' => __( 'Title', '7listings' ),
			'date'       => __( 'Date', '7listings' ),
		);
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
	<?php
	}
}
