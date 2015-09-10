<div class="control-group">
	<span class="checkbox-toggle" data-effect="fade">
		<?php Sls_Helper::checkbox_angular( "sls_$shortcode.excerpt", "sls-$shortcode-excerpt" ); ?>
		<label class="control-label"><?php _e( 'Excerpt', '7listings' ) ?></label>
	</span>
	<span class="input-append supplementary-input">
		<input ng-model="sls_<?php echo $shortcode; ?>.excerpt_length" type="number" class="amount" value="25" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1">
		<span class="add-on"><?php _e( 'Words', '7listings' ); ?></span>
	</span>
</div>
