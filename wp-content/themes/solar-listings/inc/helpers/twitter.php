<?php

function twitter_share_button( $link, $text )
{
	return sprintf(
		'<a class="twitter" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
		__( 'Tweet on Twitter', '7listings' ),
		add_query_arg( array(
			'url'  => rawurlencode( $link ),
			'text' => rawurlencode( $text ),
		), 'https://twitter.com/intent/tweet' ),
		__( 'Tweet', '7listings' )
	);
}