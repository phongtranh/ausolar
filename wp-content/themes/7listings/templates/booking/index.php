<?php
/**
 * The main template file for booking page
 */

/**
 * List of variables which will be used in template parts
 * @see sl_get_template()
 */
$params = call_user_func( array( 'Sl_' . ucfirst( get_post_type() ) . '_Frontend', 'get_booking_params' ) );
?>

<?php sl_get_template( 'templates/booking/header' ); ?>

<form action="" method="post" name="booking_form" id="content" class="left booking-form" novalidate>
	<?php sl_get_template( 'templates/booking/step1-resource', $params ); ?>
	<?php sl_get_template( 'templates/booking/step2-customer', $params ); ?>

	<input type="hidden" name="verify">
	<input type="hidden" name="device" value="desktop">
</form>

<?php sl_get_template( 'templates/booking/sidebar', $params ); ?>
<?php sl_get_template( 'templates/booking/footer' ); ?>
