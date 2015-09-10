<h2><?php _e( 'Top', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Customize the top footer area<br>footer widgets are displayed here', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<h4><?php _e( 'Background', '7listings' ); ?></h4>

<?php Sl_Form::background( 'footer_top' ); ?>

<br>
<h4><?php _e( 'Widgets', '7listings' ); ?></h4>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Title', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::color_picker( '', 'design_footer_top_title', '', false ); ?>
	</div>
</div>

<?php
Sl_Form::color_picker( __( 'Text', '7listings' ), 'design_footer_top_text' );
echo '<br>';
Sl_Form::color_picker( __( 'Link', '7listings' ), 'design_footer_top_link' );
Sl_Form::color_picker( __( 'Link Hover', '7listings' ), 'design_footer_top_link_hover' );
?>

<br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Button', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_button_text]" value="<?php echo sl_setting( 'design_footer_top_button_text' ); ?>">
			<span class="text-color"></span>
		</div>
		<div>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_button_background]" value="<?php echo sl_setting( 'design_footer_top_button_background' ); ?>">
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
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_button_primary_text]" value="<?php echo sl_setting( 'design_footer_top_button_primary_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_button_primary_background]" value="<?php echo sl_setting( 'design_footer_top_button_primary_background' ); ?>" class="color">
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
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_price_text]" value="<?php echo sl_setting( 'design_footer_top_price_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_footer_top_price_background]" value="<?php echo sl_setting( 'design_footer_top_price_background' ); ?>" class="color">
				<span class="bg-color"></span>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php if ( Sl_License::license_type() == '7Network' ) : ?>

	<h2><?php _e( 'Middle', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Customize the middle footer area<br>footer builder links are displayed here', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

	<?php Sl_Form::background( 'footer_middle' ); ?>

	<br>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Title', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::color_picker( '', 'design_footer_middle_title', '', false ); ?>
		</div>
	</div>

	<?php
	Sl_Form::color_picker( __( 'Text', '7listings' ), 'design_footer_middle_text' );
	echo '<br>';
	Sl_Form::color_picker( __( 'Link', '7listings' ), 'design_footer_middle_link' );
	Sl_Form::color_picker( __( 'Link Hover', '7listings' ), 'design_footer_middle_link_hover' );
	?>

<?php endif; ?>

<h2><?php _e( 'Bottom', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Customize the bottom footer area', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php Sl_Form::background( 'footer_bottom' ); ?>

<br>

<?php
Sl_Form::color_picker( __( 'Text', '7listings' ), 'design_footer_bottom_text' );
echo '<br>';
Sl_Form::color_picker( __( 'Link', '7listings' ), 'design_footer_bottom_link' );
Sl_Form::color_picker( __( 'Link Hover', '7listings' ), 'design_footer_bottom_link_hover' );
?>

<br>
<br>
<br>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Custom  HTML', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert any content (text or html)<br>on the bottom of the footer.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_footer_bottom_custom_html_enable' ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label>&nbsp;</label>
	</div>
	<div class="sl-input">
		<textarea name="<?php echo THEME_SETTINGS; ?>[design_footer_bottom_custom_html]" cols="100" rows="3"><?php echo sl_setting( 'design_footer_bottom_custom_html' ); ?></textarea>
	</div>
</div>
