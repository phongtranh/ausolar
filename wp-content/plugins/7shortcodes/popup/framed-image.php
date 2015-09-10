<?php Sls_Helper::modal_header( $shortcode, __( 'Framed Image', '7listings' ) ); ?>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Image URL', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.url" type="text" id="framed-image-url">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Title', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.title" type="text">
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Type', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.type" ng-init="sls_<?php echo $shortcode; ?>.type = 'rounded'">
				<?php
				Sl_Form::options( '', array(
					'rounded'   => __( 'Rounded', '7listings' ),
					'circle'    => __( 'Circle', '7listings' ),
					'thumbnail' => __( 'Thumbnail', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Width', '7listings' ); ?></label>
		<div class="controls">
			<span class="input-append supplementary-input">
				<input ng-model="sls_<?php echo $shortcode; ?>.width" type="number" class="amount">
				<span class="add-on"><?php _e( 'px', '7listings' ); ?></span>
			</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Height', '7listings' ); ?></label>
		<div class="controls">
			<span class="input-append supplementary-input">
				<input ng-model="sls_<?php echo $shortcode; ?>.height" type="number" class="amount">
				<span class="add-on"><?php _e( 'px', '7listings' ); ?></span>
			</span>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Align', '7listings' ); ?></label>
		<div class="controls">
			<div class="btn-group">
				<label class="btn btn-default">
					<i class="icon-align-left"></i>
					<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="left">
				</label>
				<label class="btn btn-default">
					<i class="icon-align-center"></i>
					<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="center">
				</label>
				<label class="btn btn-default">
					<i class="icon-align-right"></i>
					<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="right">
				</label>
			</div>
		</div>
	</div>

	<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.output = 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode";
				$text .= Sls_Helper::shortcode_atts( $shortcode, array(
					'type',
					'url',
					'title',
					'width',
					'height',
					'align',
				) );
				$text .= ']';
				echo $text;
				?></pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
