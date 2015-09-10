<?php
/**
 * Get excerpt
 * This function get content from post excerpt first. If no post excerpt then get post content. Then truncate the text
 *
 * The difference between this function and get_the_excerpt() is:
 * get_the_excerpt() returns full post excerpt if it presents
 * while this function always truncate the text to desired number of words
 *
 * @param int $num_words Number of returned words
 *
 * @see get_the_excerpt()
 * @see wp_trim_excerpt()
 *
 * @param int|WP_Post $post Post ID or post object
 *
 * @return string
 */
function sl_excerpt( $num_words = null, $post = null )
{
	// Get text from post excerpt first. If no post excerpt, get post content
	$post = get_post( $post );
	$text = get_post_field( 'post_excerpt', $post );
	if ( ! $text )
		$text = get_post_field( 'post_content', $post );

	// Default excerpt length for posts
	if ( ! $num_words )
		$num_words = sl_setting( 'post_archive_desc' );

	$excerpt = wp_trim_words( strip_shortcodes( $text ), $num_words, sl_setting( 'excerpt_more' ) );

	/**
	 * Remove naked link in the excerpt, because it's not rendered (in case of oEmbeds or post formats)
	 *
	 * @since 5.1.5
	 */
	$excerpt = preg_replace( '@https?://[^\s]*@', '', $excerpt );

	return $excerpt;
}
