<?php

class Sl_Form
{
	/**
	 * Helper function to show input
	 *
	 * @param string $label
	 * @param string $name
	 * @param string $hint
	 *
	 * @return void
	 */
	static function input( $label, $name, $hint = '' )
	{
		printf(
			'<div class="sl-settings">
				<div class="sl-label">
					<label>%s</label>
				</div>
				<div class="sl-input">
					<input type="text" name="%s[%s]" value="%s">
					%s
				</div>
			</div>',
			$label,
			THEME_SETTINGS, $name,
			esc_attr( sl_setting( $name ) ),
			$hint ? sprintf( '<span class="input-hint">%s</span>', $hint ) : ''
		);
	}

	/**
	 * Helper function to show checkbox
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	static function checkbox( $name )
	{
		$id = uniqid( mt_rand(), true );
		printf(
			'<span class="checkbox">
				<input type="checkbox" id="%s" name="%s[%s]" value="1"%s>
				<label for="%s">&nbsp;</label>
			</span>',
			$id,
			THEME_SETTINGS,
			$name,
			checked( sl_setting( $name ), 1, false ),
			$id
		);
	}

	/**
	 * Helper function to show checkbox
	 *
	 * @param string   $name  Input name
	 * @param bool|int $value Input saved value
	 *
	 * @return void
	 */
	static function checkbox_general( $name, $value )
	{
		$id = uniqid( mt_rand(), true );
		printf(
			'<span class="checkbox">
				<input type="checkbox" id="%s" name="%s" value="1"%s>
				<label for="%s">&nbsp;</label>
			</span>',
			$id,
			$name,
			checked( $value, 1, false ),
			$id
		);
	}

	/**
	 * Helper function to show options
	 *
	 * @param mixed $selected
	 * @param array $options List of options, in format $k => $v
	 *
	 * @return void
	 */
	static function options( $selected, $options )
	{
		foreach ( $options as $k => $v )
		{
			printf( '<option value="%s"%s>%s</option>', $k, selected( $k, $selected, false ), $v );
		}
	}

	/**
	 * Helper function to show checkbox
	 *
	 * @param string $label
	 * @param string $name
	 *
	 * @param string $addon
	 * @param bool   $wrapper
	 *
	 * @return void
	 */
	static function color_picker( $label, $name, $addon = '', $wrapper = true )
	{
		?>
		<?php if ( $wrapper ) : ?>
		<div class="sl-settings">
		<div class="sl-label">
			<label><?php echo $label; ?></label>
		</div>
		<div class="sl-input">
	<?php endif; ?>
		<input type="text" name="<?php echo THEME_SETTINGS . "[$name]"; ?>" value="<?php echo sl_setting( $name ); ?>" class="color">
		<?php echo $addon; ?>
		<?php if ( $wrapper ) : ?>
		</div>
		</div>
	<?php endif; ?>
	<?php
	}

	/**
	 * Helper function to show font family dropdown
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	static function font_family( $name )
	{
		$fonts = sl_get_fonts();

		$html = sprintf(
			'<select name="%s[%s]">
				<option value="-1">%s</option>
			',
			THEME_SETTINGS,
			$name,
			__( 'Select Font', '7listings' )
		);

		$font_options = array(
			'embedded' => '',
			'web'      => '',
			'google'   => '',
		);
		$selected     = sl_setting( $name );
		foreach ( $fonts as $font => $font_opt )
		{
			$option = sprintf(
				'<option value="%s"%s>%s</option>',
				$font,
				selected( $selected, $font, false ),
				isset( $font_opt['label'] ) ? $font_opt['label'] : $font
			);
			$type   = empty( $font_opt['type'] ) ? 'embedded' : $font_opt['type'];
			$font_options[$type] .= $option;
		}

		$html .= sprintf( '<optgroup label="%s">%s</optgroup>', __( 'Embedded Fonts', '7listings' ), $font_options['embedded'] );
		$html .= sprintf( '<optgroup label="%s">%s</optgroup>', __( 'Web Fonts', '7listings' ), $font_options['web'] );

		$html .= '</select>';

		echo $html;
	}

	/**
	 * Helper function to show background settings
	 *
	 * @param string $base
	 *
	 * @return void
	 */
	static function background( $base )
	{
		self::color_picker( __( 'Color', '7listings' ), 'design_' . $base . '_background' );

		$prefix = 'design_' . $base . '_background_';
		if ( 'body' == $base )
		{
			$prefix = 'design_background_';
		}
		?>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Background Image', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $prefix . 'image' ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Image', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php
					$src = '';
					if ( sl_setting( $prefix . 'image_id' ) )
					{
						// Show thumb in admin for faster load
						list( $src ) = wp_get_attachment_image_src( sl_setting( $prefix . 'image_id' ), 'sl_thumb_tiny' );
					}
					?>
					<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>">
					<input type="hidden" name="<?php echo THEME_SETTINGS . "[{$prefix}image_id]"; ?>" value="<?php echo sl_setting( $prefix . 'image_id' ); ?>">
					<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
					<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
				</div>
			</div>
			<div class="sl-sub-settings">
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Size', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}size]", sl_setting( $prefix . 'size' ) ); ?>
					</div>
				</div>
			</div>
			<div class="sl-settings toggle-choices">
				<div class="sl-label">
					<label><?php _e( 'Style' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo esc_attr( THEME_SETTINGS . "[{$prefix}type]" ); ?>" class="sl-input-small">
						<?php
						Sl_Form::options( sl_setting( $prefix . 'type' ), array(
							'full'  => __( 'Full', '7listings' ),
							'tiled' => __( 'Tiled', '7listings' ),
						) );
						?>
					</select>
				</div>
			</div>
			<div class="sl-sub-settings" data-name="<?php echo esc_attr( THEME_SETTINGS . "[{$prefix}type]" ); ?>" data-value="tiled">
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Position', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}position_x]"; ?>" class="sl-input-small">
							<?php
							self::options( sl_setting( $prefix . 'position_x' ), array(
								'left'   => __( 'Left', '7listings' ),
								'center' => __( 'Center', '7listings' ),
								'right'  => __( 'Right', '7listings' ),
							) );
							?>
						</select>
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}position_y]"; ?>" class="sl-input-small">
							<?php
							self::options( sl_setting( $prefix . 'position_y' ), array(
								'top'    => __( 'Top', '7listings' ),
								'center' => __( 'Center', '7listings' ),
								'bottom' => __( 'Bottom', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Repeat', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}repeat]"; ?>" class="sl-input-small">
							<?php
							self::options( sl_setting( $prefix . 'repeat' ), array(
								'no-repeat' => __( 'None', '7listings' ),
								'repeat'    => __( 'Tile', '7listings' ),
								'repeat-x'  => __( 'Horizontally', '7listings' ),
								'repeat-y'  => __( 'Vertically', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Background Size', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<input name="<?php echo THEME_SETTINGS . "[{$prefix}background_size]"; ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}background_size" ) ); ?>">
					</div>
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Attachment', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo THEME_SETTINGS . "[{$prefix}attachment]"; ?>" class="sl-input-small">
						<?php
						self::options( sl_setting( $prefix . 'attachment' ), array(
							'fixed'  => __( 'Fixed', '7listings' ),
							'scroll' => __( 'Scroll', '7listings' ),
						) );
						?>
					</select>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Show sidebar layout
	 *
	 * @param string $name     Input name
	 * @param bool   $settings The option is in theme settings? Default is true
	 * @param string $selected If the option is not in theme settings, then set the selected value here
	 *
	 * @return void
	 */
	static function sidebar_layout( $name, $settings = true, $selected = '' )
	{
		if ( $settings )
		{
			$layout = sl_setting( $name );
			$name   = sprintf( '%s[%s]', THEME_SETTINGS, $name );
		}
		else
		{
			$layout = $selected;
		}

		$id      = uniqid();
		$layouts = array(
			'none'  => array( 'no-sidebar', __( 'No sidebar', '7listings' ) ),
			'right' => array( 'right-sidebar', __( 'Right sidebar', '7listings' ) ),
			'left'  => array( 'left-sidebar', __( 'Left sidebar', '7listings' ) ),
		);
		foreach ( $layouts as $value => $sidebar )
		{
			$checked = checked( $layout, $value, false );
			printf( '
				<span>
					<input type="radio" name="%s" id="%s-%s" value="%s"%s>
					<label class="%s%s" title="%s" for="%s-%s">&nbsp;</label>
				</span>',
				$name,
				$id, $value,
				$value,
				$checked,
				$sidebar[0], $checked ? ' active' : '',
				$sidebar[1],
				$id, $value
			);
		}
	}

	/**
	 * Helper function to show options for sidebar
	 *
	 * @param string $name     Input name
	 * @param bool   $settings The option is in theme settings? Default is true
	 * @param string $selected If the option is not in theme settings, then set the selected value here
	 *
	 * @return void
	 */
	static function sidebar_select( $name, $settings = true, $selected = '' )
	{
		if ( $settings )
		{
			$sidebar = sl_setting( $name );
			$name    = sprintf( '%s[%s]', THEME_SETTINGS, $name );
		}
		else
		{
			$sidebar = $selected;
		}

		echo '<select name="' . $name . '">';
		echo '<option value="">' . __( 'Default', '7listings' ) . '</option>';

		global $wp_registered_sidebars;
		$options = array();
		foreach ( $wp_registered_sidebars as $registered_sidebar )
		{
			$options[$registered_sidebar['id']] = $registered_sidebar['name'];
		}
		asort( $options );
		self::options( $sidebar, $options );

		echo '</select>';
		?>
		<a target="_blank" href="<?php menu_page_url( 'sidebars' ); ?>" title="<?php _e( 'Edit Sidebars', '7listings' ); ?>" class="edit-icon-md"><?php _e( 'Edit Sidebars', '7listings' ); ?></a>
	<?php
	}

	/**
	 * Helper function to show options for image sizes
	 *
	 * @param string $name     Input name
	 * @param string $selected The selected value here
	 * @param string $att      More attributes for <select>
	 *
	 * @return void
	 */
	static function image_sizes_select( $name, $selected = '', $att = '' )
	{
		if ( $att )
			$att = " $att";
		echo "<select name='$name'$att>";

		echo '<option value="full"' . selected( $selected, 'full', false ) . '>' . __( 'Full', '7listings' ) . '</option>';
		echo '<optgroup label="' . __( 'Square (1:1)', '7listings' ) . '">';
		echo '<option value="sl_thumb_tiny"' . selected( $selected, 'sl_thumb_tiny', false ) . '>' . __( 'Tiny', '7listings' ) . ' (80x80)</option>';
		echo '<option value="sl_thumb_small"' . selected( $selected, 'sl_thumb_small', false ) . '>' . __( 'Small', '7listings' ) . ' (150x150)</option>';
		echo '<option value="sl_thumb_medium"' . selected( $selected, 'sl_thumb_medium', false ) . '>' . __( 'Medium', '7listings' ) . ' (300x300)</option>';
		echo '<option value="sl_thumb_large"' . selected( $selected, 'sl_thumb_large', false ) . '>' . __( 'Large', '7listings' ) . ' (600x600)</option>';
		echo '<option value="sl_thumb_huge"' . selected( $selected, 'sl_thumb_huge', false ) . '>' . __( 'Huge', '7listings' ) . ' (1024x1024)</option>';
		echo '</optgroup>';

		echo '<optgroup label="' . __( 'Panorama (16:9)', '7listings' ) . '">';
		echo '<option value="sl_pano_small"' . selected( $selected, 'sl_pano_small', false ) . '>' . __( 'Small', '7listings' ) . ' (168x80)</option>';
		echo '<option value="sl_pano_medium"' . selected( $selected, 'sl_pano_medium', false ) . '>' . __( 'Medium', '7listings' ) . ' (315x150)</option>';
		echo '<option value="sl_pano_large"' . selected( $selected, 'sl_pano_large', false ) . '>' . __( 'Large', '7listings' ) . ' (630x300)</option>';
		echo '<option value="sl_pano_huge"' . selected( $selected, 'sl_pano_huge', false ) . '>' . __( 'Huge', '7listings' ) . ' (1260x700)</option>';
		echo '</optgroup>';

		echo '<optgroup label="' . __( 'Featured (16:4)', '7listings' ) . '">';
		echo '<option value="sl_feat_medium"' . selected( $selected, 'sl_feat_medium', false ) . '>' . __( 'Medium', '7listings' ) . ' (1280x320)</option>';
		echo '<option value="sl_feat_large"' . selected( $selected, 'sl_feat_large', false ) . '>' . __( 'Large', '7listings' ) . ' (1920x480)</option>';
		echo '</optgroup>';

		echo '<optgroup label="' . __( 'WordPress', '7listings' ) . '">';
		foreach ( array( 'thumbnail', 'medium', 'large' ) as $size )
		{
			$label = ucwords( $size );
			printf(
				'<option value="%s"%s>%s (%sx%s)</option>',
				$name,
				selected( $selected, $size, false ),
				$label, get_option( $size . '_size_w' ), get_option( $size . '_size_h' )
			);
		}
		echo '</optgroup>';

		echo '</select>';
	}

	/**
	 * Helper function to show options for icon
	 *
	 * @param string $name Input name
	 *
	 * @return void
	 */
	static function icon( $name )
	{
		$value = sl_setting( $name );
		$list  = self::fa_icons();
		echo '<div class="icons-select">';
		$tpl = '
			<label%s>
				<i class="icon-%s"></i>
				<input type="radio" class="hidden" name="%s[%s]" value="%s"%s>
			</label>
		';

		foreach ( $list as $class => $content )
		{
			printf(
				$tpl,
				$content == $value ? ' class="active"' : '',
				$class,
				THEME_SETTINGS, $name, $content, checked( $content, $value, false )
			);
		}
		echo '</div>';
	}

	/**
	 * Get list of Font Awesome icons
	 *
	 * @return array Array of class-name => content
	 */
	static function fa_icons()
	{
		$icons = file_get_contents( THEME_DIR . 'css/less/components/plugins/font-awesome/variables.less' );
		$icons = array_filter( array_map( 'trim', explode( "\n", $icons ) ) );
		$list  = array();
		foreach ( $icons as $line )
		{
			if ( false === strpos( $line, '\f' ) )
				continue;

			list( $class, $content ) = explode( ':', trim( $line, ';@' ) );
			$list[trim( $class )] = trim( $content, '" ' );
		}

		return $list;
	}

	/**
	 * Check if a $_POST[$name] field is empty when submit and return its value
	 * This is used to handled price when submit "0" or nothing
	 *
	 * @param string $name Post field name
	 *
	 * @return string|int Empty string if field is not set or set to ''. Otherwise, integer.
	 */
	static function post_number( $name )
	{
		return isset( $_POST[$name] ) && '' !== $_POST[$name] ? intval( $_POST[$name] ) : '';
	}

	/**
	 * Helper function to show select heading style
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	static function select_heading_style( $name )
	{
		printf( '<select name="%s" class="sl-input-small">', THEME_SETTINGS . "[{$name}_heading_style]" );
		$options = array(
			'h1' => __( 'Heading 1', '7listings' ),
			'h2' => __( 'Heading 2', '7listings' ),
			'h3' => __( 'Heading 3', '7listings' ),
			'h4' => __( 'Heading 4', '7listings' ),
			'h5' => __( 'Heading 5', '7listings' ),
			'h6' => __( 'Heading 6', '7listings' ),
		);
		self::options( sl_heading_style( $name ), $options );
		echo '</select>';

	}
}

/**
 * Custom walker class for category dropdown which supports select multiple values
 */
class Sl_Walker_Category_Dropdown extends Walker_CategoryDropdown
{
	/**
	 * Start the element output.
	 *
	 * @see   Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category Category data object.
	 * @param int    $depth    Depth of category. Used for padding.
	 * @param array  $args     Uses 'selected', 'show_count', and 'value_field' keys, if they exist.
	 *                         See {@see wp_dropdown_categories()}.
	 */
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		$pad = str_repeat( '&nbsp;', $depth * 3 );

		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		if ( ! isset( $args['value_field'] ) || ! isset( $category->{$args['value_field']} ) )
		{
			$args['value_field'] = 'term_id';
		}

		$output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$args['value_field']} ) . "\"";

		if ( $args['multiple'] )
		{
			if ( in_array( $category->term_id, $args['selected'] ) )
			{
				$output .= ' selected="selected"';
			}
		}
		elseif ( $category->term_id == $args['selected'] )
		{
			$output .= ' selected="selected"';
		}
		$output .= '>';
		$output .= $pad . $cat_name;
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;(' . number_format_i18n( $category->count ) . ')';
		$output .= "</option>\n";
	}
}

/**
 * Custom walker class for list of taxomomy
 */
class Sl_Walker_Taxonomy_List extends Walker_Category
{
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		extract( $args );
		if ( '0' == $category->count )
			return;

		$cat_name = esc_attr( $category->name );
		$cat_name = apply_filters( 'list_cats', $cat_name, $category );

		$taxonomy = $args['taxonomy'];
		// Get value of filter
		$filter_features = isset( $_GET["filter_{$taxonomy}"] ) ? explode( ',', $_GET["filter_{$taxonomy}"] ) : array();

		/**
		 * Show term filter with new markup
		 * Link is remove filter with this term
		 */
		if ( in_array( $category->term_id, $filter_features ) || ( in_array( $category->slug, $filter_features ) && 'brand' == $taxonomy ) )
		{
			$class = 'chosen';

			if ( 1 == count( $filter_features ) )
			{
				$url = get_post_type_archive_link( $args['post_type'] );
			}
			else
			{
				foreach ( $filter_features as $key => $value )
				{
					if ( $category->term_id == $value )
						unset( $filter_features[$key] );

					if ( $category->term_id == $value && 'brand' == $taxonomy )
						unset( $filter_features[$key] );
				}
				$filter_features = implode( ',', $filter_features );

				$url = get_post_type_archive_link( $args['post_type'] );
				$url = add_query_arg( 'filter_' . $taxonomy, $filter_features, $url );

				if ( 'or' == $args['query_type'] )
					$url = add_query_arg( 'query_type_' . $taxonomy, 'or', $url );
			}
		}
		else
		{
			$class           = '';
			$filter_features = implode( ',', $filter_features );
			$filter_features = $filter_features ? $filter_features . ',' : '';

			$url = get_post_type_archive_link( $args['post_type'] );

			$key_term = 'brand' == $taxonomy ? esc_attr( $category->slug ) : esc_attr( $category->term_id );

			$url = add_query_arg( 'filter_' . $taxonomy, $filter_features . $key_term, $url );

			if ( 'or' == $args['query_type'] )
				$url = add_query_arg( 'query_type_' . $taxonomy, 'or', $url );

		}

		foreach ( $_GET as $key => $value )
		{
			if ( 'filter_' . $taxonomy == $key )
				continue;

			$url = add_query_arg( $key, $value, $url );
		}

		$link = '<a  href="' . $url . '" ';

		if ( empty( $category->description ) )
			$link .= 'title="' . esc_attr( sprintf( __( 'View all posts filed under %s' ), $cat_name ) ) . '"';
		else
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';


		$link .= '>' . ucfirst( $cat_name ) . '</a>';

		if ( ! empty( $show_count ) )
			$link .= '<small class="count">' . intval( $category->count ) . '</small>';

		if ( 'list' == $args['style'] )
		{
			$output .= "\t<li";
			$class .= ' cat-item cat-item-' . $category->term_id;
			if ( ! empty( $current_category ) )
			{
				$_current_category = get_term( $current_category, $category->taxonomy );
				if ( $category->term_id == $current_category )
					$class .= ' current-cat';
				elseif ( $category->term_id == $_current_category->parent )
					$class .= ' current-cat-parent';
			}

			$output .= ' class="' . $class . '"';
			$output .= ">$link\n";
		}
	}
}

/**
 * Custom walker class for Taxonomy dropdown
 */
class Sl_Walker_Taxonomy_Dropdown extends Walker_CategoryDropdown
{

	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
	{
		$pad      = str_repeat( '-', $depth );
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		if ( ! isset( $args['value'] ) )
		{
			$args['value'] = ( $category->taxonomy != 'category' ? 'slug' : 'id' );
		}

		$value = ( $args['value'] == 'slug' ? $category->slug : $category->term_id );

		$output .= "\t<option class=\"level-$depth\" value=\"" . $value . "\"";
		if ( $value === (string) $args['selected'] )
		{
			$output .= ' selected="selected"';
		}
		$output .= '>';
		$output .= $pad . ucfirst( $cat_name );
		if ( $args['show_count'] )
			$output .= '&nbsp;&nbsp;(' . $category->count . ')';

		$output .= "</option>\n";
	}
}