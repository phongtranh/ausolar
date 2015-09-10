<?php
/**
 * This widget displays list of Tour Types
 */
class Sl_Widget_Tour_Types extends Sl_Widget_Compatibility_Taxonomy
{
	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Tour_Types
	 */
	function __construct()
	{
		$this->taxonomy = sl_meta_key( 'tax_type', 'tour' );
		parent::__construct(
			'sl-tour-types',
			__( '7 - Tour Types', '7listings' ),
			array(
				'classname'   => 'taxonomies tours types',
				'description' => __( 'X - for backwards compatibility', '7listings' ),
			)
		);
	}
}
