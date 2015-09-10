<?php $post_type = 'product'; ?>
<?php $prefix = "{$post_type}_archive_"; ?>
<div class="sl-row">
	<div class="column-2">
		<h2><?php _e( 'Titles', '7listings' ); ?></h2>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Main', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for main archive', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}seo_title_main]"; ?>" value="<?php echo sl_setting( "{$prefix}seo_title_main" ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert text, html or shortcodes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<textarea name="<?php echo THEME_SETTINGS . "[{$prefix}main_description]"; ?>" cols="40" rows="5" placeholder="<?php _e( 'Main archive description', '7listings' ); ?>"><?php echo esc_textarea( sl_setting( "{$prefix}main_description" ) ); ?></textarea>
				</div>
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Categories', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for product category  archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" id="<?php echo $post_type; ?>_archive_seo_title_child" name="<?php echo THEME_SETTINGS . "[{$prefix}seo_title_child]"; ?>" value="<?php echo sl_setting( "{$prefix}seo_title_child" ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		
		<div class="section">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Tags', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for product tag archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" id="<?php echo $post_type; ?>_tag_title" name="<?php echo THEME_SETTINGS . "[{$post_type}_tag_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_tag_title" ); ?>" class="sl-input-large">
					<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
				</div>
			</div>
		</div>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Brands', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for product brand archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" id="<?php echo $post_type; ?>_brand_title" name="<?php echo THEME_SETTINGS . "[{$post_type}_brand_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_brand_title" ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<?php // Settings Attributes ?>
		<div class="section">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Attributes', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for product attribute archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" id="<?php echo $post_type; ?>_attribute_title" name="<?php echo THEME_SETTINGS . "[{$post_type}_attribute_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_attribute_title" ); ?>" class="sl-input-large">
					<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
				</div>
			</div>
		</div>
		
		<br>
		<br>
		
		<h2><?php _e( 'Main', '7listings' ); ?></h2>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select image size for<br>categories and products', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
			</div>
		</div>

		<br>
		
		<h3><?php _e( 'Categories', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product categories<br>from WooCommerce settings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h3>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category image', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}cat_thumb" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product category title', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}cat_title" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Count', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display amount of products for category', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}cat_count" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label></label>
			</div>
			<div class="sl-input">
				<?php printf( __( '<a href="%s" class="woo-settings-link icon">Display settings</a>', '7listings' ), admin_url( 'admin.php?page=wc-settings&tab=products&section=display' ) ); ?>
			</div>
		</div>
		
		<br>
		<br>

		<h3><?php _e( 'Products', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display products<br>from WooCommerce settings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h3>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}price" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display add to cart or<br>read more button', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}button" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display overall rating<br>from reviews', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}rating" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product excerpt or description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$prefix}excerpt" ); ?>
				</span>
				<span class="input-append">
					<input type="number" class="small-text" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}excerpt_length]"; ?>" value="<?php echo sl_setting( "{$prefix}excerpt_length" ); ?>">
					<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label></label>
			</div>
			<div class="sl-input">
				<?php printf( __( '<a href="%s" class="woo-settings-link icon">Display settings</a>', '7listings' ), admin_url( 'admin.php?page=wc-settings&tab=products&section=display' ) ); ?>
			</div>
		</div>
		

	</div>
	<div class="column-2">

		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>

		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category or taxonomy description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}cat_desc" ); ?>
			</div>
		</div>
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category or taxonomy image', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}cat_image" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Style', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}cat_image_type"; ?>]">
						<?php
						Sl_Form::options( sl_setting( "{$prefix}cat_image_type" ), array(
							'thumbnail'  => __( 'Thumbnail', '7listings' ),
							'background' => __( 'Background', '7listings' ),
						) );
						?>
					</select>
				</div>
			</div>
			<div class="sl-settings sub-input">
				<div class="sl-label">
					<label><?php _e( 'Size', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}cat_image_size]", sl_setting( "{$prefix}cat_image_size" ) ); ?>
				</div>
			</div>
		</div>

		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select header height<br>Medium = default settings in Design page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_height]"; ?>]" class="sl-input-small">
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

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Products', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter amount of products<br>to display on a page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="small-text" name="<?php echo THEME_SETTINGS . "[{$prefix}main_num]"; ?>" value="<?php echo sl_setting( "{$prefix}main_num" ); ?>">
					<span class="add-on"><?php _e( '/ page', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select default product sorting', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS . "[{$prefix}order_by]"; ?>">
					<?php
					Sl_Form::options( sl_setting( "{$prefix}order_by" ), array(
						'menu_order' => __( 'WordPress default', '7listings' ),
						'popularity' => __( 'Popularity', '7listings' ),
						'rating'     => __( 'Average rating', '7listings' ),
						'date'       => __( 'Newness', '7listings' ),
						'price'      => __( 'Price: low to high', '7listings' ),
						'price-desc' => __( 'Price: high to low', '7listings' )
					) );
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'User select', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display sorting inputs on product archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$prefix}sort" ); ?>
				</span>
				<span>
					<?php $selected = sl_setting( "{$prefix}sort_display" ); ?>
					<select name="<?php echo THEME_SETTINGS . "[{$prefix}sort_display]"; ?>">
						<?php
						Sl_Form::options( sl_setting( "{$prefix}sort_display" ), array(
							'above' => __( 'Above', '7listings' ),
							'below' => __( 'Below', '7listings' ),
							'both'  => __( 'Above &amp; Below', '7listings' ),
						) );
						?>
					</select>
				</span>
			</div>
		</div>
		
		<br>
		<hr class="light">

		<div class="sl-settings listing-type layout">
			<div class="sl-label">
				<label><?php _e( 'Layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select post layout design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php $layout = sl_setting( "{$prefix}layout" ); ?>
				<span>
						<?php $checked = checked( $layout, 'list', false ); ?>
					<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-list" value="list" <?php echo $checked; ?>>
						<label class="list<?php echo $checked ? ' active' : ''; ?>" for="<?php echo $post_type; ?>-archive-layout-list">&nbsp;</label>
					</span>
					<span>
						<?php $checked = checked( $layout, 'grid', false ); ?>
						<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-grid" value="grid" <?php echo $checked; ?>>
						<label class="grid<?php echo $checked ? ' active' : ''; ?>" for="<?php echo $post_type; ?>-archive-layout-grid">&nbsp;</label>
					</span>
				<?php if ( 'post' == $post_type ) : ?>
					<span>
					<?php $checked = checked( $layout, 'magazine', false ); ?>
						<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-magazine" value="magazine" <?php echo $checked; ?>>
					<label class="magazine<?php echo $checked ? ' active' : ''; ?>" for="<?php echo $post_type; ?>-archive-layout-magazine">&nbsp;</label>
				</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="sl-sub-settings columns layout">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Columns', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php
					$value     = sl_setting( "{$prefix}columns" );
					$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols', 5 => 'five-cols', 6 => 'six-cols' );
					for ( $cols = 2; $cols <= 6; $cols ++ )
					{
						$checked = checked( $value, $cols, false );
						printf( '
								<span>
									<input type="radio" name="%1$s[%2$s_archive_columns]" id="%2$s-archive-columns-%3$s" value="%3$s" %4$s>
									<label class="%5$s%6$s" for="%2$s-archive-columns-%3$s">&nbsp;</label>
								</span>',
							THEME_SETTINGS,
							$post_type,
							$cols,
							$checked,
							$cols_name[$cols],
							$checked ? ' active' : ''
						);
					}
					?>
				</div>
			</div>
		</div>

		<br>

		<div class="sl-settings sidebar layout">
			<div class="sl-label">
				<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sidebar layout<br>for archive pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::sidebar_layout( "{$prefix}sidebar_layout" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Widget Area', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select widget area<br>for sidebar', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::sidebar_select( "{$prefix}sidebar" ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
