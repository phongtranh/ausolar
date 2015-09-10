<?php Sls_Helper::modal_header( $shortcode, __( 'Slider - Slideshow', '7listings' ) ); ?>

	<div class="form-horizontal">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Select Slideshow', '7listings' ); ?></label>
			<div class="controls">
				<select ng-model="sls_<?php echo $shortcode; ?>.id">
					<option value=""><?php _e( 'Select', '7listings' ); ?></option>
					<?php
					$slideshows = get_posts( array(
						'post_type'      => 'slideshow',
						'posts_per_page' => - 1,
					) );
					$slideshows = array_combine( wp_list_pluck( $slideshows, 'ID' ), wp_list_pluck( $slideshows, 'post_title' ) );
					Sl_Form::options( '', $slideshows );
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
