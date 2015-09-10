<div class="sl-settings sidebar layout">
	<div class="sl-label">
		<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sidebar layout<br>for single pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::sidebar_layout( "{$post_type}_single_sidebar_layout" ); ?>
	</div>
</div>
<div class="sl-sub-settings extra-input-toggle">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Widget Area', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select widget area<br>for sidebar', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::sidebar_select( "{$post_type}_single_sidebar" ); ?>
		</div>
	</div>
</div>
