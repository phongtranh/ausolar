<?php

/**
 * This class will hold all things for management page
 */
class Sl_Core_Management extends Peace_Post_Management
{
	/**
	 * Constructor
	 *
	 * @param string $post_type Post type
	 */
	function __construct( $post_type )
	{
		$this->post_type = $post_type;
		parent::__construct();
	}

	/**
	 * Enqueue scripts and styles for management page
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'sl-listing-list', THEME_JS . 'admin/listing-list.js', array( 'jquery' ) );
		$params = array(
			'nonce_change_featured' => wp_create_nonce( 'change-featured' ),
			'post_type'             => $this->post_type . 's',
		);
		wp_localize_script( 'sl-listing-list', 'Sl_List', $params );
	}

	/**
	 * Change the columns for the edit screen
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function columns( $columns )
	{
		$columns = array(
			'cb'         => '<input type="checkbox">',
			'image'      => __( 'Image', '7listings' ),
			'title'      => __( 'Name', '7listings' ),
			'type'       => __( 'Type', '7listings' ),
			'price'      => __( 'Price', '7listings' ),
			'allocation' => __( 'Allocation', '7listings' ),
			'featured'   => __( 'Featured', '7listings' ),
			'users'      => __( 'Users', '7listings' ),
			'date'       => __( 'Date', '7listings' ),
		);

		return $columns;
	}

	/**
	 * Make columns sortable
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function sortable_columns( $columns )
	{
		$columns = array_merge( $columns, array(
			'featured' => 'featured',
			'price'    => 'price',
		) );

		return $columns;
	}

	/**
	 * Show the columns for the edit screen
	 *
	 * @param string $column
	 * @param int    $post_id
	 *
	 * @return void
	 */
	function show( $column, $post_id )
	{
		/**
		 * Allow other modules to change the content of management column
		 * This is used in (at least) bundle plugin
		 *
		 * @param string $output  The content of the column
		 * @param string $column  Column ID
		 * @param int    $post_id Post (booking ID)
		 * @return string The content of the column
		 */
		if ( $output = apply_filters( $this->post_type . '_management_column', '', $column, $post_id ) )
		{
			echo $output;
			return;
		}

		switch ( $column )
		{
			case 'image':
				sl_broadcasted_thumbnail( 'sl_thumb_tiny', array(), $post_id );
				break;
			case 'featured':
				$featured = ( int ) get_post_meta( $post_id, 'featured', true );
				$featured = $featured > 2 ? 0 : $featured;
				if ( 2 == $featured )
				{
					$class = 'star dashicons dashicons-star-filled';
				}
				elseif ( 1 == $featured )
				{
					$class = 'featured dashicons dashicons-yes';
				}
				else
				{
					$class = 'dashicons dashicons-no';
				}
				echo "<span class='$class' data-post_id='$post_id' data-featured='$featured'></span>";
				break;
			case 'users':
				$post = get_post( $post_id );
				the_author_meta( 'user_nicename', $post->post_author );
				break;
			case 'price':
				$price = get_post_meta( $post_id, 'price_from', true );
				echo Sl_Currency::format( $price, 'type=plain' );
				break;
			case 'type':
				$type = wp_get_post_terms( $post_id, sl_meta_key( 'tax_type', $this->post_type ), array( 'fields' => 'names' ) );
				echo is_array( $type ) ? reset( $type ) : '';
				break;
			case 'allocation':
				$booking = get_post_meta( $post_id, sl_meta_key( 'booking', $this->post_type ), true );
				$booking = (array) $booking;

				$total = 0;
				foreach ( $booking as $detail )
				{
					$allocation = isset( $detail['allocation'] ) ? $detail['allocation'] : 0;
					$total += $allocation;
				}
				echo $total;
				break;
		}
	}

	/**
	 * Filter the request to just give posts for the given taxonomy, if applicable.
	 *
	 * @return void
	 */
	function show_filters()
	{
		$type = sl_meta_key( 'tax_type', $this->post_type );
		wp_dropdown_categories( array(
			'show_option_all' => __( 'Show all types', '7listings' ),
			'taxonomy'        => $type,
			'name'            => $type,
			'orderby'         => 'name',
			'selected'        => isset( $_GET[$type] ) ? $_GET[$type] : '',
			'hierarchical'    => false,
			'show_count'      => false,
			'hide_empty'      => false,
		) );
		$featured = isset( $_GET['featured'] ) ? intval( $_GET['featured'] ) : - 1;
		echo '<select name="featured">';
		Sl_Form::options( $featured, array(
			- 1 => __( 'Show all featured', '7listings' ),
			0   => __( 'Non-featured', '7listings' ),
			1   => __( 'Featured', '7listings' ),
			2   => __( 'Star', '7listings' ),
		) );
	}

	/**
	 * Add taxonomy filter when request posts (in screen)
	 *
	 * @param WP_Query $query
	 *
	 * @return mixed
	 */
	function filter( $query )
	{
		global $wpdb;

		$vars = &$query->query_vars;

		// Filter by type
		$type = sl_meta_key( 'tax_type', $this->post_type );
		if ( ! empty( $vars[$type] ) )
		{
			$term_id = intval( $vars[$type] );
			if ( $term_id )
			{
				$term        = get_term_by( 'id', $term_id, $type );
				$vars[$type] = $term->slug;
			}
		}

		// Filter by featured
		if ( isset( $_GET['featured'] ) )
		{
			$featured = intval( $_GET['featured'] );
			if ( ! $featured )
			{
				$featured_posts       = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='featured' AND ( meta_value=1 OR meta_value=2 )" );
				$vars['post__not_in'] = $featured_posts;
			}
			elseif ( - 1 != $featured )
			{
				$vars['meta_key']   = 'featured';
				$vars['meta_value'] = $featured;
			}
		}

		// Sort by price
		if ( ! empty( $_GET['orderby'] ) && 'price' == $_GET['orderby'] )
		{
			$vars['orderby']  = 'meta_value_num';
			$vars['meta_key'] = 'price_from';
		}

		// Sort by featured
		if ( ! empty( $_GET['orderby'] ) && 'featured' == $_GET['orderby'] )
		{
			$vars['orderby']  = 'meta_value_num';
			$vars['meta_key'] = 'featured';
		}
	}
}
