<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Address', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[address]" class="sl-input-large address line-one" value="<?php echo esc_textarea( sl_setting( 'address' ) ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'City', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[general_city]" class="sl-input-medium city" value="<?php echo esc_attr( sl_setting( 'general_city' ) ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'State', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[state]" class="sl-input-medium" value="<?php echo esc_attr( sl_setting( 'state' ) ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Country', '7listings' ); ?><span class="warning-sm required right"></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[country]" class="sl-input-medium" value="<?php echo esc_attr( sl_setting( 'country' ) ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Post Code', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[postcode]" class="sl-input-small postcode" value="<?php echo esc_attr( sl_setting( 'postcode' ) ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Map', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display location with marker<br>with Google Maps', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( 'google_map' ); ?>
			</div>
		</div>

		<hr class="light">

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Email', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="email" name="<?php echo THEME_SETTINGS; ?>[email]" class="sl-input-large email" value="<?php echo esc_attr( sl_setting( 'email' ) ); ?>" placeholder="<?php esc_attr_e( 'email@mywebsite.com', '7listings' ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Phone', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="tel" name="<?php echo THEME_SETTINGS; ?>[phone]" class="sl-input-medium phone" value="<?php echo esc_attr( sl_setting( 'phone' ) ); ?>" placeholder="<?php esc_attr_e( 'Phone number', '7listings' ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Fax', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[fax]" value="<?php echo esc_attr( sl_setting( 'fax' ) ); ?>" placeholder="<?php esc_attr_e( 'Fax', '7listings' ); ?>">
			</div>
		</div>
		
		<br>
		
		<h3><?php _e( 'Business Hours', '7listings' ); ?></h3>

		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Display', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Turn ON to display business hours', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( 'business_hours' ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings checkbox-toggle" data-reverse="1">
				<div class="sl-label">
					<label><?php _e( 'Open 24/7', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Turn ON if your business is open 24/7', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( 'open_247' ); ?>
				</div>
			</div>
			<div class="sl-sub-settings">
				<?php
				$days = array(
					'mo' => __( 'Monday', '7listings' ),
					'tu' => __( 'Tuesday', '7listings' ),
					'we' => __( 'Wednesday', '7listings' ),
					'th' => __( 'Thursday', '7listings' ),
					'fr' => __( 'Friday', '7listings' ),
					'sa' => __( 'Saturday', '7listings' ),
					'su' => __( 'Sunday', '7listings' ),
				);
				foreach ( $days as $k => $v )
				{
					?>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php echo $v; ?></label>
						</div>
						<div class="sl-input">
					<span>
						<span class="checkbox-toggle" data-effect="fade">
							<?php Sl_Form::checkbox( "business_hours_$k" ); ?>
						</span>
						<span>
							<span class="input-prepend from-time">
								<span class="add-on"><?php _e( 'From', '7listings' ); ?></span>
								<input type="text" name="<?php echo THEME_SETTINGS . "[business_hours_{$k}_from]"; ?>" class="hours timepicker" value="<?php echo esc_attr( sl_setting( "business_hours_{$k}_from" ) ); ?>">
							</span>
							<span class="input-prepend to-time">
								<span class="add-on"><?php _e( 'To', '7listings' ); ?></span>
								<input type="text" name="<?php echo THEME_SETTINGS . "[business_hours_{$k}_to]"; ?>" class="hours timepicker" value="<?php echo esc_attr( sl_setting( "business_hours_{$k}_to" ) ); ?>">
							</span>
						</span>
					</span>
						</div>
					</div>
				<?php
				}
				?>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Special Days', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter a message<br>to tell your customers about special days you are open or closed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input sl-block">
						<textarea name="<?php echo THEME_SETTINGS; ?>[special_days]" rows="4"><?php echo esc_textarea( sl_setting( 'special_days' ) ); ?></textarea>
					</div>
				</div>
			</div>
		</div>
		
		<br>
		
		<?php do_action( 'sl_settings_contact_col1_bottom' ); ?>
	</div>
	<div class="column-2">
		<h3><?php _e( 'Contact Form', '7listings' ); ?></h3>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Custom Form', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Turn ON<br>and insert form shortcode for a custom contact form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( 'contact_custom_contact_form' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label>&nbsp;</label>
			</div>
			<div class="sl-input sl-block">
				<textarea name="<?php echo THEME_SETTINGS; ?>[contact_form_shortcode]" rows="4"><?php echo esc_textarea( sl_setting( 'contact_form_shortcode' ) ); ?></textarea>
			</div>
		</div>
	</div>
</div>
