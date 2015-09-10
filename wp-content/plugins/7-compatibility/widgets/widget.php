<?php

/**
 * Base widget class for 7listings theme
 * This class registers common variables and some helper functions
 */
class Sl_Compatibility_Widget extends WP_Widget
{
	/**
	 * Remove keys from an array
	 *
	 * @param array $array Original array
	 * @param array $keys  List of keys
	 *
	 * @return array
	 */
	public static function remove_atts( &$array, $keys )
	{
		foreach ( $keys as $k )
		{
			unset( $array[$k] );
		}
	}
}
