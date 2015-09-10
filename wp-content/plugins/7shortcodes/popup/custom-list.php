<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Custom List', '7listings' ) ); ?>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Icon', '7listings' ); ?></label>
		<div class="controls">
			<select id="sls-<?php echo $shortcode; ?>-icon">
				<?php
				Sl_Form::options( '', array(
					'arrow-right'        => __( 'Arrow', '7listings' ),
					'asterisk'           => __( 'Asterisk', '7listings' ),
					'caret'              => __( 'Caret', '7listings' ),
					'circle-play'        => __( 'Circle Play', '7listings' ),
					'circle'             => __( 'Circle', '7listings' ),
					'circle-blank'       => __( 'Circle Blank', '7listings' ),
					'circle-arrow-right' => __( 'Circle Arrow', '7listings' ),
					'check'              => __( 'Check', '7listings' ),
					'check-sign'         => __( 'Check Sign', '7listings' ),
					'chevron'            => __( 'Chevron', '7listings' ),
					'hand-right'         => __( 'Hand', '7listings' ),
					'plus'               => __( 'Plus', '7listings' ),
					'plus-sign'          => __( 'Plus Sign', '7listings' ),
					'ok'                 => __( 'OK', '7listings' ),
					'ok-circle'          => __( 'OK Circle', '7listings' ),
					'ok-sign'            => __( 'OK Sign', '7listings' ),
					'angle'              => __( 'Angle', '7listings' ),
					'double-angle'       => __( 'Double Angle', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Icon color', '7listings' ); ?></label>
		<div class="controls">
			<?php Sls_Helper::color_schemes( "sls_{$shortcode}.icon_color" ); ?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Text color', '7listings' ); ?></label>
		<div class="controls">
			<input id="sls-<?php echo $shortcode; ?>-text-color" type="text" class="color">
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode, true ); ?>
