<?php
add_action( 'init', 'sl_register_post_types' );
add_action( 'init', 'sl_register_taxonomies', 20 );

/**
 * Define custom post type for Accommodation & Tour Listings
 *
 * @return void
 */
function sl_register_post_types()
{
	$types = sl_setting( 'listing_types' );
	if ( empty( $types ) || ! Sl_License::is_activated() )
		return;

	if ( in_array( Sl_License::license_type(), array( '7Pro', '7Network', '7Tours', '7Accommodation', '7Rental' ) ) )
	{
		$labels = array(
			'name'               => _x( 'Bookings', 'Post Type General Name', '7listings' ),
			'singular_name'      => _x( 'Booking', 'Post Type Singular Name', '7listings' ),
			'menu_name'          => __( 'Bookings', '7listings' ),
			'parent_item_colon'  => __( 'Parent Booking:', '7listings' ),
			'all_items'          => __( 'All Bookings', '7listings' ),
			'view_item'          => __( 'View Booking', '7listings' ),
			'add_new_item'       => __( 'Add New Booking', '7listings' ),
			'add_new'            => __( 'Add New', '7listings' ),
			'edit_item'          => __( 'Edit Booking', '7listings' ),
			'update_item'        => __( 'Update Booking', '7listings' ),
			'search_items'       => __( 'Search bookings', '7listings' ),
			'not_found'          => __( 'No bookings found', '7listings' ),
			'not_found_in_trash' => __( 'No bookings found in Trash', '7listings' ),
		);
		$args   = array(
			'labels'              => $labels,

			/*
			 * We don't need title, editor, etc. for booking.
			 * When insert new booking, we manually set the title and custom fields. It just works.
			 */
			'supports'            => false,
			'public'              => true,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'query_var'           => false,
		);
		register_post_type( 'booking', $args );
	}
}

/**
 * Define custom taxonomies for Accommodation & Tour Listings
 *
 * @return void
 */
function sl_register_taxonomies()
{
	$labels = array(
		'name'                       => _x( 'Locations', 'Taxonomy General Name', '7listings' ),
		'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', '7listings' ),
		'menu_name'                  => __( 'Locations', '7listings' ),
		'all_items'                  => __( 'All Locations', '7listings' ),
		'parent_item'                => __( 'Parent Location', '7listings' ),
		'parent_item_colon'          => __( 'Parent Location:', '7listings' ),
		'new_item_name'              => __( 'New Location Name', '7listings' ),
		'add_new_item'               => __( 'Add New Location', '7listings' ),
		'edit_item'                  => __( 'Edit Location', '7listings' ),
		'update_item'                => __( 'Update Location', '7listings' ),
		'separate_items_with_commas' => __( 'Separate locations with commas', '7listings' ),
		'search_items'               => __( 'Search locations', '7listings' ),
		'add_or_remove_items'        => __( 'Add or remove locations', '7listings' ),
		'choose_from_most_used'      => __( 'Choose from the most used locations', '7listings' ),
	);
	$args   = array(
		'labels'        => $labels,
		'hierarchical'  => true,
		'show_tagcloud' => false,
		'rewrite'       => array(
			'slug' => 'area',
		)
	);
	register_taxonomy( 'location', 'attraction', $args );
}
