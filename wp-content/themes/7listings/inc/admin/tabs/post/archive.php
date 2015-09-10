<?php
$post_type = 'post';
$prefix    = 'post_archive_';
?>
<div class="sl-row">
	<div class="column-2">
		<h2><?php _e( 'Headings', '7listings' ); ?></h2>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Blog', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for blog archive', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_blog_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_blog_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert text, html or shortcodes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<textarea name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$post_type}_archive_main_description"; ?>]" cols="40" rows="5" placeholder="<?php _e( 'Main archive description', '7listings' ); ?>"><?php echo esc_textarea( sl_setting( "{$post_type}_archive_main_description" ) ); ?></textarea>
				</div>
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Category', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for tour type archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_category_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_category_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Tag', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for tour features archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_tag_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_tag_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Location', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for tour features archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_location_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_location_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		
		<h2><?php _e( 'Posts', '7listings' ); ?></h2>
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display post featured image', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}featured" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Size', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
				</div>
			</div>
		</div>
		
		<div class="sl-settings toggle-choices">
			<div class="sl-label">
				<label><?php _e( 'Description', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}display"; ?>]" class="sl-input-small">
					<option value="content"<?php selected( 'content', sl_setting( "{$prefix}display" ) ); ?>><?php _e( 'Full Post', '7listings' ); ?></option>
					<option value="excerpt"<?php selected( 'excerpt', sl_setting( "{$prefix}display" ) ); ?>><?php _e( 'Excerpt', '7listings' ); ?></option>
				</select>
			</div>
		</div>
		<div class="sl-sub-settings" data-name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}display"; ?>]" data-value="excerpt">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Length', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter excerpt length<br>for post archive', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<span class="input-append">
						<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}desc"; ?>]" value="<?php echo sl_setting( "{$prefix}desc" ); ?>">
						<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
					</span>
				</div>
			</div>
		</div>
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Read More', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display read more link<br>below excerpt', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}readmore" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Style', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Link design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}readmore_type"; ?>]" class="sl-input-small">
						<option value="text"<?php selected( 'text', sl_setting( "{$prefix}readmore_type" ) ); ?>><?php _e( 'Text', '7listings' ); ?></option>
						<option value="button"<?php selected( 'button', sl_setting( "{$prefix}readmore_type" ) ); ?>><?php _e( 'Button', '7listings' ); ?></option>
					</select>
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Text', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}readmore_text"; ?>]" value="<?php echo sl_setting( "{$prefix}readmore_text" ); ?>">
				</div>
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
				<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If not enter, default settings in Design page will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
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
		
		<br>
		
		<h3><?php _e( 'Main', '7listings' ); ?></h3>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Posts', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter amount of post<br>to display on a page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_posts_per_page]" value="<?php echo get_option( 'posts_per_page' ); ?>" class="amount posts">
					<span class="add-on"><?php _e( '/ page', '7listings' ); ?></span>
				</span><?php echo do_shortcode( '[tooltip content="' . __( 'Same input as in:<br> Settings > Reading', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?>
			</div>
		</div>
		<?php include THEME_TABS . 'parts/archive-sidebar-layout.php'; ?>
	</div>
</div>