<?php

function solar_override_breadcrumbs_title( $current )
{
	if ( is_singular() )
	{
		$title = get_post_meta( get_the_ID(), '_yoast_wpseo_bctitle', true );
		
		if ( ! empty( $title ) )
			return $title;
	}

	return $current;
}

add_filter( 'sl_breadcrumbs_current', 'solar_override_breadcrumbs_title' );