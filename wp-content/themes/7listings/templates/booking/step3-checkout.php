<?php
if ( ! sl_setting( 'eway' ) || ! sl_setting( 'eway_hosted' ) )
	return;
?>

<section class="panel payment">
	<h2><?php _e( 'Billing Info', '7listings' ); ?></h2>

	<div class="panel-content">
		<div class="sl-field error-box hidden">
			<div class="sl-input">
				<div class="error error-cc"></div>
			</div>
		</div>

		<div class="credit-card">

			<div class="sl-field">
				<label class="sl-label"><?php _e( 'Name', '7listings' ); ?></label>

				<div class="sl-input">
					<input type="text" name="card_holders_name" class="card-name">
				</div>
			</div>

			<div class="sl-field">
				<label class="sl-label"><?php _e( 'Card Number', '7listings' ); ?></label>

				<div class="sl-input">
					<input type="text" name="card_number" class="card-number"> <span class="icon"></span>
				</div>
			</div>

			<div class="sl-field">
				<label class="sl-label"><?php _e( 'Expiry', '7listings' ); ?></label>

				<div class="sl-input">
					<select name="card_expiry_month" class="card-expiry-month">
						<option value="">-</option>
						<option value="01"><?php _e( '01 - January', '7listings' ); ?></option>
						<option value="02"><?php _e( '02 - February', '7listings' ); ?></option>
						<option value="03"><?php _e( '03 - March', '7listings' ); ?></option>
						<option value="04"><?php _e( '04 - April', '7listings' ); ?></option>
						<option value="05"><?php _e( '05 - May', '7listings' ); ?></option>
						<option value="06"><?php _e( '06 - June', '7listings' ); ?></option>
						<option value="07"><?php _e( '07 - July', '7listings' ); ?></option>
						<option value="08"><?php _e( '08 - August', '7listings' ); ?></option>
						<option value="09"><?php _e( '09 - September', '7listings' ); ?></option>
						<option value="10"><?php _e( '10 - October', '7listings' ); ?></option>
						<option value="11"><?php _e( '11 - November', '7listings' ); ?></option>
						<option value="12"><?php _e( '12 - December', '7listings' ); ?></option>
					</select> <select name="card_expiry_year" class="card-expiry-year">
						<option value="">-</option>
						<option value="14">2014</option>
						<option value="15">2015</option>
						<option value="16">2016</option>
						<option value="17">2017</option>
						<option value="18">2018</option>
						<option value="19">2019</option>
						<option value="20">2020</option>
					</select>
				</div>
			</div>

			<div class="sl-field">
				<label class="sl-label">CVN/CVV2</label>

				<div class="sl-input">
					<input type="text" name="card_cvn" class="card-cvn">
				</div>
			</div>
		</div>

		<nav class="sl-field nav hidden">
			<div class="sl-input">
				<button name="submit" class="button pay"><?php _e( 'Pay Now', '7listings' ); ?></button>
			</div>
		</nav>

	</div>

</section>
