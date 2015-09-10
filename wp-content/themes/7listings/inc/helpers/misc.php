<?php

/**
 * This class contains miscellaneous helper functions
 *
 * @package    WordPress
 * @subpackage 7listings
 *
 * @since      5.1.1
 * @author     Tran Ngoc Tuan Anh <anh@7listings.net>
 */
class Sl_Helper
{
	/**
	 * Get page URL by page template name
	 *
	 * @see   http://www.deluxeblogtips.com/2014/10/get-page-page-template-wordpress.html
	 * @since 5.1.1
	 *
	 * @param string $template Template file name
	 * @param string $default  Default URL
	 *
	 * @return string
	 */
	public static function get_url_by_template( $template, $default = HOME_URL )
	{
		$url  = HOME_URL;
		$page = get_pages( array(
			'posts_per_page' => 1,
			'meta_key'       => '_wp_page_template',
			'meta_value'     => 'templates/thank-you-booking.php',
			'hierarchy'      => 0,
		) );
		if ( $page )
		{
			$page = reset( $page );
			$url  = get_permalink( $page->ID );
		}

		return $url;
	}

	/**
	 * Reformat date according WordPress's settings
	 * Mostly we store date in Australian format ('d/m/Y')
	 * We have to convert it to European format 'd-m-Y' to make sure strtotime() work correctly
	 *
	 * @param string $date    Date
	 * @param bool   $convert Whether or not convert date to European format 'd-m-Y' to make sure strtotime() work correctly
	 *
	 * @return string
	 */
	public static function date_format( $date, $convert = true )
	{
		if ( $convert )
			$date = str_replace( '/', '-', $date );

		return date( get_option( 'date_format' ), strtotime( $date ) );
	}

	/**
	 * Reformat time according WordPress's settings
	 *
	 * @param string $time Time
	 *
	 * @return string
	 */
	public static function time_format( $time )
	{
		/**
		 * Use any fake date to make sure strtotime() works
		 * Date is when we add this function!
		 */
		return date( get_option( 'time_format' ), strtotime( '11/19/2014 ' . $time ) );
	}

	/**
	 * Transform 4 arrays of first names, last names, emails and phones into an associated array
	 *
	 * @param array $data Array of data for guests
	 * @return array
	 */
	public static function compact_guests( $data )
	{
		return array_map(
			array( __CLASS__, 'compact_guests_callback' ),
			$data['first'], $data['last'], $data['email'], $data['phone']
		);
	}

	/**
	 * Callback function to compact guests info
	 *
	 * @param string $first First name
	 * @param string $last  Last name
	 * @param string $email Email
	 * @param string $phone Phone
	 * @return array
	 */
	private static function compact_guests_callback( $first, $last, $email, $phone )
	{
		return array(
			'first' => $first,
			'last'  => $last,
			'email' => $email,
			'phone' => $phone,
		);
	}
}
