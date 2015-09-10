<div class="sl-settings">
	<div class="sl-label">
		<label><?php esc_html_e( 'Import & Export', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<textarea rows="5" id="design-settings"><?php
			$saved = get_option( THEME_SETTINGS );

			// Fields not started with 'design_'
			$fields = array(
				'logo_display',
				'logo',
				'logo_svg',
				'svg_display',
				'logo_width',
				'logo_height',

				'display_site_title',
				'display_site_description',

				'weather_active',
				'weather_unit',
				'weather_timezone',
				'city',
				'woeid',

				'layout_option',

				'favicon',

				'excerpt_more',
			);
			foreach ( $saved as $k => $v )
			{

				if ( ! in_array( $k, $fields ) && 0 !== strpos( $k, 'design_' ) && 0 !== strpos( $k, 'emails_' ) )
					unset( $saved[$k] );
			}

			echo esc_textarea( base64_encode( serialize( $saved ) ) );
			?></textarea>
		<p class="description"><?php _e( 'You can transfer the saved design between different installs by copying the code inside the text box. To import design from another install, replace the code in the text box with the one from another install and click "Import Design" button.', '7listings' ); ?></p>
		<a href="#" class="button" id="import-design"><?php _e( 'Import Design', '7listings' ); ?></a>
		<span class="spinner" style="float:none"></span>
	</div>
</div>
