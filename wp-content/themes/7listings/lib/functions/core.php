<?php
/**
 * The core functions file for the Peace framework
 * Adapted from Hybrid framework
 */

/**
 * Defines the theme prefix
 *
 * @uses get_template() Defines the theme prefix based on the theme directory
 *
 * @return string The prefix of the theme
 */
function peace_get_prefix()
{
	return sanitize_key( apply_filters( 'peace_prefix', get_template() ) );
}

/**
 * Easily add action hooks to the theme
 *
 * @uses peace_get_prefix() Gets the theme prefix
 *
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed  $arg Optional additional arguments which are passed on to the functions hooked to the action.
 *
 * @return void
 */
function peace_action( $tag = '', $arg = '' )
{
	if ( empty( $tag ) )
		return;

	$pre = peace_get_prefix();

	// Get the args passed into the function and remove $tag
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	// Always do 'peace' hook
	if ( 'peace' != $pre )
	{
		do_action_ref_array( "peace_{$tag}", $args );

		foreach ( (array) peace_get_context() as $context )
			do_action_ref_array( "peace_{$context}_{$tag}", $args );
	}

	// Do actions on the basic hook
	do_action_ref_array( "{$pre}_{$tag}", $args );

	// Loop through context array and fire actions on a contextual scale
	foreach ( (array) peace_get_context() as $context )
		do_action_ref_array( "{$pre}_{$context}_{$tag}", $args );
}

/**
 * Easily add filter hooks to the theme
 *
 * @uses peace_get_prefix() Gets the theme prefix.
 *
 * @param string $tag   Usually the location of the hook but defines what the base hook is
 * @param mixed  $value The value on which the filters hooked to $tag are applied on
 *
 * @return mixed $value The value after it has been filtered
 */
function peace_filters( $tag = '', $value = '' )
{
	if ( empty( $tag ) )
		return false;

	$pre = peace_get_prefix();

	// Get the args passed into the function and remove $tag
	$args = func_get_args();
	array_splice( $args, 0, 1 );

	// Always apply 'peace' hook
	if ( 'peace' != $pre )
	{
		$value = $args[0] = apply_filters_ref_array( "peace_{$tag}", $args );

		foreach ( (array) peace_get_context() as $context )
			$value = $args[0] = apply_filters_ref_array( "peace_{$context}_{$tag}", $args );
	}

	// Apply filters on the basic hook
	$value = $args[0] = apply_filters_ref_array( "{$pre}_{$tag}", $args );

	// Loop through context array and apply filters on a contextual scale
	foreach ( (array) peace_get_context() as $context )
		$value = $args[0] = apply_filters_ref_array( "{$pre}_{$context}_{$tag}", $args );

	return $value;
}


/**
 * Peace's main contextual function. This allows code to be used more than once without running
 * hundreds of conditional checks within the theme. It returns an array of contexts based on what
 * page a visitor is currently viewing on the site. This function is useful for making dynamic/contextual
 * classes, action and filter hooks, and handling the templating system.
 *
 * Note that time and date can be tricky because any of the conditionals may be true on time-/date-
 * based archives depending on several factors. For example, one could load an archive for a specific
 * second during a specific minute within a specific hour on a specific day and so on.
 *
 * @return array $context Several contexts based on the current page.
 */
function peace_get_context()
{
	$context = array();
	$object = get_queried_object();
	$object_id = get_queried_object_id();

	// Front page of the site
	if ( is_front_page() )
		$context[] = 'home';

	// Blog page
	if ( is_home() )
	{
		$context[] = 'blog';
	}

	// Singular views
	elseif ( is_singular() )
	{
		$context[] = 'singular';
		$context[] = "singular-{$object->post_type}";
		$context[] = "singular-{$object->post_type}-{$object_id}";

		if ( is_single() )
			$context[] = 'single';
		elseif ( is_page() )
			$context[] = 'page';
	}

	// Archive views
	elseif ( is_archive() )
	{
		$context[] = 'archive';

		// Post type archives
		if ( is_post_type_archive() )
		{
			$post_type = get_post_type_object( get_query_var( 'post_type' ) );
			$context[] = "archive-{$post_type->name}";
		}

		// Taxonomy archives
		if ( is_tax() || is_category() || is_tag() )
		{
			$context[] = 'taxonomy';
			$context[] = "taxonomy-{$object->taxonomy}";

			$slug = ( 'post_format' == $object->taxonomy ) ? str_replace( 'post-format-', '', $object->slug ) : $object->slug;
			$context[] = "taxonomy-{$object->taxonomy}-" . sanitize_html_class( $slug, $object->term_id );
		}

		// User/author archives
		if ( is_author() )
		{
			$user_id = get_query_var( 'author' );
			$context[] = 'user';
			$context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $user_id ), $user_id );
		}

		// Date archives
		if ( is_date() )
		{
			$context[] = 'date';

			if ( is_year() )
				$context[] = 'year';

			if ( is_month() )
				$context[] = 'month';

			if ( get_query_var( 'w' ) )
				$context[] = 'week';

			if ( is_day() )
				$context[] = 'day';
		}

		// Time archives
		if ( is_time() )
		{
			$context[] = 'time';

			if ( get_query_var( 'hour' ) )
				$context[] = 'hour';

			if ( get_query_var( 'minute' ) )
				$context[] = 'minute';
		}
	}

	// Search results
	elseif ( is_search() )
	{
		$context[] = 'search';
	}

	// Error 404 pages
	elseif ( is_404() )
	{
		$context[] = 'error-404';
	}

	return apply_filters( 'peace_context', $context );
}
