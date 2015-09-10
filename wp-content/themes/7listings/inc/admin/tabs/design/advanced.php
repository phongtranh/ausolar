<div id="favicon" class="sl-settings favicon upload">
	<div class="sl-label">
		<label><?php _e( 'Favicon', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Add a browser favicon.<br>Recommended file types: ICO, PNG, GIF, animated GIFs, JPEG<br>Size: 16x16 or 32x32', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$src   = '';
		$class = ' class="hidden"';
		if ( sl_setting( 'favicon' ) )
		{
			$src   = wp_get_attachment_url( sl_setting( 'favicon' ) );
			$class = '';
		}
		?>
		<img src="<?php echo $src; ?>"<?php echo $class; ?>>
		<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[favicon]" value="<?php echo sl_setting( 'favicon' ); ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
	</div>
</div>

<br><br>

<div id="excerpt" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Excerpt Limit', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Maximum number of words for excerpt.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" class="amount" min="1" max="75" name="<?php echo THEME_SETTINGS; ?>[excerpt_limit]" value="<?php echo sl_setting( 'excerpt_limit' ); ?>">
			<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
		</span>
	</div>
</div>
<div id="ellipsis" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Ellipsis', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Text after incomplete paragraph<br>when you limit the amount of words displayed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS; ?>[excerpt_more]" value="<?php echo sl_setting( 'excerpt_more' ); ?>" class="sl-input-small">
	</div>
</div>

<br><br>

<div id="border-radius" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Border Radius', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div class="slider" data-min="0" data-max="200"></div>
		<span class="input-append">
			<input type="number" min="0" min="200" class="amount" name="<?php echo THEME_SETTINGS . '[design_base_border_radius]'; ?>" value="<?php echo sl_setting( 'design_base_border_radius' ); ?>">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<br>

<h2><?php _e( 'Photos', '7listings' ); ?></h2>
<?php $prefix = 'design_thumbnail_'; ?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Border Width', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<div class="slider" data-min="0" data-max="24"></div>
		<span class="input-append">
			<input type="number" min="0" max="24" class="amount" name="<?php echo THEME_SETTINGS . "[{$prefix}border_width]"; ?>" value="<?php echo sl_setting( "{$prefix}border_width" ); ?>">
			<span class="add-on">px</span>
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Border Color', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . "[{$prefix}border_color]"; ?>" value="<?php echo sl_setting( "{$prefix}border_color" ); ?>">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Background Color', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo THEME_SETTINGS . "[{$prefix}background_color]"; ?>" value="<?php echo sl_setting( "{$prefix}background_color" ); ?>">
	</div>
</div>

<br>

<?php do_action( 'sl_settings_design_advanced_before_map' ); ?>

<h2><?php _e( 'Google Maps', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default map settings<br>You can still use different map styles when you insert maps with shortcodes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php
$prefix = 'design_map_';
$name   = THEME_SETTINGS . "[$prefix";
?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Marker', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display a default map marker or use your own. <br>For SVG markers use the url input', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $src = sl_setting( "{$prefix}marker" ); ?>
		<p class="toggle-choices">
			<select name="<?php echo esc_attr( "{$name}marker_style]" ); ?>" class="sl-input-small">
				<option value=""><?php _e( 'Default', '7listings' ); ?></option>
				<option value="custom"<?php selected( sl_setting( "{$prefix}marker_style" ), 'custom' ); ?>><?php _e( 'Custom', '7listings' ); ?></option>
			</select>
		</p>
		<p data-name="<?php echo esc_attr( "{$name}marker_style]" ); ?>" data-value="custom">
			<input type="text" class="regular-text" data-type="url" name="<?php echo "{$name}marker]"; ?>" value="<?php echo $src; ?>">
			<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
			<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a><br>
			<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>>
		</p>
	</div>
</div>
<div id="maps-controls" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Marker Animation', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo esc_attr( "{$name}marker_animation]" ); ?>" class="sl-input-small">
			<option value=""><?php _e( 'None', '7listings' ); ?></option>
			<option value="drop" <?php selected( sl_setting( "{$prefix}marker_animation" ), 'drop' ); ?>><?php _e( 'Drop', '7listings' ); ?></option>
			<option value="bounce" <?php selected( sl_setting( "{$prefix}marker_animation" ), 'bounce' ); ?>><?php _e( 'Bounce', '7listings' ); ?></option>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Zoom', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Set the default zoom level for your maps<br>1 = Smallest - World<br>16 = Largest - Local area', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo "{$name}zoom]"; ?>" class="sl-input-tiny">
			<?php
			$value = sl_setting( "{$prefix}zoom" );
			for ( $i = 1; $i <= 16; $i ++ )
			{
				printf( '<option value="%d"%s>%d</option>', $i, selected( $i, $value, false ), $i );
			}
			?>
		</select>
	</div>
</div>
<h4><?php _e( 'Controls', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable or disable map controls.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>
<div class="sl-sub-settings">
	<?php
	$controls = array(
		'zoom'        => __( 'Zoom', '7listings' ),
		'pan'         => __( 'Pan', '7listings' ),
		'scale'       => __( 'Scale', '7listings' ),
		'map_type'    => __( 'Map type', '7listings' ),
		'street_view' => __( 'Street view', '7listings' ),
		'rotate'      => __( 'Rotate', '7listings' ),
		'overview'    => __( 'Overview map', '7listings' ),
		'scrollwheel' => __( 'Scrollwheel', '7listings' ),
	);
	$value    = (array) sl_setting( "{$prefix}controls" );
	foreach ( $controls as $k => $label )
	{
		$id = uniqid();
		printf(
			'<div class="sl-settings">
				<div class="sl-label"><label>%s</label></div>
				<div class="sl-input">
					<span class="checkbox">
						<input type="checkbox" id="%s" name="%scontrols][]" value="%s"%s>
						<label for="%s">&nbsp;</label>
					</span>
				</div>
			</div>',
			$label,
			$id, $name, $k,
			checked( in_array( $k, $value ), 1, false ),
			$id
		);
	}
	?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Disable Dragging', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'design_map_disable_dragging' ); ?>
		</div>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Map Type', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select what map type you want to use, for your own custom colours and and features select Custom and insert JSON', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<p class="toggle-choices">
			<select name="<?php echo "{$name}type]"; ?>" class="input-medium">
				<?php
				Sl_Form::options( sl_setting( "{$prefix}type" ), array(
					'road'      => __( 'Road map', '7listings' ),
					'satellite' => __( 'Satellite', '7listings' ),
					'hybrid'    => __( 'Hybrid', '7listings' ),
					'terrain'   => __( 'Terrain', '7listings' ),
					'default'   => __( 'Custom', '7listings' ),
				) );
				?>
			</select>
		</p>

		<div data-name="<?php echo "{$name}type]"; ?>" data-value="default">
			<p class="input-hint">Insert JSON</p>
			<textarea class="code" name="<?php echo "{$name}default_style]"; ?>" cols="120" rows="5"><?php echo esc_textarea( sl_setting( "{$prefix}default_style" ) ); ?></textarea><br>
			<span class="input-hint">Map skins/styles: <a href="http://snazzymaps.com/" target="_blank">Snazzy Maps</a><?php echo do_shortcode( '[tooltip content="' . __( 'Copy the code:<br><b>Javascript style array</b><br>and insert above', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?>
				<br>
Custom designs: <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html" target="_blank">Google Maps Tool</a><?php echo do_shortcode( '[tooltip content="' . __( 'Click: <b>Show JSON</b> button<br>copy the code<br>and insert above', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?></span>
		</div>
	</div>
</div>

<h4><?php _e( 'Drawing styles', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Change the colours and opacity of drawing elements on your maps', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Stroke', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Stroke or outline colour<br>of drawing elements', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo "{$name}stroke_color]"; ?>" value="<?php echo sl_setting( "{$prefix}stroke_color" ); ?>">
		<div class="stroke-opacity">
			<div class="slider" data-min="1" data-max="100"></div>
			<span class="input-append">
				<input type="number" min="1" max="100" class="amount" name="<?php echo "{$name}stroke_opacity]"; ?>" value="<?php echo sl_setting( "{$prefix}stroke_opacity" ); ?>"><?php echo do_shortcode( '[tooltip content="' . __( 'Opacity', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				<span class="add-on">%</span>
			</span>
		</div>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Fill', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Fill colour<br>of drawing elements', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" class="color" name="<?php echo "{$name}fill_color]"; ?>" value="<?php echo sl_setting( "{$prefix}fill_color" ); ?>">
		<?php echo do_shortcode( '[tooltip content="' . __( 'Opacity', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
		<p class="opacity"></p>
		<input type="hidden" name="<?php echo "{$name}fill_opacity]"; ?>" value="<?php echo sl_setting( "{$prefix}fill_opacity" ); ?>">
	</div>
</div>


<h2><?php _e( 'Custom CSS', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Add your own custom styles. <br>Inserting CSS or LESS here compiles your styles into one CSS file to reduce http requests.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Mode', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select mode for code highlighting<br>you can write CSS in .less mode, the mode does not affect output', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<select class="select-css-mode">
			<option value="<?php _e( 'css', '7listings' ); ?>"><?php _e( 'CSS', '7listings' ); ?></option>
			<option value="<?php _e( 'text/x-less', '7listings' ); ?>"><?php _e( 'LESS', '7listings' ); ?></option>
		</select>
	</div>
</div>

<div id="sl-custom-css" class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'CSS or LESS', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Add your own custom css, write CSS, LESS or import .less files from your child theme', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<textarea id="custom-css" class="code" name="<?php echo THEME_SETTINGS; ?>[design_custom_css]" cols="120" rows="5"><?php echo esc_textarea( sl_setting( 'design_custom_css' ) ); ?></textarea>
	</div>
</div>
