<div class="control-group listing-type layout" ng-init="sls_<?php echo $shortcode; ?>.display = 'list'">
	<label class="control-label"><?php _e( 'Layout', '7listings' ); ?></label>
	<div class="controls">
		<span>
			<input ng-model="sls_<?php echo $shortcode; ?>.display" type="radio" value="list" name="sls_<?php echo $shortcode; ?>.display" id="sls-<?php echo $shortcode; ?>-display-list">
			<label class="list" for="sls-<?php echo $shortcode; ?>-display-list">&nbsp;</label>
		</span>
		<span>
			<input ng-model="sls_<?php echo $shortcode; ?>.display" type="radio" value="grid" name="sls_<?php echo $shortcode; ?>.display" id="sls-<?php echo $shortcode; ?>-display-grid">
			<label class="grid" for="sls-<?php echo $shortcode; ?>-display-grid">&nbsp;</label>
		</span>
	</div>
</div>
<div class="control-group columns layout" ng-show="sls_<?php echo $shortcode; ?>.display == 'grid'">
	<label class="control-label"><?php _e( 'Columns', '7listings' ); ?></label>
	<div class="controls">
		<?php
		$cols_names = array(
			2 => 'two-cols',
			3 => 'three-cols',
			4 => 'four-cols',
			5 => 'five-cols',
			6 => 'six-cols',
		);
		foreach ( $cols_names as $cols => $cols_name )
		{
			printf( '
				<span>
					<input ng-model="sls_%1$s.columns" type="radio" name="sls_%1$s.columns" id="sls-%1$s-columns-%2$s" value="%2$s">
					<label class="%3$s" for="sls-%1$s-columns-%2$s">&nbsp;</label>
				</span>',
				$shortcode,
				$cols,
				$cols_name
			);
		}
		?>
	</div>
</div>
