<?php

/**
 * This class handles common things for module for #featured title area
 * such as output logo, rating, map
 */
class Sl_Accommodation_Featured_Title extends Sl_Core_Featured_Title
{
	/**
	 * Display listing meta in featured title area
	 *
	 * @return void
	 */
	function single_overall_rating()
	{
		if ( sl_setting('accommodation_single_star_rating') == 1 )
			echo Sl_Accommodation_Frontend::star_rating( '', 'star_rating', array( 'post_type' => get_post_type() ) );
	}
}
