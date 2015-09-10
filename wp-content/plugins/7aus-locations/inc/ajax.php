<?php
namespace Sl\Locations\Aus;

/**
 * This class handles ajax action to process data
 *
 * @package    Sl
 * @subpackage Sl\Locations\Aus
 */
class Ajax
{
	/**
	 * Number of rows being processed in 1 call
	 * @var int
	 */
	public static $threshold = 100;

	/**
	 * Short names of Australian states
	 * @var array
	 */
	public static $states = array(
		'ACT' => 'Australian Capital Territory',
		'QLD' => 'Queensland',
		'NSW' => 'New South Wales',
		'VIC' => 'Victoria',
		'SA'  => 'South Australia',
		'TAS' => 'Tasmania',
		'NT'  => 'Northern Territory',
		'WA'  => 'Western Australia',
	);

	/**
	 * Add hooks for ajax callbacks when class is loaded
	 *
	 * @return void
	 */
	public static function load()
	{
		add_action( 'wp_ajax_sl_aus_locations_import', array( __CLASS__, 'import' ) );
	}

	/**
	 * Import locations via ajax
	 * Locations are in the uploaded Excel file in upload folder
	 *
	 * @return void
	 */
	public static function import()
	{
		check_ajax_referer( 'process' );

		$restart = isset( $_POST['restart'] ) ? intval( $_POST['restart'] ) : 0;

		// If restart the process, reset session and send "continue" command
		if ( $restart )
		{
			session_start();
			$_SESSION['processed'] = array();
			$_SESSION['run']       = 1; // # runs

			wp_send_json_success( array(
				'message' => '',
				'type'    => 'continue',
			) );
		}

		$rows = self::get_rows();

		// If no more rows, we're done
		if ( ! $rows )
		{
			wp_send_json_success( array(
				'message' => '',
				'type'    => 'done',
			) );
		}

		foreach ( $rows as $row )
		{
			self::process( $row );
		}

		// Send command to process next rows
		wp_send_json_success( array(
			'message' => sprintf( __( 'Processed %d rows', '7aus-locations' ), count( $rows ) ),
			'type'    => 'continue',
		) );
	}

	/**
	 * Get rows to import
	 * Rows are get from working sheet with consideration of session
	 * Sessions stores processed rows to prevent duplication
	 *
	 * @return array
	 */
	public static function get_rows()
	{
		session_start();

		// Get full (absolute) path of uploaded file
		$file = Admin::get_file_path();
		$xlsx = new \Sl\SimpleXLSX( $file );

		$sheet_count = $xlsx->sheetsCount();

		$rows = array();
		for ( $i = 1; $i <= $sheet_count; $i ++ )
		{
			$sheet_rows = $xlsx->rows( $i );

			/**
			 * Ignore this sheet if all rows are processed
			 *
			 * Note: $_SESSION stores processed rows in format
			 *     array( sheet_id => max_num_row )
			 */
			if ( isset( $_SESSION['processed'][$i] ) && $_SESSION['processed'][$i] >= count( $sheet_rows ) - 1 )
				continue;

			// Remove the first line: headings
			if ( ! isset( $_SESSION['processed'][$i] ) )
				array_shift( $sheet_rows );

			/**
			 * Get the range of rows
			 * As we ignore the first row, we count from 1
			 *
			 * Assign the maximum index of row to session
			 */
			$min                       = isset( $_SESSION['processed'][$i] ) ? $_SESSION['processed'][$i] + 1 : 1;
			$max                       = $min + self::$threshold - 1;
			$_SESSION['processed'][$i] = $max;

			$rows = array_slice( $sheet_rows, $min, self::$threshold );
			break;
		}

		return $rows;
	}

	/**
	 * Import row
	 *
	 * @param array $row
	 *
	 * @return array
	 */
	public static function process( $row )
	{
		// Assign row info to variables for better understand
		$postcode = $row[0];
		$state    = $row[1];
		$suburb   = $row[2];
		$city     = $row[3];

		$taxonomy = 'location';

		// Insert state
		$state      = self::$states[strtoupper( $state )];
		$term_state = term_exists( $state, $taxonomy );
		if ( empty( $term_state ) )
		{
			$term_state = wp_insert_term( $state, $taxonomy );
		}

		// Insert city (zone)
		$city_term = term_exists( $city, $taxonomy );
		if ( empty( $city_term ) )
		{
			$city_term = wp_insert_term( $city, $taxonomy, array(
				'parent' => $term_state['term_id'],
			) );
		}

		// Insert suburb
		$suburb_term = term_exists( $suburb, $taxonomy );
		if ( empty( $suburb_term ) )
		{
			wp_insert_term( $suburb, $taxonomy, array(
				'parent' => $city_term['term_id'],
			) );
		}
	}
}
