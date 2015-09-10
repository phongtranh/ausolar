<?php
/**
 * Display numeric pagination
 *
 * @param null $query
 *
 * @return void
 */
function peace_numeric_pagination( $query = null )
{
	global $wp_query;

	if ( empty( $query ) )
		$query = $wp_query;

	// Don't print empty markup in archives if there's only one page.
	if ( $query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$big = 9999;
	$args = array(
		'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'total'     => $query->max_num_pages,
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'prev_text' => __( '&larr; Previous', 'peace' ),
		'next_text' => __( 'Next &rarr;', 'peace' ),
		'type'      => 'plain',
	);
	$args = apply_filters( __FUNCTION__, $args );

	$links = paginate_links( $args );
	if ( $links )
		echo "<nav class='pagination'>$links</nav>";
}

/**
 * Display navigation to next/previous pages when applicable
 *
 * @return void
 */
function peace_single_pagination()
{
	global $post;

	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next = get_adjacent_post( false, '', false );

	if ( !$next && !$previous )
		return;
	?>
	<nav class="pagination">

		<?php previous_post_link( '%link', _x( '&larr;', 'Previous post link', 'peace' ) . ' %title' ); ?>
		<?php next_post_link( '%link', '%title ' . _x( '&rarr;', 'Next post link', 'peace' ) ); ?>

	</nav>
	<?php
}
