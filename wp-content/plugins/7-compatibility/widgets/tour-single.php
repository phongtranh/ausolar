<?php

/**
 * This widget displays single tour in the frontend
 */
class Sl_Widget_Tour_Single extends Sl_Widget_Compatibility_Single
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Tour_Single
	 */
	function __construct()
	{
		$this->default['more_link_text'] = __( 'Read more', '7listings' );
		$this->post_type  = 'tour';
		$this->checkboxes = array(
			'post_title' => __( 'Title', '7listings' ),
			'rating'     => __( 'Rating', '7listings' ),
			'price'      => __( 'Price', '7listings' ),
			'booking'    => __( 'Booking Button', '7listings' ),
		);
		parent::__construct(
			'sl-tour-single',
			__( '7 - Tour Single', '7listings' ),
			array(
				'classname'   => 'sl-list single',
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
	}
}
