<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Output', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.output" ng-init="sls_<?php echo $shortcode; ?>.output = ''">
				<option value=""><?php _e( 'CSS', '7listings' ); ?></option>
				<option value="shortcode"><?php _e( 'Shortcode', '7listings' ); ?></option>
			</select>
		</div>
	</div>
</div>