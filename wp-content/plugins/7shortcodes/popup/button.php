<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Button', '7listings' ), 'Icon' ); ?>

<div class="form-horizontal">
<div class="control-group">
	<label class="control-label"><?php _e( 'Text', '7listings' ); ?></label>
	<div class="controls">
		<input ng-model="sls_<?php echo $shortcode; ?>.text" type="text">
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php _e( 'Link', '7listings' ); ?></label>
	<div class="controls">
		<input ng-model="sls_<?php echo $shortcode; ?>.link" type="text">
		<span class="wp_themeSkin"><span class="mce_link mceIcon"></span></span>
	</div>
</div>

<hr class="light">

<div class="control-group">
	<label class="control-label"><?php _e( 'Size', '7listings' ); ?></label>
	<div class="controls">
		<select ng-model="sls_<?php echo $shortcode; ?>.size">
			<?php
			Sl_Form::options( '', array(
				'small' => __( 'Small', '7listings' ),
				''      => __( 'Default', '7listings' ),
				'large' => __( 'Large', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>
<div class="control-group">
	<label class="control-label"><?php _e( 'Color', '7listings' ); ?></label>
	<div class="controls">
		<?php Sls_Helper::color_schemes( "sls_{$shortcode}.color" ); ?>
		<a target="_blank" href="<?php echo admin_url( 'themes.php?page=design' ); ?>" class="edit-icon-md" title="Edit default button design">&nbsp;</a>
	</div>
</div>

<hr class="light">

<div class="control-group button-inputs" ng-init="sls_<?php echo $shortcode; ?>.icon_position = ''">
	<label class="control-label"><?php _e( 'Icon Position', '7listings' ); ?></label>
	<div class="controls">
		<label class="sl-icon-button none active" title="<?php _e( 'No icon', '7listings' ); ?>">
			<input class="hidden" ng-model="sls_<?php echo $shortcode; ?>.icon_position" type="radio" name="sls_<?php echo $shortcode; ?>.icon_position" value="">
		</label>
		<label class="sl-icon-button before" title="<?php _e( 'Icon before text', '7listings' ); ?>">
			<input class="hidden" ng-model="sls_<?php echo $shortcode; ?>.icon_position" type="radio" name="sls_<?php echo $shortcode; ?>.icon_position" value="before">
		</label>
		<label class="sl-icon-button after" title="<?php _e( 'Icon after text', '7listings' ); ?>">
			<input class="hidden" ng-model="sls_<?php echo $shortcode; ?>.icon_position" type="radio" name="sls_<?php echo $shortcode; ?>.icon_position" value="after">
		</label>
	</div>
</div>

<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.icon_position != ''">
	<label class="control-label"><?php _e( 'Icon', '7listings' ); ?></label>
	<div class="controls">
		<input ng-model="search" type="text" placeholder="<?php _e( 'Search Icon', '7listings' ); ?>">
		<div class="icons-select">
			<label class="icon-single" ng-repeat="icon in icons | filter: search">
				<i class="icon-{{icon.value}}"></i>
				<input ng-model="sls_<?php echo $shortcode; ?>.icon" type="radio" name="sls_<?php echo $shortcode; ?>.icon" value="{{icon.value}}" class="hidden">
			</label>
		</div>
	</div>
</div>

<label class="advanced"><input ng-model="sls_<?php echo $shortcode; ?>.advanced" type="checkbox" class="hidden"> <?php _e( 'Advanced Settings', '7listings' ); ?>
</label>

<div ng-show="sls_<?php echo $shortcode; ?>.advanced" class="advanced-options">
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
	<div class="control-group">
		<label class="control-label"><?php _e( 'Full Width', '7listings' ); ?></label>
		<div class="controls">
			<?php Sls_Helper::checkbox_angular( "sls_{$shortcode}.full", "sls_{$shortcode}_full", 0 ); ?>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Background Color', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.background" type="text" colorpicker>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Text Color', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.text_color" type="text" colorpicker>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'Link Target', '7listings' ); ?></label>
		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.target">
				<?php
				Sl_Form::options( '', array(
					''       => __( 'Default', '7listings' ),
					'_blank' => __( 'New window/tab', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Nofollow', '7listings' ); ?></label>
		<div class="controls">
			<?php Sls_Helper::checkbox_angular( "sls_{$shortcode}.nofollow", "sls_{$shortcode}_nofollow", 0 ); ?>
		</div>
	</div>

	<hr class="light">

	<div class="control-group">
		<label class="control-label"><?php _e( 'ID', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.id" type="text">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?php _e( 'Class', '7listings' ); ?></label>
		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.class" type="text">
		</div>
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
					'size',
					'color',
					'icon',
					'icon_position',
					'link',
					'align',
					'full',
					'background',
					'text_color',
					'target',
					'nofollow',
					'id',
					'class',
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
			$prefix  = "sls_$shortcode";
			$params  = array(
				'size',
				'color',
				'icon',
				'icon_position',
				'align',
				'class',
			);
			$classes = array( 'button' );
			foreach ( $params as $param )
			{
				$classes[] = sprintf( '{{%1$s.%2$s}}', $prefix, $param );
			}
			$classes = implode( ' ', $classes );
			$classes .= "{{sls_$shortcode.full && ' full' || ''}}";

			$html = sprintf( '<a href="{{%s.link}}" class="%s"', $prefix, $classes );
			$html .= sprintf( ' id="{{%s.id}}"', $prefix );
			$html .= sprintf( ' rel="{{%s.nofollow && \'nofollow\' || \'\'}}"', $prefix );
			$html .= sprintf( ' target="{{%s.target}}"', $prefix );
			$html .= sprintf( ' style="background: {{%1$s.background}};color: {{%1$s.text_color}};"', $prefix );
			$html .= '>';
			$html .= sprintf( '<i class="icon-{{%1$s.icon_position == \'before\' && %1$s.icon || \'\'}}"></i>', $prefix );
			$html .= sprintf( '{{%s.text}}', $prefix );
			$html .= sprintf( '<i class="icon-{{%1$s.icon_position == \'after\' && %1$s.icon || \'\'}}"></i>', $prefix );
			$html .= '</a>';

			echo $html;
			?>
		</div>
	</div>
</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
