<?php

/**
 * This class outputs HTML for social share buttons
 *
 * Note:
 * - It does not enqueue Javascript to display counter
 * - It does not outputs social links
 */
class Sl_Social_Buttons_Share_Buttons
{
	/**
	 * Generate HTML for a single Share Button
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	public static function facebook( $link )
	{
		return sprintf(
			'<a class="facebook" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Share on Facebook', '7listings' ),
			add_query_arg( array(
				'u' => rawurlencode( $link ),
			), 'https://www.facebook.com/sharer/sharer.php' ),
			__( 'Share', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Twitter Button
	 *
	 * @param string $link
	 * @param string $text
	 *
	 * @return string
	 */
	public static function twitter( $url, $text )
	{
		$href = 'https://twitter.com/intent/tweet?' . http_build_query( compact( 'url', 'text' ), '', '&amp;' );
		
		return sprintf(
			'<a class="twitter" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Tweet on Twitter', '7listings' ),
			$href,
			__( 'Tweet', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Google+ Button
	 *
	 * @param string $link
	 *
	 * @return string
	 */
	public static function google( $url )
	{
		$href = 'https://plus.google.com/share?' . http_build_query( compact( 'url' ), '', '&amp;' );

		return sprintf(
			'<a class="googleplus" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Share on Google+', '7listings' ),
			$href,
			__( '+1', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Pinterest Button
	 *
	 * @param string $link
	 * @param string $text
	 * @param string $img_link
	 *
	 * @return string
	 */
	public static function pinterest( $link, $text, $img_link )
	{
		return sprintf(
			'<a class="pinterest" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Pin it on Pinterest', '7listings' ),
			htmlentities( add_query_arg( array(
				'url'         => rawurlencode( $link ),
				'media'       => rawurlencode( $img_link ),
				'description' => rawurlencode( $text )
			), 'http://pinterest.com/pin/create/button' ) ),
			__( 'Pin', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Reddit Button
	 *
	 * @param string $link
	 * @param string $text
	 *
	 * @return string
	 */
	public static function reddit( $link, $text )
	{
		return sprintf(
			'<a class="reddit" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Submit to Reddit', '7listings' ),
			htmlentities( add_query_arg( array(
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text )
			), '//www.reddit.com/submit' ) ),
			__( 'Reddit', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single StumbleUpon Button
	 *
	 * @param string $link
	 * @param string $text
	 *
	 * @return string
	 */
	public static function stumbleupon( $link, $text )
	{
		return sprintf(
			'<a class="stumbleupon" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Submit to StumbleUpon', '7listings' ),
			htmlentities( add_query_arg( array(
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text )
			), 'http://www.stumbleupon.com/submit' ) ),
			__( 'Stumble', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Linkedin Button
	 *
	 * @param string $link
	 * @param string $text
	 *
	 * @return string
	 */
	public static function linkedin( $link, $text )
	{
		return sprintf(
			'<a class="linkedin" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
			__( 'Share on LinkedIn', '7listings' ),
			htmlentities( add_query_arg( array(
				'mini'  => 'true',
				'url'   => rawurlencode( $link ),
				'title' => rawurlencode( $text )
			), 'http://www.linkedin.com/shareArticle' ) ),
			__( 'Share', '7listings' )
		);
	}

	/**
	 * Generate HTML for a single Email Button
	 *
	 * @param string $link
	 * @param string $text
	 *
	 * @return string
	 */
	public static function email( $link, $text )
	{
		return sprintf(
			'<a class="email" target="_blank" rel="nofollow" title="%s" href="mailto:?subject=%s&amp;body=%s">%s</a>',
			__( 'Send via email', '7listings' ),
			rawurlencode( $text ),
			rawurlencode( $link ),
			__( 'Email', '7listings' )
		);
	}
}
