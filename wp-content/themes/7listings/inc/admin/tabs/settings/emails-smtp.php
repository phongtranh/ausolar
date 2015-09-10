<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'SMTP', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable to send all emails from Wordpress using SMTP instead of phpmail() to prevent blocking by some hosts', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'emails_smtp_enable' ); ?> <?php echo do_shortcode( '[tooltip content="' . __( 'Warning: Deactivate other SMTP plugins to avoid any conflicts', '7listings' ) . '" type="warning"]<span class="icon"></span>[/tooltip]' ); ?>
	</div>
</div>

<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Host', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS . '[emails_smtp_host]'; ?>" value="<?php echo sl_setting( 'emails_smtp_host' ); ?>">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Secure', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<label><input type="radio" name="<?php echo THEME_SETTINGS . '[emails_smtp_secure]'; ?>" value="none" <?php checked( sl_setting( 'emails_smtp_secure' ), 'none' ); ?>> <?php _e( 'None', '7listings' ); ?>
			</label><br />
			<label><input type="radio" name="<?php echo THEME_SETTINGS . '[emails_smtp_secure]'; ?>" value="ssl" <?php checked( sl_setting( 'emails_smtp_secure' ), 'ssl' ); ?>> SSL</label>
			<br />
			<label><input type="radio" name="<?php echo THEME_SETTINGS . '[emails_smtp_secure]'; ?>" value="tls" <?php checked( sl_setting( 'emails_smtp_secure' ), 'tls' ); ?>> TLS</label>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Port', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="number" name="<?php echo THEME_SETTINGS . '[emails_smtp_port]'; ?>" value="<?php echo sl_setting( 'emails_smtp_port' ); ?>" class="sl-input-tiny">
		</div>
	</div>
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label><?php _e( 'Authorization', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'emails_smtp_auth' ); ?>
		</div>
	</div>
	<div class="sl-sub-settings">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Username', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . '[emails_smtp_username]'; ?>" value="<?php echo sl_setting( 'emails_smtp_username' ); ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Password', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="password" name="<?php echo THEME_SETTINGS . '[emails_smtp_password]'; ?>" value="<?php echo sl_setting( 'emails_smtp_password' ); ?>">
			</div>
		</div>
	</div>
</div>
