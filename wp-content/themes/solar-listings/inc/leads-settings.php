<?php

class Solar_Leads_Settings
{
	function __construct()
	{
		add_action( 'sl_admin_menu', array( $this, 'add_page' ) );

		add_action( 'wp_ajax_solar_leads_update', array( $this, 'ajax_update' ) );
		add_filter( 'sl_settings_sanitize', array( $this, 'sanitize' ), 10, 3 );
	}

	/**
	 * Add admin page
	 *
	 * @return void
	 */
	function add_page()
	{
		$page = add_submenu_page( '7listings', __( 'Leads', '7listings' ), __( 'Leads', '7listings' ), 'edit_theme_options', 'leads-settings', array( $this, 'show' ) );
		add_action( "load-$page", array( $this, 'load' ) );
		add_action( "admin_print_styles-$page", array( $this, 'enqueue' ) );
	}

	/**
	 * Show admin page
	 *
	 * @return void
	 */
	function show()
	{
		?>
		<div class="wrap">
			<form method="post" action="options.php" enctype="multipart/form-data">
				<h2><?php _e( 'Leads', '7listings' ); ?></h2>

				<?php settings_fields( THEME_SETTINGS ); ?>
				<input type="hidden" name="page" value="leads_settings">

				<div class="metabox-holder">
					<div class="postbox-container normal">
						<?php do_meta_boxes( 'sl-settings-leads', 'normal', null ); ?>
					</div>
				</div>

				<p class="submit">
					<?php submit_button( __( 'Save', '7listings' ), 'primary', 'submit', false ); ?>
				</p>
			</form>
		</div>
	<?php
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function load()
	{
		add_meta_box( 'leads-settings', __( 'Leads', '7listings' ), array( $this, 'box' ), 'sl-settings-leads', 'normal' );
	}

	/**
	 * Leads box
	 *
	 * @return void
	 */
	function box()
	{
		include CHILD_DIR . 'inc/admin-pages/leads-settings.php';
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'solar-leads-settings', CHILD_URL . 'js/admin/leads.js', array( 'jquery' ), '', true );
		wp_localize_script( 'solar-leads-settings', 'Solar', array(
			'nonceUpdate' => wp_create_nonce( 'update' )
		) );
	}

	/**
	 * Update terms & conditions time
	 *
	 * @return void
	 */
	function ajax_update()
	{
		check_ajax_referer( 'update', 'nonce' );

		$settings = get_option( THEME_SETTINGS );
		$now = time();
		$settings['terms_cond_update'] = $now;
		update_option( THEME_SETTINGS, $settings );

		wp_send_json_success( date( get_option( 'date_format' ), $now ) );
	}

	/**
	 * Sanitize options
	 *
	 * @param array  $options_new
	 * @param array  $options
	 * @param string $page
	 *
	 * @return array
	 */
	function sanitize( $options_new, $options, $page )
	{
		if ( 'leads_settings' != $page )
			return $options_new;

		// Checkboxes
		$fields = [
			'auto_leads_email_sending',
			'solar_notification_paypal',
			'solar_payment_post_pay',
			'solar_payment_direct_debit',
			'solar_payment_upfront',
			'terms_cond_popup',
			'enable_compare_membership',
			'lead_frequency_day',
			'lead_frequency_week',
			'lead_frequency_month',
			'leads_cap_notification'
		];
		
		return Sl_Settings_Page::sanitize_checkboxes( $options_new, $options, $fields );
	}
}

new Solar_Leads_Settings;
