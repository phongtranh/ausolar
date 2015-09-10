<?php
// For cart: display "Done"
if ( $is_cart )
{
	?>
	<div class="sl-field nav hidden">
		<div class="sl-input">
			<input type="submit" class="button pay done" data-type="<?php echo esc_attr( get_post_type() ); ?>" value="<?php esc_attr_e( 'Done', '7listings' ); ?>">
		</div>
	</div>
	<?php
	return;
}

// If booking resource is free, display "Book Now"
$class = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
if ( false === call_user_func( array( $class, 'get_resource_price' ), $resource ) )
{
	?>
	<div class="sl-field nav">
		<div class="sl-input">
			<input type="submit" name="submit" class="button pay" value="<?php esc_attr_e( 'Book Now', '7listings' ); ?>">
		</div>
	</div>
	<?php
	return;
}

// If eWay hosted is enabled, display "Next"
if ( sl_setting( 'eway' ) && sl_setting( 'eway_hosted' ) )
{
	?>
	<nav class="sl-field nav hidden">
		<div class="sl-input">
			<a href="#" class="button next" tabindex="211"><?php _e( 'Next', '7listings' ); ?></a>
		</div>
	</nav>
	<?php
	return;
}
?>

<?php // For normal booking, display "Pay Now" ?>
<nav class="sl-field nav hidden">
	<div class="sl-input">
		<input type="submit" name="submit" class="button pay" value="<?php esc_attr_e( 'Pay Now', '7listings' ); ?>" tabindex="211">
	</div>
</nav>
