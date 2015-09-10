<div class="sl-field">
	<label class="sl-label" for="checkin"><?php _e( 'From', '7listings' ); ?></label>
	<div class="sl-input">
		<input type="text" name="checkin" id="checkin" class="datepicker" value="<?php echo esc_attr( $data['checkin'] ); ?>" readonly>
	</div>
</div>
<div class="sl-field">
	<label class="sl-label" for="checkout"><?php _e( 'To', '7listings' ); ?></label>
	<div class="sl-input">
		<input type="text" name="checkout" id="checkout" class="datepicker" value="<?php echo esc_attr( $data['checkout'] ); ?>" readonly disabled>
	</div>
</div>

<?php sl_get_template( 'templates/booking/parts/upsells', $params ); ?>
