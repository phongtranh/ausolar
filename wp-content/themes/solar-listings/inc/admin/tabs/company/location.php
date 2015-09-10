<?php
$address   = get_post_meta( get_the_ID(), 'address', true );
$address2  = get_post_meta( get_the_ID(), 'address2', true );
$postcode  = get_post_meta( get_the_ID(), 'postcode', true );
$state     = get_post_meta( get_the_ID(), 'state', true );
$city      = get_post_meta( get_the_ID(), 'city', true );
$area      = get_post_meta( get_the_ID(), 'area', true );
$latitude  = get_post_meta( get_the_ID(), 'latitude', true );
$longitude = get_post_meta( get_the_ID(), 'longtitude', true );
$also_known_as = get_post_meta( get_the_ID(), 'also_known_as', true );
$company_service_location = get_post_meta( get_the_ID(), 'company_service_location', true );

$address_line = array();
if ( $address2 )
	$address_line[] = $address2;
if ( $area )
	$address_line[] = $area;
if ( $postcode )
	$address_line[] = $postcode;
if ( $state )
	$address_line[] = $state;
$address_line = implode( ',', $address_line );
?>

<p>
	<label><?php _e( 'Street Address', '7listings' ); ?></label>
	<input type="text" name="address" value="<?php echo $address; ?>">
</p>
<p>
	<label><?php _e( 'Level or unit and Building Name', '7listings' ); ?></label>
	<input type="text" name="address2" value="<?php echo $address2; ?>">
</p>
<p>
	<label><?php _e( 'Post Code', '7listings' ); ?> <span class="warning-sm required right"></span></label>
	<input type="number" min="0" max="9999" id="auto_fill_postcode" name="postcode" value="<?php echo $postcode; ?>">
</p>
<p>
	<label><?php _e( 'Suburb / Town', '7listings' ); ?> <span class="warning-sm required right"></span></label>
	<select id="auto_fill_suburb" name="area">
		<?php 
		if ( ! empty( $postcode ) ) : 
			$suburbs = \ASQ\Location\Location::find( array( 'postcode' => $postcode, 'type' => 'suburb' ) );
			foreach ( $suburbs as $suburb ) :
				$selected = ( !empty( $area ) && $area == $suburb['name'] ) ? 'selected' : '';
			?>
			<option value="<?php echo $suburb['name'] ?>" <?php echo $selected ?>><?php echo $suburb['name'] ?></option>
			<?php
			endforeach;
		endif; ?>
	</select>
</p>
<p>
	<label><?php _e( 'City', '7listings' ); ?></label>
	<input type="text" id="auto_fill_city" name="city" value="<?php echo $city; ?>" readonly>
</p>
<p>
	<label><?php _e( 'State', '7listings' ); ?> <span class="warning-sm required right"></span></label>
	<input type="text" name="state" id="auto_fill_state" value="<?php echo $state ?>" readonly>
</p>

<hr class="light">
<p class='checkbox-toggle'>
	<label><?php _e( 'Manual Marker Position', '7listings' ); ?></label>
	<?php Sl_Form::checkbox_general( 'location_marker', get_post_meta( get_the_ID(), 'location_marker', true ) ); ?>
</p>
<div>
	<p>
		<label><?php _e( 'Latitude', '7listings' ); ?></label>
		<input type="text" name="latitude" value="<?php echo $latitude; ?>"> <span class="input-hint"><?php printf( __( 'visit <a href="%s" target="_blank">www.latlong.net</a> for details', '7listings' ), 'http://www.latlong.net/' ); ?></span>
	</p>
	<p>
		<label><?php _e( 'Longitude', '7listings' ); ?></label>
		<input type="text" name="longtitude" value="<?php echo $longitude; ?>">
	</p>
</div>
<?php if ( ( $latitude && $longitude ) || $address_line ): ?>
	<hr class="light">
	<div>
		<label><?php _e( 'Google Maps', '7listings' ); ?></label>
		<?php
		if ( $latitude && $longitude )
		{
			sl_map( array(
				'type'       => 'latlng',
				'latitude'   => $latitude,
				'longtitude' => $longitude,
				'width'      => '425px',
				'height'     => '350px',
				'output_js'  => true,
			) );
		}
		else
		{
			sl_map( array(
				'type'      => 'address',
				'address'   => $address_line,
				'width'     => '425px',
				'height'    => '350px',
				'output_js' => true,
			) );
		}
		?>
	</div>
<?php endif; ?>

<hr />

<div class="sl-settings">
	<h3><?php _e( 'Also known as', '7listings' ); ?> <small>Separated by commas</small></h3>

	<textarea name="also_known_as" rows="3" style="width: 100%"><?php echo $also_known_as ?></textarea>
</div>

<div class="sl-settings">
	<h3><?php _e( 'Services Location', '7listings' ); ?> <small>Postcodes, separated by commas</small></h3>

	<textarea name="company_service_location" rows="3" style="width: 100%"><?php echo $company_service_location ?></textarea>
</div>