<?php

function google_share_button( $link )
{
	return sprintf(
		'<a class="google-plus" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
		__( 'Share on Google+', '7listings' ),
		add_query_arg( array(
			'url' => rawurlencode( $link ),
		), 'https://plus.google.com/share' ),
		__( 'Google', '7listings' )
	);
}