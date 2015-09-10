<?php
namespace ASQ;

/**
 * Add/change/edit content for RSS
 * @package ASQ
 */
class RSS
{
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		add_filter( 'the_excerpt_rss', [ $this, 'add_image' ] );
		add_filter( 'the_content_feed', [ $this, 'add_image' ] );
	}

	/**
	 * Add image to the RSS content
	 * Image can be featured image or first image of post body
	 * @param string $content Post content
	 * @return string
	 */
	public function add_image( $content )
	{
		if ( ! function_exists( 'get_the_image' ) || is_page_template( 'templates/template-mcrss.php' ) )
			return $content;

		$image = get_the_image( [
			'order'        => [ 'featured', 'scan' ],
			'scan'         => true,
			'echo'         => false,
			'size'         => 'sl_pano_medium',
			'link_to_post' => false,
			'before'       => '<div style="text-align:center;margin:1em auto">',
			'after'        => '</div>'
		] );
		$image = str_replace( ' itemprop="image"', '', $image );

		return $image . $content;
	}

	/**
	 * Add enclosure tag to feed items
	 * @param string $image_size Image size
	 */
	static public function enclosure( $image_size = 'thumbnail' )
	{
		$thumbnail_id = get_post_thumbnail_id();
		if ( ! $thumbnail_id )
			return;

		$file = get_attached_file( $thumbnail_id );
		list( $src ) = wp_get_attachment_image_src( $thumbnail_id, $image_size );
		$size   = filesize( $file );
		$type   = get_post_mime_type( $thumbnail_id );
		$width  = get_option( $image_size . '_size_w' );
		$height = get_option( $image_size . '_size_h' );

		echo '<enclosure url="', $src, '" length="', $size, '" type="', $type, '" />';
		echo '<media:content url="', $src, '" width="', $width, '" height="', $height, '" medium="image" />';
	}
}

new RSS;
