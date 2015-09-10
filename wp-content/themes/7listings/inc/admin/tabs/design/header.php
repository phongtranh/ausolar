<h2><?php _e( 'Background', '7listings' ); ?></h2>

<?php Sl_Form::background( 'header' ); ?>

<h2><?php _e( 'Branding', '7listings' ); ?></h2>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Logo', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'logo_display' ); ?>
	</div>
</div>
<div id="logo-options" class="sl-sub-settings">
	<div class="sl-settings checkbox-toggle">
	</div>
	<div class="sl-settings image-logo">
		<div class="sl-label">
			<label><?php _e( 'Image', '7listings' ); ?></label>
		</div>
		<div class="sl-input logo upload">
			<?php
			$src = '';
			if ( sl_setting( 'logo' ) )
			{
				// Show thumb in admin for faster load
				list( $src ) = wp_get_attachment_image_src( sl_setting( 'logo' ), 'sl_thumb_tiny' );
			}
			?>
			<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>">
			<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[logo]" value="<?php echo sl_setting( 'logo' ); ?>">
			<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
			<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
			<?php
			printf(
				'<label>&nbsp;</label>
					<span class="input-append input-prepend">
						<span class="add-on">%s</span>
						<input type="number" id="logo_width" name="%s[%s]" value="%s">
						<span class="add-on">px</span>
					</span>
					<span class="input-append input-prepend">
						<span class="add-on">%s</span>
						<input type="number" id="logo_height" name="%s[%s]" value="%s">
						<span class="add-on">px</span>
					</span>
				',
				__( 'Width', '7listings' ),
				THEME_SETTINGS,
				'logo_width',
				sl_setting( 'logo_width' ),
				__( 'Height', '7listings' ),
				THEME_SETTINGS,
				'logo_height',
				sl_setting( 'logo_height' )
			);
			?>
		</div>
	</div>
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label><?php _e( 'SVG', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable to insert custom SVG.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input svg_display">
			<?php Sl_Form::checkbox( 'svg_display' ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Code', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert SVG code<br>Note: your will have to do your CSS styling manually in<br><b>Advanced > Custom CSS</b>', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<textarea name="<?php echo THEME_SETTINGS; ?>[logo_svg]" cols="100" rows="3"><?php echo sl_setting( 'logo_svg' ); ?></textarea>
		</div>
	</div>
</div>

<div class="sl-settings site-title">
	<div class="sl-label">
		<label><?php _e( 'Site Title', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox( 'display_site_title' ); ?>
		</span>
		<span>
			<input type="text" id="site_title" name="<?php echo THEME_SETTINGS; ?>[site_title]" value="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="sl-input-medium">
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_site_title_color]" value="<?php echo sl_setting( 'design_site_title_color' ); ?>" class="color">
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" name="<?php echo THEME_SETTINGS; ?>[design_site_title_size]" value="<?php echo sl_setting( 'design_site_title_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
			<?php Sl_Form::font_family( 'design_site_title_font' ); ?>
		</span>
	</div>
</div>
<div class="sl-settings site-description">
	<div class="sl-label">
		<label><?php _e( 'Site Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display tagline after site title', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox( 'display_site_description' ); ?>
		</span>
		<span>
			<input type="text" id="tag_line" name="<?php echo THEME_SETTINGS; ?>[tag_line]" value="<?php echo esc_attr( get_bloginfo( 'description' ) ); ?>" class="sl-input-large">
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[design_site_description_color]" value="<?php echo sl_setting( 'design_site_description_color' ); ?>" class="color">
			<span class="input-append input-prepend">
				<span class="add-on"><span class="text-size"></span></span>
				<input type="number" name="<?php echo THEME_SETTINGS; ?>[design_site_description_size]" value="<?php echo sl_setting( 'design_site_description_size' ); ?>" class="font-size">
				<span class="add-on">px</span>
			</span>
			<?php Sl_Form::font_family( 'design_site_description_font' ); ?>
		</span>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Phone', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display your phone number in your header', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_header_phone' ); ?>
		<span class="input-hint"><?php echo do_shortcode( '[tooltip content="' . __( 'This input uses settings: Pages > Contact Us', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?></span>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Color', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<div class="toggle-choices" data-effect="fade">
				<select class="input-small" name="<?php echo THEME_SETTINGS . '[design_header_phone_color_scheme]'; ?>">
					<?php
					Sl_Form::options( sl_setting( 'design_header_phone_color_scheme' ), array(
						'dark'   => __( 'Dark', '7listings' ),
						'light'  => __( 'Light', '7listings' ),
						'custom' => __( 'Custom', '7listings' ),
					) );
					?>
				</select>
			</div>
			<div data-name="<?php echo THEME_SETTINGS . '[design_header_phone_color_scheme]'; ?>" data-value="custom">
				<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[design_header_phone_color]'; ?>" value="<?php echo sl_setting( 'design_header_phone_color' ); ?>">
			</div>
		</div>
	</div>
</div>

<?php do_action( 'sl_settings_design_header_after_header' ); ?>

<br>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Weather', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the current weather and wind conditions in a widget.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'weather_active' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Style', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select class="input-small" name="<?php echo THEME_SETTINGS . '[design_weather_style]'; ?>">
				<?php
				Sl_Form::options( sl_setting( 'design_weather_style' ), array(
					'image' => __( 'Image', '7listings' ),
					'icon'  => __( 'Icon', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Color', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<div class="toggle-choices" data-effect="fade">
				<select class="input-small" name="<?php echo THEME_SETTINGS . '[design_weather_color_scheme]'; ?>">
					<?php
					Sl_Form::options( sl_setting( 'design_weather_color_scheme' ), array(
						'dark'   => __( 'Dark', '7listings' ),
						'light'  => __( 'Light', '7listings' ),
						'custom' => __( 'Custom', '7listings' ),
					) );
					?>
				</select>
			</div>
			<div data-name="<?php echo THEME_SETTINGS . '[design_weather_color_scheme]'; ?>" data-value="custom">
				<input type="text" class="color" name="<?php echo THEME_SETTINGS . '[design_weather_color]'; ?>" value="<?php echo sl_setting( 'design_weather_color' ); ?>">
			</div>
		</div>
	</div>
	<br>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'City', '7listings' ); ?> <span class="warning-sm required right"></span></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[city]" value="<?php echo sl_setting( 'city' ); ?>">
			<span class="input-hint"><?php echo do_shortcode( '[tooltip content="' . __( 'Format: City, Country', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?></span>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Timezone', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="<?php echo THEME_SETTINGS; ?>[weather_timezone]">
				<?php echo wp_timezone_choice( sl_setting( 'weather_timezone' ) ); ?>
			</select>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Unit', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<label>
				<input type="radio" name="<?php echo THEME_SETTINGS; ?>[weather_unit]" value="c" <?php checked( sl_setting( 'weather_unit' ), 'c' ); ?>> <?php _e( 'Celcius', '7listings' ); ?>
			</label>
			<label>
				<input type="radio" name="<?php echo THEME_SETTINGS; ?>[weather_unit]" value="f" <?php checked( sl_setting( 'weather_unit' ), 'f' ); ?>> <?php _e( 'Fahrenheit', '7listings' ); ?>
			</label>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'WOEID', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Where On Earth ID,<br>unique location ID. If city is not enough to locate your position', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="number" name="<?php echo THEME_SETTINGS; ?>[woeid]" value="<?php echo sl_setting( 'woeid' ); ?>" class="sl-input-small">
			<span class="input-hint"><?php echo '<a href="http://woeid.rosselliot.co.nz" target="_blank" class="button white">' . __( 'Get WOEID', '7listings' ) . '</a>'; ?></span>
		</div>
	</div>
</div>

<h2><?php _e( 'Navigation', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Font', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Font settings for menu items', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append input-prepend">
			<span class="add-on"><span class="text-size"></span></span>
			<input type="number" name="<?php echo THEME_SETTINGS; ?>[design_navbar_font_size]" value="<?php echo sl_setting( 'design_navbar_font_size' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
		&nbsp;&nbsp;
		<?php Sl_Form::font_family( 'design_navbar_font' ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Navbar and first level link height', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" name="<?php echo THEME_SETTINGS; ?>[design_navbar_height_desktop]" value="<?php echo sl_setting( 'design_navbar_height_desktop' ); ?>" id="desktop-nav-height" class="size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<br>

<table class="color-group">
	<tr>
		<td width="190">&nbsp;</td>
		<td><?php _e( 'Link', '7listings' ); ?></td>
		<td><?php _e( 'Hover', '7listings' ); ?></td>
		<td><?php _e( 'Current/Active', '7listings' ); ?></td>
	</tr>
	<tr>
		<td class="input-label"><label><?php _e( 'Background', '7listings' ); ?></label></td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_background_color]" value="<?php echo sl_setting( 'design_navbar_background_color' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_link_background_hover]" value="<?php echo sl_setting( 'design_navbar_link_background_hover' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_link_background_active]" value="<?php echo sl_setting( 'design_navbar_link_background_active' ); ?>">
		</td>
	</tr>
	<tr>
		<td class="input-label"><label><?php _e( 'Text', '7listings' ); ?></label></td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_link_color]" value="<?php echo sl_setting( 'design_navbar_link_color' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_link_color_hover]" value="<?php echo sl_setting( 'design_navbar_link_color_hover' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_navbar_link_color_active]" value="<?php echo sl_setting( 'design_navbar_link_color_active' ); ?>">
		</td>
	</tr>
</table>

<h4><?php _e( 'Dropdown', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter main (desktop) dropdown menu options.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>

<table class="color-group">
	<tr>
		<td width="190">&nbsp;</td>
		<td><?php _e( 'Link', '7listings' ); ?></td>
		<td><?php _e( 'Hover', '7listings' ); ?></td>
		<td><?php _e( 'Current/Active', '7listings' ); ?></td>
	</tr>
	<tr>
		<td class="input-label"><label><?php _e( 'Background', '7listings' ); ?></label></td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_background]" value="<?php echo sl_setting( 'design_dropdown_background' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_link_background_hover]" value="<?php echo sl_setting( 'design_dropdown_link_background_hover' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_link_background_active]" value="<?php echo sl_setting( 'design_dropdown_link_background_active' ); ?>">
		</td>
	</tr>
	<tr>
		<td class="input-label"><label><?php _e( 'Text', '7listings' ); ?></label></td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_link_color]" value="<?php echo sl_setting( 'design_dropdown_link_color' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_link_color_hover]" value="<?php echo sl_setting( 'design_dropdown_link_color_hover' ); ?>">
		</td>
		<td>
			<input type="text" class="color" name="<?php echo THEME_SETTINGS; ?>[design_dropdown_link_color_active]" value="<?php echo sl_setting( 'design_dropdown_link_color_active' ); ?>">
		</td>
	</tr>
</table>

<br><br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Search', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display search form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_header_search' ); ?>
	</div>
</div>


<?php
if ( Sl_License::is_module_enabled( 'product' ) )
{
?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Cart', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display ecommerce cart', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'design_mini_cart_enable' ); ?>
		</div>
	</div>
<?php } ?>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Breadcrumbs', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display breadcrumbs', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_breadcrumbs_enable' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<?php Sl_Form::color_picker( __( 'Background', '7listings' ), 'design_breadcrumbs_background', '' ); ?>
	<?php Sl_Form::color_picker( __( 'Separators', '7listings' ), 'design_breadcrumbs_separator', '' ); ?>
	<?php Sl_Form::color_picker( __( 'Current Page', '7listings' ), 'design_breadcrumbs_current', '' ); ?>
</div>


