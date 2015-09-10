<section class="panel booking-details active">
	<h2 class="panel-title"><?php the_title(); ?></h2>

	<?php
	// Show listing aggregate rating only when it has at least 1 review
	list( $count, $average ) = sl_get_average_rating();
	if ( $count )
	{
		echo sl_listing_element( 'rating' );
	}
	?>

	<div id="lead-in-rate">
		<?php echo sl_listing_element( 'price' ); ?>
	</div>

	<div class="panel-content">

		<?php sl_get_template( 'templates/booking/parts/resource-select', $params ); ?>

		<?php sl_get_template( 'templates/' . get_post_type() . '/booking/step1-resource', $params ); ?>

		<nav class="sl-field nav hidden">
			<div class="sl-input">
				<a href="#" class="next button" id="to-contact"><?php _e( 'Next', '7listings' ); ?></a>
			</div>
		</nav>

		<input type="hidden" name="amount" value="">

	</div><!-- .panel-content -->

</section><!-- .panel.booking-details -->
