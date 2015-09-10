<?php
// If booking resource is free, do not display payment gateways
$class = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
if ( false === call_user_func( array( $class, 'get_resource_price' ), $resource ) )
{
	return;
}

// Check if we have >= 2 payment gateways to select from. If not then do nothing
$conditions = array(
	sl_setting( 'paypal' ),
	sl_setting( 'eway' ) && sl_setting( 'eway_shared' ),
);
if ( count( array_filter( $conditions ) ) < 2 )
{
	return;
}
?>

<div class="sl-field error-box hidden">
	<div class="sl-input">
		<div class="error error-gateways"></div>
	</div>
</div>

<div class="sl-field payment-gateway required">
	<label class="sl-label"><?php _e( 'Pay with', '7listings' ); ?></label>
	<div id="payment" class="sl-input gateway-options">
		<ul class="payment_methods">
			<li>
				<input type="radio" id="gateway_eway" name="payment_gateway" value="eway" tabindex="209">
				<label for="gateway_eway" class="eway" title="Pay with eWay">
					<span class="eway logo"><?php esc_attr_e( 'eWay', '7listings' ); ?></span>
					<div class="accepted-cards">
						<span class="visa icon">Visa</span>
						<span class="mastercard icon">Master Card</span>
					</div>
				</label>
				<?php if ( sl_setting( 'eway_description' ) ) : ?>
					<div class="payment_box payment_methode_eway">
						<p><?php echo esc_html( sl_setting( 'eway_description' ) ); ?></p>
					</div>
				<?php endif; ?>
			</li>
			<li>
				<input type="radio" id="gateway_paypal" name="payment_gateway" value="paypal" tabindex="210">
				<label for="gateway_paypal" class="paypal" title="Pay with PayPal">
					<span class="paypal logo"><?php esc_attr_e( 'Paypal', '7listings' ); ?></span>
					<div class="accepted-cards">
						<span class="visa icon">Visa</span>
						<span class="mastercard icon">Master Card</span>
						<span class="amex icon">American Express</span>
					</div>
				</label>
				<?php if ( sl_setting( 'paypal_description' ) ) : ?>
					<div class="payment_box payment_eway">
						<p><?php echo esc_html( sl_setting( 'paypal_description' ) ); ?></p>
					</div>
				<?php endif; ?>
			</li>
		</ul>
		<label class="sl-input-warning"></label>
	</div>
</div>


