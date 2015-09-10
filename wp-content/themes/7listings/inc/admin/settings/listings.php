<?php

class Sl_Settings_Listings extends Sl_Settings_Page
{
	/**
	 * Enqueue scripts and styles for setting pages
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'sl-listings', THEME_JS . 'admin/listings.js' );
		do_action( 'sl_admin_scripts_listings' );
	}

	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		$payment = in_array( Sl_License::license_type(), array( '7Network', '7Pro', '7Tours', '7Accommodation', '7Rental', '7Comp' ) );

		echo '
			<h2 class="nav-tab-wrapper sl-tabs">
				<a href="#listings" class="nav-tab">' . __( 'Listings', '7listings' ) . '</a>
		';

		if ( $payment )
			echo '<a href="#payment" class="nav-tab">' . __( 'Payment', '7listings' ) . '</a>';

		echo '<a href="#comments" class="nav-tab">' . __( 'Comments', '7listings' ) . '</a>';

		echo '</h2>';

		echo '<div class="sl-tabs-content">';

		echo '<div>';
		include THEME_TABS . 'settings/listings.php';
		echo '</div>';

		if ( $payment )
		{
			echo '<div>';
			include THEME_TABS . 'settings/payment.php';
			echo '</div>';
		}

		echo '<div>';
		include THEME_TABS . 'settings/comments.php';
		echo '</div>';

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
		self::sanitize_checkboxes( $options_new, $options, array(
			'attractions',
			'sandbox_mode',
			'paypal',
			'eway',
			'eway_shared',
			'eway_hosted',
			'sandbox_mode',
			'eway_sandbox',
			'payment_gateway',
			'ssl',
			'google_map',
			'comments_page',
			'comments_style',
			'comments_website'
		) );

		if ( empty( $options['listing_types'] ) )
			$options_new['listing_types'] = array();

		// Update .htaccess
		if ( ! empty( $options['eway_hosted'] ) && ! empty( $options['ssl'] ) )
			$this->update_htaccess( true );
		else
			$this->update_htaccess( false );

		// Remove WooCommerce CSS
		$woocommerce_css = get_option( 'woocommerce_frontend_css' );
		if ( 'yes' == $woocommerce_css )
			update_option( 'woocommerce_frontend_css', 'no' );

		return $options_new;
	}

	/**
	 * Add and remove rewrite rules for enabling SSL on booking pages
	 *
	 * @param bool $add
	 *
	 * @return void
	 */
	function update_htaccess( $add = true )
	{
		$file  = get_home_path() . '.htaccess';
		$rules = array();
		if ( $add )
		{
			$rules[] = '<IfModule mod_rewrite.c>';
			$rules[] = 'RewriteEngine On';
			$rules[] = 'RewriteBase /';
			$rules[] = 'RewriteCond %{ENV:HTTPS} !on';
			// $rules[] = 'RewriteCond %{HTTP_REFERER} /book/ [OR]';
			$rules[] = 'RewriteCond %{REQUEST_URI} ^/book/';
			$rules[] = 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]';
			$rules[] = '</IfModule>';
		}
		insert_with_markers( $file, 'SSL on booking pages', $rules );
	}
}

new Sl_Settings_Listings( '7listings', __( 'Settings', '7listings' ) );
