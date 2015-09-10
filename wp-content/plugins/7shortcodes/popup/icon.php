<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Icon', '7listings' ), 'IconShortcode' ); ?>

<div class="form-horizontal">
<div class="control-group">
	<label class="control-label"><?php _e( 'Icon', '7listings' ); ?></label>
	<div class="controls">
		<input ng-model="search" type="text" placeholder="<?php _e( 'Search Icon', '7listings' ); ?>">
		<div class="icons-select">
			<label class="icon-single" ng-repeat="i in icons | filter: search">
				<i class="icon-{{i.value}}"></i>
				<input ng-model="sls_<?php echo $shortcode; ?>.icon" type="radio" name="sls_<?php echo $shortcode; ?>.icon" value="{{i.value}}" class="hidden">
			</label>
		</div>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php _e( 'Size', '7listings' ); ?></label>
	<div class="controls">
		<select ng-model="sls_<?php echo $shortcode; ?>.size">
			<?php
			Sl_Form::options( '', array(
				'half'  => __( '1/2', '7listings' ),
				''      => __( 'Default', '7listings' ),
				'large' => __( 'Large', '7listings' ),
				'2x'    => __( '2x', '7listings' ),
				'3x'    => __( '3x', '7listings' ),
				'4x'    => __( '4x', '7listings' ),
				'5x'    => __( '5x', '7listings' ),
				'6x'    => __( '6x', '7listings' ),
				'7x'    => __( '7x', '7listings' ),
				'8x'    => __( '8x', '7listings' ),
				'9x'    => __( '9x', '7listings' ),
				'10x'   => __( '10x', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php _e( 'Color', '7listings' ); ?></label>
	<div class="controls">
		<?php Sls_Helper::color_schemes( "sls_{$shortcode}.color" ); ?>
	</div>
</div>

<label class="advanced"><input ng-model="sls_<?php echo $shortcode; ?>.advanced" type="checkbox" class="hidden"> <?php _e( 'Advanced Settings', '7listings' ); ?>
</label>

<div ng-show="sls_<?php echo $shortcode; ?>.advanced" class="advanced-options">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Transform', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.rotate">
				<?php
				Sl_Form::options( '', array(
					''                => __( 'None', '7listings' ),
					'rotate-90'       => __( 'Rotate 90', '7listings' ),
					'rotate-180'      => __( 'Rotate 180', '7listings' ),
					'rotate-270'      => __( 'Rotate 270', '7listings' ),
					'flip-horizontal' => __( 'Flip Horizontal', '7listings' ),
					'flip-vertical'   => __( 'Flip Vertical', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Align', '7listings' ); ?></label>
		<div class="controls">
			<div class="btn-group">
				<label class="btn btn-default">
					<i class="icon-align-left"></i>
					<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="left">
				</label>
				<label class="btn btn-default">
					<i class="icon-align-right"></i>
					<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="right">
				</label>
			</div>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Rotating Animation', '7listings' ); ?></label>
		<div class="controls">
			<?php Sls_Helper::checkbox_angular( "sls_$shortcode.animation", "sls-$shortcode-animation" ); ?>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?php _e( 'Effect', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.effect">
				<?php
				Sl_Form::options( '', array(
					''       => __( 'None', '7listings' ),
					'shadow' => __( 'Shadow', '7listings' ),
					'emboss' => __( 'Emboss', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Background Icon', '7listings' ); ?></label>
		<div class="controls">
			<?php Sls_Helper::checkbox_angular( 'stack', "sls-$shortcode-stack" ); ?>
		</div>
	</div>

	<div ng-show="stack">
		<div class="control-group stacked-icons">
			<label class="control-label"><?php _e( 'Icon', '7listings' ); ?></label>
			<div class="controls">
				<div class="icons-select">
					<label class="icon-single" ng-repeat="i in stack_icons">
						<i class="icon-{{i.value}}"></i>
						<input ng-model="$parent.stack_icon" type="radio" name="sls_<?php echo $shortcode; ?>.icon" value="{{i.value}}" class="hidden">
					</label>
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Size', '7listings' ); ?></label>
			<div class="controls">
				<select ng-model="stack_size">
					<?php
					Sl_Form::options( '', array(
						''      => __( 'Default', '7listings' ),
						'large' => __( 'Large', '7listings' ),
						'2x'    => __( '2x', '7listings' ),
						'3x'    => __( '3x', '7listings' ),
						'4x'    => __( '4x', '7listings' ),
						'5x'    => __( '5x', '7listings' ),
						'6x'    => __( '6x', '7listings' ),
						'7x'    => __( '7x', '7listings' ),
						'8x'    => __( '8x', '7listings' ),
						'9x'    => __( '9x', '7listings' ),
						'10x'   => __( '10x', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Color', '7listings' ); ?></label>
			<div class="controls">
				<?php Sls_Helper::color_schemes( 'stack_color' ); ?>
			</div>
		</div>
	</div>

</div>

<div class="control-group ng-hide" ng-init="sls_<?php echo $shortcode; ?>.output = 'shortcode'">
	<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode";
				$text .= Sls_Helper::shortcode_atts( $shortcode, array(
					'icon',
					'size',
					'color',
					'rotate',
					'animation',
					'border',
					'align',
					'effect',
				) );
				$text .= Sls_Helper::shortcode_atts( '', array(
					'stack',
					'stack_icon',
					'stack_size',
					'stack_color',
				) );
				$text .= ']';
				echo $text;
				?></pre>
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<div class="sls-css">
			<?php
			$prefix  = "sls_$shortcode";
			$params  = array(
				'icon',
				'size',
				'rotate',
				'effect',
			);
			$classes = array();
			foreach ( $params as $param )
			{
				$classes[] = sprintf( '{{%1$s.%2$s && ( \' icon-\' + %1$s.%2$s ) || \'\'}}', $prefix, $param );
			}

			$param     = 'align';
			$classes[] = sprintf( '{{%1$s.%2$s && ( \' pull-\' + %1$s.%2$s ) || \'\'}}', $prefix, $param );
			$param     = 'color';
			$classes[] = sprintf( '{{%1$s.%2$s && ( \' \' + %1$s.%2$s ) || \'\'}}', $prefix, $param );

			$classes[] = sprintf( '{{%s.animation && \' icon-spin\' || \'\'}}', $prefix );
			$classes[] = sprintf( '{{%s.border && \' icon-border\' || \'\'}}', $prefix );

			$icon_in_stack = sprintf( '<i class="%s"></i>', implode( '', $classes ) );

			$classes[] = '{{stack && \' ng-hide\' || \'\'}}';
			$icon      = sprintf( '<i class="%s"></i>', implode( '', $classes ) );

			$classes   = array( 'icon-stack-base' );
			$classes[] = '{{stack_size && ( \' icon-\' + stack_size ) || \'\'}}';
			$classes[] = '{{stack_icon && ( \' icon-\' + stack_icon ) || \'\'}}';
			$classes[] = '{{stack_color && ( \' \' + stack_color ) || \'\'}}';

			$stack = '<span ng-show="stack" class="icon-stack{{stack_size && ( \' is-\' + stack_size ) || \'\'}}">';
			$stack .= sprintf( '<i class="%s"></i>', implode( '', $classes ) );
			$stack .= $icon_in_stack;
			$stack .= '</span>';

			$html = $icon . $stack;
			echo $html;
			?>
		</div>
	</div>
</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
