<?php

namespace ASQ\Location;

class Init
{
	public function __construct()
	{
		//add_action( 'after_setup_theme', array( $this, 'run_command' ) );
		add_action( 'wp_ajax_solar_fill_location', array( $this, 'ajax_fill_location' ), 1 );
		add_action( 'wp_ajax_nopriv_solar_fill_location', array( $this, 'ajax_fill_location' ), 1 );

		add_action( 'wp_ajax_autocomplete_location', array( $this, 'ajax_autocomplete_location' ) );
		add_action( 'wp_ajax_nopriv_autocomplete_location', array( $this, 'ajax_autocomplete_location' ) );
	}

	public function ajax_fill_location()
	{
		ob_clean();

		if ( empty( $_POST['postcode'] ) )
			wp_send_json_error();

		$postcode = trim( $_POST['postcode'] );

		// Todo: Merge three queries below to 1 query
		$suburbs = Location::find( array( 'postcode' => $postcode, 'type' => 'suburb' ) );

		$sus = array();
		foreach( $suburbs as $suburb )
			$sus[$suburb['id']] = $suburb['name'];

		$city 	= Location::find( array( 'id' => $suburb['city_id'], 'type' => 'city' ), true );

		$state 	= Location::find( array( 'id' => $suburb['state_id'], 'type' => 'state' ), true );

		if ( empty( $suburb ) )
			wp_send_json_error();

		$data = array(
			'state' => array(
				'id' 	=> $state['id'],
				'name' 	=> $state['name'],
				'desc'	=> $state['description']
			),
			'city' => array(
				'id' 	=> $city['id'],
				'name'	=> $city['name']
			),
			'suburb' => $sus
		);

		wp_send_json_success( $data );
	}
	
	public function run_command()
	{
		// $_POST['postcode'] = '4220';

		// self::ajax_fill_location();
	}

	public function ajax_autocomplete_location()
	{
		global $wpdb;

		ob_clean();

		// If cache doesn't exists. Make a new cache with all locations order by alphabet.
		// Otherwise, just return it.
		$cache_file = get_stylesheet_directory() . '/files/locations.cache.php';

		if ( ! file_exists( $cache_file ) )
		{
		    $locations = $wpdb->get_results( "SELECT id, name, city_id, state_id, description, type FROM asq_locations ORDER BY name, state_id", OBJECT_K );

		    $cache = array();

		    foreach ( $locations as $location )
		    {
		        $cache[$location->id] = $location->name;

		        if ( ! empty( $location->state_id ) )
		            $cache[$location->id] .= ', ' . $locations[$location->state_id]->description;
		    }

		    $cache = array_unique( $cache );

		    file_put_contents( $cache_file, serialize( $cache ) );
		}
		else
		{
		    $cache = unserialize( file_get_contents( $cache_file ) );
		}

		$keyword = trim( $_GET['location'] );

		$i = 0;
		$matches = array();

		foreach ( $cache as $area )
		{
			if ( str_contains( $area, $keyword, false ) )
			{
				$i++;
				$matches[] = $area;
			}

			// Stop lookup when found 15 items. For better performance
			if ( $i === 15 ) break;
		}

		wp_send_json_success( $matches );
	}
}

new Init;
