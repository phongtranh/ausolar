<h2><?php _e( 'Background', '7listings' ); ?></h2>

<?php Sl_Form::background( 'sidebar' ); ?>

<h2><?php _e( 'Widgets', '7listings' ); ?></h2>

<?php Sl_Form::color_picker( __( 'Heading', '7listings' ), 'design_sidebar_heading_color', '' ); ?>
<?php Sl_Form::color_picker( __( 'Text', '7listings' ), 'design_sidebar_text_color', '' ); ?>

<br>

<?php Sl_Form::color_picker( __( 'Link', '7listings' ), 'design_sidebar_link_color', '' ); ?>
<?php Sl_Form::color_picker( __( 'Link Hover', '7listings' ), 'design_sidebar_link_hover_color', '' ); ?>

<br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Button', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_button_text]" value="<?php echo sl_setting( 'design_sidebar_button_text' ); ?>">
			<span class="text-color"></span>
		</div>
		<div>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_button_background]" value="<?php echo sl_setting( 'design_sidebar_button_background' ); ?>">
			<span class="bg-color"></span>
		</div>
	</div>
</div>

<?php if ( ! in_array( Sl_License::license_type(), array( '7Comp', '7Basic' ) ) ) : ?>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Booking Button', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_button_primary_text]" value="<?php echo sl_setting( 'design_sidebar_button_primary_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_button_primary_background]" value="<?php echo sl_setting( 'design_sidebar_button_primary_background' ); ?>" class="color">
				<span class="bg-color"></span>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php if ( ! in_array( Sl_License::license_type(), array( '7Comp', '7Basic' ) ) ) : ?>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Price', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_price_text]" value="<?php echo sl_setting( 'design_sidebar_price_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_sidebar_price_background]" value="<?php echo sl_setting( 'design_sidebar_price_background' ); ?>" class="color">
				<span class="bg-color"></span>
			</div>
		</div>
	</div>

<?php endif; ?>

<br><br><br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Width', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" step="any" class="amount" min="15" max="50" name="<?php echo THEME_SETTINGS . '[design_sidebar_width]'; ?>" value="<?php echo esc_attr( sl_setting( 'design_sidebar_width' ) ); ?>">
			<span class="add-on">%</span>
		</span>
	</div>
</div>

<h2><?php _e( 'Default for WP Pages', '7listings' ); ?></h2>

<div class="sl-settings sidebar layout visible">
	<div class="sl-label">
		<label><?php _e( 'Layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default layout for pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::sidebar_layout( 'design_layout_default_page' ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default sidebar for pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::sidebar_select( 'design_sidebar_default_page' ); ?>
	</div>
</div>
