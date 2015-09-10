<?php $post_type = 'product'; ?>
<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for product', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_single_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_single_title" ) ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<br>

		<h3><?php _e( 'Main', '7listings' ); ?></h3>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select main gallery image size', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_slider_image_size]", sl_setting( "{$post_type}_slider_image_size" ) ); ?>
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Attributes', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product attributes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_attributes" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Meta', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product meta', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_meta" ); ?>
			</div>
		</div>
		
		<br>
		<hr class="light">

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Reviews', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable and display product reviews', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
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
		
		<br>
		<hr class="light">

		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Upsells', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product/WooCommerce upsells', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input sells-checkbox">
				<?php Sl_Form::checkbox( "{$post_type}_upsells" ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Title', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_upsells_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_upsells_title" ); ?>" class="sl-input-large">
					<?php echo do_shortcode( '[tooltip content="' . __( 'Title for upsells', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
				</div>
			</div>
		</div>

		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Cross Sells', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product/WooCommerce cross sells', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input sells-checkbox">
				<?php Sl_Form::checkbox( "{$post_type}_related" ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Title', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<input type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_related_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_related_title" ); ?>" class="sl-input-large">
					<?php echo do_shortcode( '[tooltip content="' . __( 'Title for crosssells', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
				</div>
			</div>
		</div>

		<div class="sells-sub-settings">
			<h3><?php _e( 'Up & Cross Sells', '7listings' ); ?></h3>

			<div class="sl-sub-settings">
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting for listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$post_type}_sells_similar"; ?>]">
							<?php
							Sl_Form::options( sl_setting( "{$post_type}_sells_similar" ), array(
								'Categories' => __( 'Categories', '7listings' ),
								'Tags'       => __( 'Tags', '7listings' ),
								'Brands'     => __( 'Brands', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Amount', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Amount of listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="small-text" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$post_type}_sells_amount]"; ?>]" value="<?php echo sl_setting( "{$post_type}_sells_amount" ); ?>">
							<span class="add-on"><?php _e( 'listings', '7listings' ); ?></span>
						</span>
					</div>
				</div>

				<br>

				<div class="sl-settings single columns layout">
					<div class="sl-label">
						<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select layout for<br>upsells and cross sells', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
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
								<label class="%5$s%6$s" for="%2$s-similar-columns-%3$s">&nbsp;</label>
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

				<br>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select image size for products', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_similar_image_size]", sl_setting( "{$post_type}_similar_image_size" ) ); ?>
					</div>
				</div>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display overall rating<br>from reviews', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$post_type}_sells_rating" ); ?>
					</div>
				</div>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$post_type}_sells_price" ); ?>
					</div>
				</div>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display add to cart or<br>read more button', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$post_type}_sells_button" ); ?>
					</div>
				</div>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Excerpt', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product excerpt or description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="checkbox-toggle" data-effect="fade">
							<?php Sl_Form::checkbox( "{$post_type}_sells_excerpt_enable" ); ?>
						</span>
						<span class="input-append">
							<input type="number" class="small-text" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$post_type}_sells_excerpt]"; ?>]" value="<?php echo sl_setting( "{$post_type}_sells_excerpt" ); ?>">
							<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
						</span>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="column-2">
		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>

		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>
		
		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Logo', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display product brand logo', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_single_brand_logo" ); ?>
			</div>
		</div>

		<div id="brand-logo-options" class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select brand image size', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$post_type}_single_brand_logo_image_size]", sl_setting( "{$post_type}_single_brand_logo_image_size" ) ); ?>
				</div>
			</div>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Link', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable link to brand archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( "{$post_type}_single_brand_logo_link" ); ?>
				</div>
			</div>
		</div>
		
		<br>
		<br>
		
		<h3><?php _e( 'Main', '7listings' ); ?></h3>
		
		<?php include THEME_TABS . 'parts/single-sidebar-layout.php'; ?>
	</div>
</div>
