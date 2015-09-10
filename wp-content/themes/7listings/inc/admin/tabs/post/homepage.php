<?php
if ( 'post_listings' != $id )
	return;

$prefix = "homepage_{$id}_";
?>

<div id="post_listings" class="post-listings box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="post_listings">
	<span class="add-md toggle"></span>
	<span class="heading"><?php _e( 'Posts', '7listings' ); ?></span>
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
						<label><?php _e( 'Featured Image', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}featured" ); ?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Post length', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}desc]"; ?>" value="<?php echo sl_setting( "{$prefix}desc" ); ?>">
							<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				<div class="sl-settings checkbox-toggle">
					<div class="sl-label">
						<label><?php _e( 'Read more', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}readmore" ); ?>
					</div>
				</div>
				<div class="sl-sub-settings">
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Style', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<select name="<?php echo THEME_SETTINGS . "[{$prefix}readmore_type]"; ?>" class="sl-input-small">
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
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}readmore_text]"; ?>" value="<?php echo sl_setting( "{$prefix}readmore_text" ); ?>">
						</div>
					</div>
				</div>
				<br>
				<div class="sl-settings checkbox-toggle">
					<div class="sl-label">
						<label><?php _e( 'See more posts', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}more_listings" ); ?>
					</div>
				</div>
				<div class="sl-sub-settings">
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Style', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<select name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_style]"; ?>" class="sl-input-small">
								<option value="button"<?php selected( 'button', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Button', '7listings' ); ?></option>
								<option value="text"<?php selected( 'text', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Text', '7listings' ); ?></option>
							</select>
						</div>
					</div>
		
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Text', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_text]"; ?>" value="<?php echo sl_setting( "{$prefix}more_listings_text" ); ?>">
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
							<span class="add-on"><?php _e( 'Posts', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				<br>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Sort by', '7listings' ); ?> <span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
							<?php
							Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
								'date'  => __( 'Newest', '7listings' ),
								'views' => __( 'Most Viewed', '7listings' ),
								'rand'  => __( 'Random', '7listings' ),
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
							'taxonomy'        => 'category',
							'hide_empty'      => 1,
							'name'            => THEME_SETTINGS . '[homepage_post_listings_category]',
							'selected'        => sl_setting( "{$prefix}category" ),
							'id'              => "{$prefix}category",
						) );
						?>
					</div>
				</div>
				<br>
				<div class="sl-settings listing-type layout">
					<div class="sl-label">
						<label><?php _e( 'Layout', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php $layout = sl_setting( "{$prefix}layout" ); ?>
						<span>
							<?php $checked = checked( $layout, 'list', false ); ?>
							<input type="radio" name="<?php echo THEME_SETTINGS . "[{$prefix}layout]"; ?>" id="homepage_post-listings-layout-list" value="list" <?php echo $checked; ?>>
							<label for="homepage_post-listings-layout-list" title="<?php _e( 'List', '7listings' ); ?>" class="list<?php echo $checked ? ' active' : ''; ?>">&nbsp;</label>
						</span>
						<span>
							<?php $checked = checked( $layout, 'grid', false ); ?>
							<input type="radio" name="<?php echo THEME_SETTINGS . "[{$prefix}layout]"; ?>" id="homepage_post-listings-layout-grid" value="grid" <?php echo $checked; ?>>
							<label for="homepage_post-listings-layout-grid" title="<?php _e( 'Columns', '7listings' ); ?>" class="grid<?php echo $checked ? ' active' : ''; ?>">&nbsp;</label>
						</span>
						<span>
							<?php $checked = checked( $layout, 'magazine', false ); ?>
							<input type="radio" name="<?php echo THEME_SETTINGS . "[{$prefix}layout]"; ?>" id="homepage_post-listings-layout-magazine" value="magazine" <?php echo $checked; ?>>
							<label for="homepage_post-listings-layout-magazine" title="<?php _e( 'Magazine', '7listings' ); ?>" class="magazine<?php echo $checked ? ' active' : ''; ?>">&nbsp;</label>
						</span>
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
							$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols' );
							$post_type = 'post';
							for ( $cols = 2; $cols <= 4; $cols ++ )
							{
								$checked = checked( $value, $cols, false );
								printf( '
									<span>
										<input type="radio" name="%1$s[homepage_%2$s_listings_columns]" id="%2$s-listings-columns-%3$s" value="%3$s" %4$s>
										<label for="%2$s-listings-columns-%3$s" title="%3$s %7$s" class="%5$s%6$s">&nbsp;</label>
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
				</div>
				<div class="sl-settings sidebar layout visible">
					<div class="sl-label">
						<label><?php _e( 'Sidebar', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::sidebar_layout( "{$prefix}sidebar_layout" ); ?>
					</div>
				</div>
				<div class="sl-sub-settings sl-sidebar-options">
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Widget Area', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::sidebar_select( "{$prefix}sidebar" ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
