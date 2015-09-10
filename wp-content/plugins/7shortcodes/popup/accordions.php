<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Accordions', '7listings' ), 'Block' ); ?>

<div class="form-horizontal" ng-init="sls_<?php echo $shortcode; ?>.output='shortcode'">

	<div ng-repeat="block in blocks" class="accordion-content-edit">
		<i class="icon-reorder"></i>
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
		<div class="controls">
			<a ng-click="add()" href="#" class="btn"><?php _e( 'Add Accordion', '7listings' ); ?></a>
		</div>
	</div>

	<hr class="light">

	<div class="control-group toggle-icons">
		<label class="control-label"><?php _e( 'Icon', '7listings' ); ?></label>
		<div class="controls">
			<div class="icons-select">
				<label class="icon-single" ng-repeat="i in icons">
					<i class="icon-{{i.value}}"></i>
					<input ng-model="$parent.icon" type="radio" name="sls_<?php echo $shortcode; ?>_icon" value="{{i.value}}" class="hidden">
				</label>
			</div>
		</div>
	</div>

	<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.ouput = 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode">[accordions icon="{{icon}}"]<div ng-repeat="block in blocks">[accordion title="{{block.title}}"]{{block.content}}[/accordion]</div>[/accordions]</pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode, false, 'text' ); ?>
