<?php

class Sl_Widget_Tours extends Sl_Widget_Compatibility_List
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Tours
	 */
	function __construct()
	{
		$this->post_type                     = 'tour';
		$this->checkboxes                    = array(
			'rating'  => __( 'Rating', '7listings' ),
			'price'   => __( 'Price', '7listings' ),
			'booking' => __( 'Booking Button', '7listings' ),
		);
		$this->default['more_listings_text'] = __( 'See more listings', '7listings' );
		parent::__construct(
			'sl-tours',
			__( '7 - Tours', '7listings' ),
			array(
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
	}
}
