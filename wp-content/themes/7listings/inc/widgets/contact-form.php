<?php

/**
 * Contact form widget
 */
class Sl_Widget_Contact_Form extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default;

	/**
	 * Widget constructor
	 */
	public function __construct()
	{
		$this->default = array(
			'title'         => '',
			'customButton'  => 0,
			'bgColor'       => '#00adee',
			'textColor'     => '#fff',
			'email'         => get_bloginfo( 'admin_email' ),
			'customMessage' => 0,
			'message'       => '',
		);
		parent::__construct(
			'sl-contact-form',
			__( '7 - Contact Form', '7listings' ),
			array(
				'classname'   => 'widget_contact_form',
				'description' => __( 'Simple contact form.', '7listings' ),
			)
		);

		if ( is_active_widget( false, false, $this->id_base ) )
		{
			// Ajax callback for sending email
			add_action( 'wp_ajax_sl_widget_cf_send', array( __CLASS__, 'send_email' ) );
			add_action( 'wp_ajax_nopriv_sl_widget_cf_send', array( __CLASS__, 'send_email' ) );
		}
	}

	/**
	 * Show widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		$instance = array_merge( $this->default, $instance );

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		$email = str_replace( '@', '*', $instance['email'] );
		$style = '';
		if ( $instance['customButton'] )
			$style = ' style="background-color:' . $instance['bgColor'] . ';color:' . $instance['textColor'] . '"';
		?>
		<form class="contact_form" method="post" novalidate>
			<div class="status hidden alert"></div>
			<input type="hidden" value="<?php echo $email; ?>" name="contact_to">
			<input type="text" required class="contact_widget_input text_input name" name="contact_name" placeholder="<?php _e( 'Name', '7listings' ); ?>" autocomplete="name">
			<input type="text" required class="contact_widget_input text_input email" name="contact_email" placeholder="<?php _e( 'Email', '7listings' ); ?>" autocomplete="email">
			<textarea name="contact_content" required class="contact_widget_input message" cols="30" rows="5" placeholder="<?php _e( 'Message', '7listings' ); ?>"></textarea>
			<input type="hidden" name="contact_custom_message" value="<?php echo $instance['customMessage']; ?>">
			<input type="hidden" name="contact_message" value="<?php echo esc_attr( $instance['message'] ); ?>">
			<button type="submit"<?php echo $style; ?> class="button primary small"><?php _e( 'Send', '7listings' ); ?></button>
		</form>
		<?php

		echo $args['after_widget'];
	}

	/**
	 * Save form
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance                  = $old_instance;
		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['customButton']  = empty( $new_instance['customButton'] ) ? 0 : 1;
		$instance['bgColor']       = strip_tags( $new_instance['bgColor'] );
		$instance['textColor']     = strip_tags( $new_instance['textColor'] );
		$instance['email']         = strip_tags( $new_instance['email'] );
		$instance['customMessage'] = empty( $new_instance['customMessage'] ) ? 0 : 1;
		$instance['message']       = $new_instance['message'];

		return $instance;
	}

	/**
	 * Display form
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Email', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" value="<?php echo esc_attr( $instance['email'] ); ?>">
			</div>
		</div>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Custom Button', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'customButton' ), $instance['customButton'] ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Button Background', '7listings' ) ?></label>
				</div>
				<div class="sl-input">
					<input type="text" class="color" name="<?php echo esc_attr( $this->get_field_name( 'bgColor' ) ); ?>" value="<?php echo esc_attr( $instance['bgColor'] ); ?>">
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Button Text', '7listings' ) ?></label>
				</div>
				<div class="sl-input">
					<input type="text" class="color" name="<?php echo esc_attr( $this->get_field_name( 'textColor' ) ); ?>" value="<?php echo esc_attr( $instance['textColor'] ); ?>">
				</div>
			</div>
		</div>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Custom Message', '7listings' ) ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'customMessage' ), $instance['customMessage'] ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-input sl-fullwidth">
				<textarea class="widefat" name="<?php echo $this->get_field_name( 'message' ); ?>"><?php echo esc_textarea( $instance['message'] ); ?></textarea>
			</div>
		</div>
	<?php
	}

	/**
	 * Send email via ajax
	 * Use static method for all widget instances
	 *
	 * @return void
	 */
	public function send_email()
	{
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'send-email' ) )
			wp_send_json_error( __( 'Invalid form submit', '7listings' ) );

		$errors = array();

		$to             = empty( $_POST['to'] ) ? '' : $_POST['to'];
		$to             = str_replace( '*', '@', $to );
		$name           = empty( $_POST['name'] ) ? '' : $_POST['name'];
		$email          = empty( $_POST['email'] ) ? '' : $_POST['email'];
		$content        = empty( $_POST['content'] ) ? '' : $_POST['content'];
		$custom_message = ! empty( $_POST['customMessage'] );
		$message        = empty( $_POST['message'] ) ? '' : $_POST['message'];
		$message        = str_replace( '\\', '', $message );
		$url            = empty( $_POST['url'] ) ? '' : $_POST['url'];

		if ( empty( $to ) )
			$errors[] = __( 'Invalid form submit', '7listings' );
		if ( empty( $name ) )
			$errors[] = __( 'Please enter your: <strong>name</strong>.', '7listings' );
		if ( empty( $email ) )
			$errors[] = __( 'Please enter your: <strong>email</strong>.', '7listings' );
		if ( ! is_email( $email ) )
			$errors[] = __( 'Invalid: <strong>email address</strong>.', '7listings' );
		if ( empty( $content ) )
			$errors[] = __( 'Please send a: <strong>message</strong>.', '7listings' );

		if ( ! empty( $errors ) )
			wp_send_json_error( implode( '<br>', $errors ) );

		$replacements = array(
			'[name]'           => $name,
			'[customer-email]' => $email,
			'[message]'        => $content,
			'[page-url]'       => $url,
		);

		$site_name = get_bloginfo( 'name' );

		// Send email to admin
		$subject = sprintf( __( '%s\'s message from %s', '7listings' ), $site_name, $name );
		$body    = sl_email_content( '', 'widget-contact-admin', $replacements );
		$ok      = wp_mail( $to, $subject, $body, "Reply-To: $email" );
		if ( ! $ok )
			wp_send_json_error( __( 'Error sending email to admin, please try again', '7listings' ) );

		// Send email to visitor
		$subject = sprintf( __( 'You have sent a message on %s', '7listings' ), $site_name );
		$body    = sl_email_content( '', 'widget-contact', $replacements );
		$ok      = wp_mail( $email, $subject, $body );
		if ( ! $ok )
			wp_send_json_error( __( 'Error sending email notification to you, please try again', '7listings' ) );

		$message_send = __( '<strong>Thank You!</strong><br>Your message was sent successfully.', '7listings' );

		if ( $custom_message && $message )
			$message_send = $message;

		wp_send_json_success( $message_send );
	}
}
