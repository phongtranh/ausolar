<?php
add_filter( 'peace_prefix', 'sl_prefix' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'peace_numeric_pagination', 'sl_pagination' );
add_action( '7listings_footer_bottom_bottom', 'sl_footer_custom_html' );

/**
 * Prefix for theme hooks
 *
 * @return string
 */
function sl_prefix()
{
	return 'sl';
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @return void
 */
function sl_post_meta()
{
	echo '<div class="entry-meta-wrapper">';
	$info = array(
		sl_listing_element( 'date' ),
		sl_listing_element( 'categories' ),
		sl_listing_element( 'comments' ),
	);
	echo implode( ' <span class="sep">|</span> ', array_filter( $info ) );
	echo sl_listing_element( 'author' );
	echo '</div>';

}

/**
 * Display numeric pagination
 *
 * @param array
 *
 * @return array
 */
function sl_pagination( $args )
{
	$args['type'] = 'list';

	return $args;
}

/**
 * Show custom HTML in footer
 *
 * @return void
 */
function sl_footer_custom_html()
{
	if ( ! sl_setting( 'design_footer_bottom_custom_html_enable' ) )
		return;
	if ( $html = sl_setting( 'design_footer_bottom_custom_html' ) )
		echo "<div class='custom-footer-content'>$html</div>";
}

add_action( 'sl_body', 'sl_schema_body_tag' );

/**
 * Echo schema.org in <body> tag
 *
 * @return void
 */
function sl_schema_body_tag()
{
	if ( ! is_single() )
		return;

	$type      = get_post_type();
	$item_type = in_array( $type, sl_setting( 'listing_types' ) ) ? 'Product' : 'Article';
	if ( 'post' == $type )
		$item_type = 'NewsArticle';

	$item_type = peace_filters( 'schema_item_type', $item_type );

	echo " itemscope itemtype='http://schema.org/$item_type'";
}

/**
 * Display single listing in list
 *
 * @param array $args Array of arguments
 *
 * @return string
 */
function sl_post_list_single( $args = array() )
{
	$args = array_merge( array(
		'post_type'      => get_post_type(),
		'thumbnail'      => 1,
		'image_size'     => 'sl_thumb_tiny',
		'post_title'     => 1,
		'title_length'   => 0,
		'rating'         => 1,
		'address'        => 0,
		'price'          => 1,
		'booking'        => 1,
		'date'           => 0,
		'excerpt'        => 1,
		'excerpt_length' => 25,
		'class'          => '', // Additional class
		'elements'       => array( 'post_title', 'rating', 'address', 'excerpt', 'price', 'booking' ), // Elements to show, modules can change order or add more elements
		'before'         => '', // Before HTML
		'after'          => '', // After HTML
	), $args );

	$class = array( 'post' );
	if ( $args['post_type'] && 'post' != $args['post_type'] )
		$class[] = $args['post_type'];
	if ( $args['class'] )
		$class[] = $args['class'];
	$class = implode( ' ', get_post_class( $class ) );

	$html = '<article class="' . $class . '">';

	$html .= $args['before'];

	if ( $args['thumbnail'] )
		$html .= sl_listing_element( 'thumbnail', $args );

	$html .= '<div class="details">';

	foreach ( $args['elements'] as $element )
	{
		if ( $args[$element] )
			$html .= sl_listing_element( $element, $args );
	}

	/**
	 * Always add date and author to entry meta to prevent errors in Google Webmaster Tools
	 * Keep them hidden to not affect current view
	 */
	if ( ! in_array( 'date', $args['elements'] ) || ! $args['date'] )
	{
		$html .= sl_listing_element( 'date', array( 'date_class' => 'hidden' ) );
	}
	$html .= sl_listing_element( 'author' );

	$html .= '</div>'; // .details

	$html .= $args['after'];

	$html .= '</article>';

	return $html;
}

/**
 * Display listing element
 *
 * @param string $element Element name
 * @param array  $args    Arguments for element, such as image_size, title_length, excerpt_length
 *
 * @return string
 */
function sl_listing_element( $element, $args = array() )
{
	$args = array_merge( array(
		'post_type'      => '',
		'thumbnail_link' => true, // Link thumbnail to current post?
		'image_size'     => 'sl_thumb_tiny',

		'title_length'   => 0,
		'title_tag'      => 'h4',

		/**
		 * 'rel' attribute for post title link.
		 * It is usually 'bookmark' but also can be 'prev' / 'next' for previous / next post when viewing single post
		 */
		'rel'            => 'bookmark',

		'excerpt_length' => null,
		'more_link_type' => 'text',
		'more_link_text' => __( 'Read more', '7listings' ),

		'date_class'     => '',
		'date_type'      => 'entry',
		'author'         => get_the_author(),
	), $args );

	$html = '';
	switch ( $element )
	{
		case 'thumbnail':
			$thumbnail = sl_broadcasted_thumbnail( $args['image_size'], array(
				'alt'   => the_title_attribute( 'echo=0' ),
				'title' => the_title_attribute( 'echo=0' ),
			), null, false );
			if ( $thumbnail )
			{
				$html = $thumbnail;
				if ( $args['thumbnail_link'] && false !== strpos( $thumbnail, '<img' ) )
					$html = '<a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '">' . $thumbnail . '</a>';
			}
			break;
		case 'post_title':
			$title = get_the_title();
			if ( $title )
			{
				if ( $args['title_length'] && mb_strlen( $title ) > $args['title_length'] )
					$title = mb_substr( $title, 0, $args['title_length'] ) . '...';
			}
			else
			{
				$title = get_the_ID();
			}
			$html = sprintf(
				'<%s class="entry-title"><a class="title" href="%s" title="%s" rel="%s">%s</a></%s>',
				$args['title_tag'],
				get_permalink(),
				the_title_attribute( 'echo=0' ),
				$args['rel'],
				$title,
				$args['title_tag']
			);
			break;
		case 'price':
			$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', get_post_type() ), true );

			// Don't show price if there's no booking resource
			if ( empty( $resources ) )
				break;

			/**
			 * Get minimum listing price from all resources
			 * Don't use 'price_from' because it stores lowest price, while resource price should be lead in rate price (tour)
			 *
			 * If price = 0, display 'Free'
			 * If no prices, set it to false
			 * If there're > 1 booking resources AND there are different prices, display 'from'
			 */
			$different = false;
			$price     = 999999;
			$class     = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
			foreach ( $resources as $resource )
			{
				$resource_price = call_user_func( array( $class, 'get_resource_price' ), $resource );
				if ( false !== $resource_price & $resource_price < $price )
				{
					$price = $resource_price;
					// If price is set, and there's another "lower" price, that means there are different prices
					if ( 999999 != $price )
					{
						$different = true;
					}
				}
			}
			/**
			 * If listing has no prices, set it to false
			 */
			if ( 999999 == $price )
				$price = false;

			$before = $price && 1 < count( $resources ) && $different ? __( 'from ', '7listings' ) : '';
			$html   = false !== $price ? Sl_Currency::format( $price, "class=entry-meta&before=$before" ) : '';
			break;
		case 'resource_price':
			$resource = $args['resource'];
			$type     = isset( $resource['lead_in_rate'] ) ? $resource['lead_in_rate'] : 'adult';

			$price_types = array_merge( array( $type ), array( 'adult', 'child', 'senior', 'family', 'infant' ) );
			$price_types = array_unique( $price_types );

			$html = '<div class="tour-prices">';
			foreach ( $price_types as $type )
			{
				// If price is OFF, it will be saved as an empty string. We will ignore such price
				if ( isset( $resource["price_$type"] ) && '' !== $resource["price_$type"] )
				{
					$prices[] = $resource["price_$type"];
					$html .= '<div class="passenger-type ' . $type . '"><label class="guest">' . ucfirst( $type ) . '</label>' . Sl_Currency::format( $resource["price_$type"], 'class=entry-meta' );

					if ( 'family' == $type )
					{
						$html .= "<label for='guest-family' class='sl-label-inline guest hint--right' data-hint='" . __( '2 Adults + 2 Children', '7listings' ) . "'>";
						$html .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="60" height="40" viewBox="0 0 60 40" enable-background="new 0 0 60 40" xml:space="preserve" class="family-icon"><circle cx="8.2" cy="5.2" r="2.9"/><circle cx="24.7" cy="4.4" r="3"/><path d="M27.8 8.3h-5.9c-1.9 0-2.7 1-3.4 3.2l-2.4 5.9 -2.3-5.8c-0.3-0.9-1.5-2.6-3.6-2.7H6.2C4.1 9 2.9 10.6 2.6 11.6L0.1 19.9c-0.5 1.8 1.8 2.5 2.3 0.8l2.2-7.7h0.6L1.4 26.5h3.6v10.1c0 1.8 2.8 1.8 2.8 0v-10.1h0.8v10.1c0 1.8 2.7 1.8 2.7 0v-10.1h3.7l-3.9-13.5h0.7l3.1 8c0 0 0 0 0 0 0.2 1.2 2 1.5 2.5 0.1l3.4-8.2h0.3v23.3c0 2.5 3.4 2.4 3.4 0V22.7h0.5v13.5c0.1 2.4 3.5 2.5 3.5 0V12.9h0.6v8.5c0 1.8 2.6 1.8 2.6 0v-9.2C31.6 10.3 30.1 8.3 27.8 8.3z"/><circle cx="41.6" cy="13.2" r="2.2"/><path d="M45.9 18.1c-0.3-0.7-1.2-2-2.7-2h-3c-1.6 0-2.5 1.3-2.7 2l-1.9 6.3c-0.4 1.4 1.3 1.9 1.8 0.6l1.7-5.8h0.5l-2.9 10.2h2.7v7.6c0 1.4 2.1 1.4 2.1 0v-7.6h0.6v7.6c0 1.4 2 1.4 2 0v-7.6h2.8l-3-10.2h0.5l2.3 6c0.4 1.3 2.1 0.8 1.8-0.6L45.9 18.1zM59.8 18.8c-0.1-0.6-0.3-0.9-0.7-1.3 -0.4-0.4-0.9-0.7-1.4-0.8 -0.5-0.1-5.3-0.1-5.8 0 -1 0.2-1.8 1-2.1 2 -0.1 0.4-0.1 7.6 0 7.9 0.2 0.4 0.7 0.6 1.1 0.5 0.3-0.1 0.5-0.2 0.6-0.4 0.1-0.2 0.1-0.3 0.1-3.4v-3.2h0.2 0.2v8.5c0 5.7 0 8.5 0.1 8.7 0.1 0.3 0.4 0.6 0.8 0.8 0.2 0.1 0.8 0 1-0.1 0.2-0.1 0.5-0.4 0.6-0.6 0.1-0.1 0.1-1.1 0.1-5.2v-5h0.2 0.2v5c0 5.5 0 5.2 0.3 5.5 0.3 0.3 0.5 0.4 1 0.4 0.4 0 0.7-0.1 1-0.4 0.3-0.3 0.3 0.3 0.3-9.1v-8.5h0.2 0.2l0 3.3 0 3.3 0.1 0.2c0.2 0.3 0.8 0.5 1.2 0.3 0.3-0.1 0.4-0.2 0.5-0.5 0.1-0.2 0.1-0.7 0.1-3.8C59.9 19.4 59.9 19.2 59.8 18.8zM54.2 16.2c0.2 0.1 0.4 0.1 0.8 0.1 0.4 0 0.5 0 0.9-0.2 0.5-0.2 0.8-0.6 1-1 0.1-0.3 0.1-0.4 0.1-0.9 0-0.6 0-0.6-0.2-1 -0.8-1.6-3.1-1.6-4 0 -0.2 0.3-0.2 0.4-0.2 0.9 0 0.5 0 0.6 0.2 0.9C53.1 15.6 53.5 15.9 54.2 16.2z"/></svg>';
						$html .= '</label>';
					}

					$html .= '</div>';
				}
			}
			$html .= '</div>';

			if ( ! empty( $prices ) && 1 == count( $prices ) )
				$html = Sl_Currency::format( $prices[0], 'class=entry-meta' );

			break;
		case 'rating':
			$html = sl_rating( null, false );
			break;
		case 'address':
			if ( $element['address'] )
			{
				$city = get_post_meta( get_the_ID(), 'city', true );
				if ( $city )
				{
					$city = get_term( intval( $city ), 'location' );
					if ( null !== $city && ! is_wp_error( $city ) )
						$city = $city->name;
					else
						$city = '';
				}
				else
				{
					$city = '';
				}
				$address = get_post_meta( get_the_ID(), 'address', true );
				if ( $city )
					$address = ", $city";
				$html = "<address class='entry-meta address'>$address</address>";
			}
			break;
		case 'excerpt':
			$html = '<p class="entry-summary excerpt">' . sl_excerpt( $args['excerpt_length'] ) . '</p>';
			break;
		case 'booking':
			/**
			 * Show booking button for current listing
			 * Booking button is shown for the resource which has lowest price
			 */
			$html    = '';
			$post_id = get_the_ID();
			$class   = 'Sl_' . ucfirst( $args['post_type'] ) . '_Helper';

			if ( sl_setting( $args['post_type'] . '_booking' ) )
			{
				$resources = get_post_meta( $post_id, sl_meta_key( 'booking', $args['post_type'] ), true );
				if ( ! empty( $resources ) )
				{
					$min_price = 9999999;
					$min       = - 1;

					foreach ( $resources as $k => $resource )
					{
						if ( call_user_func( array( $class, 'get_resource_price' ), $resource ) < $min_price && false !== call_user_func( array( $class, 'get_resource_price' ), $resource ) )
						{
							$min       = $k;
							$min_price = call_user_func( array( $class, 'get_resource_price' ), $resource );
						}
					}
					if ( - 1 != $min )
					{
						$resource                = $resources[$min];
						$resource['resource_id'] = $min;
						$html                    = apply_filters( 'booking_button', '', $resource );
					}
				}
			}
			break;
		case 'date':
			$datetime = 'entry' == $args['date_type'] ? get_the_time( 'c' ) : date( 'c' );
			$date     = 'entry' == $args['date_type'] ? get_the_date() : date( get_option( 'date_format' ) );
			$html     = '<time class="entry-meta entry-date updated date ' . $args['date_class'] . '" datetime="' . esc_attr( $datetime ) . '">' . esc_html( $date ) . '</time>';
			break;
		case 'categories':
			$html = '<span class="entry-meta categories">' . get_the_category_list( ', ' ) . '</span>';
			break;
		case 'tags':
			$html = get_the_tag_list( '<span class="entry-meta tags">', ', ', '</span>' );
			break;
		case 'comments':
			if ( ! sl_setting( 'post_comment_status' ) )
				break;

			if ( ! get_comments_number() && ! comments_open() )
				break;

			$title = get_comments_number_text( __( 'Leave a comment', '7listings' ), __( '1 Comment', '7listings' ), __( '% Comments', '7listings' ) );
			$html  = '<a href="' . get_comments_link() . '" class="entry-meta comments-link">' . $title . '</a>';
			break;
		case 'author':
			$author_url = get_user_meta( get_the_author_meta( 'ID' ), 'googleplus', true );
			if ( ! $author_url && sl_setting( 'googleplus' ) )
			{
				$author_url = sl_setting( 'googleplus' );
			}
			else
			{
				$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
			}
			$html = sprintf(
				'<span class="byline hidden"><span class="author vcard"><a class="url fn n" href="%s" rel="author">%s</a></span></span>',
				esc_url( $author_url ),
				esc_html( $args['author'] )
			);
			break;
		case 'more_link':
			$html = sprintf(
				'<a href="%s" title="%s" rel="nofollow" class="more-link%s">%s</a>',
				get_permalink(),
				the_title_attribute( 'echo=0' ),
				$args['more_link_type'] == 'button' ? ' button' : '',
				$args['more_link_text']
			);
			break;
	}

	$html = apply_filters( __FUNCTION__, $html, $element, $args );

	return $html;
}

add_action( 'template_redirect', 'sl_listings_search_hooks' );

/**
 * Add filter to homepage to show search results in listings search widget
 *
 * @return void
 */
function sl_listings_search_hooks()
{
	if ( empty( $_GET['sl_widget_search'] ) || ! wp_verify_nonce( $_GET['sl_widget_search'], 'widget-search' ) )
		return;

	/**
	 * Filter the query vars in listing archive page to process parameters and show correct search results
	 * Note: we set 'post_type' param and that makes WordPress use post type archive page template
	 */
	add_filter( 'sl_listing_archive_query_args', 'sl_listings_search_filter_query_vars' );

	// Change the featured title
	add_filter( 'sl_featured_title_title', 'sl_listings_search_featured_title' );
	add_filter( 'sl_featured_title_subtitle', 'sl_listings_search_featured_subtitle' );
}

/**
 * Filter the query vars to process search params and show the search results correctly
 *
 * @param array $args Query var array
 *
 * @return array
 */
function sl_listings_search_filter_query_vars( $args )
{
	/**
	 * Filter by type or feature taxonomy
	 * Set taxonomy query parameter
	 */
	$tax_query = isset( $args['tax_query'] ) ? $args['tax_query'] : array();
	if ( ! empty( $_GET['sl_type_' . $_GET['post_type']] ) )
	{
		$tax_query[] = array(
			'taxonomy' => sl_meta_key( 'tax_type', $_GET['post_type'] ),
			'terms'    => $_GET['sl_type_' . $_GET['post_type']],
		);
	}

	/**
	 * Filter by location
	 * Set taxonomy query parameter
	 *
	 * Note: since version 5.4.3, we updated so listings have correct locations as post terms
	 * So we can use tax query safely here
	 */
	if ( ! empty( $_GET['sl_location_' . $_GET['post_type']] ) )
	{
		$tax_query[] = array(
			'taxonomy' => 'location',
			'terms'    => intval( $_GET['sl_location_' . $_GET['post_type']] ),
		);
	}

	/**
	 * Filter by star rating, for accommodation only
	 * Stars are saved in 'stars' taxonomy with name started with number, e.g. '1 Star', '2 Stars', etc.
	 * We convert term name into integer to compare
	 */
	if ( 'accommodation' == $_GET['post_type'] && ! empty( $_GET['star_rating'] ) )
	{
		$terms = array();
		$stars = get_terms( 'stars' );
		foreach ( $stars as $star )
		{
			if ( intval( $star->name ) >= intval( $_GET['star_rating'] ) )
				$terms[] = $star->term_id;
		}
		$tax_query[] = array(
			'taxonomy' => 'stars',
			'terms'    => $terms,
		);
	}

	// Set taxonomy query only when it's not empty
	if ( $tax_query )
		$args['tax_query'] = $tax_query;

	return $args;
}

/**
 * Change the featured title for search results page (by listings search widget)
 *
 * @param string $title
 *
 * @return string
 */
function sl_listings_search_featured_title( $title )
{
	return __( 'Search results', '7listings' );
}

/**
 * Change the featured subtitle for search results page (by listings search widget)
 *
 * @param string $subtitle
 *
 * @return string
 */
function sl_listings_search_featured_subtitle( $subtitle )
{
	// Get search info: search query, taxonomies and location

	// Search query
	$search = get_search_query();

	// "Type" taxonomy
	$terms = array();
	if ( ! empty( $_GET['sl_type_' . $_GET['post_type']] ) )
	{
		$taxonomy = sl_meta_key( 'tax_type', $_GET['post_type'] );
		foreach ( $_GET['sl_type_' . $_GET['post_type']] as $type )
		{
			$term = get_term( $type, $taxonomy );
			if ( $term && ! is_wp_error( $term ) )
			{
				$terms[] = $term->name;
			}
		}
	}

	// Location
	$location = '';
	if ( ! empty( $_GET['sl_location_' . $_GET['post_type']] ) )
	{
		$term = get_term( $_GET['sl_location_' . $_GET['post_type']], 'location' );
		if ( $term && ! is_wp_error( $term ) )
		{
			$location = $term->name;
		}
	}

	// Build subtitle string
	$subtitle = array();
	if ( $search )
	{
		$subtitle[] = "&quot;$search&quot;";
	}
	if ( $terms )
	{
		$subtitle[] = implode( ', ', $terms );
	}
	$subtitle = array( implode( ', ', $subtitle ) );
	if ( $location )
		$subtitle[] = $location;
	$subtitle = implode( __( ' in ', '7listings' ), $subtitle );

	return sprintf( __( 'You searched for:<br>%s', '7listings' ), $subtitle );
}

/**
 * Get or echo booked data from cart
 *
 * @param string $key
 * @param bool   $return
 *
 * @return string
 */
function sl_cart_data( $key, $return = false, $data = '' )
{
	if ( empty( $data ) )
		global $data;

	$value = isset( $data[$key] ) ? $data[$key] : '';

	if ( 0 === strpos( $key, 'upsell_' ) && ! empty( $data['upsells'] ) )
	{
		$key = str_replace( 'upsell_', '', $key );
		foreach ( $data['upsells'] as $item )
		{
			if ( $item['name'] == $key )
			{
				$value = $item['num'];
				break;
			}
		}
	}
	if ( 'guest' == $key )
	{
		$value = array(
			'first' => '',
			'last'  => '',
			'email' => '',
			'phone' => '',
		);
		if ( ! empty( $data['guests'] ) )
			$value = array_shift( $data['guests'] );
	}

	if ( ! $return )
		echo $value;

	return $value;
}
