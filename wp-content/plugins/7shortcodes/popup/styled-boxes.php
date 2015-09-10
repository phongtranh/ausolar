<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Styled Boxes', '7listings' ) ); ?>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Style', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.style" ng-init="sls_<?php echo $shortcode; ?>.style = ''">
				<?php
				Sl_Form::options( '', array(
					''      => __( 'Color', '7listings' ),
					'alert' => __( 'Alert', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>

	<hr class="light">

	<div ng-show="sls_<?php echo $shortcode; ?>.style == 'alert'">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Title', '7listings' ); ?></label>
			<div class="controls">
				<input ng-model="sls_<?php echo $shortcode; ?>.title" type="text">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Text', '7listings' ); ?></label>
			<div class="controls">
				<textarea ng-model="sls_<?php echo $shortcode; ?>.text" class="widget-text-content"></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Type', '7listings' ); ?></label>
			<div class="controls">
				<select ng-model="sls_<?php echo $shortcode; ?>.type">
					<?php
					Sl_Form::options( '', array(
						''        => __( 'Alert', '7listings' ),
						'success' => __( 'Success', '7listings' ),
						'error'   => __( 'Error', '7listings' ),
						'info'    => __( 'Info', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
	</div>
	<div ng-show="sls_<?php echo $shortcode; ?>.style == ''">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Text', '7listings' ); ?></label>
			<div class="controls">
				<textarea ng-model="sls_<?php echo $shortcode; ?>.text" class="widget-text-content"></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Color', '7listings' ); ?></label>
			<div class="controls">
				<?php Sls_Helper::color_schemes( "sls_{$shortcode}.color" ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Rounded', '7listings' ); ?></label>
			<div class="controls">
				<?php Sls_Helper::checkbox_angular( "sls_{$shortcode}.rounded", uniqid() ); ?>
			</div>
		</div>
	</div>
</div>

<hr class="light">

<?php include SL_SHORTCODES_DIR . 'tpl/output.php'; ?>

<div class="form-horizontal" ng-show="sls_<?php echo $shortcode; ?>.output == 'shortcode'">
	<div class="control-group">
		<div class="controls">
			<pre class="sls-shortcode"><?php
				$prefix = "sls_$shortcode";
				$text   = '[styled_box';
				$text .= Sls_Helper::shortcode_atts( $shortcode, array( 'style' ) );
				$text .= sprintf( "{{( %1\$s.style == '' && %1\$s.color ) && ( ' color=\"' + %1\$s.color + '\"' ) || ''}}", $prefix );
				$text .= sprintf( "{{( %1\$s.style == '' && %1\$s.rounded ) && ( ' rounded=\"' + %1\$s.rounded + '\"' ) || ''}}", $prefix );
				$text .= sprintf( "{{( %1\$s.style == 'alert' && %1\$s.title ) && ( ' title=\"' + %1\$s.title + '\"' ) || ''}}", $prefix );
				$text .= sprintf( "{{( %1\$s.style == 'alert' && %1\$s.type ) && ( ' type=\"' + %1\$s.type + '\"' ) || ''}}", $prefix );
				$text .= "]{{sls_$shortcode.text}}[/styled_box]";
				echo $text;
				?></pre>
		</div>
	</div>
</div>

<div class="form-horizontal" ng-show="sls_<?php echo $shortcode; ?>.output == ''">
	<div class="control-group">
		<div class="controls">
			<div class="sls-css" ng-show="sls_<?php echo $shortcode; ?>.style == 'alert'">
				<?php echo "<div class='alert{{sls_$shortcode.type && (\" alert-\" + sls_$shortcode.type) || \"\"}}'><h4>{{sls_$shortcode.title}}</h4>{{sls_$shortcode.text}}</div>&nbsp;"; ?>
			</div>
			<div class="sls-css" ng-show="sls_<?php echo $shortcode; ?>.style == ''">
				<?php echo "<div class='color-box{{sls_$shortcode.color && (\" \" + sls_$shortcode.color) || \"\"}}{{sls_$shortcode.rounded && \" rounded\" || \"\"}}'>{{sls_$shortcode.text}}</div>&nbsp;"; ?>
			</div>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
