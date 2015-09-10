<?php
$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
$longitude = get_post_meta( get_the_ID(), 'longitude', true );
?>
<?php if ( ( $latitude && $longitude ) || ( ! empty( $address ) && ! empty( $city ) && ! empty( $postcode ) ) ): ?>

	<div>
		<?php
		if ( $latitude && $longitude )
		{
			sl_map( array(
				'type'      => 'latlng',
				'latitude'  => $latitude,
				'longitude' => $longitude,
				'height'    => '350px',
				'zoom'      => 16,
			) );
		}
		else
		{
			sl_map( array(
				'type'    => 'address',
				'address' => "$address, $city, $postcode",
				'height'  => '240px',
				'zoom'    => 15,
			) );
		}
		?>
	</div>

<?php endif; ?>

<div class="sl-settings manual-marker checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Manual Marker Position', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'location_marker', get_post_meta( get_the_ID(), 'location_marker', true ) ) ?>
	</div>
</div>

<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Latitude', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="number" step="any" name="latitude" class="sl-input-medium latitude" value="<?php echo $latitude; ?>">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Longitude', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="number" step="any" name="longitude" class="sl-input-medium longitude" value="<?php echo $longitude; ?>">
			<p class="description"><?php _e( 'Enter Latitude and Longitude to position map marker', '7listings' ); ?></p>
		</div>
	</div>
</div>
