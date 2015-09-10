<?php

/**
 * This class adds settings for default placeholder in Settings \ Media page
 * The settings page uses API to add settings section and fields and we use that to add our upload fields
 * But it does not handle saving settings, so we have to do that by our own
 *
 * Reference code from WooCommerce: includes/admin/class-wc-admin-permalink-settings.php
 */
class Sl_Media_Settings
{
	/**
	 * Constructor
	 *
	 * @return Sl_Media_Settings
	 */
	public function __construct()
	{
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Add hooks to show and save settings
	 *
	 * @return void
	 */
	public function init()
	{
		// Enqueue scripts
		add_action( 'admin_print_styles-options-media.php', array( $this, 'enqueue' ) );

		/**
		 * Add custom settings field
		 * WordPress uses Settings API to add more settings field to this page, so we just use it
		 */
		add_settings_field(
			'sl_default_placeholder',               // id
			__( 'Image Placeholder', '7listings' ), // setting title
			array( $this, 'image_placeholder' ),    // display callback
			'media',                                // settings page
			'default'                               // settings section
		);

		/**
		 * Save action is handled in 'options.php', so we can't hook it in 'options-media.php' page using
		 * 'load-$hook.php' or similar. Instead we have to run it in 'admin_init' and check for nonce
		 */
		$this->save();
	}

	/**
	 * Enqueue scripts and styles for Settings \ Media page
	 *
	 * @return void
	 */
	public function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_script( 'sl-choose-image' );
	}

	/**
	 * Display image placeholder field
	 *
	 * @return void
	 */
	public function image_placeholder()
	{
		$src   = '';
		$class = ' class="hidden"';
		if ( sl_setting( 'image_placeholder' ) )
		{
			$src   = wp_get_attachment_url( sl_setting( 'image_placeholder' ) );
			$class = '';
		}

		/**
		 * Choose image JS script looks for hidden <input> and change value when select an image
		 * Wrapping in a <span> makes sure value of nonce field is not changed
		 */
		echo '<span>';
		wp_nonce_field( 'save', 'sl_nonce_media', false );
		echo '</span>';
		?>
		<img src="<?php echo $src; ?>"<?php echo $class; ?>>
		<input type="hidden" name="sl_image_placeholder" value="<?php echo sl_setting( 'image_placeholder' ); ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
	<?php
	}

	/**
	 * Save image placeholder in Settings \ Media
	 *
	 * @return void
	 */
	public function save()
	{
		if ( empty( $_POST['sl_nonce_media'] ) || ! wp_verify_nonce( $_POST['sl_nonce_media'], 'save' ) )
			return;
		$placeholder = isset( $_POST['sl_image_placeholder'] ) ? $_POST['sl_image_placeholder'] : '';
		sl_set_setting( 'image_placeholder', $placeholder );
	}
}
