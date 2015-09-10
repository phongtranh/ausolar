<?php

/**
 * This class handles common things for module for #featured title area
 * such as output logo, rating, map
 */
class Sl_Company_Featured_Title extends Sl_Core_Featured_Title
{
	/**
	 * Add hooks for featured title for singular pages
	 *
	 * @return void
	 * @since 5.0.8
	 */
	function singular()
	{
		parent::singular();

		// Show booking button
		remove_action( self::$prefix . 'bottom', array( 'Sl_' . ucfirst( $this->post_type ) . '_Helper', 'booking_button' ) );

		// Show div.gold-member
		add_action( self::$prefix . 'top', array( $this, 'single_ribbon_membership' ) );

		// Show company meta in featured title area
		add_action( self::$prefix . 'bottom', array( $this, 'single_location' ) );
	}

	/**
	 * Show overall rating
	 *
	 * @return void
	 * @since 5.0.8
	 */
	function single_overall_rating()
	{
		Sl_Company_Helper::show_average_rating();
	}

	/**
	 * Show map in featured title area for single page
	 * This function also adds class .map to featured title area
	 *
	 * The behaviour depends on current hook
	 *
	 * This function loads map from /company/ template folder, instead using the same one as other modules
	 *
	 * @param array $class
	 *
	 * @return void|array
	 * @since 5.0.8
	 */
	function single_map( $class = array() )
	{
		if ( self::$prefix . 'class' == current_filter() )
		{
			$class[] = 'map';

			return $class;
		}

		get_template_part( 'templates/company/map' );
	}

	/**
	 * Show div.gold-member
	 *
	 * @return void
	 */
	function single_ribbon_membership()
	{
		if ( $membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true ) )
			echo "<div class='$membership-member'>$membership</div>";
	}

}
