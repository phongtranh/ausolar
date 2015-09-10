<?php

class Sl_Settings_Advanced extends Sl_Settings_Page
{
	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	function page_content()
	{
		include THEME_TABS . 'settings/advanced.php';
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
			'jquery_on_top',
			'css_no_var',
		) );

		// Custom action
		$actions = array( 'fix_less', 'fix_counter', 'cleanup', 'htaccess', 'fix_reviews' );
		$actions = apply_filters( 'sl_settings_advanced_actions', $actions );
		foreach ( $actions as $action )
		{
			if ( empty( $_POST[$action] ) )
				continue;

			if ( method_exists( $this, $action ) )
				$this->$action( $options_new, $options );
			elseif ( function_exists( "sl_settings_advanced_$action" ) )
				call_user_func( "sl_settings_advanced_$action" );
		}

		return $options_new;
	}

	/**
	 * Fix booking counter
	 *
	 * @param $options_new
	 * @param $options
	 *
	 * @return void
	 */
	function fix_counter( &$options_new, $options )
	{
		// Update post meta
		$bookings = get_posts( array(
			'post_type'      => 'booking',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'order'          => 'ASC',
			'orderby'        => 'post_date',
		) );
		$counter  = 0;
		foreach ( $bookings as $booking )
		{
			update_post_meta( $booking->ID, 'booking_id', ++ $counter );
		}

		// Update counter
		$options_new['counter'] = $counter;
	}

	/**
	 * Fix LESS
	 *
	 * @param $options_new
	 * @param $options
	 *
	 * @return void
	 */
	function fix_less( &$options_new, $options )
	{
		if ( is_multisite() )
		{
			global $wpdb;
			$blogs = $wpdb->get_col( "SELECT DISTINCT blog_id FROM {$wpdb->blogs}" );

			foreach ( $blogs as $blog )
			{
				switch_to_blog( $blog );
				self::remove_less_cache();
				restore_current_blog();
			}
		}
		else
		{
			self::remove_less_cache();
		}

		// Re-compile all admin CSS files
		$admin_less = array(
			'sl-admin-menu'      => THEME_LESS . 'admin/menu.less',
			'sl-admin'           => THEME_LESS . 'admin/7admin.less',
			'sl-tinymce-preview' => THEME_LESS . 'admin/shortcodes/tinymce-preview.less',
			'sl-tooltip'         => THEME_LESS . 'admin/tooltip.less',
			'sl-booking-report'  => THEME_LESS . 'admin/booking-report.less',
		);
		foreach ( $admin_less as $handle => $src )
		{
			Sl_Less::parse( $src, $handle );
		}

		// Trigger to update design on load
		$options_new['updated_design'] = 1;
	}

	/**
	 * Remove LESS cache directory
	 *
	 * Static method = used in other places
	 *
	 * @return void
	 */
	static function remove_less_cache()
	{
		$upload_dir = wp_upload_dir();
		$dir        = path_join( $upload_dir['basedir'], 'peace-less' );

		// Create less dir if not available
		if ( ! file_exists( $dir ) || ! is_dir( $dir ) )
			wp_mkdir_p( $dir );

		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file )
		{
			unlink( "$dir/$file" );
		}

		// Write custom CSS to .less file to make sure it's recompile on next load
		$file = trailingslashit( $dir ) . 'custom.less';
		@file_put_contents( $file, sl_setting( 'design_custom_css' ) );
	}

	/**
	 * Remove extra ".00" from prices
	 *
	 * @param $options_new
	 * @param $options
	 *
	 * @return void
	 */
	function cleanup( &$options_new, $options )
	{
		global $wpdb;

		$post_types = sl_setting( 'listing_types' );
		foreach ( $post_types as &$post_type )
		{
			$post_type = "'$post_type'";
		}
		$post_types = implode( ',', $post_types );

		// Remove extra ".00" from prices
		$query    = "SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type IN ($post_types)";
		$post_ids = $wpdb->get_col( $query );
		if ( empty( $post_ids ) )
			return;

		foreach ( $post_ids as $post_id )
		{
			// Lead in rate
			$price_from = get_post_meta( $post_id, 'price_from', true );
			$price_from = ( int ) $price_from;
			update_post_meta( $post_id, 'price_from', $price_from );

			$post_type = get_post_type( $post_id );
			$name      = sl_meta_key( 'booking', $post_type );
			$details   = get_post_meta( $post_id, $name, true );
			if ( empty( $details ) || ! is_array( $details ) )
				continue;

			// Accommodation booking
			if ( 'accommodation' == $post_type )
			{
				foreach ( $details as & $detail )
				{
					foreach ( array( 'price', 'price_extra' ) as $type )
					{
						if ( ! empty( $detail[$type] ) )
							$detail[$type] = intval( $detail[$type] );
					}
				}
				update_post_meta( $post_id, $name, $details );
			}

			// Tour booking
			if ( 'tour' == $post_type )
			{
				foreach ( $details as & $detail )
				{
					foreach ( array( 'adult', 'senior', 'child', 'infant', 'family' ) as $type )
					{
						if ( ! empty( $detail["price_{$type}"] ) )
							$detail["price_{$type}"] = intval( $detail["price_{$type}"] );
					}

					if ( ! empty( $detail['upsell_prices'] ) && is_array( $detail['upsell_prices'] ) )
						$detail['upsell_prices'] = array_map( 'intval', $detail['upsell_prices'] );
				}
				update_post_meta( $post_id, $name, $details );
			}

			// Rental booking
			if ( 'rental' == $post_type )
			{
				foreach ( $details as & $detail )
				{
					if ( empty( $detail['price'] ) )
						continue;
					$detail['price'] = array_filter( array_map( 'intval', $detail['price'] ) );
				}
				update_post_meta( $post_id, $name, $details );
			}
		}
	}

	/**
	 * Add rules to .htaccess
	 *
	 * @param $options_new
	 * @param $options
	 *
	 * @return void
	 */
	function htaccess( &$options_new, $options )
	{
		$file = get_home_path() . '.htaccess';

		$rules   = array();
		$rules[] = 'Header unset ETag';
		$rules[] = 'FileETag None';
		$rules[] = 'ExpiresActive On';
		$rules[] = 'ExpiresDefault "access plus 1 month"';
		$rules[] = 'ExpiresByType text/html "access plus 1 day"';
		$rules[] = 'ExpiresByType image/gif "access plus 1 year"';
		$rules[] = 'ExpiresByType image/jpeg "access plus 1 year"';
		$rules[] = 'ExpiresByType image/png "access plus 1 year"';
		$rules[] = 'ExpiresByType text/css "access plus 1 year"';
		$rules[] = 'ExpiresByType text/javascript "access plus 1 year"';
		$rules[] = 'ExpiresByType application/x-javascript "access plus 1 year"';
		$rules[] = 'ExpiresByType application/javascript "access plus 1 year"';
		$rules[] = '<FilesMatch ".(js|css|html|htm|php|xml)$">';
		$rules[] = 'SetOutputFilter DEFLATE';
		$rules[] = '</FilesMatch>';

		insert_with_markers( $file, '7listings', $rules );
	}

	/**
	 * Fix reviews: set comment and ping status to open
	 *
	 * @param $options_new
	 * @param $options
	 *
	 * @return void
	 */
	function fix_reviews( &$options_new, $options )
	{
		global $wpdb;
		$wpdb->query( "UPDATE $wpdb->posts SET comment_status = 'open'" );
		$wpdb->query( "UPDATE $wpdb->posts SET ping_status = 'open'" );
	}
}

new Sl_Settings_Advanced( 'advanced', __( 'Advanced', '7listings' ) );
