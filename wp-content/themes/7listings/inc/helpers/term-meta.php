<?php
/**
 * Add term meta
 *
 * @param int    $term_id
 * @param string $key
 * @param mixed  $value
 *
 * @since 5.0.5 Use theme settings to store term meta as array, not in separated option
 */
function sl_update_term_meta( $term_id, $key, $value )
{
	$settings  = get_option( THEME_SETTINGS );
	$term_meta = isset( $settings['term_meta'] ) ? $settings['term_meta'] : array();

	if ( ! isset( $term_meta[$term_id] ) )
		$term_meta[$term_id] = array();
	$term_meta[$term_id][$key] = $value;

	$settings['term_meta'] = $term_meta;
	update_option( THEME_SETTINGS, $settings );
}

/**
 * Delete term meta
 *
 * @param int    $term_id
 * @param string $key
 *
 * @since 5.0.5 Use theme settings to store term meta as array, not in separated option
 *
 * @return void
 */
function sl_delete_term_meta( $term_id, $key )
{
	$settings  = get_option( THEME_SETTINGS );
	$term_meta = isset( $settings['term_meta'] ) ? $settings['term_meta'] : array();

	if ( ! isset( $term_meta[$term_id] ) || ! isset( $term_meta[$term_id][$key] ) )
		return;

	unset( $term_meta[$term_id][$key] );

	$settings['term_meta'] = $term_meta;
	update_option( THEME_SETTINGS, $settings );
}

/**
 * Get term meta
 *
 * @param int    $term_id
 * @param string $key
 *
 * @since 5.0.5 Use theme settings to store term meta as array, not in separated option
 *
 * @return mixed
 */
function sl_get_term_meta( $term_id, $key )
{
	$term_meta = sl_setting( 'term_meta' );
	$value     = isset( $term_meta[$term_id] ) && isset( $term_meta[$term_id][$key] ) ? $term_meta[$term_id][$key] : false;

	return $value;
}
