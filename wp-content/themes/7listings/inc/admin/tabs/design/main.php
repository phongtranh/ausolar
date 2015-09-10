<h2><?php _e( 'Background', '7listings' ); ?></h2>

<?php Sl_Form::background( 'main' ); ?>

<br>

<h2><?php _e( 'Headings', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 1', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h1&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h1_color]" value="<?php echo sl_setting( 'design_h1_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="20" name="<?php echo THEME_SETTINGS; ?>[design_h1_size]" value="<?php echo sl_setting( 'design_h1_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h1_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 2', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h2&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h2_color]" value="<?php echo sl_setting( 'design_h2_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="18" name="<?php echo THEME_SETTINGS; ?>[design_h2_size]" value="<?php echo sl_setting( 'design_h2_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h2_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 3', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h3&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h3_color]" value="<?php echo sl_setting( 'design_h3_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="16" name="<?php echo THEME_SETTINGS; ?>[design_h3_size]" value="<?php echo sl_setting( 'design_h3_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h3_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 4', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h4&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h4_color]" value="<?php echo sl_setting( 'design_h4_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="14" name="<?php echo THEME_SETTINGS; ?>[design_h4_size]" value="<?php echo sl_setting( 'design_h4_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h4_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 5', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h5&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h5_color]" value="<?php echo sl_setting( 'design_h5_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="12" name="<?php echo THEME_SETTINGS; ?>[design_h5_size]" value="<?php echo sl_setting( 'design_h5_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h5_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Heading 6', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure &#8249;h6&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_h6_color]" value="<?php echo sl_setting( 'design_h6_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_h6_size]" value="<?php echo sl_setting( 'design_h6_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_h6_font' ); ?>
		</div>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Case Transform', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS; ?>[design_heading_transform]">
			<?php
			Sl_Form::options( sl_setting( 'design_heading_transform' ), array(
				'none'       => __( 'None', '7listings' ),
				'capitalize' => __( 'Capitalize', '7listings' ),
				'uppercase'  => __( 'UPPERCASE', '7listings' ),
				'lowercase'  => __( 'lowercase', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>

<br>

<h2><?php _e( 'Text', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Paragraph', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure default text<br>&#8249;p&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_text_color]" value="<?php echo sl_setting( 'design_text_color' ); ?>" class="color">
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_text_size]" value="<?php echo sl_setting( 'design_text_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_text_font' ); ?>
		</div>
	</div>
</div>

<h4><?php _e( 'List', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Unordered list options<br>&#8249;ul&#8250;', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Icon', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure bullet icon for your lists', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_list_icon_color]" value="<?php echo esc_attr( sl_setting( 'design_list_icon_color' ) ); ?>">
		</div>
		<select name="<?php echo THEME_SETTINGS; ?>[design_list_icon]">
			<?php
			Sl_Form::options( sl_setting( 'design_list_icon' ), array(
				'\f061' => __( 'Arrow', '7listings' ),
				'\f069' => __( 'Asterisk', '7listings' ),
				'\f0da' => __( 'Caret', '7listings' ),
				'\f111' => __( 'Circle', '7listings' ),
				'\f10c' => __( 'Circle Blank', '7listings' ),
				'\f01d' => __( 'Circle Play', '7listings' ),
				'\f0a9' => __( 'Circle Arrow', '7listings' ),
				'\f046' => __( 'Check', '7listings' ),
				'\f14a' => __( 'Check Sign', '7listings' ),
				'\f054' => __( 'Chevron', '7listings' ),
				'\f0a4' => __( 'Hand', '7listings' ),
				'\f067' => __( 'Plus', '7listings' ),
				'\f055' => __( 'Plus Sign', '7listings' ),
				'\f00c' => __( 'OK', '7listings' ),
				'\f05d' => __( 'OK Circle', '7listings' ),
				'\f058' => __( 'OK Sign', '7listings' ),
				'\f105' => __( 'Angle', '7listings' ),
				'\f101' => __( 'Double Angle', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>

<h2><?php _e( 'Links', '7listings' ); ?></h2>

<?php Sl_Form::color_picker( __( 'Link', '7listings' ), 'design_link_color', '' ); ?>
<?php Sl_Form::color_picker( __( 'Link Hover', '7listings' ), 'design_link_color_hover', '' ); ?>

<br>

<h2><?php _e( 'Graphics', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Edit default button design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_button_text]" value="<?php echo sl_setting( 'design_button_text' ); ?>" class="color">
			<span class="text-color"></span>
		</div>
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_button_background]" value="<?php echo sl_setting( 'design_button_background' ); ?>" class="color">
			<span class="bg-color"></span>
		</div>
		<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_button_font_size]" value="<?php echo sl_setting( 'design_button_font_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
		</div>
		<div>
			<?php Sl_Form::font_family( 'design_button_font' ); ?>
		</div>
	</div>
</div>

<?php if ( ! in_array( Sl_License::license_type(), array( '7Comp', '7Basic' ) ) ) : ?>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Booking Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Edit booking/primary button design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_button_primary_text]" value="<?php echo sl_setting( 'design_button_primary_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_button_primary_background]" value="<?php echo sl_setting( 'design_button_primary_background' ); ?>" class="color">
				<span class="bg-color"></span>
			</div>
			<div>
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_button_primary_font_size]" value="<?php echo sl_setting( 'design_button_primary_font_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
			</div>
			<div>
				<?php Sl_Form::font_family( 'design_button_primary_font' ); ?>
			</div>
		</div>
	</div>

<?php endif; ?>

<?php if ( ! in_array( Sl_License::license_type(), array( '7Comp', '7Basic' ) ) ) : ?>

	<br>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Change the design of price tags', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_price_text]" value="<?php echo sl_setting( 'design_price_text' ); ?>" class="color">
				<span class="text-color"></span>
			</div>
			<div>
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_price_background]" value="<?php echo sl_setting( 'design_price_background' ); ?>" class="color">
				<span class="bg-color"></span>
			</div>
		</div>
	</div>

<?php endif; ?>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Label', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_label_text_color]" value="<?php echo sl_setting( 'design_label_text_color' ); ?>" class="color">
			<span class="text-color"></span>
		</div>
		<div>
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_label_background_color]" value="<?php echo sl_setting( 'design_label_background_color' ); ?>" class="color">
			<span class="bg-color"></span>
		</div>
	</div>
</div>

<h2><?php _e( 'Ratings / Reviews', '7listings' ); ?></h2>

<?php if ( Sl_License::is_module_enabled( 'accommodation' ) ) : ?>

	<h4><?php _e( 'Accommodation Rating', '7listings' ); ?></h4>
	<?php Sl_Form::color_picker( __( 'Stars', '7listings' ), 'design_star_rating_color', '' ); ?>

	<br>

<?php endif; ?>

<h4><?php _e( 'Reviews', '7listings' ); ?></h4>
<?php Sl_Form::color_picker( __( 'Color', '7listings' ), 'design_review_rating_color', '' ); ?>

<div class="sl-settings review-icons">
	<div class="sl-label">
		<label><?php _e( 'Icon', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select a custom icon for reviews', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input custom-rating top">
		<?php Sl_Form::icon( 'design_review_rating_icon' ); ?>
	</div>
</div>
<div class="sl-settings review-icons">
	<div class="sl-label">
		<label><?php _e( 'Background Icon', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'For best effect use an outline version of the Review Icon or the same icon', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input custom-rating bottom">
		<?php Sl_Form::icon( 'design_review_rating_background_icon' ); ?>
	</div>
</div>
