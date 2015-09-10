<?php
add_action( 'admin_enqueue_scripts', 'sl_admin_enqueue_scripts' );

/**
 * Enqueue scripts and styles for editing page
 */
function sl_admin_enqueue_scripts()
{
	$screen = get_current_screen();

	if ( 'page' == $screen->post_type && 'post' == $screen->base )
	{
		wp_enqueue_script( 'sl-choose-image' );
		wp_enqueue_script( 'sl-admin-page', THEME_JS . 'admin/page.js', array( 'jquery' ), '', true );

		return;
	}

	$types = sl_setting( 'listing_types' );
	if ( empty( $types ) || 'post' !== $screen->base )
	{
		return;
	}

	wp_enqueue_style( 'jquery-ui' );
	wp_enqueue_style( 'jquery-ui-timepicker' );
	wp_enqueue_style( 'media-views' );

}


add_action( 'post_edit_form_tag', 'sl_post_edit_form_tag' );

/**
 * Add data encoding type for file uploading
 *
 * @return void
 */
function sl_post_edit_form_tag()
{
	echo ' enctype="multipart/form-data"';
}


add_action( 'post_comment_status_meta_box-options', 'sl_page_comment_status' );

/**
 * Add checkbox option for showing old comments on pages
 *
 * @param object $post
 *
 * @return void
 */
function sl_page_comment_status( $post )
{
	?>
	<br>
	<label id="old-comments">
		<input name="show_old_comments" type="checkbox" value="1" <?php checked( get_post_meta( $post->ID, 'show_old_comments', true ) ); ?>>
		<?php _e( 'Show old comments, but disable new comments.', '7listings' ); ?>
	</label>
<?php
}


add_action( 'add_meta_boxes', 'sl_add_meta_boxes', 10, 2 );

/**
 * Add meta boxes
 * @param string  $post_type Post type
 * @param WP_Post $post      Post object
 *
 * @return void
 */
function sl_add_meta_boxes( $post_type, $post )
{
	// Custom sidebar for pages
	add_meta_box( 'sidebar', __( 'Sidebar', '7listings' ), 'page_sidebar_render', 'page', 'side' );
	add_meta_box( 'featured-header', __( 'Featured Header Settings', '7listings' ), 'page_settings_render', 'page', 'advanced', 'high' );

	// Slug meta box is always visible (not removed by post type support). We have to remove it manually.
	remove_meta_box( 'slugdiv', 'booking', 'normal' );

	// Attractions
	remove_meta_box( 'locationdiv', 'attraction', 'side' );
	add_meta_box( 'attraction', __( 'Attraction', '7listings' ), 'attraction', 'advanced', 'high' );
}

/**
 * Render sidebar meta box
 *
 * @return void
 */
function page_sidebar_render()
{
	include THEME_TABS . 'page/sidebar.php';
}

/**
 * Show page settings meta box
 *
 * @return void
 */
function page_settings_render()
{
	include THEME_TABS . 'page/featured-header.php';
}

add_action( 'save_post', 'sl_save_meta_boxes' );

/**
 * Save meta boxes
 *
 * @param $post_id
 */
function sl_save_meta_boxes( $post_id )
{
	if (
		defined( 'DOING_AJAX' )
		|| wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
	)
	{
		return;
	}

	// Get proper post type. @link http://www.deluxeblogtips.com/forums/viewtopic.php?id=161
	$post_type = '';
	$post      = get_post( $post_id );

	if ( $post )
	{
		$post_type = $post->post_type;
	}
	elseif ( isset( $_POST['post_type'] ) && post_type_exists( $_POST['post_type'] ) )
	{
		$post_type = $_POST['post_type'];
	}

	$func = "sl_save_{$post_type}_meta_boxes";

	if ( $post_type && function_exists( $func ) )
	{
		call_user_func( $func, $post_id );
	}
}

/**
 * Save meta boxes
 *
 * @param $post_id
 */
function sl_save_page_meta_boxes( $post_id )
{
	$fields = array(
		'layout',
		'custom_sidebar',
		'featured_header_text_content',
		'featured_header_slideshow_id',
		'featured_header_height',
		'show_old_comments',
	);
	foreach ( $fields as $field )
	{
		if ( ! empty( $_POST[$field] ) )
		{
			update_post_meta( $post_id, $field, $_POST[$field] );
		}
		else
		{
			delete_post_meta( $post_id, $field );
		}
	}

	// Checkboxes
	$fields = array(
		'custom_layout',
		'featured_header_title',
		'featured_header_text',
		'featured_header_slideshow',
		'featured_image',
		'featured_header_height_enable',
	);
	foreach ( $fields as $field )
	{
		if ( ! empty( $_POST[$field] ) )
		{
			update_post_meta( $post_id, $field, 1 );
		}
		else
		{
			update_post_meta( $post_id, $field, 0 );
		}
	}
}

/**
 * Save meta boxes
 *
 * @param $post_id
 */
function sl_save_attraction_meta_boxes( $post_id )
{
	// Text and Select fields
	$texts = array( 'address', 'address2', 'state', 'postcode', 'latitude', 'longitude' );
	foreach ( $texts as $text )
	{
		$value = ! empty( $_POST[$text] ) ? strip_tags( $_POST[$text] ) : '';
		update_post_meta( $post_id, $text, $value );
	}

	// City, area/suburb
	$locations = array( 'city', 'area' );
	foreach ( $locations as $location )
	{
		$value = ! empty( $_POST[$location] ) ? sanitize_title( $_POST[$location] ) : '';
		$term  = get_term_by( 'slug', $value, 'location' );
		if ( ! empty( $term ) && ! is_wp_error( $term ) )
		{
			update_post_meta( $post_id, $location, $term->term_id );
		}
	}
}
