<?php
$tpl = sprintf( '
	<li class="sl-settings uploaded photo-detail-container" id="item_%%s">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<img src="%%s">
			<input type="text" value="%%s" data-attachment_id="%%s" placeholder="%s">
			<a href="#" class="button delete-file" data-attachment_id="%%s">%s</a>
		</div>
	</li>',
	__( 'Photo', '7listings' ),
	__( 'Enter Photo Description', '7listings' ),
	__( 'Delete', '7listings' )
);

$photos = get_post_meta( get_the_ID(), sl_meta_key( 'photos', get_post_type() ), false );
if ( ! empty( $photos ) )
{
	echo '<ul class="reorder">';
	$featured = get_post_thumbnail_id();
	foreach ( $photos as $photo )
	{
		list( $photo_src ) = wp_get_attachment_image_src( $photo, 'sl_thumb_tiny' );
		$description = get_post_field( 'post_excerpt', $photo );

		printf(
			$tpl,
			$photo,
			$photo_src,
			$description, $photo,
			$photo
		);
	}
	echo '</ul>';
}
?>
<div class="sl-settings upload hidden">
	<div class="sl-label">
		<label><?php _e( 'Photo', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<img src="" class="hidden">
		<input type="hidden" name="post_photo_ids[]">
		<a href="#" class="choose-image hidden">&nbsp;</a>
		<a href="#" class="button delete-image hidden"><?php _e( 'Delete', '7listings' ); ?></a>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label>&nbsp;</label>
	</div>
	<div class="sl-input">
		<a href="#" class="button add-file"><?php _e( 'Add Photo', '7listings' ); ?></a>
	</div>
</div>
