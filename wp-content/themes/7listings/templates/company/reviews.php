<?php
/**
 * Company Reviews
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

if ( post_password_required() )
	return;
?>
<section id="comments">

	<?php
	$count = Sl_Company_Helper::get_no_reviews();

	// Show add review button when there's a lot of reviews
	if ( $count > 5 )
		echo '<p class="add-review"><a href="#comment-form" class="button large full" data-toggle="modal">' . sprintf( __( 'Add your review for %s', '7listings' ), get_the_title() ) . '</a></p>';

	echo '<div class="comments-title"><h3>' . sprintf( __( '%d reviews for %s', '7listings' ), $count, get_the_title() ) . '</h3></div>';
	$title_reply = '';

	if ( have_comments() )
	{
		echo '<ol class="commentlist">';
		wp_list_comments( array( 'callback' => array( 'Sl_Company_Frontend', 'comments' ) ) );
		echo '</ol>';

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) )
		{
			?>
			<nav class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', '7listings' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', '7listings' ) ); ?></div>
			</nav>
		<?php
		}
		$title_reply = __( 'Add a review', '7listings' );
	}
	else
	{
		$title_reply = __( 'Be the first to review', '7listings' ) . ' &ldquo;' . get_the_title() . '&rdquo;';
		echo '<p>' . esc_html__( 'There are no reviews yet', '7listings' ) . '</p>';
	}

	echo '<p class="add-review"><a href="#comment-form" class="button large full" data-toggle="modal">' . sprintf( __( 'Add your review for %s', '7listings' ), get_the_title() ) . '</a></p>';
	?>


	<div id="comment-form" class="modal hide fade">

		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo esc_html( $title_reply ); ?></h3>
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
					'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name', '7listings' ) . '</label> ' .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required></p>',
					'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', '7listings' ) . '</label> ' .
						'<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required></p>',
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

</section>
