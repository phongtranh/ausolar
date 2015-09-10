<p>
	<label class="input-label"><?php _e( 'Transition', '7listings' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'transition' ); ?>">
		<?php
		Sl_Form::options( $instance['transition'], array(
			'fade'       => __( 'Fade', '7listings' ),
			'scrollHorz' => __( 'Scroll Horizontally', '7listings' ),
		) );
		?>
	</select>
</p>
<p>
	<label class="input-label"><?php _e( 'Delay', '7listings' ); ?></label>
	<span class="input-append">
		<input class="amount large" type="number" name="<?php echo $this->get_field_name( 'delay' ); ?>" value="<?php echo $instance['delay']; ?>">
		<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
	</span>
</p>
<p>
	<label class="input-label"><?php _e( 'Speed', '7listings' ); ?></label>
	<span class="input-append">
		<input class="amount large" type="number" name="<?php echo $this->get_field_name( 'speed' ); ?>" value="<?php echo $instance['speed']; ?>">
		<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
	</span>
</p>
