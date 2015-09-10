<?php
include THEME_TABS . 'parts/policies.php';

$checkin  = get_post_meta( get_the_ID(), 'checkin', true );
$checkin  = $checkin ? $checkin : 4; // 9:00 am
$checkout = get_post_meta( get_the_ID(), 'checkout', true );
$checkout = $checkout ? $checkout : 15; // 5:00 pm
?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Pick Up', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="checkin" id="checkin">
			<option value="1" <?php selected( $checkin, 1 ); ?>>7:30 am</option>
			<option value="2" <?php selected( $checkin, 2 ); ?>>8:00 am</option>
			<option value="3" <?php selected( $checkin, 3 ); ?>>8:30 am</option>
			<option value="4" <?php selected( $checkin, 4 ); ?>>9:00 am</option>
			<option value="5" <?php selected( $checkin, 5 ); ?>>9:30 am</option>
			<option value="6" <?php selected( $checkin, 6 ); ?>>10:00 am</option>
			<option value="7" <?php selected( $checkin, 7 ); ?>>10:30 am</option>
			<option value="8" <?php selected( $checkin, 8 ); ?>>11:00 am</option>
			<option value="9" <?php selected( $checkin, 9 ); ?>>11:30 am</option>
			<option value="10" <?php selected( $checkin, 10 ); ?>>Noon</option>
			<option value="11" <?php selected( $checkin, 11 ); ?>>1:00 pm</option>
			<option value="12" <?php selected( $checkin, 12 ); ?>>2:00 pm</option>
			<option value="13" <?php selected( $checkin, 13 ); ?>>3:00 pm</option>
			<option value="14" <?php selected( $checkin, 14 ); ?>>4:00 pm</option>
			<option value="15" <?php selected( $checkin, 15 ); ?>>5:00 pm</option>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Drop Off', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="checkout" id="checkout">
			<option value="1" <?php selected( $checkout, 1 ); ?>>7:30 am</option>
			<option value="2" <?php selected( $checkout, 2 ); ?>>8:00 am</option>
			<option value="3" <?php selected( $checkout, 3 ); ?>>8:30 am</option>
			<option value="4" <?php selected( $checkout, 4 ); ?>>9:00 am</option>
			<option value="5" <?php selected( $checkout, 5 ); ?>>9:30 am</option>
			<option value="6" <?php selected( $checkout, 6 ); ?>>10:00 am</option>
			<option value="7" <?php selected( $checkout, 7 ); ?>>10:30 am</option>
			<option value="8" <?php selected( $checkout, 8 ); ?>>11:00 am</option>
			<option value="9" <?php selected( $checkout, 9 ); ?>>11:30 am</option>
			<option value="10" <?php selected( $checkout, 10 ); ?>>Noon</option>
			<option value="11" <?php selected( $checkout, 11 ); ?>>1:00 pm</option>
			<option value="12" <?php selected( $checkout, 12 ); ?>>2:00 pm</option>
			<option value="13" <?php selected( $checkout, 13 ); ?>>3:00 pm</option>
			<option value="14" <?php selected( $checkout, 14 ); ?>>4:00 pm</option>
			<option value="15" <?php selected( $checkout, 15 ); ?>>5:00 pm</option>
		</select>
	</div>
</div>

