<div class="sl-settings listing-type layout">
	<div class="sl-label">
		<label><?php _e( 'Layout', '7listings' ); ?></label>
	</div>

	<?php
	$layout = sl_setting( "{$prefix}layout" );
	$id     = uniqid();
	?>
	<div class="sl-input">
		<span>
			<?php $checked = checked( $layout, 'list', false ); ?>
			<input type="radio" name="<?php echo THEME_SETTINGS . "[{$prefix}layout]"; ?>" id="<?php echo $id; ?>-list" value="list" <?php echo $checked; ?>>
			<label class="list<?php echo $checked ? ' active' : ''; ?>" title="<?php _e( 'List', '7listings' ); ?>" for="<?php echo $id; ?>-list">&nbsp;</label>
		</span>
		<span>
			<?php $checked = checked( $layout, 'grid', false ); ?>
			<input type="radio" name="<?php echo THEME_SETTINGS . "[{$prefix}layout]"; ?>" id="<?php echo $id; ?>-grid" value="grid" <?php echo $checked; ?>>
			<label class="grid<?php echo $checked ? ' active' : ''; ?>" title="<?php _e( 'Columns', '7listings' ); ?>" for="<?php echo $id; ?>-grid">&nbsp;</label>
		</span>
	</div>
</div>
<div class="sl-sub-settings homepage columns layout">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select columns layout', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php
			$value     = sl_setting( "{$prefix}columns" );
			$cols_name = array(
				2 => 'two-cols',
				3 => 'three-cols',
				4 => 'four-cols',
				5 => 'five-cols',
				6 => 'six-cols',
			);
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
