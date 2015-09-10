<?php
/**
 * This class will hold all things for company management page
 */
class Solar_Company_Management extends Sl_Company_Management
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
		$cols = array();
		foreach ( $columns as $k => $v )
		{
			$cols[$k] = $v;
			if ( $k == 'account' )
				$cols['leads'] = __( '#Leads', '7listings' );
		}
		$cols['date_buy'] = __( 'Date buying leads', '7listings' );
		return $cols;
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
		$columns['leads'] = 'leads';
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
			case 'leads':
				$enabled = get_post_meta( $post_id, 'leads_enable', true );
				if ( !$enabled )
				{
					$class = 'stop';
				}
				elseif ( get_post_meta( $post_id, 'cancel_reason', true ) )
				{
					$class = 'suspended';
				}
				else
				{
					$key = date( 'm' ) . '-' . date( 'Y' );
					$total = solar_leads_count_total( $post_id, $key );
					$limit = get_post_meta( $post_id, 'leads', true );

					$class = $limit <= $total ? 'max-reached' : 'buying';
				}
				echo "<span class='$class'>" . get_post_meta( $post_id, 'leads', true ) . '</span>';
				break;
			case 'date_buy';
				if ( get_post_meta( $post_id, 'leads_paid', true ) )
					echo date( 'd/m/Y H:i', get_post_meta( $post_id, 'leads_paid', true ) );
				break;
		}
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
		// Sort by leads
		if ( ! empty( $_GET['orderby'] ) && 'leads' === $_GET['orderby'] )
		{
			$query->query_vars['orderby']  = 'meta_value_num';
			$query->query_vars['meta_key'] = 'leads';
		}
	}
}

new Solar_Company_Management( 'company' );
