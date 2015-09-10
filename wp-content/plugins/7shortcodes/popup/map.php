<?php Sls_Helper::modal_header( $shortcode, __( 'Insert/Edit Map', '7listings' ), 'Map' ); ?>

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Map Center ', '7listings' ); ?></label>

		<div class="controls">
			<label>
				<input ng-model="sls_<?php echo $shortcode; ?>.type" name="sls_<?php echo $shortcode; ?>_type" type="radio" value="address" ng-init="sls_<?php echo $shortcode; ?>.type = 'address'">
				<?php _e( 'Address', '7listings' ); ?>
			</label>
			<label>
				<input ng-model="sls_<?php echo $shortcode; ?>.type" name="sls_<?php echo $shortcode; ?>_type" type="radio" value="latlng">
				<?php _e( 'Coordinates', '7listings' ); ?>
			</label>
		</div>
	</div>

	<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'address'">
		<label class="control-label"><?php _e( 'Address', '7listings' ); ?></label>

		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.address" id="sls-map-address" type="text">
		</div>
	</div>
	<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'latlng'">
		<label class="control-label"><?php _e( 'Latitude', '7listings' ); ?></label>

		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.latitude" id="sls-map-latitude" type="text">
		</div>
	</div>
	<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'latlng'">
		<label class="control-label"><?php _e( 'Longtitude', '7listings' ); ?></label>

		<div class="controls">
			<input ng-model="sls_<?php echo $shortcode; ?>.longtitude" id="sls-map-longtitude" type="text">
		</div>
	</div>
	<hr class="light">
	<div class="control-group">
		<label class="control-label"><?php _e( 'Map type', '7listings' ); ?></label>

		<div class="controls">
			<select ng-model="sls_<?php echo $shortcode; ?>.map_type" ng-init="sls_<?php echo $shortcode; ?>.map_type = 'default'">
				<?php
				Sl_Form::options( '', array(
					'default'   => __( 'Default', '7listings' ),
					'road'      => __( 'Road map', '7listings' ),
					'satellite' => __( 'Satellite', '7listings' ),
					'hybrid'    => __( 'Hybrid', '7listings' ),
					'terrain'   => __( 'Terrain', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="control-group map-dimensions">
		<label class="control-label"><?php _e( 'Size', '7listings' ); ?></label>

		<div class="controls">
				<span class="input-append input-prepend">
					<span class="add-on"><?php _e( 'Width', '7listings' ); ?></span>
					<input ng-model="sls_<?php echo $shortcode; ?>.width" ng-init="sls_<?php echo $shortcode; ?>.width = 100" type="text" class="dimension">
					<select ng-model="sls_<?php echo $shortcode; ?>.width_unit" ng-init="sls_<?php echo $shortcode; ?>.width_unit = '%'">
						<?php
						Sl_Form::options( '', array(
							'%'  => '%',
							'px' => 'px',
						) );
						?>
					</select>
				</span> <span style="height: 6px; display: block;"></span>
				<span class="input-append input-prepend">
					<span class="add-on"><?php _e( 'Height', '7listings' ); ?></span>
					<input ng-model="sls_<?php echo $shortcode; ?>.height" ng-init="sls_<?php echo $shortcode; ?>.height = 400" type="text" class="dimension">
					<select ng-model="sls_<?php echo $shortcode; ?>.height_unit" ng-init="sls_<?php echo $shortcode; ?>.height_unit = 'px'">
						<?php
						Sl_Form::options( '', array(
							'%'  => '%',
							'px' => 'px',
						) );
						?>
					</select>
				</span>
		</div>
	</div>

	<div  ng-repeat="map in maps" class="map-content-edit">
		<hr class="light">

		<div class="control-group">
			<label class="control-label"><?php _e( 'Insert Marker using', '7listings' ); ?></label>

			<div class="controls">
				<label>
					<input ng-model="sls_<?php echo $shortcode; ?>.type"  type="radio" value="address" ng-init="sls_<?php echo $shortcode; ?>.type = 'address'">
					<?php _e( 'Address', '7listings' ); ?>
				</label>
				<label>
					<input ng-model="sls_<?php echo $shortcode; ?>.type"  type="radio" value="latlng">
					<?php _e( 'Coordinates', '7listings' ); ?>
				</label>
			</div>
		</div>

		<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'address'">
			<label class="control-label"><?php _e( 'Address', '7listings' ); ?></label>

			<div class="controls">
				<input ng-model="map.address" id="sls-map-address" type="text">
			</div>
		</div>
		<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'latlng'">
			<label class="control-label"><?php _e( 'Latitude', '7listings' ); ?></label>

			<div class="controls">
				<input ng-model="map.latitude" id="sls-map-latitude" type="text">
			</div>
		</div>
		<div class="control-group" ng-show="sls_<?php echo $shortcode; ?>.type == 'latlng'">
			<label class="control-label"><?php _e( 'Longtitude', '7listings' ); ?></label>

			<div class="controls">
				<input ng-model="map.longitude" id="sls-map-longtitude" type="text">
			</div>
		</div>

		<div class="control-group">
			<label class="control-label"><?php _e( 'Marker Title', '7listings' ); ?></label>

			<div class="controls">
				<input ng-model="map.marker_title" type="text">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Info Window', '7listings' ); ?></label>

			<div class="controls">
				<textarea ng-model="map.info_window" class="widget-text-content"></textarea>
			</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label">&nbsp;</label>
		<div class="controls">
			<a ng-click="addmarker()" href="#" class="btn"><?php _e( 'Add Marker', '7listings' ); ?></a>
		</div>
	</div>

	<hr class="light">
	<label class="advanced"><input ng-model="sls_<?php echo $shortcode; ?>.advanced" type="checkbox" class="hidden"> <?php _e( 'Advanced Settings', '7listings' ); ?>
	</label>

	<div ng-show="sls_<?php echo $shortcode; ?>.advanced" class="advanced-options">
		<div class="control-group">
			<label class="control-label"><?php _e( 'Map Controls', '7listings' ); ?></label>

			<div class="controls">
				<label ng-repeat="control in controls">
					<input type="checkbox" ng-model="control.checked" value="{{control.value}}"> {{control.name}}
				</label>
			</div>
		</div>

		<div class="control-group gmaps-zoom-level">
			<label class="control-label"><?php _e( 'Zoom', '7listings' ); ?></label>

			<div class="controls">
				<select ng-model="sls_<?php echo $shortcode; ?>.zoom" ng-init="sls_<?php echo $shortcode; ?>.zoom = 8">
					<?php
					Sl_Form::options( '', array(
						6  => 6,
						7  => 7,
						8  => 8,
						9  => 9,
						10 => 10,
						11 => 11,
						12 => 12,
						13 => 13,
						14 => 14,
						15 => 15,
						16 => 16,
					) );
					?>
				</select>
			</div>
		</div>

		<hr class="light">

		<div class="control-group">
			<label class="control-label"><?php _e( 'Align', '7listings' ); ?></label>

			<div class="controls">
				<div class="btn-group">
					<label class="btn btn-default"> <i class="icon-align-left"></i>
						<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="left">
					</label> <label class="btn btn-default"> <i class="icon-align-center"></i>
						<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="center">
					</label> <label class="btn btn-default"> <i class="icon-align-right"></i>
						<input ng-model="sls_<?php echo $shortcode; ?>.align" type="radio" class="hidden" value="right">
					</label>
				</div>
			</div>
		</div>

		<hr class="light">

		<div class="control-group">
			<label class="control-label"><?php _e( 'Scrollwheel', '7listings' ); ?></label>

			<div class="controls">
				<?php Sls_Helper::checkbox_angular( "sls_$shortcode.scrollwheel", "sls-$shortcode.scrollwheel" ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Disable Dragging', '7listings' ); ?></label>

			<div class="controls">
				<?php Sls_Helper::checkbox_angular( "sls_$shortcode.disable_dragging", "sls-$shortcode.disable_dragging" ); ?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?php _e( 'Custom Marker Icon', '7listings' ); ?></label>

			<div class="controls">
				<input ng-model="sls_<?php echo $shortcode; ?>.marker_icon" type="text" data-type="url">
				<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
				<a href="#" class="button delete-image hidden"><?php _e( 'Delete', '7listings' ); ?></a>
				<br>
				<img src="" class="hidden">
			</div>
		</div>
	</div>
	<!-- .advanced-options -->

	<div class="control-group" ng-init="sls_<?php echo $shortcode; ?>.output = 'shortcode'">
		<div class="controls">
			<pre class="sls-shortcode"><?php
				$text = "[$shortcode";
				$text .= Sls_Helper::shortcode_atts( $shortcode, array(
					'address',
					'latitude',
					'longitude',
					'map_type',
					'marker_title',
					'zoom',
					'align',
					'scrollwheel',
					'disable_dragging',
					'marker_icon',
				) );
				$params = array(
					'width',
					'height',
				);
				foreach ( $params as $param )
				{
					$text .= sprintf( ' %1$s="{{sls_%2$s.%1$s}}{{sls_%2$s.%1$s_unit}}"', $param, $shortcode );
				}
				$text .= "]";
				echo $text;
				?>
				</br><div ng-repeat="map in maps">[marker   address="{{map.address}}" latitude="{{map.latitude}}" longitude="{{map.longitude}}" marker_title="{{map.marker_title}}"]{{map.info_window}}[/marker]</div>[/map]</pre>
		</div>
	</div>
</div>

<?php Sls_Helper::modal_footer( $shortcode, false, 'text' ); ?>
