<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Post extends Sl_Core
{
	/**
	 * Set default settings for this custom post type
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function default_settings( $settings )
	{
		$type = $this->post_type;

		// For homepage
		$settings = array_merge( array(
			'homepage_' . $type . '_listings_display'             => 10,
			'homepage_' . $type . '_listings_orderby'             => 'date',

			'homepage_' . $type . '_listings_featured'            => 1,
			'homepage_' . $type . '_listings_desc'                => 25,
			'homepage_' . $type . '_listings_readmore_text'       => __( 'Read more', '7listings' ),
			'homepage_' . $type . '_listings_readmore_type'       => 'button',
			'homepage_' . $type . '_listings_more_listings_text'  => __( 'See more posts', '7listings' ),
			'homepage_' . $type . '_listings_more_listings_style' => 'button',
			'homepage_' . $type . '_listings_layout'              => 'list',
			'homepage_' . $type . '_listings_columns'             => 2,
			'homepage_' . $type . '_listings_sidebar_layout'      => 'none',
		), $settings );

		// Add all widgets if they're missed
		$widgets = array(
			$type . '_listings',
		);
		foreach ( $widgets as $widget )
		{
			if ( ! in_array( $widget, $settings['homepage_order'] ) )
				$settings['homepage_order'][] = $widget;
		}
		// Check if panels are active
		$fields   = array(
			'homepage_' . $type . '_listings_active' => 0,
		);
		$settings = array_merge( $fields, $settings );

		$settings = array_merge( array(
			// Page Settings

			// Archive Headings
			$type . '_blog_title'                       => __( 'Blog', '7listings' ),
			$type . '_archive_main_description'         => __( 'Blog Description', '7listings' ),
			$type . '_category_title'                   => __( '%TERM%', '7listings' ),
			$type . '_tag_title'                        => __( '%TERM%', '7listings' ),
			$type . '_location_title'                   => __( '%TERM%', '7listings' ),

			// Archive
			$type . '_archive_display'                  => 'content',
			$type . '_archive_desc'                     => 25,
			$type . '_archive_readmore_type'            => 'button',
			$type . '_archive_readmore_text'            => __( 'Read more', '7listings' ),

			// Archive Layout
			$type . '_archive_layout'                   => 'list',
			$type . '_archive_columns'                  => 2,
			$type . '_archive_sidebar_layout'           => 'none',

			// Single
			$type . '_single_title'                     => '%TITLE%',
			$type . '_single_featured_title_image_size' => 'full',
			$type . '_nextprev'                         => 1,
			$type . '_related_excerpt_length'           => 25,
			$type . '_comment_status'                   => 1,
			$type . '_ping_status'                      => 1,

			// Single Layout
			$type . '_single_featured'                  => 1,
			$type . '_single_image_size'                => 'sl_thumb_small',
			$type . '_single_sidebar_layout'            => 'none',
		), $settings );

		return $settings;
	}

	/**
	 * Register custom taxonomies for our custom post type
	 *
	 * @return void
	 */
	function register_taxonomies()
	{
		// Location
		register_taxonomy_for_object_type( 'location', $this->post_type );
	}

	/**
	 * Add 'post' and 'page' to broadcasted post type list
	 *
	 * @param array $post_types
	 *
	 * @return array
	 */
	function add_broadcasted_post_types( $post_types )
	{
		$post_types   = (array) $post_types;
		$post_types[] = 'post';
		$post_types[] = 'page';

		return $post_types;
	}
}

new Sl_Post( 'post' );
