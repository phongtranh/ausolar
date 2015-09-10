<?php

class Sl_Widget_Contact_Info extends WP_Widget
{
	/**
	 * @var array Default values of widget
	 */
	public $default = array(
		'color'     => '',
		'title'     => '',
		'text'      => '',
		'phone'     => '',
		'cellphone' => '',
		'email'     => '',
		'link'      => '',
		'address'   => '',
		'city'      => '',
		'state'     => '',
		'zip'       => '',
		'name'      => '',
	);

	/**
	 * Widget constructor
	 */
	function __construct()
	{
		parent::__construct(
			'sl-contact-info',
			__( '7 - Contact Info', '7listings' ),
			array(
				'classname'   => 'widget_contact_info',
				'description' => __( 'List of contact info.', '7listings' ),
			)
		);
	}

	/**
	 * Show widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	function widget( $args, $instance )
	{
		extract( $args );

		$instance = array_merge( $this->default, $instance );

		extract( $instance );

		echo $before_widget;

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="contact_info_wrap">';

		if ( $text )
			echo '<p>' . $text . '</p>';
		if ( $name )
			echo '<p><span class="icon-user ' . $color . '">' . $name . '</span></p>';
		if ( $phone )
			echo '<p><span class="icon-phone ' . $color . '">' . $phone . '</span></p>';
		if ( $cellphone )
			echo '<p><span class="icon-mobile-phone ' . $color . '">' . $cellphone . '</span></p>';
		if ( $email )
			echo '<p><a href="mailto:' . $email . '" class="icon-envelope-alt ' . $color . '">' . $email . '</a></p>';
		if ( $link )
			echo '<p><a href="' . $link . '" target="_blank" class="icon-link ' . $color . '">' . $link . '</a></p>';
		if ( $address || $city || $state || $zip )
		{
			echo '<p class="address"><span class="icon-map-marker ' . $color . '"></span><span class="contact_block">';
			$html = array();
			if ( $address )
				$html[] = '<span class="street">' . $address . '</span>';
			if ( $city )
				$html[] = '<span class="city">' . $city . '</span>';
			if ( $state )
				$html[] = '<span class="state">' . $state . '</span>';
			if ( $zip )
				$html[] = '<span class="zip">' . $zip . '</span>';
			echo implode( '<br>', $html );
			echo '</span></p>';
		}

		echo '</div>';

		echo $after_widget;
	}

	/**
	 * Save form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		return array_merge( $old_instance, array_map( 'strip_tags', $new_instance ) );
	}

	/**
	 * Display form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Custom Text', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<textarea class="widefat" rows="3" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
			</div>
		</div>
		<?php
		$fields = array(
			'name'      => __( 'Name', '7listings' ),
			'phone'     => __( 'Phone', '7listings' ),
			'cellphone' => __( 'Cell / Mobile', '7listings' ),
			'email'     => __( 'Email', '7listings' ),
			'link'      => __( 'Link', '7listings' ),
			'address'   => __( 'Address', '7listings' ),
			'city'      => __( 'City', '7listings' ),
			'state'     => __( 'State', '7listings' ),
			'zip'       => __( 'Zip', '7listings' ),
		);
		foreach ( $fields as $k => $v )
		{
			printf(
				'<div class="sl-settings">
					<div class="sl-label">
						<label>%s</label>
					</div>
					<div class="sl-input">
						<input type="text" class="widefat" name="%s" value="%s">
					</div>
				</div>',
				$v,
				$this->get_field_name( $k ),
				isset( $instance[$k] ) ? esc_attr( $instance[$k] ) : ''
			);
		}
		?>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Icon Color', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'color' ); ?>" class="widefat">
					<?php
					Sl_Form::options( $instance['color'], array(
						'default'     => __( 'Default', '7listings' ),
						'rosy'        => __( 'Rosy', '7listings' ),
						'pink'        => __( 'Pink', '7listings' ),
						'pink-dark'   => __( 'Dark Pink', '7listings' ),
						'red'         => __( 'Red', '7listings' ),
						'magenta'     => __( 'Magenta', '7listings' ),
						'orange'      => __( 'Orange', '7listings' ),
						'orange-dark' => __( 'Dark Orange', '7listings' ),
						'yellow'      => __( 'Yellow', '7listings' ),
						'green-light' => __( 'Light Green', '7listings' ),
						'green-lime'  => __( 'Lime Green', '7listings' ),
						'green'       => __( 'Green', '7listings' ),
						'blue'        => __( 'Blue', '7listings' ),
						'blue-dark'   => __( 'Dark Blue', '7listings' ),
						'indigo'      => __( 'Indigo', '7listings' ),
						'violet'      => __( 'Violet', '7listings' ),
						'cappuccino'  => __( 'Cappuccino', '7listings' ),
						'brown'       => __( 'Brown', '7listings' ),
						'brown-dark'  => __( 'Dark Brown', '7listings' ),
						'gray'        => __( 'Gray', '7listings' ),
						'gray-dark'   => __( 'Dark Gray', '7listings' ),
						'black'       => __( 'Black', '7listings' ),
						'white'       => __( 'White', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
	<?php
	}
}
