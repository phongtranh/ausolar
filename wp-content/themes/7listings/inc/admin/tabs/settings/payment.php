<?php
$currencies = Sl_Currency::all();
$positions  = Sl_Currency::positions();
foreach ( $currencies as $code => $name )
{
	$currencies[$code] = $name . ' (' . Sl_Currency::symbol( $code ) . ')';
}
?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Invoice Code', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS; ?>[invoice_code]" value="<?php echo sl_setting( 'invoice_code' ); ?>" class="sl-input-small">
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Currency', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS; ?>[currency]">
			<?php Sl_Form::options( sl_setting( 'currency' ), $currencies ); ?>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Currency Position', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS; ?>[currency_position]">
			<?php Sl_Form::options( sl_setting( 'currency_position' ), $positions ); ?>
		</select>
	</div>
</div>

<br>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><span class="paypal logo"></span></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'paypal' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Paypal Email', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="email" name="<?php echo THEME_SETTINGS; ?>[paypal_email]" value="<?php echo sl_setting( 'paypal_email' ); ?>">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Sandbox', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'sandbox_mode' ); ?>
			<span class="description"><?php _e( 'Test Mode', '7listings' ); ?></span>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Description', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<textarea  cols="120" rows="5" name="<?php echo THEME_SETTINGS . '[paypal_description]'; ?>"><?php echo esc_textarea( sl_setting( 'paypal_description' ) ); ?></textarea>
		</div>
	</div>
</div>

<br>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><span class="eway logo"></span></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'eway' ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Customer ID', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[eway_id]" value="<?php echo sl_setting( 'eway_id' ); ?>" class="sl-input-small">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Username', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS; ?>[eway_username]" value="<?php echo sl_setting( 'eway_username' ); ?>">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Sandbox', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'eway_sandbox' ); ?>
			<span class="description"><?php _e( 'Test Mode', '7listings' ); ?></span>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Shared Payments', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'eway_shared' ); ?>
		</div>
	</div>
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label><?php _e( 'Self Hosted Payments', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'eway_hosted' ); ?>
			<span class="description"><?php _e( 'SSL required for this option', '7listings' ); ?><span>
		</div>
	</div>
	<div class="sl-sub-settings">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'SSL', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( 'ssl' ); ?>
				<span class="description"><?php _e( 'For testing turn off', '7listings' ); ?></span>
			</div>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Description', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<textarea  cols="120" rows="5" name="<?php echo THEME_SETTINGS . '[eway_description]'; ?>"><?php echo esc_textarea( sl_setting( 'eway_description' ) ); ?></textarea>
		</div>
	</div>
</div>
