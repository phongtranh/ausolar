<?php
if ( ! sl_setting( get_post_type() . '_single_featured_title_image' ) )
	return;

if ( has_post_thumbnail() )
{
	$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'sl_feat_large' );
	echo '<img src="' . $image_url[0] . '" alt="' . the_title_attribute( 'echo=0' ) . '" />';
}
