<section id="sidebar" class="right summary">
	<h2><?php _e( 'Summary', '7listings' ); ?></h2>

	<h3 class="title listing"><?php the_title(); ?></h3>
	<h4 class="title resource"><?php echo $resource['title']; ?></h4>

	<?php
	if ( ! empty( $resource['photos'] ) )
	{
		echo '<figure class="thumbnail" id="post-thumbnail">' . sl_resource_photo( $resource['photos'] ) . '</figure>';
	}
	else
	{
		sl_broadcasted_thumbnail( 'sl_pano_medium' );
	}

	sl_get_template( 'templates/' . get_post_type() . '/booking/parts/summary', $params );

	/**
	 * Show reviews for current listing
	 * But do not show all reviews, show only 5-star, 4-star reviews
	 * Reviews are sorted from high to low
	 */
	$reviews = sl_review_list(
		array(
			'post_title'     => 0,
			'number'         => 3,
			'excerpt_length' => 17,
		),
		array(
			'post_id'    => get_the_ID(),
			'orderby'    => 'meta_value_num',
			'order'      => 'DESC',
			'meta_key'   => 'rating',
			'meta_query' => array(
				array(
					'key'     => 'rating',
					'value'   => 3,
					'compare' => '>',
					'type'    => 'NUMERIC',
				),
			),
		)
	);
	if ( $reviews['nums'] > 0 )
	{
		echo '<h3 class="review-title">' . __( 'Reviews', '7listings' ) . '</h3>';
		echo $reviews['html'];
	}
	?>
</section>
