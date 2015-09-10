<?php

// Utility functions
if ( ! function_exists( 'array_fetch' ) )
{
	/**
	 * Create key/value array from provided array
	 *
	 * @param  mixed  $array Source array
	 * @param  string $key   key
	 * @param  string $value value
	 *
	 * @return array array with key / value pairs
	 */
	function array_fetch( $array, $key, $value = '' )
	{
		// Cast if is object
		$array = (array) $array;

		if ( is_array( $array ) && isset( $array[$key] ) )
			return array( $key => $array[$key], $value => $array[$value] );

		$output = array();

		foreach ( $array as $node )
			$output[] = array_fetch( $output, $key, $value );

		return $output;
	}
}


if ( ! function_exists( 'str_title' ) )
{
	function str_title( $str )
	{
		$str = str_replace( array( '_', '-' ), ' ', $str );

		return mb_convert_case( $str, MB_CASE_TITLE, 'UTF-8' );
	}
}

if ( ! function_exists( 'str_contains' ) )
{
	/**
	 * Determine if a given string contains a given substring.
	 *
	 * @param  string  $haystack String to check
	 * @param  string|array  $needles
	 * @param bool $case_sensitive
	 * @return bool
	 */
	function str_contains( $haystack, $needles, $case_sensitive = true )
	{
		foreach ( (array) $needles as $needle )
		{
			if ( ! $case_sensitive )
			{
				$haystack 	= strtolower( $haystack );
				$needle		= strtolower( $needle );
			}

			if ( $needle != '' && strpos( $haystack, $needle ) !== false )
				return true;
		}

		return false;
	}
}

if ( ! function_exists( 'url_contains') )
{
	function url_contains( $needles )
	{
		return str_contains( $_SERVER['REQUEST_URI'], $needles, false );
	}
}

if ( ! function_exists( 'starts_with' ) )
{
	/**
	 * Determine if a given string starts with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function starts_with( $haystack, $needles )
	{
		foreach ((array) $needles as $needle)
		{
			if ($needle != '' && strpos($haystack, $needle) === 0) return true;
		}
		return false;
	}
}

if ( ! function_exists( 'ends_with' ) )
{
	/**
	 * Determine if a given string ends with a given substring.
	 *
	 * @param  string  $haystack
	 * @param  string|array  $needles
	 * @return bool
	 */
	function ends_with( $haystack, $needles )
	{
		foreach ((array) $needles as $needle)
		{
			if ((string) $needle === substr($haystack, -strlen($needle))) return true;
		}

		return false;
	}
}

if ( ! function_exists( 'sp' ) )
{
	function sp( $object )
	{
		if ( isset( $_GET['db'] ) )
		{
			p( $object );
		}
	}
}

if ( ! function_exists( 'str_snake' ) )
{
    function str_snake( $str )
    {
        $str = sanitize_title( $str );

        return str_replace( '-', '_', $str );
    }
}

/**
 * Put an array. Then make the key same as value. Or key will become slug of value of slug is set to true
 * 
 * @param  Array  $array Array to convert
 * @param  boolean $slug  Slug the key or not
 * @return Array output
 */
function array_symmetry( $array, $slug = false )
{
	$output = array();

	foreach ( (array) $array as $key => $value )
	{
		$output_key = $slug ? str_snake( $value ) : $value;

		$output[$output_key] = $value;
	}

	return $output;
}

if ( ! function_exists( 'str_random' ) )
{
	function str_random( $length = 16 )
	{
		$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr( str_shuffle( str_repeat( $pool, $length ) ), 0, $length );
	}
}

if ( ! function_exists( 'csv_to_array' ) )
{
	/**
	 * Convert a comma separated file into an associated array.
	 * The first row should contain the array keys.
	 *
	 * Example:
	 *
	 * @param string $filename Path to the CSV file
	 * @param string $delimiter The separator used in the file
	 * @return array
	 * @link http://gist.github.com/385876
	 * @author Jay Williams <http://myd3.com/>
	 * @copyright Copyright (c) 2010, Jay Williams
	 * @license http://www.opensource.org/licenses/mit-license.php MIT License
	 */
	function csv_to_array( $filename = '', $delimiter = ',' )
	{
		if ( ! file_exists( $filename ) || ! is_readable( $filename ) )
			return FALSE;

		$header = NULL;

		$data = array();

		if ( ( $handle = fopen( $filename, 'r' ) ) !== FALSE )
		{
			while ( ( $row = fgetcsv($handle, 1000, $delimiter ) ) !== FALSE )
			{
				if ( ! $header )
					$header = $row;
				else
					$data[] = array_combine( $header, $row );
			}

			fclose( $handle );
		}

		return $data;
	}
}

if ( ! function_exists( 'p' ) )
{
	// Pretty print_r and die
	function p( $object )
	{
		echo '<pre>';
		print_r( $object );
		exit;
	}
}

if ( ! function_exists( 'array_swap' ) )
{
	/**
	 * This function works like array_flip but allows key => value which value is an array
	 * @param  Array $array Array to be swapped
	 * @return Array array output
	 */
	function array_swap( array $array )
	{
		$swapped = array();

		foreach ( $array as $key => $nested )
		{
			foreach ( (array) $nested as $index => $value )
			{
				$swapped[$value] = $key; 
			}
		}

		return $swapped;
	}
}

function get_company_id_by_meta( $meta_key, $meta_value )
{
	global $wpdb;

	$meta_value = trim( $meta_value );

	$query = "
		SELECT DISTINCT(post_id) FROM asq_postmeta
		WHERE meta_key = '{$meta_key}'
		AND meta_value LIKE '%{$meta_value}%'
	";

	return $wpdb->get_col( $query );
}

/**
 * This function runs when users enter data to the search fields of installers directory
 * 
 * @param  String $name     Search by name
 * @param  String $location Search by location
 * 
 * @return array Companies ID
 */
function asq_directory_search( $name, $location )
{
	$name 		= trim( $name );
	$location 	= trim( $location );

	$companies_id = [];
	
	if ( ! empty( $name ) )
	{
		$companies_by_name 	= asq_search_companies_by_name( $name );

		$companies_id 		= $companies_by_name;
	}

	if ( ! empty( $location ) )
	{
		if ( intval( $location ) > 0 && ! str_contains( $location, ',' ) )
			$postcodes = $location;
		else
			$postcodes = asq_get_cities_postcodes( $location );

		$companies_by_location 	= asq_search_companies_by_postcodes( $postcodes );

		$companies_id 			= $companies_by_location;
	}

	if ( ! empty( $companies_by_name ) && ! empty( $companies_by_location ) )
		$companies_id = array_intersect( $companies_by_name, $companies_by_location );

	$companies_id = asq_sort_companies( $companies_id );
	
	return $companies_id;
}

/**
 * Search companies by name. It will look at their post_title or also_known_as field
 * 
 * @param  String $name Company name to search
 * @return array List of matched companies
 */
function asq_search_companies_by_name( $name )
{
	global $wpdb;

	$keyword = $wpdb->esc_like( $name );

	// Convert 'Google Chrome' to '%Google%Chrome%'
	$keyword = asq_better_search_keyword( $keyword );

	$q = "
		SELECT DISTINCT(ID) FROM asq_posts
		WHERE post_type = 'company'
		AND post_status NOT IN ( 'draft', 'trash' )
		AND post_title LIKE %s
	";

	$companies_by_title = $wpdb->get_col( $wpdb->prepare( $q, $keyword ) );

	$q = "
		SELECT DISTINCT(post_id) FROM asq_postmeta
		WHERE meta_key = 'also_known_as'
		AND meta_value LIKE %s
	";

	$also_known_as 		= $wpdb->get_col( $wpdb->prepare( $q, $keyword ) );

	return array_unique( array_merge( $companies_by_title, $also_known_as ) );
}

function asq_search_companies_by_postcodes( $postcodes )
{
	global $wpdb;

	$nearby_postcodes = $postcodes;

	if ( ! str_contains( $postcodes, ',' ) )
	{
		// Search all nearby postcodes
		$postcode = asq_better_search_keyword( $postcodes );
		$q = $wpdb->prepare( "
			SELECT cached_postcodes FROM asq_locations
			WHERE type='city' 
			AND cached_postcodes LIKE %s
		", $postcode );

		$nearby_postcodes = $wpdb->get_var( $q );
	}

	// Search companies by postcode
	$q = "
		SELECT DISTINCT(post_id) FROM asq_postmeta
		WHERE meta_key = 'postcode'
		AND meta_value IN($nearby_postcodes)
	";

	$companies_by_postcode = $wpdb->get_col( $q );
	
	// Search companies by service locations
	$meta_value_like = '';
	$nearby_postcodes = explode( ',', $nearby_postcodes );
	foreach ( $nearby_postcodes as $postcode )
		$meta_value_like .= " OR meta_value LIKE '%$postcode%'";
	
	$meta_value_like = ltrim( $meta_value_like, ' OR' );
	
	$q = "
		SELECT DISTINCT(post_id) FROM asq_postmeta
		WHERE meta_key = 'company_service_location'
		AND ( $meta_value_like )
	";

	$companies_by_service_location = $wpdb->get_col( $q );

	return array_unique( array_merge( $companies_by_postcode, $companies_by_service_location ) );
}

/**
 * Put a list of string separated by commas and remove duplicated items.
 * 
 * @param  String $str String input
 * 
 * @return String String which has removed duplicated items
 */
function str_unique( $str )
{
	if ( ! str_contains( $str, ',' ) )
		return $str;

	$array = array_unique( explode( ',', $str ) );

	return join( ',', $array );
}

/**
 * Get postcode and nearby postcodes of a location
 * 
 * @param  String $address Address, can be a suburb or city. Can combine with State by separating by comma
 * 
 * @return String Postcodes, separated by commas
 */
function asq_get_cities_postcodes( $address )
{
	global $wpdb;

	$area = $address;

	// That means, users combine state in their search. For example, "Brisbane, QLD"
	if ( str_contains( $address, ',' ) )
	{
		$addresses 	= explode( ',', $address );
		$area 		= $addresses[0];
		$state 		= trim( strtolower( $addresses[1] ) );

		// Hardcode the state id from asq_locations table
		$states = array(
			'act' 	=> 1,
			'qld'	=> 157,
			'nsw'	=> 4099,
			'vic'	=> 12049,
			'sa'	=> 15944,
			'tas'	=> 18568,
			'nt'	=> 19528,
			'wa'	=> 19887
		);
	}

	$area = asq_better_search_keyword( $area );

	// Append state_id = $state when there're state in their search query
	$append_state = ( isset( $state) ) ? ' AND state_id = ' . $states[$state] : '';

	$q = $wpdb->prepare( "SELECT id, city_id FROM asq_locations WHERE name LIKE %s AND type IN('suburb', 'city'){$append_state}", $area );
	
	$locations = $wpdb->get_results( $q );

	$city_ids = array();

	// If its a city, just get it ID, otherwise, get city_id
	foreach ( $locations as $location )
	{
		if ( isset( $location->city_id ) ) // Its a postcode
			$city_ids[] = $location->city_id;
		else
			$city_ids[] = $location->id; // Its a city
	}

	$city_ids = join( ',', array_unique ( $city_ids ) );

	$q = "SELECT cached_postcodes FROM asq_locations WHERE id IN ($city_ids)";

	$postcodes = $wpdb->get_col( $q );

	return str_unique( join( ',', $postcodes ) );
}

/**
 * Sort companies by their membership and leads enable
 * 
 * @param  Mixed $companies_id an array of companies id or a list separated by commas
 * 
 * @return Array Collection of companies which sorted
 */
function asq_sort_companies( $companies_id )
{
	global $wpdb, $cached_average_reviews;

	$rank = [];

	// Rank: None = 0, Bronze = 1, Silver = 2, Gold = 3
	$membership_ranks = array_swap( [ '', 'bronze', 'silver', 'gold' ] );

	if ( ! is_array( $companies_id ) )
		$companies_id = explode( ',', $companies_id );

	foreach ( $companies_id as $company_id )
	{
		$rank[$company_id] = 0;

		// Get a little bit higher rank if they're buying leads. Max +.5
		if ( get_post_meta( $company_id, 'leads_enable', true ) )
			$rank[$company_id] += 0.5;

		$owner_id = get_post_meta( $company_id, 'user', true );
		if ( empty( $owner_id ) ) 
			continue;

		$membership = get_user_meta( $owner_id, 'membership', true );
		if ( empty( $membership ) )
			continue;
		else
			$rank[$company_id] += $membership_ranks[$membership];

		// Get higher ranking if they get better rating (max: +0.5)
		$average_review 	= Sl_Company_Helper::get_average_rating( $company_id );
		
		$cached_average_reviews[$company_id] = $average_review;

		$num_of_ratings = Sl_Company_Helper::get_no_reviews( $company_id );
		$rank[$company_id] += $average_review / 10;
		$rank[$company_id] += $num_of_ratings / 1000;
	}

	// Sort by rank. Higher to lower
	arsort( $rank );
	
	return array_keys( $rank );
}

/**
 * Break long keyword into each keyword to search.
 *
 * @param  String $keyword Keyword to optimize
 *
 * @return String The keyword has optimized
 */
function asq_better_search_keyword( $keyword )
{
	$keywords = explode( ' ', $keyword );

	return '%' . implode( '%', $keywords ) . '%';
}

/**
 * Get payment method of company
 *
 * @param  int    $company_id ID of company
 * @return String payment method
 */
function solar_get_company_payment_method( $company_id, $title_friendly = true )
{
	$payment_method = get_post_meta( $company_id, 'leads_payment_type', true );

	// Todo: Log this error;
	// if ( in_array( $payment_method, solar_get_payment_methods( false ) ) )

	if ( $title_friendly )
	{
		$payment_methods = solar_get_payment_methods();

		return $payment_methods[$payment_method];
	}

	return $payment_method;
}

/**
 * Users cannot delete leads
 */
add_action( 'gform_delete_lead', function( $lead_id )
{
	// Todo: Allows Admin ID = 3 (Anh) deleting leads.
	// Remember to log old value before delete.
	die( __( 'You cannot delete lead!', '7listings' ) );
} );

/**
 * Get Suppliers Sources
 *
 * @param string $code Code, one character. If not provided. It will returns all suppliers.
 * @param string $field Field, get title or slug of this supplier?
 * @return array Suppliers
 */
function asq_get_supplier_sources( $code = '', $field = '' )
{
	$suppliers = get_posts( [
		'post_type' 		=> 'wholesale',
		'post_status' 		=> 'publish',
		'posts_per_page' 	=> -1
	] );

	$sources = [];

	foreach ( $suppliers as $supplier )
	{
		$supplier_code 						= get_post_meta( $supplier->ID, 'wholesale_code', true );
		$sources[$supplier_code]['title'] 	= $supplier->post_title;
		$sources[$supplier_code]['slug']	= $supplier->post_name;
	}

	if ( ! empty( $code ) )
	{

		if ( ! empty( $field ) && in_array( $field, ['title', 'slug'] ) )
			return $sources[$supplier_code][$field];

		return $sources[$supplier_code];
	}

	return $sources;
}

/**
 * Get logo of company
 * 
 * @param  int $company_id Company ID
 * @return String thumbnail URL
 */
function asq_get_company_logo( $company_id )
{
	if ( ! is_numeric( $company_id ) || $company_id < 0 )
		return '';

	$logo_by_logo_meta = function_exists( 'sl_broadcasted_image_src' ) ? sl_broadcasted_image_src( sl_meta_key( 'logo', get_post_type( $company_id ) ), 'full' ) : '';
	
	if ( has_post_thumbnail( $company_id ) )
	{
		$logo = wp_get_attachment_image_src( get_post_thumbnail_id(  $company_id ), 'single-post-thumbnail' );
		return $logo[0];
	}
	else if ( ! empty( $logo_by_logo_meta ) )
	{

		return $logo_by_logo_meta;
	}
	else 
	{
		return sl_broadcasted_image_src( sl_meta_key( '_thumbnail_id', get_post_type( $company_id ) ), 'full' );
	}
}

function asq_is_company_has_logo( $company_id )
{
	// Check the custom field is exists
}

/**
 * Convert Postcode to LatLng data
 * 
 * @param  Int $postcode Postcode
 * 
 * @return String Lat,Long
 */
function asq_postcode_to_latlng( $postcode = null )
{
	$postcodes_latlng = get_option( 'postcodes_latlng' );

	if ( isset( $postcode ) )
		return $postcodes_latlng[$postcode];

	return $postcodes_latlng;
}

/**
 * Put state, if it's shortname, then return full name, and vice versa.
 * 
 * @param  String $state State name
 * @return String
 */
function asq_states_format( $state = '' )
{
	$states = [
		'QLD' 	=> 'Queensland',
		'VIC' 	=> 'Victoria',
		'SA' 	=> 'South Australia',
		'TAS' 	=> 'Tasmania',
		'ACT' 	=> 'Australian Capital Territory',
		'WA'	=> 'Western Australia',
		'NT'	=> 'Northern Territory',
		'NSW'   => 'New South Wales'
	];

	if ( empty( $state ) )
		return $states;
	
	if ( empty( $states[$state] ) ) 
	{
		$states = array_swap( $states );
		
		if ( empty( $states[$state] ) )
			return false;
	}

	return $states[$state];
}

/**
 * Explode a string by multiple delimeters at a time.
 *
 * @param String $string String to be explode
 * @param Array $delimeters Optional. Default: ,; |:/
 *
 * @return  Array Array has exploded
 */
function multi_explode( $string, $delimiters = [',', ';', ' ', '|', ':', '/'] ) 
{
    $output = [];

    $ready = str_replace( $delimiters, $delimiters[0], $string );
    
    $array = explode( $delimiters[0], $ready );
    
    foreach ( $array as $var )
    {
        if ( ! empty( $var ) )
        {
            $output[] = trim( $var );
        }
    }

    return  $output;
}

/**
 * Input number and percent, get the output
 * 
 * @param  Double $number  Number to get percent
 * @param  Int $percent Percent to get (optional) (default: null)
 * 
 * @return Mixed Output number
 */
function numeric_percent( $number, $percent = null )
{
	$output = [];

	if ( is_numeric( $percent ) )
	{
		$output = floor( $number * $percent / 100 );
	}
	else if ( is_array( $percent ) )
	{
		foreach ( $percent as $n ) 
		{
			if ( intval( $n ) > 0 )
				$output[$n] = floor( $number * $n / 100 );
			
			if ( $n === 100 )
				$output[100] = $number;
		}
	}

	return $output;
}