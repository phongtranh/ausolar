<h2><?php _e( 'Navbar', '7listings' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Height', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" min="30" name="<?php echo THEME_SETTINGS; ?>[design_mobile_menu_height]" value="<?php echo sl_setting( 'design_mobile_menu_height' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<?php Sl_Form::color_picker( __( 'Background', '7listings' ), 'design_mobile_nav_background', '' ); ?>

<br>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Logo', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_mobile_logo_display' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings sub-options">
		<div class="sl-label">
			<label><?php _e( 'Image', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<p class="logo upload">
				<?php
				$src = '';
				if ( sl_setting( 'design_mobile_logo' ) )
					$src = wp_get_attachment_url( sl_setting( 'design_mobile_logo' ) );
				?>
				<img src="<?php echo $src; ?>"<?php if ( ! $src )
					echo ' class="hidden"'; ?>>
				<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[design_mobile_logo]" value="<?php echo sl_setting( 'design_mobile_logo' ); ?>">
				<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
				<a href="#" class="button delete-image<?php if ( ! $src )
					echo ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
			</p>
			<?php
			printf(
				'<div class="sl-settings">
					<div class="sl-label">
						<label>&nbsp;</label>
					</div>
					<div class="sl-input">
						<span class="input-append input-prepend">
							<span class="add-on">%s</span>
							<input type="number" min="0" id="design_mobile_logo_width" name="%s[%s]" value="%s">
							<span class="add-on">px</span>
						</span>
						&nbsp;&nbsp;
						<span class="input-append input-prepend">
							<span class="add-on">%s</span>
							<input type="number" min="0" id="design_mobile_logo_height" name="%s[%s]" value="%s">
							<span class="add-on">px</span>
						</span>
					</div>
				</div>',
				__( 'Width', '7listings' ),
				THEME_SETTINGS,
				'mobile_logo_width',
				sl_setting( 'mobile_logo_width' ),
				__( 'Height', '7listings' ),
				THEME_SETTINGS,
				'mobile_logo_height',
				sl_setting( 'mobile_logo_height' )
			);
			?>
		</div>
	</div>
</div>

<br>

<div id="site-title" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Site Title', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox( 'display_mobile_site_title' ); ?>
		</span>
		<span class="input-append input-prepend">
			<?php Sl_Form::color_picker( '', 'design_mobile_site_title_color', '', false ); ?>
			<span class="add-on"><span class="text-size"></span></span>
				<input type="number" min="14" name="<?php echo THEME_SETTINGS; ?>[design_mobile_site_title_size]"
				   value="<?php echo sl_setting( 'design_mobile_site_title_size' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<div id="site-description" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Site Description', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox( 'display_mobile_site_description' ); ?>
		</span>
		<span class="input-append input-prepend">
			<?php Sl_Form::color_picker( '', 'design_mobile_site_description_color', '', false ); ?>
			<span class="add-on"><span class="text-size"></span></span>
			<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_mobile_site_description_size]"
				   value="<?php echo sl_setting( 'design_mobile_site_description_size' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<br>

<h2><?php _e( 'Navigation', '7listings' ); ?></h2>

<div class="sl-settings mobile-slideout layout">
	<div class="sl-label">
		<label>
			<?php _e( 'Layout', '7listings' ); ?> <?php echo do_shortcode( '[tooltip content="' . __( 'Position of slideout menu', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
		</label>
	</div>
	<div class="sl-input">
		<span>
			<input type="radio" name="<?php echo THEME_SETTINGS; ?>[design_layout_mobile_nav]" id="design-layout-mobile-nav-left" value="left"<?php checked( sl_setting( 'design_layout_mobile_nav' ), 'left' ); ?>>
			<label class="left-slideout<?php echo 'left' == sl_setting( 'design_layout_mobile_nav' ) ? ' active' : ''; ?>" title="<?php _e( 'Left slideout', '7listings' ); ?>" for="design-layout-mobile-nav-left">&nbsp;</label>
		</span>
		<span>
			<input type="radio" name="<?php echo THEME_SETTINGS; ?>[design_layout_mobile_nav]" id="design-layout-mobile-nav-right" value="right"<?php checked( sl_setting( 'design_layout_mobile_nav' ), 'right' ); ?>>
			<label class="right-slideout<?php echo 'right' == sl_setting( 'design_layout_mobile_nav' ) ? ' active' : ''; ?>" title="<?php _e( 'Right slideout', '7listings' ); ?>" for="design-layout-mobile-nav-right">&nbsp;</label>
		</span>
	</div>
</div>

<div id="custom-mobile-menu" class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Custom Menu', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Turn ON<br>to select a custom WP menu', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'design_mobile_custom_menu' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Menu', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="<?php echo THEME_SETTINGS . '[design_mobile_menu]'; ?>">
				<?php
				$menus = wp_get_nav_menus( 'nav_menu' );
				if ( ! is_wp_error( $menus ) && $menus )
					$menus = array_combine( wp_list_pluck( $menus, 'term_id' ), wp_list_pluck( $menus, 'name' ) );
				else
					$menus = array();
				Sl_Form::options( sl_setting( 'design_mobile_menu' ), $menus );
				?>
			</select>
		</div>
	</div>
</div>

<br>

<?php Sl_Form::color_picker( __( 'Background', '7listings' ), 'design_mobile_menu_background' ); ?>

<br>

<h4><?php _e( 'Link', '7listings' ); ?></h4>

<?php Sl_Form::color_picker( __( 'Colour', '7listings' ), 'design_mobile_link_color' ); ?>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Size', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append input-prepend">
		<span class="add-on"><span class="text-size"></span></span>
			<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_mobile_link_size]" value="<?php echo sl_setting( 'design_mobile_link_size' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Size Sub-Level', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append input-prepend">
			<span class="add-on"><span class="text-size"></span></span>
			<input type="number" min="10" name="<?php echo THEME_SETTINGS; ?>[design_mobile_link_size_sub]" value="<?php echo sl_setting( 'design_mobile_link_size_sub' ); ?>" class="font-size">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<h4><?php _e( 'Current Link', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Current Page link, highlight the active menu item.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>

<?php Sl_Form::color_picker( __( 'Text', '7listings' ), 'design_mobile_link_color_active' ); ?>
<?php Sl_Form::color_picker( __( 'Background', '7listings' ), 'design_mobile_link_background_active' ); ?>

<br>

<div id="mobile-advanced-settings" class="mobile-advanced-settings">
	<h2><?php _e( 'Advanced', '7listings' ); ?></h2>

	<div class="sl-settings mobile-break-point">
		<div class="sl-label">
			<label><?php _e( 'Display Mobile Nav', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select the mobile breakpoint when the mobile navigation header becomes visible.' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input sl-block-label">
			<label><input type="radio" name="<?php echo THEME_SETTINGS; ?>[design_mobile_nav_break_point]" value="1024px"<?php checked( '1024px', sl_setting( 'design_mobile_nav_break_point' ) ); ?>> <?php _e( '< 1024px - Small Desktop', '7listings' ); ?>
			</label><br />
			<label><input type="radio" name="<?php echo THEME_SETTINGS; ?>[design_mobile_nav_break_point]" value="980px"<?php checked( '980px', sl_setting( 'design_mobile_nav_break_point' ) ); ?>> <?php _e( '&nbsp;&nbsp;< 980px - Landscape Tablets', '7listings' ); ?>
			</label><br />
			<label><input type="radio" name="<?php echo THEME_SETTINGS; ?>[design_mobile_nav_break_point]" value="768px"<?php checked( '768px', sl_setting( 'design_mobile_nav_break_point' ) ); ?>> <?php _e( '&nbsp;&nbsp;< 768px - Portrait Tablets  <span class="input-hint">(recommended)</span>', '7listings' ); ?>
			</label>
		</div>
	</div>
</div>
