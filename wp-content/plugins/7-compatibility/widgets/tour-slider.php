<?php

class Sl_Widget_Tour_Slider extends Sl_Widget_Compatibility_Slider
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Tour_Slider
	 */
	function __construct()
	{
		$this->post_type  = 'tour';
		$this->checkboxes = array(
			'post_title' => __( 'Title', '7listings' ),
			'rating'     => __( 'Rating', '7listings' ),
			'price'      => __( 'Price', '7listings' ),
			'booking'    => __( 'Booking Button', '7listings' ),
		);
		parent::__construct(
			'sl-tour-slider',
			__( '7 - Tour Slider', '7listings' ),
			array(
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
	}
}
