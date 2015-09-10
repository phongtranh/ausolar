<div class="control-group checkbox-toggle">
	<?php Sls_Helper::checkbox_angular( "sls_$shortcode.thumbnail", uniqid() ); ?>
	<label class="control-label"><?php _e( 'Thumbnail', '7listings' ); ?></label>
</div>
<div class="control-group">
	<label class="control-label"><?php _e( 'Image Size', '7listings' ) ?></label>
	<div class="controls">
		<?php Sl_Form::image_sizes_select( '', '', 'ng-model=sls_' . $shortcode . '.image_size' ); ?>
	</div>
</div>
