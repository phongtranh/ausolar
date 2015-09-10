<?php

/**
 * Check post exists by ID
 *
 *
 * @param    int    $id    The ID of the post to check
 * @return   bool          True if the post exists; otherwise, false.
 */
function check_post_exists( $id ) {
	return is_string( get_post_status( $id ) );
}