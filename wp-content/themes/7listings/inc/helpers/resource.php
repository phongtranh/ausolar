<?php
/**
 * Get timezone offset
 *
 * @return int
 */
function sl_timezone_offset()
{
	$gmt_offset      = get_option( 'gmt_offset' );
	$timezone_string = get_option( 'timezone_string' );
	if ( ! $gmt_offset && $timezone_string )
	{
		$timezone_selected = new DateTimeZone( $timezone_string );
		$gmt_offset        = timezone_offset_get( $timezone_selected, date_create() ) / 3600;
	}

	return $gmt_offset;
}

/**
 * Find resource
 *
 * @param int    $post_id
 * @param string $resource_title
 * @param string $type Post type
 *
 * @return bool|array False if not found. Array ( resource_index, resource ) if success
 */
function sl_find_resource( $post_id = null, $resource_title, $type = 'accommodation' )
{
	$resources = get_post_meta( $post_id, sl_meta_key( 'booking', $type ), true );

	if ( empty( $resources ) )
		return false;

	$sanitized_title = sanitize_title( $resource_title );
	foreach ( $resources as $index => $resource )
	{
		if ( $sanitized_title == sanitize_title( $resource['title'] ) )
			return array( $index, $resource );
	}

	return false;
}

/**
 * Get seasonal price from the schedules
 *
 * @param int    $base      Based price
 * @param string $date      Current date
 * @param array  $schedules Array of scheduled seasonal prices
 *
 * @return void Based price will be changed if there are schedules apply
 */
function sl_seasonal_price( &$base, $date, $schedules )
{
	// New price
	$new = null;

	// Loop through scheduled seasonal prices and calculate the final price
	foreach ( $schedules as $value )
	{
		$value = array_merge( array(
			'from'   => '',
			'to'     => '',
			'enable' => 0,
		), $value );

		$from = strtotime( str_replace( '/', '-', $value['from'] ) );
		$to   = strtotime( str_replace( '/', '-', $value['to'] ) );

		if ( ! $value['enable'] || $date < $from || $date > $to )
			continue;

		$new = sl_calculate_seasonal_price( $base, $value );
	}

	// Only change price if schedules apply
	if ( null !== $new )
		$base = $new;
}

/**
 * Calculate ONE seasonal price from the ONE schedule
 *
 * @param int   $base     Based price
 * @param array $schedule The schedule
 *
 * @return float New price
 */
function sl_calculate_seasonal_price( $base, $schedule )
{
	$schedule = array_merge( array(
		'fixed'      => '',
		'percentage' => '',
		'source'     => '',
	), $schedule );

	// Calculate by percentage
	if ( 'percentage' == $schedule['source'] )
	{
		$price = (float) $schedule['percentage'] / 100;
		if ( false !== strpos( $schedule['percentage'], '-' ) || false !== strpos( $schedule['percentage'], '+' ) )
			$price += 1;
		$price = $price * $base;
	}
	// Calculate by fixed price
	else
	{
		$price = (float) $schedule['fixed'];
		if ( false !== strpos( $schedule['fixed'], '-' ) || false !== strpos( $schedule['fixed'], '+' ) )
			$price += $base;
	}

	return $price;
}

/**
 * Get permanent price from the schedule and update the $base variable
 *
 * @param int    $base     Based price, will be updated if necessary
 * @param string $date     Current date
 * @param array  $schedule The schedule
 *
 * @return bool|float New price if success. False if no schedule
 */
function sl_permanent_price( &$base, $date, $schedule )
{
	$schedule = array_merge( array(
		'date'   => '',
		'price'  => '',
		'enable' => 0,
	), $schedule );

	$schedule['date'] = strtotime( str_replace( '/', '-', $schedule['date'] ) );
	if ( $schedule['enable'] && $schedule['price'] && $date >= $schedule['date'] )
		$base = $schedule['price'];
}

