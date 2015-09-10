<?php
/**
 * A simple log and debugging system in WordPress
 * Todo: Log values before deleting
 *
 * @author Tan Nguyen <tan@fitwp.com>
 * @package ASQ
 * @version 0.1
 */
namespace ASQ;

class Log
{
	/**
	 * Make a log
	 */
	public static function make( $log = array(), $mail = false )
	{
		global $wpdb;

		$user_id		= get_current_user_id();
		$session 		= ( ! isset( $_SESSION ) ) ? '' : serialize( $_SESSION );

		$request 		= ( ! isset( $_REQUEST ) ) ? '' : $_REQUEST;

		// Do not log heartbeat and other core WP actions
		if ( isset( $request['action'] ) && in_array( $request['action'], array( 'heartbeat', 'wp-remove-post-lock', 'sl_company_views', 'peace_entry_views',
		'sl_social_buttons_get_counter', 'get-comments', 'solar_fill_location'
		) ) )
			return;

		// Do not log password fields
		foreach ( $request as $key => $value )
		{
			if ( str_contains( $key, array( 'pass', 'pwd' ) ) )
				unset( $request[$key] );
		}

		$request = serialize( $request );

		$server			= ( ! isset( $_SERVER ) ) ? '' : serialize( $_SERVER );

		// Remember that we can't serialize Closure.
		$stacktrace		= serialize( debug_backtrace() );
		
		$action			= $_SERVER['REQUEST_URI'];
		$description 	= ( ! isset( $log['description'] ) ) ? '' : $log['description'];
		$type 			= ( ! isset( $log['description'] ) ) ? 'auto' : 'manual';

		$output 		= '';

		$time = date( 'Y-m-d H:i:s' );

		$prepared_log = compact( 'user_id', 'session', 'request', 'server',
			'type', 'stacktrace', 'action', 'description', 'output'
		);

		try
		{
			$wpdb->insert( 'asq_logs', $prepared_log, array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
		}
		catch ( Exception $e )
		{
			// Send mail to developer whilst cannot create a log. Perhaps DB connection error.
			wp_mail( 'tan@fitwp.com', 'Error during creating log at ' . $time, $prepared_log . '|||' . $e->getMessage() );
		}
	}

	/**
	 * Find a log on db
	 * @param  int $id ID of the log
	 * @return Entire log or throw exception
	 */
	public static function find( $id )
	{
		global $wpdb;

		$query = "SELECT * FROM asq_logs WHERE id = %d";

		$row = $wpdb->get_row( $wpdb->prepare( $query, $id ) );

		if ( ! empty( $row ) )
			return $row;
		else
			throw new Exception("Error during finding log", 1);
	}

	/**
	 * Recent Activity Widget. Display recent 10 actions by users like Facebook.
	 *
	 * Todo: Display it with friendly print.
	 */
	public static function recent_activity()
	{
		global $wpdb;

		// Todo: Recent Activity Widget here
	}
}

// id | user_id | log type | backtrace | action | description | output message | request | session | server | created at
if ( ! empty( $_POST ) )
{
	Log::make();
}
