<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Widget Area', '7listings' ) ); ?>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Widget area', '7listings' ); ?></label>
			<div class="controls">
				<select ng-model="sls_<?php echo $shortcode; ?>.id">
					<option value=""><?php _e( 'Select widget area', '7listings' ); ?></option>
					<?php
					global $wp_registered_sidebars;
					$options = array();
					foreach ( $wp_registered_sidebars as $registered_sidebar )
					{
						$options[$registered_sidebar['id']] = $registered_sidebar['name'];
					}
					asort( $options );
					Sl_Form::options( '', $options );
					?>
				</select>
			</div>
		</div>

		<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.output = 'shortcode'">
			<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode id=\"{{sls_$shortcode.id}}\"]";
				echo $text;
				?></pre>
			</div>
		</div>
	</div>

<?php Sls_Helper::modal_footer( $shortcode ); ?>
