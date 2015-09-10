<?php
$post_type = get_post_type();
if ( 'company' != $post_type && sl_setting( $post_type . '_single_logo' ) )
{
	if ( $src = sl_broadcasted_image_src( sl_meta_key( 'logo', $post_type ), 'full' ) )
		echo '<img src="' . $src . '" class="brand-logo" alt="' . the_title_attribute( 'echo=0' ) . '">';
}

if ( 'company' == $post_type )
{
	$membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true );
	if ( ! $membership )
		$membership = 'none';
	if ( sl_setting( "{$post_type}_single_logo_{$membership}" ) )
	{
		if ( $src = sl_broadcasted_image_src( sl_meta_key( 'logo', $post_type ), 'full' ) )
			echo '<img src="' . $src . '" class="brand-logo" alt="' . the_title_attribute( 'echo=0' ) . '">';
	}
}
