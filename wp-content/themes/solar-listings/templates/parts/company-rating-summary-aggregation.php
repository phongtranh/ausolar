<?php
$pages_slug = array( 'solar-link-gold-coast', 'solar-link-australia', 'solar-link-melbourne' );
$post_type = 'company';
$company_ids = array();
foreach( $pages_slug as $slug )
{
	$args = array(
		'name'  => $slug,
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => 1,
	);
	$my_posts = get_posts( $args );
	$post_id = $my_posts[0]->ID;
	$company_ids[] = $post_id;
	$average_rating += Sl_Company_Helper::get_average_rating( $post_id );
	$reviews += Sl_Company_Helper::get_no_reviews( $post_id );
}
?>
<section id="rating-summary">
	<div class="summary">
		<h4 class="description"><?php _e( 'Overall Rating', '7listings' ); ?></h4>
		<span class="value"><?php sl_star_rating( $average_rating, 'type=flat' ); ?></span>
		<?php printf( __( '<div class="detail rated"><span class="description">Rated</span><span class="value">%s out of 5</span></div>', '7listings' ), $average_rating ); ?>
		<?php printf( '<div class="detail amount"><span class="description">Based on</span><span class="value">%s reviews</span></div>', $reviews ); ?>
	</div>
	<div class="rating-details">
		<?php
		$ratings = array(
			'rating_sales'        => __( 'Sales Rep', '7listings' ),
			'rating_service'      => __( 'Service', '7listings' ),
			'rating_installation' => __( 'Installation', '7listings' ),
			'rating_quality'      => __( 'Quality Of System', '7listings' ),
			'rating_timelyness'   => __( 'Timelyness', '7listings' ),
			'rating_price'        => __( 'Price', '7listings' ),
		);
		foreach ( $ratings as $rating_type => $rating_label ):
			?>
			<div class="detail type">
				<?php
				$average = 0;
				foreach( $company_ids as $id )
				{
					$average += sl_get_rating_average_of_type( $id, $rating_type );
				}
				?>
				<label class="description"><?php echo $rating_label; ?></label>

				<span class="value">
					<?php sl_star_rating( $average, 'type=flat' ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</section>