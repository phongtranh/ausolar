<?php
global $woocommerce;

$tax  = 'product_cat';
$args = array();

$parent = is_post_type_archive() ? 0 : get_queried_object_id();

if ( is_post_type_archive() )
{
		$args['parent'] = $parent;
}
else
{
		$args['child_of'] = $parent;
}

$cats = get_terms( $tax, $args );
?>

<?php if ( ! empty( $cats ) ) : ?>

	<section class="sl-list posts products categories">

		<?php
		$size = sl_setting( 'product_archive_image_size' );
		$i    = 0;
		?>

		<?php foreach ( $cats as $cat ) : ?>

			<?php $i ++; ?>

			<article class="post category<?php
			if ( $i % sl_setting( 'product_archive_columns' ) == 0 )
				echo ' last';
			elseif ( ( $i - 1 ) % sl_setting( 'product_archive_columns' ) == 0 )
				echo ' first';

			if ( $cat->parent != $parent )
				echo ' child';
			?>">
				<a href="<?php echo get_term_link( $cat, $tax ); ?>">
					<?php
					if ( sl_setting( 'product_archive_cat_thumb' ) )
					{
						$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
						if ( $thumbnail_id )
						{
							list( $image ) = wp_get_attachment_image_src( $thumbnail_id, $size );
							echo '<figure class="thumbnail"><img class="photo" src="' . $image . '" alt="cat-thumb"></figure>';
						}
						else
						{
							echo '<figure class="thumbnail">' . wc_placeholder_img( $size ) . '</figure>';
						}
					}

					if ( sl_setting( 'product_archive_cat_title' ) )
						echo '<h2 class="entry-title">' . $cat->name . '</h2>';

					if ( sl_setting( 'product_archive_cat_count' ) )
						echo '<span class="category-count amount">' . $cat->count . '</span>';
					?>
				</a>
			</article>

		<?php endforeach; ?>

	</section>

<?php endif; ?>
