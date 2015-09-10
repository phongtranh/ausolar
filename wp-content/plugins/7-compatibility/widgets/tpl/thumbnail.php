<p class="checkbox-toggle">
	<?php Sl_Form::checkbox_general( $this->get_field_name( 'thumbnail' ), $instance['thumbnail'] ); ?>
	<label><?php _e( 'Thumbnail', '7listings' ) ?></label>
</p>
<p>
	<label class="input-label"><?php _e( 'Image Size', '7listings' ); ?></label>
	<?php Sl_Form::image_sizes_select( $this->get_field_name( 'image_size' ), $instance['image_size'] ); ?>
</p>
