<?php
echo '<h2>' . sprintf( __( '%d reviews for %s', '7listings' ), Sl_Company_Helper::get_no_reviews(), get_the_title() ) . '</h2>';
?>
<div class="row-fluid company-admin">
	<div class="span4">
		<?php get_template_part( 'templates/parts/company-rating-summary' ); ?>
	</div>
	<!-- .span3 -->

	<div class="span8">
		<div id="comments">

			<?php
			if ( have_comments() ) :

				echo '<ol class="commentlist">';

				wp_list_comments( array( 'callback' => array( 'Sl_Company_Frontend', 'comments' ) ) );

				echo '</ol>';

				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
					<div class="navigation">
						<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', '7listings' ) ); ?></div>
						<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', '7listings' ) ); ?></div>
					</div>
				<?php endif;

			else :

				echo '<p>' . __( 'There are no reviews yet.', '7listings' ) . '</p>';

			endif;
			?>

		</div>

		<div id="comment-form" class="modal hide fade">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php _e( 'Reply', '7listings' ); ?></h3>
			</div>

			<div class="modal-body">

				<?php
				$commenter = wp_get_current_commenter();
				wp_nonce_field( 'comment_rating' );

				comment_form( array(
					'title_reply'          => '',
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'fields'               => array(
						'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', '7listings' ) . '</label> ' . '<span class="required">*</span>' .
							'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" aria-required="true"></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', '7listings' ) . '</label> ' . '<span class="required">*</span>' .
							'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" aria-required="true"></p>',
					),
					'label_submit'         => __( 'Submit Review', '7listings' ),
					'logged_in_as'         => '',
					'comment_field'        => '
						<div class="comment-rates">
							<span class="detailed-rating">
								<label for="rating_sales">' . __( 'Sales Rep', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_sales" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_service">' . __( 'Service', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_service" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_installation">' . __( 'Installation', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_installation" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_quality">' . __( 'Quality Of System', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_quality" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_timelyness">' . __( 'Timelyness', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_timelyness" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
							<span class="detailed-rating">
								<label for="rating_price">' . __( 'Price', '7listings' ) . '</label>' .
								sl_star_rating( '', 'type=select&echo=0' ) . '
								<select name="rating_price" class="rating-select hidden">
									<option value="">' . __( 'Rate...', '7listings' ) . '</option>
									<option value="5">' . __( 'Perfect', '7listings' ) . '</option>
									<option value="4">' . __( 'Good', '7listings' ) . '</option>
									<option value="3">' . __( 'Average', '7listings' ) . '</option>
									<option value="2">' . __( 'Not that bad', '7listings' ) . '</option>
									<option value="1">' . __( 'Very Poor', '7listings' ) . '</option>
								</select>
							</span>
						</div>
						<div class="comment-questions">
							<p>
								<label>' . __( 'What size system did you purchase?', '7listings' ) . '</label>
								<select name="size_system">
									<option value="">' . __( 'Select', '7listings' ) . '</option>
									<option value="1.5kW">1.5kW</option>
									<option value="2kW">2kW</option>
									<option value="2.5kW">2.5kW</option>
									<option value="3kW">3kW</option>
									<option value="4kW">4kW</option>
									<option value="5kW">5kW</option>
									<option value="more than 5kW">' . __( 'more than 5kW', '7listings' ) . '</option>
								</select>
							</p>
							<p>
								<label>How much did you spend?</label>
								<select name="spend">
									<option value="">Select</option>
									<option value="less than $2,500">less than $2,500</option>
									<option value="$2,500 - $4,999">$2,500 - $4,999</option>
									<option value="$5,000 - $9,999">$5,000 - $9,999</option>
									<option value="$10,000 - $14,999">$10,000 - $14,999</option>
									<option value="more than $15,000">more than $15,000</option>
								</select>
							</p>
						</div>
						<div class="review-text">
							<label for="comment">' . __( 'Your Review', '7listings' ) . '</label>
							<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
						</div>'
				) );

				?>

			</div>
			<!-- .modal-body -->

			<div class="modal-footer">
				<button class="button primary" id="comment-form-submit"><?php _e( 'Submit', '7listings' ); ?></button>
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Close', '7listings' ); ?></button>
			</div>

		</div>

	</div>
	<!-- .span9 -->
</div>
