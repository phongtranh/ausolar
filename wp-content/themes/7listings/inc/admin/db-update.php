<?php

/**
 * Run updates for database (theme options) if needed for each new version
 *
 * @since 5.0.4
 */
class Sl_Database_Update
{
	/**
	 * Add hooks
	 *
	 * @return Sl_Database_Update
	 */
	function __construct()
	{
		// Run as soon as possible
		add_action( 'after_setup_theme', array( $this, 'update' ), 0 );
	}

	/**
	 * Perform database update
	 * Each database update will be a separated function
	 *
	 * @return void
	 */
	function update()
	{
		// Get theme version
		$settings = get_option( THEME_SETTINGS );
		$version  = isset( $settings['version'] ) ? $settings['version'] : '0';

		// Update theme version
		$theme = wp_get_theme( get_template() ); // Use get_template() to make sure it runs for 7listings, not child theme

		// If current theme is the latest version
		if ( version_compare( $version, $theme->version, '>=' ) )
			return;

		$settings['version'] = $theme->version;
		update_option( THEME_SETTINGS, $settings );

		// Run update for each version change
		if ( version_compare( $version, '5.0.5', '<' ) )
		{
			$this->tax_image();
		}
		if ( version_compare( $version, '5.1.1', '<' ) )
		{
			$this->social_buttons();
		}
		if ( version_compare( $version, '5.1.2', '<' ) )
		{
			$this->update_tour_departure_types();
		}
		if ( version_compare( $version, '5.3.2', '<' ) )
		{
			$this->update_background();
			$this->update_background( 'featured' );
		}
	}

	/**
	 * Update option for taxonomy images
	 *
	 * @since 5.0.5
	 *
	 * @return void
	 */
	function tax_image()
	{
		$old_option_name = 'rwaa_tax_image';

		// Get old option
		if ( $tax_image = get_option( $old_option_name ) )
		{
			// Turn it into another format and add to theme settings
			$term_meta = array();
			foreach ( $tax_image as $term_id => $thumbnail_id )
			{
				if ( ! isset( $term_meta[$term_id] ) )
					$term_meta[$term_id] = array();
				$term_meta[$term_id]['thumbnail_id'] = $thumbnail_id;
			}

			// Update to theme options
			$settings              = get_option( THEME_SETTINGS );
			$settings['term_meta'] = $term_meta;
			update_option( THEME_SETTINGS, $settings );
		}

		// Delete old option
		delete_option( $old_option_name );
	}

	/**
	 * Update option for social buttons
	 *
	 * @since 5.1.1
	 *
	 * @return void
	 */
	function social_buttons()
	{
		// Update to theme options
		$settings = get_option( THEME_SETTINGS );
		if ( ! isset( $settings['social_display'] ) )
			return;

		$settings['design_header_social_display'] = $settings['social_display'];
		unset( $settings['social_display'] );
		update_option( THEME_SETTINGS, $settings );
	}

	/**
	 * Update departure types
	 *
	 * @since 5.1.2
	 */
	function update_tour_departure_types()
	{
		$listings = get_posts( array(
			'post_type'      => 'tour',
			'posts_per_page' => - 1,
		) );
		$meta_key = sl_meta_key( 'booking', 'tour' );
		foreach ( $listings as $listing )
		{
			$resources = get_post_meta( $listing->ID, $meta_key, true );
			if ( empty( $resources ) )
				continue;
			foreach ( $resources as &$resource )
			{
				$type = 'custom';
				if ( ! empty( $resource['depart'] ) )
				{
					$type = 'daily';
				}
				elseif ( ! empty( $resource['daily_departure'] ) )
				{
					$days = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
					foreach ( $days as $day )
					{
						if ( ! empty( $resource["{$day}_depart"] ) )
						{
							$type = 'specific';
							break;
						}
					}
				}

				unset( $resource['custom_departure'] );
				unset( $resource['daily_departure'] );
				$resource['departure_type'] = $type;
			}
			update_post_meta( $listing->ID, $meta_key, $resources );
		}
	}

	/**
	 * Update departure types
	 *
	 * @since 5.3.2
	 *
	 * @param string $base
	 */
	function update_background( $base = '' )
	{
		if ( ! $base )
		{
			$prefix = 'design_background_';
			$full   = 'design_full_background_';
			$tiled  = 'design_tiled_background_';
		}
		else
		{
			$prefix = 'design_' . $base . '_background_';
			$full   = 'design_' . $base . '_full_background_';
			$tiled  = 'design_' . $base . '_tiled_background_';
		}

		$settings = get_option( THEME_SETTINGS );

		// Full background
		if ( isset( $settings[$prefix . 'type'] ) && 'full' == $settings[$prefix . 'type'] )
		{
			$settings[$prefix . 'image'] = 1;

			$fields = array(
				$full . 'image_id',
				$full . 'attachment',
			);
			foreach ( $fields as $field )
			{
				if ( ! empty( $settings[$tiled . $field] ) )
				{
					$settings[$prefix . $field] = $settings[$full . $field];
				}
			}
		}
		// Tiled background
		elseif ( isset( $settings[$prefix . 'type'] ) && 'tiled' == $settings[$prefix . 'type'] )
		{
			$settings[$prefix . 'image'] = 1;

			$fields = array(
				$tiled . 'image_id',
				$tiled . 'position_x',
				$tiled . 'position_y',
				$tiled . 'repeat',
				$tiled . 'attachment',
			);
			foreach ( $fields as $field )
			{
				if ( ! empty( $settings[$tiled . $field] ) )
				{
					$settings[$prefix . $field] = $settings[$tiled . $field];
				}
			}
		}

		// Change option name from 'featured_area_background' to 'featured_background'
		if ( ! empty( $settings['design_featured_area_background'] ) )
		{
			$settings['design_featured_background'] = $settings['design_featured_area_background'];
		}

		unset(
			$settings[$full . 'image_id'],
			$settings[$full . 'attachment'],
			$settings[$tiled . 'image_id'],
			$settings[$tiled . 'position_x'],
			$settings[$tiled . 'position_y'],
			$settings[$tiled . 'repeat'],
			$settings[$tiled . 'attachment'],
			$settings['design_featured_area_background']
		);

		update_option( THEME_SETTINGS, $settings );
	}
}
