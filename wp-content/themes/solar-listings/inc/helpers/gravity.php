<?php
/**
 * Contain functions use for gravity form
 * User: TaiDN
 */

add_filter( 'gform_field_value_sl_company_name', 'solar_company_auto_populate_name' );

/*
 * Automatically fill company name when form loaded
 *
 * @param string $value
 *
 * @return string
 */

function solar_company_auto_populate_name( $value )
{
	global $current_user;
	get_currentuserinfo();

	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => $current_user->ID,
	) );

	if ( empty( $company ) )
		return 'nothing';

	$company = current( $company );

	return $company->post_title;
}

add_filter('gform_field_value_hidden_token', 'solar_populate_hidden_token');

/*
 * Automatically fill value for hidden field if user isn't administrator
 *
 * @param string $value
 *
 * @return string
 */


function solar_populate_hidden_token( $value )
{
	if ( ! current_user_can( 'administrator' ) )
	{
		$value = "{secret_field}";
	}
	return $value;
}