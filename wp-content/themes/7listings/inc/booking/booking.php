<?php
/**
 * Booking module main file
 */
if ( is_admin() )
{
	require THEME_INC . 'booking/management.php';
	new Sl_Booking_Management;

	require THEME_INC . 'booking/edit.php';
	new Sl_Booking_Edit;

	require THEME_INC . 'booking/booking-admin.php';
}
else
{
	require THEME_INC . 'booking/payment.php';
	new Sl_Booking_Payment;

	require THEME_INC . 'booking/featured-title.php';
	new Sl_Booking_Featured_Title;
}
