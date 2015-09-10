<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Excerpt', '7listings' ) ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle" data-effect="fade">
			<?php Sl_Form::checkbox_general( $this->get_field_name( 'excerpt' ), $instance['excerpt'] ); ?>
		</span>
		<span class="input-append">
			<input type="number" class="small-text" min="1" max="<?php echo intval( sl_setting( 'excerpt_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'excerpt_length' ) ); ?>" value="<?php echo intval( $instance['excerpt_length'] ); ?>">
			<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
		</span>
	</div>
</div>
