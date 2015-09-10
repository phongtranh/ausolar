<?php
/**
 * Handle file upload
 *
 * @param string $name Name of the input field
 * @param array  $args Arguments
 *
 * @return mixed Single ID or array of IDs of uploaded files
 */
function peace_handle_upload( $name, $args = array() )
{
	if ( empty( $_FILES[$name] ) )
		return null;

	$args = wp_parse_args( $args, array(
		'error_setting' => '',
		'multiple'      => false,
		'extensions'    => array( 'jpg', 'jpeg', 'gif', 'png' ),
	) );
	extract( $args );

	// Get list of uploaded files
	// Force to use array to make it easier to use foreach below
	$files = $multiple ? peace_fix_file_array( $_FILES[$name] ) : array( $_FILES[$name] );

	$uploaded = array();
	foreach ( $files as $file_item )
	{
		if ( $file_item['error'] )
			continue;

		// Check file extension
		$ext = strtolower( substr( $file_item['name'], strrpos( $file_item['name'], '.' ) + 1 ) );
		if ( !in_array( $ext, $extensions ) )
		{
			if ( $error_setting )
				add_settings_error( $error_setting, $name, __( 'Invalid file extension.', 'peace' ), 'error' );
			continue;
		}

		$file = wp_handle_upload( $file_item, array( 'test_form' => false ) );

		if ( !isset( $file['file'] ) )
		{
			if ( $error_setting )
				add_settings_error( $error_setting, $name, __( 'Error uploading. Please try again.', 'peace' ), 'error' );
			continue;
		}

		$filename = $file['file'];

		$attachment = array(
			'post_mime_type' => $file['type'],
			'guid'           => $file['url'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => ''
		);
		$id = wp_insert_attachment( $attachment, $filename );

		if ( is_wp_error( $id ) )
		{
			if ( $error_setting )
				add_settings_error( $error_setting, $name, __( 'Cannot insert attachment. Please try again.', 'peace' ), 'error' );
			continue;
		}
		else
		{
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $filename ) );
			$uploaded[] = $id;
		}
	}

	return $multiple ? $uploaded : array_pop( $uploaded );
}

/**
 * Fixes the odd indexing of multiple file uploads from the format:
 *     $_FILES['field']['key']['index']
 * To the more standard and appropriate:
 *     $_FILES['field']['index']['key']
 *
 * @param $files
 *
 * @return array
 */
function peace_fix_file_array( $files )
{
	$output = array();
	foreach ( $files as $key => $list )
	{
		foreach ( $list as $index => $value )
		{
			$output[$index][$key] = $value;
		}
	}
	return $output;
}
