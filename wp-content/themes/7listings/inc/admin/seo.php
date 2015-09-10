<?php
add_filter( 'wpseo_pre_analysis_post_content', 'sl_get_analysis_content', 10, 2 );

/**
 * Get HTML of page to analyse, not only post content
 * Used in WordPress SEO plugin
 *
 * @param string $content
 * @param object $post
 *
 * @return string
 */
function sl_get_analysis_content( $content, $post )
{
	$url = get_permalink( $post );
	if ( ! $url )
		return $content;

	$request = wp_remote_get( $url );
	$html    = wp_remote_retrieve_body( $request );
	if ( ! $html )
		return $content;

	$dom                      = new DOMDocument;
	$dom->preserveWhiteSpace  = false; // Remove redundant white spaces
	$dom->recover             = true; // Allow to parse non-well formatted HTML
	$dom->strictErrorChecking = false; // Disable strict error checking

	// Make sure DOM load correct HTML
	libxml_use_internal_errors( true );
	@$dom->loadHTML( $html );
	libxml_use_internal_errors( false );
	$xpath = new DOMXPath( $dom );

	// Main content
	$query           = '//div[@id="content"]';
	$element         = $xpath->query( $query );
	$section_content = false === $element || ! $element->length ? '' : $dom->saveHTML( $element->item( 0 ) );

	// Featured title area
	$query            = '//div[@id="featured"]';
	$element          = $xpath->query( $query );
	$section_featured = false === $element || ! $element->length ? '' : $dom->saveHTML( $element->item( 0 ) );

	if ( $section_content || $section_featured )
		$content = $section_featured . $section_content;

	return $content;
}
