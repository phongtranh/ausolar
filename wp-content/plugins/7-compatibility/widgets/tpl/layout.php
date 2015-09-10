<p class="listing-type layout">
	<label class="input-label"><?php _e( 'Layout', '7listings' ); ?></label>

	<?php $layout = $instance['display']; ?>

	<span>
		<?php $checked = checked( $layout, 'list', false ); ?>
		<input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>-list" value="list" <?php echo $checked; ?>>
		<label class="list<?php echo $checked ? ' active' : ''; ?>" for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>-list">&nbsp;</label>
	</span>
	<span>
		<?php $checked = checked( $layout, 'grid', false ); ?>
		<input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>-grid" value="grid" <?php echo $checked; ?>>
		<label class="grid<?php echo $checked ? ' active' : ''; ?>" for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>-grid">&nbsp;</label>
	</span>
</p>
<p class="columns layout">
	<label class="input-label"><?php _e( 'Columns', '7listings' ); ?></label>
	<?php
	$value     = $instance['columns'];
	$cols_names = array(
		2 => 'two-cols',
		3 => 'three-cols',
		4 => 'four-cols',
		//5 => 'five-cols',
		//6 => 'six-cols',
	);
	foreach ( $cols_names as $cols => $cols_name )
	{
		$checked = checked( $value, $cols, false );
		printf( '
			<span>
				<input type="radio" name="%s" id="%s-%s" value="%s"%s>
				<label class="%s %s" for="%s-%s">&nbsp;</label>
			</span>',
			$this->get_field_name( 'columns' ),
			$this->get_field_id( 'columns' ),
			$cols,
			$cols,
			$checked,
			$cols_name,
			$checked ? ' active' : '',
			$this->get_field_id( 'columns' ),
			$cols
		);
	}
	?>
</p>
