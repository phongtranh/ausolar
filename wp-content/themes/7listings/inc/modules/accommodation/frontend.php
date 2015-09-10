<?php

class Sl_Accommodation_Frontend extends Sl_Core_Frontend
{
	/**
	 * Add more param to JS object in frontend
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	function js_params( $params )
	{
		// Add accommodation check in/out for booking page
		if ( get_query_var( 'book' ) && $this->post_type == get_post_type() )
		{
			$params['accommodation'] = array(
				'guests'       => __( 'Guests', '7listings' ),
				'extra'        => __( 'Extra Guests', '7listings' ),
				'invalidDates' => __( 'Invalid date', '7listings' ),
				'guestTitle'   => __( 'Guest', '7listings' ),
			);
		}

		return $params;
	}

	/**
	 * Change meta title
	 *
	 * @param string $title
	 * @param string $sep
	 *
	 * @return string
	 */
	function meta_title( $title = '', $sep = '' )
	{
		$title = parent::meta_title( $title, $sep );

		$taxonomies = array( 'amenity', 'type' );
		foreach ( $taxonomies as $tax )
		{
			if ( is_tax( $tax ) )
			{
				$term  = get_queried_object();
				$title = str_replace( '%TERM%', $term->name, sl_setting( "{$this->post_type}_{$tax}_title" ) );
			}
		}

		return $title;
	}

	/**
	 * Display slider
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	static function slider( $args )
	{
		$args = array_merge( array(
			'number'     => 5,
			'type'       => '',
			'location'   => '',

			'hierarchy'  => 0,     // Priority sorting
			'display'    => 'all',

			'orderby'    => 'date',
			'transition' => 'fade',
			'delay'      => 0,
			'speed'      => 1000,

			'container'  => 'div', // Container tag
		), $args );

		$query_args = array(
			'post_type' => 'accommodation',
		);
		sl_build_query_args( $query_args, $args );

		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$args['class']           = 'slide';
		$args['image_size']      = 'sl_pano_medium';
		$GLOBALS['sl_list_args'] = $args;

		// Sort by priority
		if ( $args['hierarchy'] )
		{
			sl_query_with_priority( $query_args, 'sl_list_callback' );
		}
		else
		{
			$query = new WP_Query( $query_args );
			sl_list_callback( $query, $args['number'] );
			wp_reset_postdata();
		}

		// Get content
		$html = ob_get_clean();

		wp_enqueue_script( 'jquery-cycle2' );

		return sprintf(
			'<%s class="sl-list posts tours cycle-slideshow" data-cycle-slides="> article" data-cycle-fx="%s" data-cycle-delay="%s" data-cycle-speed="%s">%s</%s>',
			$args['container'],
			$args['transition'], $args['delay'], $args['speed'], $html,
			$args['container']
		);
	}

	/**
	 * Display post list
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	public static function post_list( $args )
	{
		$args = array_merge( array(
			'number'              => 5,
			'type'                => '',
			'location'            => '',
			'orderby'             => 'date',
			'display'             => 'list',
			'columns'             => 1,
			'more_listings'       => 1,
			'more_listings_text'  => __( 'See more listings', '7listings' ),
			'more_listings_style' => 'button',

			'hierarchy'           => 0,       // Priority sorting
			'display_order'       => 'all',

			'container'           => 'aside', // Container tag
		), $args );

		$query_args = array(
			'post_type' => 'accommodation',
		);
		sl_build_query_args( $query_args, $args );


		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$GLOBALS['sl_list_args'] = $args;

		// Sort by priority
		if ( $args['hierarchy'] )
		{
			sl_query_with_priority( $query_args, 'sl_list_callback' );
		}
		else
		{
			$query = new WP_Query( $query_args );
			sl_list_callback( $query, $args['number'] );
			wp_reset_postdata();
		}

		// Get content
		$html = ob_get_clean();

		$class = 'sl-list posts accommodations';
		$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

		$html = "<{$args['container']} class='$class'>$html</{$args['container']}>";

		/**
		 * Add 'View more listings' links
		 * Link to term archive page and fallback to post type archive page
		 * If the archive page does not have more listing, then don't show this link
		 */
		if ( $args['more_listings'] )
		{
			$show = true;

			$link = get_post_type_archive_link( 'accommodation' );

			// Fix ugly post type archive link
			if ( strpos( $link, '?' ) )
				$link = home_url( sl_setting( 'accommodation_base_url' ) . '/' );

			// If set 'type' taxonomy, get link to that taxonomy page
			if ( $args['type'] )
			{
				$term = get_term( absint( $args['type'] ), sl_meta_key( 'tax_type', 'accommodation' ) );
				if ( ! is_wp_error( $term ) )
				{
					// Don't show view more listings if the term doesn't have more listings
					if ( $term->count <= $args['number'] )
						$show = false;

					$term_link = get_term_link( $term, sl_meta_key( 'tax_type', 'accommodation' ) );
					if ( ! is_wp_error( $term_link ) )
						$link = $term_link;
				}
			}

			if ( $show )
			{
				$html .= sprintf(
					'<a%s href="%s">%s</a>',
					'button' == $args['more_listings_style'] ? ' class="button"' : '',
					$link,
					$args['more_listings_text']
				);
			}
		}

		return $html;
	}

	/**
	 * Show star rating of current listing
	 *
	 * @param string $output  Output
	 * @param string $element Element name, must be 'star_rating'
	 * @param array  $args    Argument
	 *
	 * @return string
	 */
	static function star_rating( $output, $element, $args )
	{
		if ( 'star_rating' != $element )
			return $output;

		$post_type = isset( $args['post_type'] ) ? $args['post_type'] : get_post_type();
		if ( 'accommodation' != $post_type )
			return $output;

		$star = get_the_terms( get_the_ID(), 'stars' );
		if ( ! is_array( $star ) )
			return $output;

		$star = array_pop( $star );
		list( $star ) = explode( ' ', $star->name . ' ' );
		$star   = intval( $star );
		$output = "<div class='stars hotel'><span class='active star-$star'></span></div>";

		return $output;
	}

	/**
	 * Get dropdown list for accommodation menu item
	 *
	 * @param string $ul
	 *
	 * @return string
	 * @since  5.4.4
	 */
	function menu_dropdown( $ul )
	{
		if ( 'locations' == sl_setting( $this->post_type . '_menu_dropdown' ) )
		{
			// Get only locations of accommodation
			global $wpdb;
			$query     = "SELECT t.term_id FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
				WHERE p.post_type = 'accommodation' AND tt.taxonomy = 'location'
				GROUP BY t.term_id";
			$locations = $wpdb->get_col( $query );

			$states = get_terms( 'location', array(
				'include' => $locations,
				'parent'  => 0, // Only get top level - states
			) );
			if ( ! is_array( $states ) || empty( $states ) )
				return $ul;

			// Get current state to add class 'active'
			$current_location = get_query_var( 'location' );
			if ( $current_location )
				$current_location = get_term_by( 'slug', $current_location, 'location' );

			$current_state = $current_location;
			if ( ! empty( $current_state ) && ! is_wp_error( $current_state ) )
			{
				while ( $current_state->parent )
				{
					$current_state = get_term( $current_state->parent, 'location' );
				}
			}
			else
			{
				$current_state = '';
			}

			$ul = '<ul class="dropdown-menu">';
			foreach ( $states as $term )
			{
				$class = ! empty( $current_state ) && $term->term_id == $current_state->term_id ? ' class="active"' : '';
				$ul .= "<li$class><a href='" . home_url( '/area/' . $term->slug ) . "'>{$term->name}</a></li>";
			}
			$ul .= '</ul>';

			return $ul;
		}
		$terms = null;
		switch ( sl_setting( $this->post_type . '_menu_dropdown' ) )
		{
			case 'types':
				$tax = 'type';
				break;
			case 'stars':
				$tax = 'stars';
				break;
			case 'amenities':
				$tax = 'amenity';
				break;
			default:
				return $ul;
		}

		$terms = get_terms( $tax, array(
			'orderby' => 'count',
			'order'   => 'DESC',
			'include' => $terms,
		) );
		if ( ! is_array( $terms ) || empty( $terms ) )
			return $ul;

		$ul = '<ul class="dropdown-menu">';
		foreach ( $terms as $term )
		{
			$class = is_tax( $tax, $term ) ? ' class="active"' : '';
			$ul .= "<li$class><a href='" . get_term_link( $term, $tax ) . "'>{$term->name}</a></li>";
		}
		$ul .= '</ul>';

		return $ul;
	}

	/**
	 * Get parameters for booking page
	 *
	 * @return array
	 */
	public static function get_booked_params()
	{
		return array_merge( parent::get_booked_params(), array(
			// Date
			'checkin'  => '',
			'checkout' => '',
		) );
	}
}

new Sl_Accommodation_Frontend( 'accommodation' );
