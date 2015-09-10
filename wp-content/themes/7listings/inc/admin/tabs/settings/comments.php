<h2><?php _e( 'Form', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default settings for all comments & reviews forms', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Style', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select style to display comment form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS; ?>[comments_style]" class="sl-input-small">
			<?php Sl_Form::options( sl_setting( 'comments_style' ), array( __( 'Modal', '7listings' ), __( 'Body', '7listings' )) ); ?>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Website', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable website links in comments', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'comments_website' ); ?>
	</div>
</div>


<h2><?php _e( 'WP Pages', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default settings for WP Pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Display', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'OFF: disables comments<br>ON: enables comments and uses individual page settings if available', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'comments_page' ); ?>
	</div>
</div>

<br>
<br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'See also', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php printf( __( '<a href="%s" class="wp-settings-link icon">Discussion Settings</a>', '7listings' ), admin_url( 'options-discussion.php' ) ); ?>
	</div>
</div>