<?php
$resources = get_post_meta( get_the_ID(), sl_meta_key( 'booking', get_post_type() ), true );

// Show booking resources only if there are 2+
if ( empty( $resources ) || 1 == count( $resources ) )
	return;

$class = 'Sl_' . ucfirst( get_post_type() ) . '_Helper';
?>
<section id="booking-resources" class="sl-list resources <?php echo get_post_type(); ?>s">

	<h2 class="booking title"><?php the_title(); ?> <?php echo sl_setting( get_post_type() . '_label' ); ?>s</h2>

	<?php
	foreach ( $resources as $k => $resource )
	{
		?>
		<article class="post resource <?php echo get_post_type(); ?>">

			<?php
			if ( ! empty( $resource['photos'] ) )
			{
				?>
				<figure class="thumbnail"><?php echo sl_resource_photo( $resource['photos'], 'sl_thumb_tiny' ); ?></figure>
			<?php
			}
			?>
			<div class="details">
				<h3 class="title"><?php echo $resource['title']; ?></h3>
				<p class="description">
					<?php
					echo $resource['desc'];
					if ( method_exists( $class, 'booking_times' ) )
						call_user_func( array( $class, 'booking_times' ), $resource, '<h4>' . __( 'Departs', '7listings' ) . '</h4>' );
					?>
				</p>
				<?php
				$resource['resource_id'] = $k;

				if ( $price = sl_listing_element( 'resource_price', array( 'resource' => $resource ) ) )
				{
					$book_button = apply_filters( 'booking_button', '', $resource );
					echo $price . $book_button;
					do_action( 'sl_by_voucher_button', get_the_ID(), $k );
				}
				?>
			</div>

		</article>
	<?php
	}
	?>

</section><!-- #booking-resources -->
