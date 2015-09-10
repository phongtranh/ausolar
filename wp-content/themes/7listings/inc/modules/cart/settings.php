<?php

/**
 * This class will hold all settings for cart
 */
class Sl_Cart_Settings extends Sl_Core_Settings
{
	/**
	 * @var string Identify name of current module
	 */
	public $post_type = 'cart';

	/**
	 * Add hooks
	 *
	 * @return Sl_Cart_Settings
	 */
	function __construct()
	{
		add_action( 'sl_settings_modules', array( $this, 'show_settings_module' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Check module is enabled and then add hooks if needed
	 *
	 * @return void
	 */
	function init()
	{
		if ( ! Sl_License::is_module_enabled( $this->post_type, false ) )
			return;

		add_action( 'sl_email_tab', array( $this, 'email_tab' ), 5 );
		add_action( 'sl_email_tab_content', array( $this, 'email_tab_content' ), 5 );

		// Save settings for this module in main theme settings page (7listings)
		add_filter( 'sl_settings_sanitize_7listings', array( $this, 'sanitize' ), 10, 2 );
	}

	/**
	 * Show on/off setting for module
	 *
	 * @return void
	 */
	function show_settings_module()
	{
		$enable = in_array( Sl_License::license_type(), array( '7Pro', '7Tours', '7Accommodation', '7Network' ) );
		if ( ! $enable )
			return;
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label>
					<?php _e( 'Cart', '7listings' ); ?>
					<?php echo do_shortcode( '[tooltip content="' . __( 'Enable Shopping Cart<br>for Accommodations, Tours and Rentals', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( 'cart' ); ?>
			</div>
		</div>
	<?php
	}

	/**
	 * Sanitize options
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options )
	{
		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, array( 'cart' ) );
	}

	/**
	 * Add settings tab in "email" settings page
	 *
	 * @return void
	 */
	function email_tab()
	{
		printf( '<a href="#cart-booking" class="nav-tab">%s</a>', __( 'Cart Booking', '7listings' ) );
	}
}

new Sl_Cart_Settings;
