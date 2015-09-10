<?php Sls_Helper::modal_header( $shortcode, __( 'Social Links', '7listings' ) ); ?>

<div class="form-horizontal">

	<div class="control-group">
		<?php Sls_Helper::checkbox_angular( "sls_$shortcode.counter", "sls-$shortcode-counter", true ); ?>
		<label class="control-label"><?php _e( 'Counter', '7listings' ) ?></label>
	</div>

	<label class="advanced"><input ng-model="sls_<?php echo $shortcode; ?>.advanced" type="checkbox" class="hidden"> <?php _e( 'Advanced Settings', '7listings' ); ?>
	</label>

	<div ng-show="sls_<?php echo $shortcode; ?>.advanced" class="advanced-options">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Size', '7listings' ); ?></label>
			<div class="controls">
				<select ng-model="sls_<?php echo $shortcode; ?>.size" ng-init="sls_<?php echo $shortcode; ?>.size = ''">
					<option value="small"><?php _e( 'Small', '7listings' ); ?></option>
					<option value=""><?php _e( 'Medium', '7listings' ); ?></option>
					<option value="large"><?php _e( 'Large', '7listings' ); ?></option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Additional CSS Class', '7listings' ); ?></label>
			<div class="controls">
				<input ng-model="sls_<?php echo $shortcode; ?>.class" type="text">
			</div>
		</div>

		<hr class="light">
		<h4>
			<?php _e( 'Custom social profile links', '7listings' ); ?>
			<?php echo do_shortcode( '[tooltip content="' . __( 'If no custom profile links entered, links will be get from setttings page 7Listings > Social Media.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
		</h4>

		<?php
		$text_fields = array(
			// Social networks supported
			'facebook'   => __( 'Facebook', '7listings' ),
			'googleplus' => __( 'Google+', '7listings' ),
			'twitter'    => __( 'Twitter', '7listings' ),
			'pinterest'  => __( 'Pinterest', '7listings' ),
			'linkedin'   => __( 'LinkedIn', '7listings' ),
			'instagram'  => __( 'Instagram', '7listings' ),
			'rss'        => __( 'RSS', '7listings' ),
		);
		foreach ( $text_fields as $key => $label )
		{
			?>
			<div class="control-group">
				<label class="control-label"><?php echo $label; ?></label>
				<div class="controls">
					<input type="text" ng-model="sls_<?php echo $shortcode; ?>.<?php echo $key; ?>">
				</div>
			</div>
		<?php
		}
		?>
	</div>
	<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.output = 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode";
				$text .= Sls_Helper::shortcode_atts( $shortcode, array(
					'counter',
					'size',
					'class',
					'facebook',
					'twitter',
					'googleplus',
					'pinterest',
					'linkedin',
					'instagram',
					'rss',
				) );
				$text .= ']';
				echo $text;
				?></pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
