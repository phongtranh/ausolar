<?php

/**
 * This class will hold all settings for rental
 */
class Sl_Rental_Settings extends Sl_Core_Settings
{
	/**
	 * Add settings tab in "email" settings page
	 *
	 * @return void
	 */
	function email_tab()
	{
		printf( '<a href="#rental-booking" class="nav-tab">%s</a>', __( 'Rental Booking', '7listings' ) );
	}
}

new Sl_Rental_Settings( 'rental' );
