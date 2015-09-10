<?php
/**
 * Add param for lazyload (blank image)
 *
 * @param array $sl_params
 *
 * @return array
 */
function sl_lazyload_js_params( $sl_params )
{
	$sl_params['lazyLoader'] = THEME_IMG . 'ui/blank.png';

	return $sl_params;
}
add_filter( 'sl_js_params', 'sl_lazyload_js_params' );
?>