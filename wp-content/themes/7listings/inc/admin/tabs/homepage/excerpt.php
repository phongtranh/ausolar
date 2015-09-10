<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Excerpt', '7listings' ) ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle" data-effect="fade">
			<?php Sl_Form::checkbox( "{$prefix}excerpt" ); ?>
		</span>
		<span class="input-append supplementary-input">
			<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo esc_attr( THEME_SETTINGS . "[{$prefix}excerpt_length]" ); ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}excerpt_length" ) ); ?>">
			<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
		</span>
	</div>
</div>
