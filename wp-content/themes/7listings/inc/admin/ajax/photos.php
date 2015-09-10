<?php
add_action( 'wp_ajax_sl_photo_update_description', 'sl_ajax_photo_update_description' );
add_action( 'wp_ajax_sl_photo_delete', 'sl_ajax_photo_delete' );
add_action( 'wp_ajax_sl_photos_reorder', 'sl_ajax_photos_reorder' );

/**
 * Ajax callback for updating ALT attributes of image
 *
 * @return void
 */
function sl_ajax_photo_update_description()
{
	check_admin_referer( 'update-description' );
	$attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
	if ( ! $attachment_id )
		wp_send_json_error();

	$description = isset( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
	wp_update_post( array(
		'ID'           => $attachment_id,
		'post_excerpt' => $description, // Caption
	) );
	wp_send_json_success();
}

/**
 * Ajax callback for deleting photos
 *
 * @return void
 */
function sl_ajax_photo_delete()
{
	check_admin_referer( 'delete' );

	$post_id       = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
	$resource_id   = isset( $_POST['resource_id'] ) ? $_POST['resource_id'] : null;

	if ( ! $post_id || ! $attachment_id )
		wp_send_json_error();

	// Delete listing photo
	if ( null === $resource_id )
	{
		delete_post_meta( $post_id, sl_meta_key( 'photos', get_post_type( $post_id ) ), $attachment_id );
	}
	// Delete booking resource photo
	else
	{
		$meta_key = sl_meta_key( 'booking', get_post_type( $post_id ) );
		$bookings = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $bookings[$resource_id] ) || empty( $bookings[$resource_id]['photos'] ) || ! in_array( $attachment_id, $bookings[$resource_id]['photos'] ) )
			wp_send_json_error();

		$bookings[$resource_id]['photos'] = array_diff( $bookings[$resource_id]['photos'], array( $attachment_id ) );
		update_post_meta( $post_id, $meta_key, $bookings );
	}

	wp_send_json_success();
}

/**
 * Ajax callback for reordering images
 *
 * @return void
 */
function sl_ajax_photos_reorder()
{
	check_admin_referer( 'reorder' );

	$post_id     = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	$order       = isset( $_POST['order'] ) ? $_POST['order'] : '';
	$resource_id = isset( $_POST['resource_id'] ) ? $_POST['resource_id'] : null;

	parse_str( $order, $items );
	$items = $items['item'];

	// Reorder listing photos
	if ( null === $resource_id )
	{
		$meta_key = sl_meta_key( 'photos', get_post_type( $post_id ) );
		delete_post_meta( $post_id, $meta_key );
		foreach ( $items as $item )
		{
			add_post_meta( $post_id, $meta_key, $item );
		}
	}
	// Reorder booking resource photos
	else
	{
		$meta_key = sl_meta_key( 'booking', get_post_type( $post_id ) );
		$bookings = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $bookings[$resource_id] ) || empty( $bookings[$resource_id]['photos'] ) )
			wp_send_json_error();

		$bookings[$resource_id]['photos'] = $items;
		update_post_meta( $post_id, $meta_key, $bookings );
	}

	wp_send_json_success();
}
