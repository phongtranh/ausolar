<?php

/**
 * Parse XML response from eway and place them into an array
 * Used in hosted payment
 *
 * @param string $xml XML reponse
 *
 * @return array
 */
function eway_parse_response( $xml )
{
	$p = xml_parser_create();
	xml_parse_into_struct( $p, $xml, $xml_data );
	xml_parser_free( $p );

	$fields = array();
	foreach ( $xml_data as $data )
	{
		if ( empty( $data['level'] ) || empty( $data['tag'] ) || empty( $data['value'] ) )
			continue;
		if ( 2 == $data['level'] )
			$fields[$data['tag']] = $data['value'];
	}

	return $fields;
}

/**
 * Fetch data from eway
 * Used in shared payment
 *
 * @param $string
 * @param $start_tag
 * @param $end_tag
 *
 * @return string
 */
function eway_fetch_data( $string, $start_tag, $end_tag )
{
	$position       = stripos( $string, $start_tag );
	$str            = substr( $string, $position );
	$str_second     = substr( $str, strlen( $start_tag ) );
	$second_positon = stripos( $str_second, $end_tag );
	$str_third      = substr( $str_second, 0, $second_positon );
	$fetch_data     = trim( $str_third );

	return $fetch_data;
}
