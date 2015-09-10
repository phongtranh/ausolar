<?php

/**
 * This class displays list of locations in the frontend
 */
class Sl_Widget_Locations extends Sl_Widget_Compatibility_Taxonomy
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Locations
	 */
	function __construct()
	{
		$this->taxonomy = 'location';
		parent::__construct(
			'sl-locations',
			__( '7 - Locations', '7listings' ),
			array(
				'classname'   => 'taxonomies locations',
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
	}
}
