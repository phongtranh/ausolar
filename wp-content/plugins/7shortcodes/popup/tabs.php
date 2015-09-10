<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Tabs', '7listings' ), 'Block' ); ?>

<div class="form-horizontal" ng-init="sls_<?php echo $shortcode; ?>.output='shortcode'">

	<div ng-repeat="block in blocks" class="tab-content-edit">
		<?php /*?><strong><?php _e( 'Tab', '7listings' ); ?></strong><?php */ ?>
		<i class="icon-folder-close-alt"></i>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Title', '7listings' ); ?></label>
			<div class="controls">
				<input ng-model="block.title" type="text">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Content', '7listings' ); ?></label>
			<div class="controls">
				<textarea ng-model="block.content" class="widget-text-content"></textarea>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<a ng-click="add()" href="#" class="btn"><?php _e( 'Add Tab', '7listings' ); ?></a>
		</div>
	</div>
	<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.ouput = 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode">[tabs]<div ng-repeat="block in blocks">[tab title="{{block.title}}"]{{block.content}}[/tab]</div>[/tabs]</pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode, false, 'text' ); ?>
