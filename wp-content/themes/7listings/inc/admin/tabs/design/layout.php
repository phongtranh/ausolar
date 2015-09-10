<div class="layout-option">
	<span>
		<label for="layout-option-1" title="<?php _e( 'Full width', '7listings' ); ?>">&nbsp;</label>
		<input name="<?php echo THEME_SETTINGS; ?>[layout_option]" type="radio" value="no-box" id="layout-option-1" <?php checked( sl_setting( 'layout_option' ), 'no-box' ); ?>>
	</span>
	<span>
		<label for="layout-option-2" title="<?php _e( '1 Box', '7listings' ); ?>">&nbsp;</label>
		<input name="<?php echo THEME_SETTINGS; ?>[layout_option]" type="radio" value="one-box" id="layout-option-2" <?php checked( sl_setting( 'layout_option' ), 'one-box' ); ?>>
	</span>
	<span>
		<label for="layout-option-3" title="<?php _e( 'Boxed', '7listings' ); ?>">&nbsp;</label>
		<input name="<?php echo THEME_SETTINGS; ?>[layout_option]" type="radio" value="boxes" id="layout-option-3" <?php checked( sl_setting( 'layout_option' ), 'boxes' ); ?>>
	</span>
</div>
<br><br>
<div class="sl-settings layout site">
	<div class="sl-label">
		<label><?php _e( 'Alignment', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span>
			<input type="radio" id="layout-position-left" name="<?php echo THEME_SETTINGS; ?>[layout_position]" value="left"<?php checked( sl_setting( 'layout_position' ), 'left' ); ?>>
			<label for="layout-position-left" title="<?php _e( 'Content left', '7listings' ); ?>" class="left-content<?php echo 'left' == sl_setting( 'layout_position' ) ? ' active' : ''; ?>">&nbsp;</label>
		</span>
		<span>
			<input type="radio" id="layout-position-center" name="<?php echo THEME_SETTINGS; ?>[layout_position]" value="center"<?php checked( sl_setting( 'layout_position' ), 'center' ); ?>>
			<label for="layout-position-center" title="<?php _e( 'Content centered', '7listings' ); ?>" class="centered-content<?php echo 'center' == sl_setting( 'layout_position' ) ? ' active' : ''; ?>">&nbsp;</label>
		</span>
		<span>
			<input type="radio" id="layout-position-right" name="<?php echo THEME_SETTINGS; ?>[layout_position]" value="right"<?php checked( sl_setting( 'layout_position' ), 'right' ); ?>>
			<label for="layout-position-right" title="<?php _e( 'Content right', '7listings' ); ?>" class="right-content<?php echo 'right' == sl_setting( 'layout_position' ) ? ' active' : ''; ?>">&nbsp;</label>
		</span>
	</div>
</div>
