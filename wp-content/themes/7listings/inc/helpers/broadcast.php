<?php
/**
 * Check if a blog exists in single or multisite environment
 *
 * @param int $blog_id
 *
 * @return bool
 */
function sl_blog_exists( $blog_id )
{
	// If theme is running on a single site, then there's only 1 blog and it always exists
	if ( ! is_multisite() )
		return true;

	/**
	 * Get all blogs in the multisite environment
	 * We use static variable for faster look up if we have to run this function 2+ times
	 */
	static $blogs = null;
	if ( null === $blogs )
	{
		global $wpdb;
		$blogs = $wpdb->get_col( "SELECT DISTINCT blog_id FROM {$wpdb->blogs}" );
	}

	return in_array( $blog_id, $blogs );
}

/**
 * Get broadcasted data of a listing
 *
 * @since 5.0.10
 *
 * @param string $name    Broadcasted data, can be:
 *                        - 'linked_parent': return array(blog_id, parent_id)
 *                        - 'linked_children': return array(blog_id_1 => child_id_1, blog_id_2 => child_id_2)
 *                        - 'maybe_broadcast': return true or false if the listing is shared
 *
 * @param int    $post_id Listing ID. Optional. Get current post ID if missed
 * @param null   $blog    Blog ID. Optional. Get current blog ID if missed
 *
 * @return mixed
 */
function sl_get_broadcasted_data( $name = '', $post_id = null, $blog = null )
{
	// Return immediately if not in multi-site environment
	if ( ! is_multisite() )
	{
		return false;
	}

	/**
	 * Get all broadcasted data
	 *
	 * @param array $data    Array of broadcasted data. Default is empty array
	 * @param int   $post_id Post ID. Use current post ID if null
	 * @param int   $blog    Blog ID. Use current blog ID if null
	 */
	$data = apply_filters( __FUNCTION__, array(), $post_id, $blog );

	$value = empty( $data ) || empty( $data[$name] ) ? false : $data[$name];

	// Special $name for faster check
	if ( 'maybe_broadcast' == $name )
		$value = isset( $data['linked_parent'] ) || isset( $data['linked_children'] );

	// Make sure all returned blogs exist
	if ( 'linked_parent' == $name )
	{
		if ( empty( $value['blog_id'] ) || ! sl_blog_exists( $value['blog_id'] ) )
		{
			$value = false;
		}
	}
	elseif ( 'linked_children' == $name )
	{
		$value = (array) $value;
		foreach ( $value as $k => $v )
		{
			if ( ! sl_blog_exists( $k ) )
				unset( $value[$k] );
		}
	}

	return $value;
}

/**
 * Get all broadcasted listings, e.g. same as current listing
 * No matter current listing is original or shared listing, this will get all blogs that have this listing
 *
 * @param int $post_id Post ID in current blog
 *
 * @return array
 */
function sl_get_broadcasted_listings( $post_id )
{
	global $blog_id;

	// Get all blogs which has this listing shared
	$all_blogs = array();
	if ( sl_get_broadcasted_data( 'maybe_broadcast', $post_id ) )
	{
		// If this is original post, just get its children
		$all_blogs = sl_get_broadcasted_data( 'linked_children', $post_id );

		// If this is a shared post, get all children from its parent
		if ( ! $all_blogs )
		{
			// Get parent and its children
			$parent    = sl_get_broadcasted_data( 'linked_parent', $post_id );
			$all_blogs = sl_get_broadcasted_data( 'linked_children', $parent['post_id'], $parent['blog_id'] );

			// Add the parent (original) post
			$all_blogs[$parent['blog_id']] = $parent['post_id'];
		}
	}

	// Add current blog
	$all_blogs[$blog_id] = $post_id;

	return $all_blogs;
}

/**
 * Display single (first) photo for resource
 * Post can be broadcasted, in that case we get the URL from main site
 *
 * @param array  $photos
 * @param string $size
 * @param int    $post_id
 *
 * @return string
 */
function sl_resource_photo( $photos, $size = 'sl_pano_medium', $post_id = 0 )
{
	if ( empty( $photos ) )
		return '';

	$post_id = $post_id ? $post_id : get_the_ID();
	$parent  = sl_get_broadcasted_data( 'linked_parent', $post_id );
	if ( $parent )
		switch_to_blog( $parent['blog_id'] );

	$photo = current( $photos );
	list( $src ) = wp_get_attachment_image_src( $photo, $size );
	$description = get_post_field( 'post_excerpt', $photo );
	$image       = sprintf( '<img class="photo" src="%1$s" alt="%2$s" title="%2$s">', $src, $description );

	if ( $parent )
		restore_current_blog();

	return $image;
}

/**
 * Get image src for a post
 * Post can be broadcasted, in that case we get the URL from main site
 *
 * @param string $key
 * @param string $size
 * @param int    $post_id
 *
 * @return string
 */
function sl_broadcasted_image_src( $key, $size = 'thumbnail', $post_id = null )
{
	if ( ! $post_id )
		$post_id = get_the_ID();

	$parent = sl_get_broadcasted_data( 'linked_parent', $post_id );
	if ( $parent === false )
	{
		$meta = get_post_meta( $post_id, $key, true );
		$src  = wp_get_attachment_image_src( $meta, $size );
	}
	else
	{
		switch_to_blog( $parent['blog_id'] );
		$meta = get_post_meta( $parent['post_id'], $key, true );
		$src  = wp_get_attachment_image_src( $meta, $size );
		restore_current_blog();
	}

	return is_array( $src ) ? $src[0] : '';
}

/**
 * Show post thumbnail of a shared listings
 *
 * Note
 * - In older version, we copy image and update "_thumbnail_id" meta
 * - In newer version, we don't copy image, "_thumbnail_id" meta is the value of original post
 *
 * @param string $size
 * @param array  $atts
 * @param int    $post_id
 * @param bool   $echo
 *
 * @return string
 */
function sl_broadcasted_thumbnail( $size = 'thumbnail', $atts = array(), $post_id = null, $echo = true )
{
	if ( ! $post_id )
		$post_id = get_the_ID();

	$parent = sl_get_broadcasted_data( 'linked_parent', $post_id );
	if ( $parent === false )
	{
		$html = get_the_post_thumbnail( $post_id, $size, $atts );
	}
	else
	{
		switch_to_blog( $parent['blog_id'] );
		$html = get_the_post_thumbnail( $parent['post_id'], $size, $atts );
		restore_current_blog();
	}

	if ( $echo )
		echo $html;

	return $html;
}

/**
 * Display photos for slider
 * Post can be broadcasted, in that case we get the URL from main site
 *
 * @return void
 */
function sl_photo_slider()
{
	$photos = get_post_meta( get_the_ID(), sl_meta_key( 'photos', get_post_type() ), false );
	if ( empty( $photos ) )
	{
		$featured = get_post_thumbnail_id();
		if ( ! $featured )
			return;

		$photos = array( $featured );
	}

	$parent = sl_get_broadcasted_data( 'linked_parent' );
	if ( $parent )
		switch_to_blog( $parent['blog_id'] );

	// Slider
	echo '<div id="slider" class="cycle-slideshow"
		data-cycle-slides="> .thumbnail"
		data-cycle-fx="fade"
		data-cycle-pager-template=""
		data-cycle-caption-template="{{cycleCaption}}"
	>';

	foreach ( $photos as $photo )
	{
		list( $large ) = wp_get_attachment_image_src( $photo, sl_setting( get_post_type() . '_slider_image_size' ) );
		$description = get_post_field( 'post_excerpt', $photo );
		echo "<figure class='thumbnail' data-cycle-caption='$description'><img src='$large' alt='$description'></figure>";
	}

	// Caption
	echo '<div class="cycle-caption"></div>';

	// Pager
	if ( count( $photos ) > 1 )
	{
		echo '<div class="cycle-pager">';
		foreach ( $photos as $photo )
		{
			list( $thumb ) = wp_get_attachment_image_src( $photo, 'sl_thumb_tiny' );
			$description = get_post_field( 'post_excerpt', $photo );
			echo "<figure class='thumbnail'><img src='$thumb' alt='$description'></figure>";
		}
		echo '</div>';
	}

	echo '</div>'; // .cycle-slideshow

	if ( $parent )
		restore_current_blog();
}

/**
 * Get thumbnail of resource
 *
 * @param object|int|null $post_id
 * @param array|int       $resource
 * @param string          $size
 *
 * @return string
 */
function sl_resource_thumb( $post_id, $resource, $size = 'sl_thumb_tiny' )
{
	global $post;

	// Get post object and post ID
	if ( is_object( $post_id ) )
	{
		$booking_post = $post_id;
		$post_id      = $post_id->ID;
	}
	elseif ( is_numeric( $post_id ) )
	{
		$booking_post = get_post( $post_id );
	}
	else
	{
		$post_id      = $post->ID;
		$booking_post = $post;
	}

	if ( is_numeric( $resource ) )
	{
		$resources = get_post_meta( $post_id, sl_meta_key( 'booking', $booking_post->post_type ), true );
		$resource  = $resources[$resource];
	}

	if ( empty( $resource['photos'] ) || ! is_array( $resource['photos'] ) )
	{
		$thumb = sl_broadcasted_thumbnail( $size, array(), $post_id, false );
	}
	else
	{
		$thumb = sl_resource_photo( $resource['photos'], $size, $post_id );
	}

	return $thumb;
}
