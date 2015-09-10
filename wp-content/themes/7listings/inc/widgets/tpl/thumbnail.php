<div class="sl-settings checkbox-toggle">
	<div class="sl-label"><label><?php _e( 'Image', '7listings' ) ?></label></div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( $this->get_field_name( 'thumbnail' ), $instance['thumbnail'] ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label"><label class="input-label"><?php _e( 'Size', '7listings' ); ?></label></div>
		<div class="sl-input">
			<?php Sl_Form::image_sizes_select( $this->get_field_name( 'image_size' ), $instance['image_size'] ); ?>
		</div>
	</div>
</div>