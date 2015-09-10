<p>
	<span class="checkbox-toggle" data-effect="fade">
		<?php Sl_Form::checkbox_general( $this->get_field_name( 'excerpt' ), $instance['excerpt'] ); ?>
		<label><?php _e( 'Excerpt', '7listings' ) ?></label>
	</span>
	<span class="input-append supplementary-input">
		<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo esc_attr( $this->get_field_name( 'excerpt_length' ) ); ?>" value="<?php echo esc_attr( $instance['excerpt_length'] ); ?>">
		<span class="add-on"><?php _e( 'Words', '7listings' ); ?></span>
	</span>
</p>
