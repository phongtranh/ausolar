<section id="rating-summary">
	<div class="summary">
		<h4 class="description"><?php _e( 'Overall Rating', '7listings' ); ?></h4>
		<span class="value"><?php Sl_Company_Helper::show_average_rating(); ?></span>
		<?php printf( __( '<div class="detail rated"><span class="description">Rated</span><span class="value">%s out of 5</span></div>', '7listings' ), Sl_Company_Helper::get_average_rating() ); ?>
		<?php printf( '<div class="detail amount"><span class="description">Based on</span><span class="value">%s reviews</span></div>', Sl_Company_Helper::get_no_reviews() ); ?>
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
				<?php $average = Sl_Company_Frontend::get_rating( $rating_type ); ?>
				<label class="description"><?php echo $rating_label; ?></label>

				<span class="value">
					<?php sl_star_rating( $average, 'type=flat' ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</section>