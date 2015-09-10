<h2><?php _e( 'Top', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert image above header<br>Max. width: 600px', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $src = sl_setting( 'emails_header_image' ); ?>
		<input type="text" data-type="url" name="<?php echo THEME_SETTINGS . '[emails_header_image]'; ?>" value="<?php echo $src; ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php if ( ! $src )
			echo ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
		<br>
		<img src="<?php echo $src; ?>"<?php if ( ! $src )
			echo ' class="hidden"'; ?>>
	</div>
</div>

<h2><?php _e( 'Header', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Background', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_base_color]'; ?>" value="<?php echo sl_setting( 'emails_base_color' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Text', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_header_color]'; ?>" value="<?php echo sl_setting( 'emails_header_color' ); ?>">
	</div>
</div>


<h2><?php _e( 'Content', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Background', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_body_background]'; ?>" value="<?php echo sl_setting( 'emails_body_background' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Headings', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter headings colour<br>this setting may be lost in emails clients that strip out embedded styles when emails are sent from WP or plugins', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_heading_color]'; ?>" value="<?php echo sl_setting( 'emails_heading_color' ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Text', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_body_text_color]'; ?>" value="<?php echo sl_setting( 'emails_body_text_color' ); ?>">
	</div>
</div>

<hr class="light">

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Footer Text', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS . '[emails_footer_text]'; ?>" value="<?php echo sl_setting( 'emails_footer_text' ); ?>">
	</div>
</div>


<h2><?php _e( 'Background', '7listings' ); ?></h2>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Background', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[emails_background]'; ?>" value="<?php echo sl_setting( 'emails_background' ); ?>">
	</div>
</div>
<br>
<div class="sl-settings">
	<div class="sl-label">
		<label></label>
	</div>
	<div class="sl-input">
		<a target="_blank" class="button secondary" href="<?php echo sl_email_preview_link( 'contact_message', 'contact' ); ?>"><?php _e( 'Preview email', '7listings' ); ?></a>
	</div>
</div>