<?php

class Sl_Settings_License extends Sl_Settings_Page
{
	/**
	 * Constructor
	 *
	 * @return Sl_Settings_License
	 */
	function __construct()
	{
		add_action( 'admin_menu', array( $this, 'run' ), 1 );
	}

	/**
	 * Add hooks, replicate parent constructor
	 *
	 * @return void
	 */
	function run()
	{
		$slug = Sl_License::is_activated() ? 'license' : '7listings';
		parent::__construct( $slug, __( 'License', '7listings' ) );
		add_action( 'sl_admin_menu_no_license', array( $this, 'add_page' ) );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		include THEME_TABS . 'settings/license.php';
	}

	/**
	 * Add more custom content on the bottom of the form
	 *
	 * @return void
	 */
	function form_bottom()
	{
		?>
		<div class="hint seo">
			<?php
			_e( '<strong>Note:</strong><br>
			You need a valid license to activate theme.<br>
			Purchase a license at <a href="http://www.7listings.net/" target="_blank">www.7listings.net</a><br><br>
			<a href="http://www.7listings.net/my-account/" target="_blank">My Account</a> | <a href="http://www.7listings.net/my-account/lost-licence/" target="_blank">Lost License</a>', '7listings' );
			?>
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
		if ( empty( $options['license_email'] ) || empty( $options['license_key'] ) )
			return $options_new;

		$options_new['license_valid'] = Sl_License::activate( $options['license_email'], $options['license_key'] );

		if ( $options_new['license_valid'] )
			add_settings_error( 'sl-settings', 'license', __( 'License Updated', '7listings' ), 'updated' );
		else
			add_settings_error( 'sl-settings', 'license', __( 'Invalid License', '7listings' ), 'error' );

		return $options_new;
	}
}

new Sl_Settings_License;
