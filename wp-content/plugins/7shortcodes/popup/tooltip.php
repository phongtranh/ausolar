<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Tooltip', '7listings' ), 'Icon' ); ?>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Text', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.text" type="text">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Tooltip content', '7listings' ); ?></label>
		<div class="controls">
			<textarea ng-model="sls_<?php echo $shortcode; ?>.content"></textarea>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Type', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.type">
				<?php
				Sl_Form::options( '', array(
					''        => __( 'None', '7listings' ),
					'info'    => __( 'Info', '7listings' ),
					'warning' => __( 'Warning', '7listings' ),
					'note'    => __( 'Note', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Placement', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.placement">
				<?php
				Sl_Form::options( '', array(
					''       => __( 'Top', '7listings' ),
					'bottom' => __( 'Bottom', '7listings' ),
					'left'   => __( 'Left', '7listings' ),
					'right'  => __( 'Right', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php _e( 'Output', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.output" ng-init="sls_<?php echo $shortcode; ?>.output = ''">
				<option value=""><?php _e( 'CSS', '7listings' ); ?></option>
				<option value="shortcode"><?php _e( 'Shortcode', '7listings' ); ?></option>
			</select>
		</div>
	</div>

	<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.output == 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode";
				$text .= Sls_Helper::shortcode_atts( $shortcode, array(
					'content',
					'type',
					'placement',
				) );
				$text .= ']';
				$text .= sprintf( '{{sls_%s.text}}', $shortcode );
				$text .= "[/$shortcode]";
				echo $text;
				?></pre>
		</div>
	</div>
	<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.output == ''">
		<div class="controls">
			<div class="sls-css">
				<?php
				$prefix = "sls_$shortcode";
				$html   = sprintf( '<a href="#" data-toggle="tooltip" data-html="true" class="{{%1$s.type}}" data-placement="{{%1$s.placement}}" title="{{%1$s.content}}">{{%1$s.text}}</a>', $prefix );
				echo $html;
				?>
			</div>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
