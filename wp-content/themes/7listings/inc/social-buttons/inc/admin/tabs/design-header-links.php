<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Social Buttons', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display social media buttons in your header', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_header_social_display' ); ?>
		<span class="input-hint"><?php echo do_shortcode( '[tooltip content="' . __( 'This input uses settings: 7Listings > Social Media', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?></span>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Color', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<div class="toggle-choices" data-effect="fade">
				<select class="input-small" name="<?php echo THEME_SETTINGS . '[design_header_social_color_scheme]'; ?>">
					<?php
					Sl_Form::options( sl_setting( 'design_header_social_color_scheme' ), array(
						' '       => __( 'Default', '7listings' ),
						'dark'   => __( 'Dark', '7listings' ),
						'light'  => __( 'Light', '7listings' ),
						'custom' => __( 'Custom', '7listings' ),
					) );
					?>
				</select>
			</div>
			<div data-name="<?php echo THEME_SETTINGS . '[design_header_social_color_scheme]'; ?>" data-value="custom">
				<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[design_header_social_color]'; ?>" value="<?php echo sl_setting( 'design_header_social_color' ); ?>">
			</div>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Counter', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'design_header_social_counter' ); ?>
		</div>
	</div>
</div>
