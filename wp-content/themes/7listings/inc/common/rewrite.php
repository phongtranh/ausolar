<?php
add_filter( 'rewrite_rules_array', 'sl_add_rewrite_rules' );

/**
 * Create custom rewrite rule for booking page
 *
 * @param array $rules All WordPress rewrite rules
 *
 * @return array
 */
function sl_add_rewrite_rules( $rules )
{
	if ( count( sl_setting( 'listing_types' ) ) )
		$rules['book/(.*?)/(.*?)/?$'] = 'index.php?book=1&book_slug=$matches[1]&resource=$matches[2]';

	return $rules;
}

add_filter( 'query_vars', 'sl_add_query_vars' );

/**
 * Add booking query vars
 *
 * @param array $vars
 *
 * @return array
 */
function sl_add_query_vars( $vars )
{
	if ( count( sl_setting( 'listing_types' ) ) )
	{
		$vars[] = 'book';
		$vars[] = 'book_slug';
		$vars[] = 'resource';
	}

	return $vars;
}
