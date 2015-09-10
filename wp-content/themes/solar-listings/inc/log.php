<?php
/**
 * Log data
 *
 * @param array $data
 *
 * @return void
 */
function solar_log( $data )
{
	if ( !class_exists( 'SH' ) )
		return;

	SH::log( $data );
}