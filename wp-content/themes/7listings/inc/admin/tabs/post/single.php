<?php
$post_type = 'post';
$prefix    = 'post_single_';
?>
<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Title for listing', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}title" ) ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Meta', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display post meta<br>like categories and tags', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_single_meta" ); ?>
			</div>
		</div>
		
		<br>
		<hr class="light">
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the featured image<br>with post content', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_single_featured" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings image-size">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Size', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select featured image size', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_single_image_size]", sl_setting( "{$post_type}_single_image_size" ) ); ?>
				</div>
			</div>
		</div>
		
		<hr class="light">
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Author', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display author info:<br>image, social links and biography', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_author_details" ); ?>
			</div>
		</div>
		
		<br>
		<hr class="light">
		
		<h3><?php _e( 'Post Links', '7listings' ); ?></h3>
		
		<?php
		$fields = array(
			'related'        => __( 'Related Posts', '7listings' ),
			'nextprev'       => __( 'Prev/Next Posts', '7listings' ),
			'recent'         => __( 'Recent Posts', '7listings' ),
			'popular'        => __( 'Popular Posts', '7listings' ),
		);
		foreach ( $fields as $k => $v )
		{
			echo '<div class="sl-settings">';
			echo "<div class='sl-label'><label>$v</label></div>";
			echo '<div class="sl-input">';
			Sl_Form::checkbox( "{$post_type}_{$k}" );
			echo '</div>';
			echo '</div>';
		}
		?>
		<h4><?php _e( 'Link excerpt settings', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Applies to all links', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h4>
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Excerpt', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_related_excerpt" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings excerpt">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Excerpt Length', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<span class="input-append">
						<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$post_type}_related_excerpt_length]"; ?>" value="<?php echo sl_setting( "{$post_type}_related_excerpt_length" ); ?>">
						<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
					</span>
				</div>
			</div>
		</div>
		
		<br>
		<hr class="light">
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Comments', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable and display post comments', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_comment_status" ); ?>
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
				<?php Sl_Form::checkbox( "{$post_type}_ping_status" ); ?>
			</div>
		</div>
		<?php do_action( 'post_edit_single_meta_after' ); ?>
	</div>
	<div class="column-2">
		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>
		
		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>
		
		<div class="sl-settings checkbox-switch checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the featured image<br>as full size background in header', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}featured_title_image" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings image-size">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Size', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}featured_title_image_size]", sl_setting( "{$prefix}featured_title_image_size" ) ); ?>
				</div>
			</div>
		</div>
		
		<br>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If not enter, default settings in Design page will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_height]"; ?>" class="sl-input-small">
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