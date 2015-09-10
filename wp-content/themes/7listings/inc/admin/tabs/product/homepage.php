<?php
$prefix = "homepage_{$id}_";
if ( 'product_featured' == $id )
{
	?>
	<!-- Product Featured Listings Slider -->
	<div class="box">
		<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="product_featured">
		<span class="add-md toggle"></span>
		<span class="heading"><?php _e( 'Product Slider', '7listings' ); ?></span>
		<span class="on-off">
			<?php _e( 'Display:', '7listings' ); ?>
			<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
		</span>

		<section class="widget-settings hidden">
			<div class="sl-row">
				<div class="column-2">

					<div class="sl-settings sl-widget-title">
						<div class="sl-label">
							<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
						</div>
						<div class="sl-input">
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
							<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
						</div>
					</div>

				</div>
				<div class="column-2">

					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Sort by', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
								<option value="-1"><?php _e( 'Select', '7listings' ); ?></option>
								<?php
								Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
									'date'       => __( 'Newest', '7listings' ),
									'views'      => __( 'Most Viewed', '7listings' ),
									'price-asc'  => __( 'Price (low-high)', '7listings' ),
									'price-desc' => __( 'Price (high-low)', '7listings' ),
									'rand'       => __( 'Random', '7listings' ),
								) );
								?>
							</select>
						</div>
					</div>
					<br>
					<div class="sl-settings columns layout">
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
										<input type="radio" name="%s[homepage_product_featured_columns]" id="homepage-product-featured-columns-%s" value="%s"%s>
										<label class="%s%s" title="%s" for="homepage-product-featured-columns-%s">&nbsp;</label>
									</span>',
									THEME_SETTINGS,
									$cols,
									$cols,
									$checked,
									$cols_name[$cols],
									$checked ? ' active' : '',
									$cols . __( ' Columns', '7listings' ),
									$cols
								);
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
	return;
}

if ( 'product_listings' == $id )
{
	?>
	<!-- Product Listings -->
	<div class="product-listings box">
		<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="product_listings">
		<span class="add-md toggle"></span>
		<span class="heading"><?php _e( 'Products', '7listings' ); ?></span>
		<span class="on-off">
			<?php _e( 'Display:', '7listings' ); ?>
			<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
		</span>

		<section class="widget-settings hidden">
			<div class="sl-row">
				<div class="column-2">

					<div class="sl-settings sl-widget-title">
						<div class="sl-label">
							<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
						</div>
						<div class="sl-input">
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
							<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
						</div>
					</div>
					<br>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Image Size', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
						</div>
					</div>

					<div class="sl-settings checkbox-toggle">
						<div class="sl-label">
							<label><?php _e( 'See more listings', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox( "{$prefix}more_listings" ); ?>
						</div>
					</div>
					<div class="sl-sub-settings">
						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Text', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_text]"; ?>" value="<?php echo sl_setting( "{$prefix}more_listings_text" ); ?>">
							</div>
						</div>
						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Style', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<select class="input-small" name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_style]"; ?>">
									<option value="button"<?php selected( 'button', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Button', '7listings' ); ?></option>
									<option value="text"<?php selected( 'text', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Text', '7listings' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="column-2">

					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Display', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<span class="input-append">
								<input type="number" class="amount" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
								<span class="add-on"><?php _e( 'Products', '7listings' ); ?></span>
							</span>
						</div>
					</div>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Sort by', '7listings' ); ?> <span class="warning-sm required right"></span></label>
						</div>
						<div class="sl-input">
							<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
								<?php
								Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
									'date'       => 'Newest',
									'views'      => 'Most Viewed',
									'price-asc'  => 'Price (low-high)',
									'price-desc' => 'Price (high-low)',
									'rand'       => 'Random',
								) );
								?>
							</select>
						</div>
					</div>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Category', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php
							wp_dropdown_categories( array(
								'show_option_all' => __( 'All', '7listings' ),
								'taxonomy'        => 'product_cat',
								'hide_empty'      => 1,
								'name'            => THEME_SETTINGS . '[homepage_product_listings_category]',
								'selected'        => sl_setting( "{$prefix}category" ),
								'id'              => "{$prefix}category",
							) );
							?>
						</div>
					</div>
					<br>
					<div class="sl-settings columns layout">
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
										<input type="radio" name="%s[homepage_product_listings_columns]" id="homepage-product-listings-columns-%s" value="%s"%s>
										<label class="%s%s" title="%s" for="homepage-product-listings-columns-%s">&nbsp;</label>
									</span>',
									THEME_SETTINGS,
									$cols,
									$cols,
									$checked,
									$cols_name[$cols],
									$checked ? ' active' : '',
									$cols . __( ' Columns', '7listings' ),
									$cols
								);
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
	return;
}

if ( 'product_categories' == $id )
{
	?>
	<!-- Product Categories -->
	<div class="product-types box">
		<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="product_categories">
		<span class="add-md toggle"></span>
		<span class="heading"><?php _e( 'Product Categories', '7listings' ); ?></span>
		<span class="on-off">
			<?php _e( 'Display:', '7listings' ); ?>
			<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
		</span>
		<section class="widget-settings hidden">
			<div class="sl-row">
				<div class="column-2">

					<div class="sl-settings sl-widget-title">
						<div class="sl-label">
							<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
						</div>
						<div class="sl-input">
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
							<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
						</div>
					</div>

					<br>

					<div class="sl-settings checkbox-toggle">
						<div class="sl-label">
							<label><?php _e( 'Image', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox( "{$prefix}thumb" ); ?>
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
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Title', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox( "{$prefix}category_title" ); ?>
						</div>
					</div>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Product Counter', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox( "{$prefix}count" ); ?>
						</div>
					</div>
				</div>
				<div class="column-2">

					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Display', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<span class="input-append">
								<input type="number" class="amount" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
								<span class="add-on"><?php _e( 'Categories', '7listings' ); ?></span>
							</span>
						</div>
					</div>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Sort by', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
								<?php
								Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
									'none'          => __( 'None', '7listings' ),
									'name'          => __( 'Name', '7listings' ),
									'listings-asc'  => __( '#Listings Up', '7listings' ),
									'listings-desc' => __( '#Listings Down', '7listings' ),
								) );
								?>
							</select>
						</div>
					</div>
					<br>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Sub-Categories', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::checkbox( "{$prefix}sub" ); ?>
						</div>
					</div>
					<br>
					<div class="sl-settings columns layout">
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
										<input type="radio" name="%s[homepage_product_categories_columns]" id="homepage-product-categories-columns-%s" value="%s"%s>
										<label class="%s%s" title="%s" for="homepage-product-categories-columns-%s">&nbsp;</label>
									</span>',
									THEME_SETTINGS,
									$cols,
									$cols,
									$checked,
									$cols_name[$cols],
									$checked ? ' active' : '',
									$cols . __( ' Columns', '7listings' ),
									$cols
								);
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	<?php
	return;
}
