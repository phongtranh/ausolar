<?php
$prefix = "homepage_{$post_type}_types_";
?>
<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo "{$post_type}_types"; ?>">
	<span class="add-md toggle"></span>
	<span class="heading"><?php echo $title; ?></span>
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
						<input type="text" class="widget-title sl-input-large" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>">
						<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
					</div>
				</div>

				<br>

				<div class="sl-settings checkbox-toggle">
					<div class="sl-label">
						<label><?php _e( 'Image', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}image" ); ?>
					</div>
				</div>
				<div class="sl-sub-settings sl-image-size">
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
						<label><?php _e( 'Description', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}desc" ); ?>
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
							<input type="number" class="amount" min="2" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
							<span class="add-on"><?php _e( 'Categories', '7listings' ); ?></span>
						</span>
					</div>
				</div>

				<br>

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Sorting', '7listings' ); ?></label>
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

				<div class="sl-settings homepage columns layout">
					<div class="sl-label">
						<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select columns layout', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php
						$value     = sl_setting( "{$prefix}columns" );
						$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols', 5 => 'five-cols', 6 => 'six-cols' );
						for ( $cols = 2; $cols <= 6; $cols ++ )
						{
							$checked = checked( $value, $cols, false );
							$id      = uniqid();
							printf( '
									<span>
										<input type="radio" name="%s[%s]" id="%s" value="%s"%s>
										<label class="%s%s" title="%s" for="%s">&nbsp;</label>
									</span>',
								THEME_SETTINGS,
								"{$prefix}columns",
								$id,
								$cols,
								$checked,
								$cols_name[$cols],
								$checked ? ' active' : '',
								$cols . __( ' Columns', '7listings' ),
								$id
							);
						}
						?>
					</div>
				</div>

			</div>
		</div>
	</section>
</div>
