<?php $prefix = "{$post_type}_single_"; ?>
<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert page title for listing', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}title" ) ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<?php if ( 'accommodation' == $post_type ) : ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Stars', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display accommodation star rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}star_rating" ); ?>
			</div>
		</div>
		<?php endif; ?>

		<br>

		<h3><?php _e( 'Main', '7listings' ); ?></h3>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select image size for gallery', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_slider_image_size]", sl_setting( "{$post_type}_slider_image_size" ) ); ?>
			</div>
		</div>

		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Address', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing address', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}address" ); ?>
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Contact Details', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display contact details<br>eg: phone, url, email', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}contact" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Features', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing features<br>that link to archive pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}features" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Map', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display Location map', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $post_type . '_google_maps' ); ?>
			</div>
		</div>

		<br>
		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Reviews', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable and display reviews', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $post_type . '_comment_status' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label>
					<?php
					printf(
						'<a href="%s" target="_blank">%s</a>',
						'http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments',
						__( 'Trackbacks &amp; Pingbacks', '7listings' )
					);
					?>
				</label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $post_type . '_ping_status' ); ?>
			</div>
		</div>

		<br>

		<br>

		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Related Listings', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_similar_enable" ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert title for related listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$post_type}_similar_title"; ?>]" value="<?php echo sl_setting( "{$post_type}_similar_title" ); ?>" class="sl-input-large">
				</div>
			</div>
			<br>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting for related listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<select id="<?php echo $post_type; ?>_similar_by" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$post_type}_similar_by"; ?>]" class="sl-input-small">
						<?php
						Sl_Form::options( sl_setting( "{$post_type}_similar_by" ), array(
							'type'  => __( 'Type', '7listings' ),
							'price' => __( 'Price', '7listings' ),
						) );
						?>
					</select>
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Amount', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter amount of related listings<br>displayed in columns', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<span class="input-append">
						<input type="number" min="0" class="amount" name="<?php echo THEME_SETTINGS . "[{$post_type}_similar_display]"; ?>" value="<?php echo sl_setting( "{$post_type}_similar_display" ); ?>">
						<span class="add-on"><?php _e( 'Listings', '7listings' ); ?></span>
					</span>
				</div>
			</div>

			<br>

			<div class="sl-settings single columns layout">
				<div class="sl-label">
					<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select related listings layout', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php
					$value     = sl_setting( "{$post_type}_similar_columns" );
					$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols', 5 => 'five-cols', 6 => 'six-cols' );
					for ( $cols = 2; $cols <= 6; $cols ++ )
					{
						$checked = checked( $value, $cols, false );
						printf( '
							<span>
								<input type="radio" name="%1$s[%2$s_similar_columns]" id="%2$s-similar-columns-%3$s" value="%3$s" %4$s>
								<label class="%5$s%6$s" title="%3$s %7$s" for="%2$s-similar-columns-%3$s">&nbsp;</label>
							</span>',
							THEME_SETTINGS,
							$post_type,
							$cols,
							$checked,
							$cols_name[$cols],
							$checked ? ' active' : '',
							__( 'Columns', '7listings' )
						);
					}
					?>
				</div>
			</div>
			<br>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select image size', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_similar_image_size]", sl_setting( "{$post_type}_similar_image_size" ) ); ?>
				</div>
			</div>

			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display customer review rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( "{$post_type}_similar_rating" ); ?>
				</div>
			</div>
			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( "{$post_type}_similar_price" ); ?>
				</div>
			</div>
			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display booking button', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( "{$post_type}_similar_booking" ); ?>
				</div>
			</div>
			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Excerpt', '7listings' ) ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display excerpt or<br>XX words from description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<span class="checkbox-toggle" data-effect="fade">
						<?php Sl_Form::checkbox( "{$post_type}_similar_excerpt" ); ?>
					</span>
					<span class="input-append supplementary-input">
						<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$post_type}_similar_excerpt_length]"; ?>]" value="<?php echo esc_attr( sl_setting( "{$post_type}_similar_excerpt_length" ) ); ?>">
						<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="column-2">
		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>

		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>

		<div class="sl-settings checkbox-switch checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display featured image as a full size background', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}featured_title_image" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Size', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}featured_title_image_size]", sl_setting( "{$prefix}featured_title_image_size" ) ); ?>
				</div>
			</div>
		</div>

		<div class="sl-settings checkbox-toggle checkbox-switch">
			<div class="sl-label">
				<label><?php _e( 'Map', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display location map as background', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}featured_title_map" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Zoom Level', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_map_zoom]"; ?>" class="sl-input-tiny">
						<?php
						$value = sl_setting( "{$prefix}featured_title_map_zoom" );
						for ( $i = 1; $i <= 16; $i ++ )
						{
							printf( '<option value="%d"%s>%d</option>', $i, selected( $i, $value, false ), $i );
						}
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Logo', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing logo', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}logo" ); ?>
			</div>
		</div>

		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If not enter, default settings in Design page will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_height]"; ?>" class="sl-input-small height-select">
					<?php
					$sizes = array(
						'tiny'  => __( 'Tiny', '7listings' ),
						'small' => __( 'Small', '7listings' ),
						''      => __( 'Medium', '7listings' ),
						'large' => __( 'Large', '7listings' ),
						'huge'  => __( 'Huge', '7listings' ),
					);
					Sl_Form::options( sl_setting( "{$prefix}featured_title_height" ), $sizes );
					?>
				</select>
			</div>
		</div>

		<br><br>

		<h3><?php _e( 'Main', '7listings' ); ?></h3>
		<?php include THEME_TABS . 'parts/single-sidebar-layout.php'; ?>
	</div>
</div>
