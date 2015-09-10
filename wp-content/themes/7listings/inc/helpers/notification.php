<?php
/**
 * Main file for notification modules
 *
 * Will store all public API (functions) for the notification system
 */

/**
 * Class Sl_Notification
 * Main class for notification module
 */
class Sl_Notification
{
	/**
	 * @var array Store all notifications as 'id' => array of messages
	 */
	public static $notifications = array();

	/**
	 * Add a message to a notification area
	 *
	 * @param string $message Message
	 * @param mixed  $code    Slug-name to identify the error, in case we want to index message with id (not number) for later removing, reference
	 * @param string $type    Type of message
	 * @param string $id      ID of the notification area
	 *
	 * @return void
	 */
	public static function add( $message, $code = null, $type = 'warning', $id = 'all' )
	{
		if ( ! isset( self::$notifications[$id] ) )
			self::$notifications[$id] = array();

		$data = array(
			'message' => $message,
			'type'    => $type,
		);
		if ( $code )
			self::$notifications[$id][$code] = $data;
		else
			self::$notifications[$id][] = $data;
	}

	/**
	 * Remove a notification message
	 *
	 * @param mixed  $code Slug-name to identify the error, in case we want to index message with id (not number) for later removing, reference
	 * @param string $id   ID of the notification area
	 *
	 * @return void
	 */
	public static function remove( $code = null, $id = 'all' )
	{
		if ( ! $code || ! isset( self::$notifications[$id] ) || ! isset( self::$notifications[$id][$code] ) )
			return;

		unset( self::$notifications[$id][$code] );
	}

	/**
	 * Display messages of a notification area
	 *
	 * @param string $id ID of notification area
	 */
	public static function show( $id = 'all' )
	{
		if ( empty( self::$notifications[$id] ) )
			return;

		echo '<div class="alert notification">';
		foreach ( self::$notifications[$id] as $data )
		{
			printf( '<p class="%s">%s</p>', $data['type'], $data['message'] );
		}
		echo '</div>';
	}
}

// Hook to show messages of a notification area
// Call: do_action( 'sl_notification', 'id' );
add_action( 'sl_notification', array( 'Sl_Notification', 'show' ) );
