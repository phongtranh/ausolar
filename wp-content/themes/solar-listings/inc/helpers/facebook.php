<?php

$app_id = '492232084268237';
$app_secret = '385fc549fe09bc9def0e34d5d8837df6';

function facebook_share_button($link, $name, $description)
{
	$url = sprintf('https://www.facebook.com/dialog/feed?app_id=%s&display=popup&name=%s&description=%s&link=%s&redirect_uri=%s',
		'492232084268237',
		urlencode ( $name ),
		urlencode( $description ),
		urlencode( $link ),
		urlencode( $link )
	);
	return sprintf(
		'<a class="facebook" target="_blank" rel="nofollow" title="%s" href="%s">%s</a>',
		__( 'Share on Facebook', '7listings' ),
		$url,
		__( 'Facebook', '7listings' )
	);
}