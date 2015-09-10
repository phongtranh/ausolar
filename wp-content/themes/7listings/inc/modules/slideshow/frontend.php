<?php

/**
 * This class will hold all things for slideshow management page
 */
class Sl_Slideshow_Frontend
{
	/**
	 * @var string Post type
	 */
	public $post_type = 'slideshow';

	/**
	 * Constructor
	 *
	 * @return Sl_Slideshow_Frontend
	 */
	function __construct()
	{
		add_shortcode( $this->post_type, array( $this, 'shortcode' ) );
	}

	/**
	 * Display slideshow
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	function shortcode( $atts )
	{
		if ( ! isset( $atts['id'] ) )
			return '';

		$photos = get_post_meta( $atts['id'], sl_meta_key( 'photos', $this->post_type ), false );
		if ( empty( $photos ) )
			return '';

		wp_enqueue_script( 'jquery-cycle2' );

		// Slider options
		$timeout = get_post_meta( $atts['id'], 'slideshow_speed', true );
		if ( ! $timeout )
			$timeout = 3000;
		$speed = get_post_meta( $atts['id'], 'animation_speed', true );
		if ( ! $speed )
			$speed = 300;
		$options = array(
			'fx'          => get_post_meta( $atts['id'], 'animation', true ),
			'loop'        => 1 - get_post_meta( $atts['id'], 'loop', true ),
			'speed'       => $speed,
			'timeout'     => $timeout,
			'auto-height' => 'container',
			'slides'      => '> .thumbnail',
		);

		// Open div
		$open = "<div class='cycle-slideshow'";
		foreach ( $options as $k => $v )
		{
			$open .= " data-cycle-$k='$v'";
		}
		$open .= '>';

		// Slider HTML
		$html         = '';
		$tpl          = '<figure class="thumbnail"><img class="photo" src="%s" alt="%s"></figure>';
		$height       = get_post_meta( $atts['id'], 'height', true );
		$fixed_height = $height && get_post_meta( $atts['id'], 'fixed_height', true );
		foreach ( $photos as $photo )
		{
			/**
			 * Get image source URL
			 * If slideshow has fixed height, we resize image
			 * When resizing images, we have only fixed height in settings. To make resize work, we set width 1200px
			 * which is big enough
			 */
			if ( $fixed_height )
			{
				$photo_src = sl_resize( $photo, 1200, $height );
			}
			else
			{
				$photo_src = wp_get_attachment_url( $photo );
			}

			$description = get_post_field( 'post_excerpt', $photo );
			$html .= sprintf(
				$tpl,
				$photo_src,
				$description
			);
		}

		// Next/prev
		if ( get_post_meta( $atts['id'], 'nextprev', true ) )
			$html .= '<div class="cycle-prev"></div><div class="cycle-next"></div>';

		// Pagination
		if ( get_post_meta( $atts['id'], 'pagination', true ) )
		{
			if ( 'thumbnails' == get_post_meta( $atts['id'], 'pagination_type', true ) )
			{
				$html .= '<div class="cycle-pager">';
				foreach ( $photos as $photo )
				{
					list( $thumb ) = wp_get_attachment_image_src( $photo, 'sl_thumb_tiny' );
					$description = get_post_field( 'post_excerpt', $photo );
					$html .= "<figure class='thumbnail'><img class='photo' src='$thumb' alt='$description'></figure>";
				}
				$html .= '</div>';
			}
			else
			{
				$html .= '<div class="cycle-pager"></div>';
			}
		}

		return $open . $html . '</div>';
	}
}

new Sl_Slideshow_Frontend;
