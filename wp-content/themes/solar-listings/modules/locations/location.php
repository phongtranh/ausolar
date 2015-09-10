<?php

namespace ASQ\Location;

class Location
{
	/**
	 * Create a location then return it
	 * 
	 * @return Location Location Object
	 */
	public static function make( $data )
	{
		global $wpdb;

		if ( ! isset( $data['slug'] ) )
			$data['slug'] 		= sanitize_title( $data['name'] );
		
		if ( $data['type'] === 'suburb' && isset( $data['city_id'] ) && !isset( $data['state_id'] ) )
		{
			$state = self::parents( $data['city_id'] );
			$data['state_id'] = $state[0]->id;
		}

		$data['user_id'] 	= get_current_user_id();

		$r = $wpdb->insert( $wpdb->prefix . 'locations', $data );

		if ( $r )
		{
			$data['id'] = $wpdb->insert_id;

			return $data;
		}
			
		return false;
	}

	/**
	 * Update the location
	 * 
	 * @param  array $data  Input Data
	 * @param  array  $where Where
	 * @return int Num of affected rows
	 */
	public static function update( $data, $where = array() )
	{
		global $wpdb;

		$data['updated_at'] = date( 'Y-m-d H:i:s' );
		$data['user_id'] 	= get_current_user_id();

		if ( empty( $where ) )
			$where = array( 'id' => $data['id'] );
		
		$affected_rows = $wpdb->update( $wpdb->prefix . 'locations', $data, $where );

		return $affected_rows;

	}

	/**
	 * Check if slug exists
	 * 
	 * @param  string  $slug Slug if location
	 * 
	 * @return boolean  
	 */
	public static function is_location_exists( $slug, $type = '' )
	{
		$where = compact( 'slug' );
		if ( ! empty( $type ) )
			$where = compact( 'slug', 'type' );

		$rows = self::find( $where, false, false );
		
		return ( count( $rows ) > 0 );
	}

	public static function force_delete( $where )
	{
		global $wpdb;

		return $wpdb->delete( $wpdb->prefix . 'locations', $where );
	}

	public static function soft_delete( $where )
	{
		$location = self::find( $where, true );
		
		$data = array(
			'id' 			=> $location['id'],
			'deleted_at' 	=> date( 'Y-m-d H:i:s' )
		);

		return self::update( $data );
	}

	public static function un_soft_delete( $where )
	{
		$location = self::find( $where, true );
		
		$data = array(
			'id' 			=> $location['id'],
			'deleted_at' 	=> '0000-00-00 00:00:00'
		);

		return self::update( $data );
	}
	/**
	 * Get a location
	 * 
	 * @return 
	 */
	public static function find( $where = '', $single = false, $hide_soft_deleted = true )
	{
		global $wpdb;

		$str_where = '';
		
		if ( is_numeric( $where ) )
			$str_where = " AND id = {$where}";

		if ( is_array( $where ) )
		{
			foreach ( $where as $k => $v )
			{
				$str_where .= " AND {$k} = '{$v}'";
			}
		}
	
		if ( $hide_soft_deleted )
			$str_where .= " AND ( deleted_at IS NULL OR deleted_at = '0000:00:00 00:00:00' )";

		$query = "SELECT * FROM {$wpdb->prefix}locations WHERE 1 = 1 {$str_where}";

		if ( $single || is_numeric( $where ) )
			$results = $wpdb->get_row( $query, ARRAY_A );
		else
			$results = $wpdb->get_results( $query, ARRAY_A );

		return $results;
	}

	public static function get_postcode( $id )
	{
		$location = self::find( $id );

		return $location['postcode'];
	}

	/**
	 * Get all state
	 * 
	 * @return [type] [description]
	 */
	public static function all_states()
	{
		$data = array( 'type' => 'state' );

		return self::find( $data );
	}

	public static function childs( $id, $deep = 1 )
	{
		global $wpdb;

		if ( $deep > 2 )
			return;

		$location = self::find( $id, true );
		
		$append = '';
		if ( $location['type'] === 'state' && $deep === 1 )
			$append = " AND type = 'city'";

		if ( $location['type'] === 'state' && $deep == -1 )
			$append .= " AND type != 'city'";

		$query = "SELECT * FROM {$wpdb->prefix}locations
				  WHERE {$location['type']}_id = {$id}
				  {$append}";

		$results = $wpdb->get_results( $query );

		return $results;
	}

	public static function parents( $id, $deep = 1 )
	{
		global $wpdb;

		if ( $deep > 2 )
			return;

		$location = self::find( $id, true );
		
		$append = ' id =' . $location['state_id'];

		if ( $location['type'] === 'suburb' && $deep == 1 )
			$append = ' id = ' . $location['city_id'];
		
		if ( $location['type'] === 'suburb' && $deep == 2 )
			$append = ' id = ' . $location['city_id'] . ' OR id = ' . $location['state_id'] ;

		if ( $location['type'] === 'suburb' && $deep == -1 )
			$append = ' id = ' . $location['state_id'];
		
		// Select state and suburb
		$query = "SELECT * FROM {$wpdb->prefix}locations
					WHERE {$append}";

		return $wpdb->get_results( $query );
	}
}