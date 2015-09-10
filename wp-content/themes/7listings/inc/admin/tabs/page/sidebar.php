<?php $default_layout = sl_setting( 'design_layout_default_page' ); ?>
<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><strong><?php _e( 'Custom layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'This will overwrite layout settings<br>in Design for this page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></strong></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'custom_layout', get_post_meta( get_the_ID(), 'custom_layout', true ) ); ?>
	</div>
</div>

<?php echo 'none' == $default_layout ? '<div>' : ''; ?>

<div class="sl-settings sidebar layout">
	<div class="sl-label">
		<label><?php _e( 'Layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Change the layout<br>of this page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$value = get_post_meta( get_the_ID(), 'layout', true );
		if ( ! $value )
			$value = $default_layout;
		Sl_Form::sidebar_layout( 'layout', false, $value );
		?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select a widget area<br>to use as the sidebar for this page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::sidebar_select( 'custom_sidebar', false, get_post_meta( get_the_ID(), 'custom_sidebar', true ) ); ?>
	</div>
</div>

<?php echo 'none' == $default_layout ? '</div>' : ''; ?>
