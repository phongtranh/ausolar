<?php

/**
 * Widget Social Links
 * This widget outputs social links from 7Listings -> Social Media
 * It uses [social_links] shortcode to output the widget. Shortcode attributes are converted to widget settings.
 */
class Sl_Widget_Social_Links extends WP_Widget
{
	/**
	 * @var array Default settings for widget
	 */
	public $default;

	/**
	 * @var array Widget configuration
	 */
	public $sl_instance;

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Social_Links
	 */
	function __construct()
	{
		$this->default = array(
			'title'      => '',
			'counter'    => 1, // Show counter?
			'class'      => '', // Additional CSS classes
			'size'       => '', // Icon size, can be 'small', 'medium' (or empty - default), 'large'

			// Social networks supported
			'facebook'   => '',
			'twitter'    => '',
			'googleplus' => '',
			'pinterest'  => '',
			'linkedin'   => '',
			'instagram'  => '',
			'rss'        => '',
		);
		parent::__construct(
			'sl-social-links',
			__( '7 - Social Links', '7listings' ),
			array(
				'description' => __( 'Add links to social media profiles.', '7listings' ),
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
		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		unset( $instance['title'] );
		$atts = '';
		foreach ( $instance as $k => $v )
		{
			if ( ! empty( $v ) )
				$atts .= " $k=\"$v\"";
		}

		echo do_shortcode( "[social_links$atts]" );

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
		$instance            = $old_instance;
		$instance['title']   = strip_tags( $new_instance['title'] );
		$instance['counter'] = empty( $new_instance['counter'] ) ? 0 : 1;

		// Sanitize all text fields
		$text_fields = $this->default;
		unset( $text_fields['title'], $text_fields['counter'] );
		$text_fields = array_keys( $text_fields );
		foreach ( $text_fields as $field )
		{
			$instance[$field] = strip_tags( $new_instance[$field] );
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
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat widget-title" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>">
			</div>
		</div>
		<?php
		$checkboxes = array(
			'counter' => __( 'Counter', '7listings' ),
		);
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Size', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'size' ); ?>">
					<option value="small"<?php selected( $instance['size'], 'small' ); ?>><?php _e( 'Small', '7listings' ); ?></option>
					<option value=""<?php selected( $instance['size'], '' ); ?>><?php _e( 'Medium', '7listings' ); ?></option>
					<option value="large"<?php selected( $instance['size'], 'large' ); ?>><?php _e( 'Large', '7listings' ); ?></option>
				</select>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Additional CSS Class', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="widefat" name="<?php echo $this->get_field_name( 'class' ); ?>" value="<?php echo $instance['class']; ?>">
			</div>
		</div>
		<h4>
			<?php _e( 'Custom social profile links', '7listings' ); ?>
			<?php echo do_shortcode( '[tooltip content="' . __( 'If no custom profile links entered, links will be get from setttings page 7Listings > Social Media.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
		</h4>

		<?php
		$text_fields = array(
			// Social networks supported
			'facebook'   => __( 'Facebook', '7listings' ),
			'twitter'    => __( 'Twitter', '7listings' ),
			'googleplus' => __( 'Google+', '7listings' ),
			'pinterest'  => __( 'Pinterest', '7listings' ),
			'linkedin'   => __( 'LinkedIn', '7listings' ),
			'instagram'  => __( 'Instagram', '7listings' ),
			'rss'        => __( 'RSS', '7listings' ),
		);
		foreach ( $text_fields as $key => $label )
		{
			?>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php echo $label; ?></label>
				</div>
				<div class="sl-input">
					<input type="text" class="widefat" name="<?php echo $this->get_field_name( $key ); ?>" value="<?php echo $instance[$key]; ?>">
				</div>
			</div>
		<?php
		}
		?>
	<?php
	}
}
