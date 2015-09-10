<?php

/**
 * This class will hold all helper functions
 */
class Sl_Company_Helper
{
	/**
	 * Post type
	 *
	 * @var string
	 */
	static $post_type = 'accommodation';

	/**
	 * Get average rating of current post
	 *
	 * @param null|int $post_id
	 *
	 * @return float
	 */
	static function get_average_rating( $post_id = null )
	{
		global $wpdb;

		if ( ! $post_id )
			$post_id = get_the_ID();

		$average = 0;
		$count   = self::get_no_reviews( $post_id );

		if ( ! $count )
			return $average;

		$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
		$total = 0;
		foreach ( $names as $name )
		{
			$rating = $wpdb->get_var( "
				SELECT SUM(meta_value) FROM {$wpdb->commentmeta}
				LEFT JOIN {$wpdb->comments} ON {$wpdb->commentmeta}.comment_id = {$wpdb->comments}.comment_ID
				WHERE meta_key = '$name'
				AND comment_post_ID = $post_id
				AND comment_approved = '1'
			" );
			$total += (int) $rating;
		}

		$average = number_format( $total / ( 6.0 * $count ), 1 );

		return $average;
	}

	/**
	 * Display comment rating
	 *
	 * @param  int  $post_id
	 * @param  bool $echo
	 *
	 * @return string
	 */
	static function show_average_rating( $post_id = null, $echo = true )
	{
		return sl_star_rating( self::get_average_rating( $post_id ), array(
			'echo'  => $echo,
			'count' => self::get_no_reviews( $post_id ),
		) );
	}

	/**
	 * Get number of reviews
	 *
	 * @param null|int $post_id
	 *
	 * @return int
	 */
	static function get_no_reviews( $post_id = null )
	{
		global $wpdb;

		if ( ! $post_id )
			$post_id = get_the_ID();

		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM $wpdb->commentmeta AS m
			LEFT JOIN $wpdb->comments AS c ON m.comment_id = c.comment_ID
			WHERE
				meta_key = %s AND
				comment_post_ID = %d AND
				comment_approved = 1
			", 'rating_sales', $post_id ) );

		return ( int ) $count;
	}
}
