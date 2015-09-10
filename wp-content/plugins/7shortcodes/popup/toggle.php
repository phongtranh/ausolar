<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Toggle', '7listings' ), 'Block' ); ?>

<div class="form-horizontal" ng-init="sls_<?php echo $shortcode; ?>.output='shortcode'">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Title', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="title" type="text">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Content', '7listings' ); ?></label>
		<div class="controls">
			<textarea ng-model="content" class="widget-text-content"></textarea>
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
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode icon={{icon}} title={{title}}]{{content}}[/$shortcode]";
				echo $text;
				?></pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode, false, 'text' ); ?>
