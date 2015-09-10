<select id="sl-booking-action">
	<option value=""><?php esc_attr_e( 'Actions', '7listings' ); ?></option>
	<optgroup label="<?php esc_attr_e( 'Resend order emails', '7listings' ); ?>">
		<option value="customer"><?php esc_html_e( 'Send customer email', '7listings' ); ?></option>
		<option value="admin"><?php esc_html_e( 'Send admin email', '7listings' ); ?></option>
	</optgroup>
</select>
<button class="button" id="sl-booking-apply"><?php esc_html_e( 'Apply', '7listings' ); ?></button>
<span class="spinner"></span>
<span class="success hidden"><?php esc_html_e( 'Email was sent', '7listings' ); ?></span>
<span class="error hidden"><?php esc_html_e( 'Error sending email!', '7listings' ); ?></span>
