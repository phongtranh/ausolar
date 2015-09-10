<h2><?php _e( 'Social Icons', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Color', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div class="toggle-choices" data-effect="fade">
			<select class="input-small" name="<?php echo THEME_SETTINGS . '[design_social_icon_color_scheme]'; ?>">
				<?php
				Sl_Form::options( sl_setting( 'design_social_icon_color_scheme' ), array(
					''       => __( 'Default', '7listings' ),
					'dark'   => __( 'Dark', '7listings' ),
					'light'  => __( 'Light', '7listings' ),
					'custom' => __( 'Custom', '7listings' ),
				) );
				?>
			</select>
		</div>
		<div data-name="<?php echo THEME_SETTINGS . '[design_social_icon_color_scheme]'; ?>" data-value="custom">
			<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[design_social_icon_color]'; ?>" value="<?php echo sl_setting( 'design_social_icon_color' ); ?>">
		</div>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Counter', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_social_icon_counter' ); ?>
	</div>
</div>
