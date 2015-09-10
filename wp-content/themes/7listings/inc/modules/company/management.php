<?php

/**
 * This class will hold all things for company management page
 */
class Sl_Company_Management extends Sl_Core_Management
{
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
			'cb'       => '<input type="checkbox">',
			'image'    => __( 'Image', '7listings' ),
			'title'    => __( 'Name', '7listings' ),
			'rating'   => __( 'Rating', '7listings' ),
			'account'  => __( 'Account', '7listings' ),
			'featured' => __( 'Featured', '7listings' ),
			'state'    => __( 'State', '7listings' ),
			'city'     => __( 'City', '7listings' ),
			'users'    => __( 'Users', '7listings' ),
			'date'     => __( 'Date', '7listings' ),
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
			'state'    => 'state',
			'city'     => 'city',
			//			'account'  => 'account',
			'featured' => 'featured',
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
		switch ( $column )
		{
			case 'state':
				echo get_post_meta( $post_id, 'state', true );
				break;
			case 'city':
				echo get_post_meta( $post_id, 'city', true );
				break;
			case 'account':
				$user_id    = get_post_meta( $post_id, 'user', true );
				$membership = get_user_meta( $user_id, 'membership', true );
				if ( ! $membership )
					$membership = 'none';
				$title = ucwords( $membership );
				echo "<span class='member-$membership' title='$title'></span>";
				break;
			case 'users':
				$user_id = get_post_meta( $post_id, 'user', true );
				if ( ! $user_id )
				{
					_e( 'No owner', '7listings' );
				}
				else
				{
					$user = get_userdata( $user_id );
					$name = $user->user_nicename;
					if ( $user->user_firstname && $user->user_lastname )
						$name = "$user->user_firstname $user->user_lastname";
					echo $name;
				}
				break;
			case 'rating':
				$star = intval( Sl_Company_Helper::get_average_rating( $post_id ) );
				$icon = '<i class="dashicons dashicons-star-filled"></i>';
				echo str_repeat( $icon, $star );
				break;

				break;
			default:
				parent::show( $column, $post_id );
		}
	}

	/**
	 * Filter the request to just give posts for the given taxonomy, if applicable.
	 *
	 * @return void
	 */
	function show_filters()
	{
		$featured = isset( $_GET['featured'] ) ? intval( $_GET['featured'] ) : - 1;
		echo '<select name="featured">';
		Sl_Form::options( $featured, array(
			- 1 => __( 'Show all featured', '7listings' ),
			0   => __( 'Non-featured', '7listings' ),
			1   => __( 'Featured', '7listings' ),
		) );

		echo '</select>';
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
		// Sort by account
		if ( ! empty( $_GET['orderby'] ) && 'account' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value_num';
			$query->query_vars['meta_key'] = 'account';
		}

		// Sort by state
		elseif ( ! empty( $_GET['orderby'] ) && 'state' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value';
			$query->query_vars['meta_key'] = 'state';
		}

		// Sort by city
		elseif ( ! empty( $_GET['orderby'] ) && 'city' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value';
			$query->query_vars['meta_key'] = 'city';
		}

		// Default filter
		else
		{
			parent::filter( $query );
		}
	}
}

new Sl_Company_Management( 'company' );
