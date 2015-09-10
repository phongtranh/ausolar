<?php

class Sl_Settings_Emails extends Sl_Settings_Page
{
	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_media();
		wp_enqueue_script( 'sl-choose-image' );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		echo '<h2 class="nav-tab-wrapper sl-tabs">';
		do_action( 'sl_email_tab' );
		printf( '<a href="#contact-us" class="nav-tab">%s</a>', __( 'Contact Us', '7listings' ) );
		echo '</h2>';

		echo '<div class="sl-tabs-content">';
		do_action( 'sl_email_tab_content' );

		echo '<div id="emails-contact">';
		include THEME_TABS . 'settings/emails-contact.php';
		echo '</div>';
		echo '</div>';

		echo '<br><br><br><br>';

		echo '<h2 class="nav-tab-wrapper sl-tabs">';
		printf( '<a href="#settings" class="nav-tab">%s</a>', __( 'Settings', '7listings' ) );
		printf( '<a href="#smtp" class="nav-tab">%s</a>', __( 'SMTP', '7listings' ) );
		echo '</h2>';

		echo '<div class="sl-tabs-content">';
		echo '<div>';
		include THEME_TABS . 'settings/emails-settings.php';
		echo '</div>';
		echo '<div>';
		include THEME_TABS . 'settings/emails-smtp.php';
		echo '</div>';
		echo '</div>';

	}

	/**
	 * Add more custom content on the bottom of the form
	 *
	 * @return void
	 */
	function form_bottom()
	{
		echo '<div class="hint seo">';
		_e( '<strong>Google Custom Campaigns</strong><br>Your email templates have the following strings in url:<br>?utm_campaign=Website Notification<br>&utm_medium=email<br>&utm_source=WebsiteEmail', '7listings' );
		echo '</div>';
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
		return self::sanitize_checkboxes( $options_new, $options, array(
			'emails_use_template',
			'emails_smtp_enable',
			'emails_smtp_auth',
		) );
	}
}

new Sl_Settings_Emails( 'emails', __( 'Emails', '7listings' ) );
