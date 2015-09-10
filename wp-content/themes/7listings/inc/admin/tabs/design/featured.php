<h2><?php _e( 'Background', '7listings' ); ?></h2>

<?php Sl_Form::background( 'featured' ); ?>

<br><br>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Min. Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Set minimum height for the featured area, the height is responsive for smaller screensizes.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" id="featured-header-height" class="size" name="<?php echo THEME_SETTINGS; ?>[design_featured_min_height]" value="<?php echo sl_setting( 'design_featured_min_height' ); ?>">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<br>

<h2><?php _e( 'Text', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Page Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert title colour', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::color_picker( '', 'design_featured_heading_color', '', false ); ?>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Text', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert content text colour', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::color_picker( '', 'design_featured_custom_text', '', false ); ?>
	</div>
</div>
