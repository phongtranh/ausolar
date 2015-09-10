<?php
if ( ! sl_setting( get_post_type() . '_featured_graphics' ) )
	return;

$featured = intval( get_post_meta( get_the_ID(), 'featured', true ) );
if ( 1 == $featured )
	echo '<div class="featured-listing">' . __( 'Featured', '7listings' ) . '</div>';
elseif ( 2 == $featured )
	echo '<div class="featured-listing star-listing">' . __( 'Favorite', '7listings' ) . '</div>';
