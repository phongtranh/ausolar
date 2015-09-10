<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Image', '7listings' ) ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}thumbnail" ); ?>
	</div>
</div>
<div class="sl-sub-settings sl-image-size">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Size', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
		</div>
	</div>
</div>
