<?php

/**
 * This class will hold all things for accommodation management page
 */
class Sl_Accommodation_Management extends Sl_Core_Management
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
			'cb'         => '<input type="checkbox">',
			'image'      => __( 'Image', '7listings' ),
			'title'      => __( 'Name', '7listings' ),
			'type'       => __( 'Type', '7listings' ),
			'stars'      => __( 'Stars', '7listings' ),
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
		$columns = array_merge( parent::sortable_columns( $columns ), array(
			'stars' => 'stars',
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
			case 'stars':
				$star = get_the_terms( $post_id, 'stars' );
				if ( ! is_array( $star ) )
					break;

				$icon = '<i class="dashicons dashicons-star-filled"></i>';
				$star = current( $star );
				$star = (int) $star->name;
				echo str_repeat( $icon, $star );
				break;
			case 'allocation':
				$booking = get_post_meta( $post_id, sl_meta_key( 'booking', $this->post_type ), true );
				$booking = (array) $booking;

				$total = 0;
				foreach ( $booking as $detail )
				{
					$num_depart = isset( $detail['depart'] ) ? count( $detail['depart'] ) : 1;
					$allo       = isset( $detail['allocation'] ) ? $detail['allocation'] : 0;
					$total += $num_depart * $allo;
				}
				echo $total;
				break;
			default:
				parent::show( $column, $post_id );
		}
	}

	/**
	 * Sort by taxonomy and 'star'
	 *
	 * @param array  $clauses
	 * @param object $wp_query
	 *
	 * @return array
	 */
	function posts_clauses( $clauses, $wp_query )
	{
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) && 'stars' === $wp_query->query['orderby'] )
		{
			$clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
			$clauses['where'] .= " AND (taxonomy = 'stars' OR taxonomy IS NULL)";
			$clauses['groupby'] = 'object_id';
			$clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';
		}

		return $clauses;
	}
}

new Sl_Accommodation_Management( 'accommodation' );
