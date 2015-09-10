<?php
add_filter( 'user_contactmethods', 'sl_contactmethods' );

/**
 * Add/Remove Contact Methods
 *
 * @param array $contactmethods
 *
 * @return array
 */
function sl_contactmethods( $contactmethods )
{
	$contactmethods['twitter']     = 'Twitter';
	$contactmethods['facebook']    = 'Facebook';
	$contactmethods['googleplus']  = 'Google+';
	$contactmethods['linkedin']    = 'LinkedIn';
	$contactmethods['pinterest']   = 'Pinterest';
	$contactmethods['instagram']   = 'Instagram';
	$contactmethods['rss']         = 'RSS';
	$contactmethods['mobile']      = __( 'Mobile', '7listings' );
	$contactmethods['direct_line'] = __( 'Direct Line', '7listings' );

	// Remove Contact Methods
	unset( $contactmethods['aim'] );
	unset( $contactmethods['yim'] );
	unset( $contactmethods['jabber'] );

	return $contactmethods;
}
