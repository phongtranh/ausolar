<?php
$tpl = sprintf( '
	<li class="uploaded photo-detail-container" id="item_%%s">
		<label>%s</label>
		<span class="star dashicons dashicons-star-%%s" data-attachment_id="%%s" title="%s"></span>
		<img src="%%s">
		<input type="text" value="%%s" data-attachment_id="%%s" placeholder="%s">
		<a href="#" class="delete-file button" data-attachment_id="%%s">%s</a>
	</li>',
	__( 'Photo', '7listings' ),
	__( 'Use as featured image', '7listings' ),
	__( 'Photo description', '7listings' ),
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
		$class       = $featured == $photo ? 'filled' : 'empty';
		$description = get_post_field( 'post_excerpt', $photo );

		printf(
			$tpl,
			$photo,
			$class, $photo,
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
<hr>
<?php
$name   = sl_meta_key( 'movies', get_post_type() );
$url    = $movies = get_post_meta( get_the_ID(), $name, true );
$custom = '';
// If a movie has been uploaded
if ( is_numeric( $movies ) )
{
	$url        = '';
	$attachment = wp_get_attachment_link( $movies );
	$custom     = sprintf( '
		<p>
			<strong>%s</strong>: %s
			<input type="hidden" name="%s" value="%s"><br>
			<a class="delete-movie button" href="#">%s</a>
		</p>',
		__( 'Uploaded', '7listings' ),
		$attachment,
		$name, $movies,
		__( 'Delete', '7listings' )
	);
}

printf( '
	<div class="sl-settings upload">
		<div class="sl-label">
			<label>%s</label>
		</div>
		<div class="sl-input">
			<span class="input-prepend">
				<span class="add-on"><i class="icon-youtube-play"></i></span>
				<input type="url" name="%s" value="%s">
				<span class="description">%s</span>
			</span>
		</div>
	</div>
	<div class="sl-settings upload">
		<div class="sl-label">
			<label>&nbsp;</label>
		</div>
		<div class="sl-input">
			%s
			<input type="file" name="%s">
		</div>
	</div>',
	__( 'Video', '7listings' ),
	$name, $url,
	__( 'URL e.g.: Youtube, Vimeo, etc.', '7listings' ),
	$custom, $name
);
