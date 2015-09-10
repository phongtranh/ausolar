<?php

/**
 * Add info for images in media screen
 *
 * @since   5.0.10
 *
 * @package 7listings
 * @author  Tran Ngoc Tuan Anh <anh@7listings.net>
 */
class Sl_Media_Screen
{
	/**
	 * Run when file is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		add_filter( 'manage_media_columns', array( __CLASS__, 'columns' ), 10, 2 );
		add_action( 'manage_media_custom_column', array( __CLASS__, 'show' ), 10, 2 );
	}

	/**
	 * Replace default WordPres column 'Uploaded to' with our custom 'Uploaded to' column
	 * That allows us to change column content because WordPress does not have filter or action to do that in core
	 *
	 * @param array $columns  An array of columns displayed in the Media list table
	 * @param bool  $detached Whether the list table contains media not attached to any posts. Default true.
	 *
	 * @return array
	 */
	public static function columns( $columns, $detached )
	{
		// Delete default WordPress column
		if ( isset( $columns['parent'] ) )
			unset( $columns['parent'] );

		// Add custom column with same name
		$columns['sl_parent'] = _x( 'Uploaded to', 'column name', '7listings' );

		return $columns;
	}

	/**
	 * Display column content in Media screen
	 *
	 * @param string $column_name Name of the custom column.
	 * @param int    $post_id     Attachment ID.
	 *
	 * @return void
	 */
	public static function show( $column_name, $post_id )
	{
		if ( 'sl_parent' != $column_name )
			return;

		// Check if image is used by theme in various locations
		// Each check will be a separate method of the class
		$text   = array();
		$checks = array(
			'check_parent',
			'check_design',
			'check_taxonomy_image',
			'check_page_featured_image',
		);
		foreach ( $checks as $check )
		{
			$text = array_merge( $text, self::$check( $post_id ) );
		}
		if ( $text )
		{
			echo implode( '<br>', $text );

			return;
		}

		// If image is not attached, show attach link
		_e( '(Unattached)', '7listings' );
		echo '<br>';
		if ( current_user_can( 'edit_post', $post_id ) )
		{
			?>
			<a class="hide-if-no-js" href="#the-list" onclick="findPosts.open( 'media[]','<?php echo $post_id ?>' ); return false;"><?php _e( 'Attach', '7listings' ); ?></a>
		<?php
		}
	}

	/**
	 * Check if image is attached to a parent post/page
	 * Display as WordPress does
	 *
	 * @param int $post_id Attachment ID
	 *
	 * @return array List of text returned to display
	 */
	public static function check_parent( $post_id )
	{
		global $post;

		$text   = array();
		$parent = $post->post_parent ? get_post( $post->post_parent ) : false;
		if ( ! $parent )
			return $text;

		$title       = _draft_or_post_title( $post->post_parent );
		$parent_type = get_post_type_object( $parent->post_type );

		$output = $title;
		if ( $parent_type && $parent_type->show_ui && current_user_can( 'edit_post', $post->post_parent ) )
			$output = sprintf( '<strong><a href="%s">%s</a></strong>', get_edit_post_link( $post->post_parent ), $title );

		$output .= ', ' . get_the_time( __( 'Y/m/d', '7listings' ) );
		$text[] = $output;

		return $text;
	}

	/**
	 * Check if image is used in design settings
	 *
	 * @param int $post_id Attachment ID
	 *
	 * @return array List of text locations
	 */
	public static function check_design( $post_id )
	{
		$text      = array();
		$locations = array(
			'design_header_background_image_id'        => __( 'Design: Header Background', '7listings' ),
			'logo'                                     => __( 'Design: Logo', '7listings' ),
			'design_featured_background_image_id'      => __( 'Design: Featured Title Background', '7listings' ),
			'design_footer_top_background_image_id'    => __( 'Design: Footer Top Background', '7listings' ),
			'design_footer_middle_background_image_id' => __( 'Design: Footer Middle Background', '7listings' ),
			'design_footer_bottom_background_image_id' => __( 'Design: Footer Bottom Background', '7listings' ),
			'design_background_image_id'               => __( 'Design: Background', '7listings' ),
			'design_mobile_logo'                       => __( 'Design: Mobile Logo', '7listings' ),
			'favicon'                                  => __( 'Design: Favicon', '7listings' ),
		);
		foreach ( $locations as $setting => $info )
		{
			if ( sl_setting( $setting ) == $post_id )
				$text[] = $info;
		}

		return $text;
	}

	/**
	 * Check if image is used for taxonomy image
	 *
	 * @param int $post_id Attachment ID
	 *
	 * @return array List of taxonomy terms use the image
	 */
	public static function check_taxonomy_image( $post_id )
	{
		$text      = array();
		$term_meta = sl_setting( 'term_meta' );
		foreach ( $term_meta as $term_id => $meta )
		{
			$term = null;
			foreach ( $meta as $key => $value )
			{
				if ( $post_id != $value )
					continue;

				// Get term object, but get only once
				if ( null === $term )
				{
					$term = self::get_term_by_id( $term_id );
					if ( ! $term || is_wp_error( $term ) )
						break;
				}

				switch ( $key )
				{
					case 'thumbnail_id':
						$text[] = sprintf( '<strong><a href="%s">%s</a></strong> ' . __( '(Thumbnail)', '7listings' ), get_edit_term_link( $term_id, $term->taxonomy ), $term->name );
						break;
					case 'icon_id':
						$text[] = sprintf( '<strong><a href="%s">%s</a></strong> ' . __( '(Map Marker)', '7listings' ), get_edit_term_link( $term_id, $term->taxonomy ), $term->name );
						break;
				}
			}
		}

		return $text;
	}

	/**
	 * Check if image is used as featured image
	 *
	 * @param int $post_id Attachment ID
	 *
	 * @return array List of pages use the image as featured image
	 */
	public static function check_page_featured_image( $post_id )
	{
		global $post, $wpdb;

		$text = array();

		// If image has parent post/page, just ignore
		if ( $post->post_parent )
			return $text;

		$pages = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_thumbnail_id' AND meta_value='%d'", $post_id ) );
		if ( ! $pages )
			return $text;

		foreach ( $pages as $page )
		{
			$title  = _draft_or_post_title( $page );
			$text[] = sprintf( '<strong><a href="%s">%s</a></strong>', get_edit_post_link( $page ), $title );
		}

		return $text;
	}

	/**
	 * Helper function to get term by ID without knowing it's taxonomy
	 *
	 * @param int    $term_id
	 * @param string $output
	 * @param string $filter
	 *
	 * @return mixed|null|\WP_Error
	 */
	public static function get_term_by_id( $term_id, $output = OBJECT, $filter = 'raw' )
	{
		global $wpdb;

		$taxonomy = $wpdb->get_var( $wpdb->prepare( "SELECT taxonomy FROM $wpdb->term_taxonomy WHERE term_id = %s LIMIT 1", $term_id ) );

		return get_term( $term_id, $taxonomy, $output, $filter );
	}
}
