<?php

class Sl_Tour_Frontend extends Sl_Core_Frontend
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
		// Add error message for booking page when total guests exceed tour allocation
		if ( get_query_var( 'book' ) && $this->post_type == get_post_type() )
		{
			$params['tour'] = array(
				'exceedAllocation' => __( 'Total amount of guests must not exceed <strong>%d</strong>.<br>Please select less guests or a different date.', '7listings' ),
				'guestTitle'       => __( 'Guest', '7listings' ),
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
	public function meta_title( $title = '', $sep = '' )
	{
		$title = parent::meta_title( $title, $sep );

		$replacement = array(
			'%SEP%'     => $sep,
			'%LABEL%'   => sl_setting( $this->post_type . '_label' ),
			'%CITY%'    => sl_setting( 'general_city' ),
			'%STATE%'   => sl_setting( 'state' ),
			'%COUNTRY%' => sl_setting( 'country' ),
		);

		$taxonomies = array( 'features', 'tour_type' );
		foreach ( $taxonomies as $tax )
		{
			if ( ! is_tax( $tax ) )
				continue;

			$tax                   = str_replace( "{$this->post_type}_", '', $tax );
			$term                  = get_queried_object();
			$replacement['%TERM%'] = $term->name;
			$title                 = sl_setting( "{$this->post_type}_{$tax}_title" );
			$title                 = strtr( $title, $replacement );
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
			'title'      => '',
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
			'post_type' => 'tour',
		);
		sl_build_query_args( $query_args, $args );

		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$args['class']           = 'slide';
		$args['image_size']      = 'sl_pano_large';
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
			'post_type' => 'tour',
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

		$class = 'sl-list posts tours';
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

			// Default link is listing archive page
			$link = get_post_type_archive_link( 'tour' );

			// Fix ugly post type archive link
			if ( strpos( $link, '?' ) )
				$link = home_url( sl_setting( 'tour_base_url' ) . '/' );

			// If set 'type' taxonomy, get link to that taxonomy page
			if ( $args['type'] )
			{
				$term = get_term( absint( $args['type'] ), sl_meta_key( 'tax_type', 'tour' ) );
				if ( ! is_wp_error( $term ) )
				{
					// Don't show view more listings if the term doesn't have more listings
					if ( $term->count <= $args['number'] )
						$show = false;

					$term_link = get_term_link( $term, sl_meta_key( 'tax_type', 'tour' ) );
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
	 * Show booking button at the bottom of content when there's only 1 booking resource
	 *
	 * @return void
	 */
	public function single_booking_button()
	{
		$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', $this->post_type ), true );
		// Show price only if there's only one booking resource
		if ( empty( $resources ) || 1 != count( $resources ) )
			return;

		$resource = current( $resources );
		$times    = Sl_Tour_Helper::booking_times( $resource, '<h3>' . __( 'Departs', '7listings' ) . '</h3>', false );
		$button   = apply_filters( 'booking_button', '', $resource );

		$type = isset( $resource['lead_in_rate'] ) ? $resource['lead_in_rate'] : 'adult';

		$price_types = array_merge( array( $type ), array( 'adult', 'child', 'senior', 'family', 'infant' ) );
		$price_types = array_unique( $price_types );

		$prices      = array();
		$price       = '';
		foreach ( $price_types as $type )
		{
			// If price is OFF, it will be saved as an empty string. We will ignore such price
			if ( isset( $resource["price_$type"] ) && '' !== $resource["price_$type"] )
			{
				$prices[] = $resource["price_$type"];

				$price .= '<div class="passenger-type '.$type.'"><label class="guest">' . ucfirst( $type ) . '</label>' . Sl_Currency::format( $resource["price_$type"], 'class=entry-meta' );

				if ( 'family' == $type )
				{
					$price .= "<label for='guest-family' class='sl-label-inline guest hint--right' data-hint='" . __( '2 Adults + 2 Children', '7listings' ) . "'>";
					$price .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="60" height="40" viewBox="0 0 60 40" enable-background="new 0 0 60 40" xml:space="preserve" class="family-icon"><circle cx="8.2" cy="5.2" r="2.9"/><circle cx="24.7" cy="4.4" r="3"/><path d="M27.8 8.3h-5.9c-1.9 0-2.7 1-3.4 3.2l-2.4 5.9 -2.3-5.8c-0.3-0.9-1.5-2.6-3.6-2.7H6.2C4.1 9 2.9 10.6 2.6 11.6L0.1 19.9c-0.5 1.8 1.8 2.5 2.3 0.8l2.2-7.7h0.6L1.4 26.5h3.6v10.1c0 1.8 2.8 1.8 2.8 0v-10.1h0.8v10.1c0 1.8 2.7 1.8 2.7 0v-10.1h3.7l-3.9-13.5h0.7l3.1 8c0 0 0 0 0 0 0.2 1.2 2 1.5 2.5 0.1l3.4-8.2h0.3v23.3c0 2.5 3.4 2.4 3.4 0V22.7h0.5v13.5c0.1 2.4 3.5 2.5 3.5 0V12.9h0.6v8.5c0 1.8 2.6 1.8 2.6 0v-9.2C31.6 10.3 30.1 8.3 27.8 8.3z"/><circle cx="41.6" cy="13.2" r="2.2"/><path d="M45.9 18.1c-0.3-0.7-1.2-2-2.7-2h-3c-1.6 0-2.5 1.3-2.7 2l-1.9 6.3c-0.4 1.4 1.3 1.9 1.8 0.6l1.7-5.8h0.5l-2.9 10.2h2.7v7.6c0 1.4 2.1 1.4 2.1 0v-7.6h0.6v7.6c0 1.4 2 1.4 2 0v-7.6h2.8l-3-10.2h0.5l2.3 6c0.4 1.3 2.1 0.8 1.8-0.6L45.9 18.1zM59.8 18.8c-0.1-0.6-0.3-0.9-0.7-1.3 -0.4-0.4-0.9-0.7-1.4-0.8 -0.5-0.1-5.3-0.1-5.8 0 -1 0.2-1.8 1-2.1 2 -0.1 0.4-0.1 7.6 0 7.9 0.2 0.4 0.7 0.6 1.1 0.5 0.3-0.1 0.5-0.2 0.6-0.4 0.1-0.2 0.1-0.3 0.1-3.4v-3.2h0.2 0.2v8.5c0 5.7 0 8.5 0.1 8.7 0.1 0.3 0.4 0.6 0.8 0.8 0.2 0.1 0.8 0 1-0.1 0.2-0.1 0.5-0.4 0.6-0.6 0.1-0.1 0.1-1.1 0.1-5.2v-5h0.2 0.2v5c0 5.5 0 5.2 0.3 5.5 0.3 0.3 0.5 0.4 1 0.4 0.4 0 0.7-0.1 1-0.4 0.3-0.3 0.3 0.3 0.3-9.1v-8.5h0.2 0.2l0 3.3 0 3.3 0.1 0.2c0.2 0.3 0.8 0.5 1.2 0.3 0.3-0.1 0.4-0.2 0.5-0.5 0.1-0.2 0.1-0.7 0.1-3.8C59.9 19.4 59.9 19.2 59.8 18.8zM54.2 16.2c0.2 0.1 0.4 0.1 0.8 0.1 0.4 0 0.5 0 0.9-0.2 0.5-0.2 0.8-0.6 1-1 0.1-0.3 0.1-0.4 0.1-0.9 0-0.6 0-0.6-0.2-1 -0.8-1.6-3.1-1.6-4 0 -0.2 0.3-0.2 0.4-0.2 0.9 0 0.5 0 0.6 0.2 0.9C53.1 15.6 53.5 15.9 54.2 16.2z"/></svg>';
					$price .= '</label>';
				}

				$price .= '</div>';
			}
		}

		echo $times;

		if ( 1 == count( $prices ) )
		{
			$price = sl_listing_element( 'price' );

			if ( $button )
				echo "<p>$price  $button</p>";
			else
				echo $price;
		}
		else
		{
			echo '<div class="tour-prices"> ';
			echo $price;
			echo '</div>';

			if ( $button )
				echo "<p> $button</p>";
		}
	}

	/**
	 * Show booking button for a booking resource
	 * Overwrite parent method with additional check: if booking resource does not have booking times, return nothing
	 *
	 * @param string $output   Button markup
	 * @param array  $resource Resource params
	 *
	 * @return string
	 */
	public function booking_button( $output, $resource )
	{
		if ( $this->post_type != get_post_type() || ! Sl_Tour_Helper::booking_times( $resource, '', false ) )
			return $output;

		return parent::booking_button( $output, $resource );
	}

	/**
	 * Get dropdown list for tour menu item
	 *
	 * @param string $ul
	 *
	 * @return string
	 * @since  5.4.4
	 */
	function menu_dropdown( $ul )
	{
		$dropdown = sl_setting( $this->post_type . '_menu_dropdown' );
		if ( 'locations' == $dropdown )
		{
			// Get only locations of tour
			global $wpdb;
			$query     = "SELECT t.term_id FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
				WHERE p.post_type = 'tour' AND tt.taxonomy = 'location'
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
		switch ( $dropdown )
		{
			case 'types':
				$tax = $this->post_type . '_type';
				break;
			case 'features':
				$tax = 'features';
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
			'day'           => '',

			// Time
			'daily_depart'  => '', // Daily
			'custom_depart' => '', // Custom/Charter
			'sun_depart'    => '', // Specific days
			'mon_depart'    => '',
			'tue_depart'    => '',
			'wed_depart'    => '',
			'thu_depart'    => '',
			'fri_depart'    => '',
			'sat_depart'    => '',

			// Guests
			'adults'        => '',
			'children'      => '',
			'seniors'       => '',
			'families'      => '',
			'infants'       => '',
		) );
	}
}

new Sl_Tour_Frontend( 'tour' );
