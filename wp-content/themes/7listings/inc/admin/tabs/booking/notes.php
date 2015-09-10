<?php
printf( '<p>
	<div class="view">
		%s
		<a class="sl-edit edit-customer-message dashicons dashicons-edit" href="#"></a>
	</div>
	<div class="edit hidden">
		<textarea type="text" name="customer_message">%s</textarea>
	</div>
	</p>',
	sl_booking_meta( 'customer_message' ) ? sl_booking_meta( 'customer_message' ) : __( 'No customer message', '7listings' ),
	sl_booking_meta( 'customer_message' ) ? sl_booking_meta( 'customer_message' ) : ''
);