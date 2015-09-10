<?php
if ( ! sl_setting( get_post_type() . '_similar_enable' ) )
	return;

$class = 'sl-list posts ' . get_post_type() . 's';
$class .= ' columns-' . sl_setting( get_post_type() . '_similar_columns' );

echo "<aside id='related' class='$class'>";

// Similar listings
$total = sl_setting( get_post_type() . '_similar_display' );
$by    = sl_setting( get_post_type() . '_similar_by' );

// Common query args
$args = array(
	'post_type'      => get_post_type(),
	'post_status'    => 'publish',
	'posts_per_page' => $total,
	'post__not_in'   => array( get_the_ID() )
);

// Similar by type
$types    = wp_get_post_terms( get_the_ID(), sl_meta_key( 'tax_type', get_post_type() ) );
$type_ids = array();
foreach ( $types as $type )
{
	$type_ids[] = $type->term_id;
}
$type_args = array_merge( $args, array(
	'tax_query' => array(
		array(
			'taxonomy' => sl_meta_key( 'tax_type', get_post_type() ),
			'field'    => 'id',
			'terms'    => $type_ids,
		),
	),
) );

// Similar by price
$price = (int) get_post_meta( get_the_ID(), 'price_from', true );
$from  = $price - 40;
$to    = $price + 40;

$price_args = array_merge( $args, array(
	'meta_query' => array(
		array(
			'key'     => 'price_from',
			'value'   => array( $from, $to ),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		),
	),
) );

switch ( $by )
{
	case 'price':
		$set_args = $price_args;
		break;
	default:
		$set_args = $type_args;
		break;
}
$before = '<h3>' . sl_setting( get_post_type() . '_similar_title' ) . '</h3>';
$after  = '';

global $sl_not_duplicated;
$callback_function     = 'sl_similar_' . get_post_type();
$rebuild_args_function = 'sl_' . get_post_type() . '_rebuild_args';

$num_posts = sl_query_with_priority( $set_args, $callback_function, $before, '' );
$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );

// If have posts, but not enough
if ( $num_posts )
{
	if ( $num_posts < $total && $by != 'type' )
	{
		$num_posts += sl_query_with_priority( $type_args, $callback_function, '', '', true, $total - $num_posts );
		$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );
	}
	if ( $num_posts < $total && $by != 'price' )
	{
		$num_posts += sl_query_with_priority( $price_args, $callback_function, '', '', true, $total - $num_posts );
		$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );
	}
	if ( $num_posts < $total )
	{
		$num_posts += sl_query_with_priority( $args, $callback_function, '', '', true, $total - $num_posts );
	}
}
// If no posts, query another
else
{
	if ( $by != 'type' )
	{
		$num_posts = sl_query_with_priority( $type_args, $callback_function, $before, '' );
		$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );
	}

	if ( $num_posts )
	{
		if ( $num_posts < $total && $by != 'price' )
		{
			$num_posts += sl_query_with_priority( $price_args, $callback_function, '', '', true, $total - $num_posts );
			$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );
		}
		if ( $num_posts < $total )
			$num_posts += sl_query_with_priority( $args, $callback_function, '', '', true, $total - $num_posts );
	}
	else
	{
		if ( $by != 'price' )
		{
			$num_posts = sl_query_with_priority( $price_args, $callback_function, $before, '' );
			$rebuild_args_function( $sl_not_duplicated, $args, $type_args, $price_args );
		}

		if ( $num_posts )
		{
			if ( $num_posts < $total )
				$num_posts += sl_query_with_priority( $args, $callback_function, '', '', true, $total - $num_posts );
		}
		else
			$num_posts = sl_query_with_priority( $args, $callback_function, $before, '' );
	}
}

if ( $num_posts )
	echo $after;

echo '</aside>';
