<?php
/**
 * Get correct key for a key, due to difference between post types
 *
 * @param string $key The key name
 * @param string $post_type
 *
 * @return string
 */
function sl_meta_key( $key, $post_type = 'tour' )
{
	$keys                = array();
	$keys['photos']      = array(
		'tour'          => 'tour_photos',
		'accommodation' => 'hotel_photos',
	);
	$keys['logo']        = array(
		'tour'          => 'tour_logo',
		'accommodation' => 'hotel_logo',
	);
	$keys['movies']      = array(
		'tour'          => 'tour_movies',
		'accommodation' => 'hotel_movies',
	);
	$keys['booking']     = array(
		'tour'          => 'tour_booking',
		'accommodation' => 'hotel_booking',
	);
	$keys['tax_type']    = array(
		'tour'          => 'tour_type',
		'accommodation' => 'type',
		'rental'        => 'rental_type',
		'product'       => 'product_cat',
		'attraction'    => 'attraction_type',
		'post'          => 'category',
	);
	$keys['tax_feature'] = array(
		'tour'          => 'features',
		'accommodation' => 'amenity',
		'rental'        => 'feature',
		'attraction'    => 'attraction_feature',
		'post'          => 'post_tag',
		'company'       => 'company_service',
		'product'       => 'product_tag',
	);
	if ( isset( $keys[$key] ) && isset( $keys[$key][$post_type] ) )
		$key = $keys[$key][$post_type];

	return apply_filters( __FUNCTION__, $key, $post_type );
}

/**
 * Get booking meta value of a 'booking' post type
 *
 * @param string $key     Meta key.
 * @param int    $post_id Booking ID. Optional. Default is current post ID.
 * @param array  $item    Array of booking info, used only when booking item is in cart.
 *                        If present, get meta value from $item['data'] array instead of post meta.
 *
 * @return mixed
 */
function sl_booking_meta( $key, $post_id = null, $item = null )
{
	if ( ! $post_id )
		$post_id = get_the_ID();

	// If booking is not from cart
	if ( empty( $item ) || empty( $item['data'] ) )
		return get_post_meta( $post_id, $key, true );

	// If booking is from cart
	$data = $item['data'];
	if ( empty( $data[$key] ) )
		return '';

	return $data[$key];
}

/**
 * Display comment rating
 *
 * @param  int  $post_id
 * @param  bool $show
 *
 * @return string
 */
function sl_rating( $post_id = null, $show = true )
{
	global $wpdb;

	if ( ! $post_id )
		$post_id = get_the_ID();

	$html    = '';
	$average = $wpdb->get_var( $wpdb->prepare( "
		SELECT AVG(m.meta_value)
		FROM $wpdb->commentmeta m
		LEFT JOIN $wpdb->comments c ON m.comment_id = c.comment_ID
		WHERE m.meta_key='rating' AND c.comment_post_ID='%s' AND c.comment_approved=1
	", $post_id ) );
	$average = number_format_i18n( $average, 2 );
	$html .= sl_star_rating( $average, 'type=rating&echo=0' );

	if ( $show )
		echo $html;

	return $html;
}

/**
 * Show star rating
 *
 * @param  mixed $value
 *
 * @param string $args
 *
 * @return string
 */
function sl_star_rating( $value, $args = '' )
{
	$args = wp_parse_args( $args, array(
		'type'            => 'aggregate', // 'aggregate', 'rating', 'select', 'flat': will display aggregate value, single rating value or 5 stars for select
		'classes'         => '',          // Add more classes if needed
		'replace_classes' => false,       // Will replace default classes or just append classes?
		'count'           => '',
		'echo'            => true,
		'item'            => '',
	) );

	$output = '';

	// Normalize value
	if ( ! is_numeric( $value ) || $value < 0 || $value > 5 )
		$value = 0;

	// Convert to integer if needed
	if ( intval( $value ) == $value )
		$value = intval( $value );

	// Set classes
	$classes = array( 'stars' );
	if ( ! empty( $args['classes'] ) )
		$classes[] = $args['classes'];
	$classes = implode( ' ', $classes );

	// Aggregate rating
	if ( $args['type'] == 'aggregate' )
	{
		$title  = sprintf( __( 'Rated %s out of 5', '7listings' ), number_format( $value, 2 ) );
		$output = "<span title='$title' class='$classes'>";

		// Int value
		if ( is_int( $value ) )
			$output .= '<span class="active star-' . $value . '">' . $value . '</span>';
		// Float value
		else
			$output .= '<span class="active" style="width:' . ( $value * 20 ) . '%">' . $value . '</span>';

		// Must have 'count'
		$output .= '<span style="display:none">' . $args['count'] . '</span>';

		$output .= '</span>';
	}
	else
	{
		$atts = array(
			'class' => $classes,
		);

		if ( $args['type'] == 'rating' && $value > 0 )
		{
			$atts = array_merge( $atts, array(
				'itemscope' => '',
				'itemtype'  => 'http://schema.org/Rating',
				'itemprop'  => 'reviewRating',
				'bestRating' => '5',
				'worstRating' => '0'
			) );
		}
		if ( 'select' != $args['type'] )
			$atts['title'] = sprintf( __( 'Rated %s out of 5', '7listings' ), number_format( $value, 2 ) );

		$output .= '<span';
		foreach ( $atts as $k => $v )
		{
			$output .= $v ? " $k='$v'" : " $k";
		}
		$output .= '>';

		// For select: in comment form
		$active = '';
		if ( 'select' == $args['type'] )
		{
			for ( $i = 1; $i <= 5; $i ++ )
			{
				$active .= "<a class='star-$i' href='#'>$i</a>";
			}
		}
		// For aggregate && single rating value
		else
		{
			$itemprop = '';
			if ( $args['type'] == 'rating' && $value > 0 )
				$itemprop = ' itemprop="ratingValue"';
			// Integer value
			if ( is_int( $value ) )
				$active .= '<span' . $itemprop . ' class="active star-' . $value . '">' . $value . '</span>';
			// Float value
			else
				$active .= '<span' . $itemprop . ' class="active" style="width:' . ( $value * 20 ) . '%">' . $value . '</span>';
		}

		$output .= $active;

		$output .= '</span>'; // itemscope
	}

	if ( $args['echo'] )
		echo $output;

	return $output;
}

/**
 * Show overall rating for a post
 *
 * @return array
 */
function sl_get_average_rating()
{
	global $wpdb;

	$post_id = get_the_ID();
	$data    = $wpdb->get_row( "
		SELECT COUNT(meta_value) AS count, SUM(meta_value) AS total
		FROM {$wpdb->commentmeta} AS cm
		LEFT JOIN {$wpdb->comments} AS c ON cm.comment_id = c.comment_ID
		WHERE meta_key = 'rating'
		AND comment_post_ID = '$post_id'
		AND comment_approved = '1'
	" );

	if ( empty( $data ) )
	{
		$count   = 0;
		$average = 0;
	}
	else
	{
		$count   = intval( $data->count );
		$average = $count ? (float) $data->total / $count : 0;
	}

	return array( $count, $average );
}

/**
 * Show 2 digits after decimal point if not a whole number
 *
 * @param float $number
 *
 * @return string
 */
function sl_format_number( $number )
{
	$int = (int) $number;

	if ( $int == $number )
		return $int;

	return number_format_i18n( $number, 2 );
}
