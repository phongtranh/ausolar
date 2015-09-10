<?php
// Not enable booking?
if ( ! sl_setting( get_post_type() . '_booking' ) || ! sl_setting( get_post_type() . '_book_in_archive' ) )
	return;

// Not list layout?
if ( 'list' != sl_setting( get_post_type() . '_archive_layout' ) )
	return;

$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', get_post_type() ), true );
if ( empty( $resources ) )
	return;
?>

<section class="resources <?php echo get_post_type(); ?>s">

	<?php
	foreach ( $resources as $k => $resource )
	{
		$resource['resource_id'] = $k;

		$price          = sl_listing_element( 'resource_price', array( 'resource' => $resource ) );
		$booking_button = apply_filters( 'booking_button', '', $resource );
		?>
		<article class="post resource <?php echo get_post_type(); ?>">
			<?php if ( $resource['photos'] ) : ?>
				<figure class="thumbnail"><?php echo sl_resource_photo( $resource['photos'], 'sl_thumb_tiny' ); ?></figure>
			<?php endif; ?>
			<div class="details">
				<h4 class="entry-title"><?php echo $resource['title']; ?></h4>
				<p class="entry-summary excerpt">
					<?php
					echo wp_trim_words(
						$resource['desc'],
						sl_setting( get_post_type() . '_archive_resource_desc' ),
						sl_setting( 'excerpt_more' )
					);
					?>
				</p>
				<?php echo $price . $booking_button; ?>
			</div>
		</article>
	<?php
	}
	?>

</section>

