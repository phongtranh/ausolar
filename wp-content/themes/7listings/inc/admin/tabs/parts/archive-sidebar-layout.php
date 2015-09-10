<?php $prefix = "{$post_type}_archive_"; ?>
<div class="sl-settings listing-type layout">
	<div class="sl-label">
		<label><?php _e( 'Layout', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select post layout design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $layout = sl_setting( "{$prefix}layout" ); ?>
		<span>
			<?php $checked = checked( $layout, 'list', false ); ?>
			<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-list" value="list" <?php echo $checked; ?>>
			<label class="list<?php echo $checked ? ' active' : ''; ?>" title="<?php _e( 'List', '7listings' ); ?>" for="<?php echo $post_type; ?>-archive-layout-list">&nbsp;</label>
		</span>
		<span>
			<?php $checked = checked( $layout, 'grid', false ); ?>
			<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-grid" value="grid" <?php echo $checked; ?>>
			<label class="grid<?php echo $checked ? ' active' : ''; ?>" title="<?php _e( 'Columns', '7listings' ); ?>" for="<?php echo $post_type; ?>-archive-layout-grid">&nbsp;</label>
		</span>
		<?php if ( 'post' == $post_type ) : ?>
			<span>
				<?php $checked = checked( $layout, 'magazine', false ); ?>
				<input type="radio" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}layout"; ?>]" id="<?php echo $post_type; ?>-archive-layout-magazine" value="magazine" <?php echo $checked; ?>>
				<label class="magazine<?php echo $checked ? ' active' : ''; ?>" title="<?php _e( 'Magazine', '7listings' ); ?>" for="<?php echo $post_type; ?>-archive-layout-magazine">&nbsp;</label>
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
						<label class="%5$s%6$s" title="%3$s %7$s" for="%2$s-archive-columns-%3$s">&nbsp;</label>
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

<br>

<div class="sl-settings sidebar layout">
	<div class="sl-label">
		<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sidebar layout<br>for archive pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::sidebar_layout( "{$post_type}_archive_sidebar_layout" ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings sub-input">
		<div class="sl-label">
			<label><?php _e( 'Widget Area', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select widget area<br>for sidebar', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::sidebar_select( "{$post_type}_archive_sidebar" ); ?>
		</div>
	</div>
</div>