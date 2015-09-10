<?php if ( post_password_required() ) return; ?>

<aside id="comments">

	<div id="comment-list">

		<?php
		$count = Sl_Company_Helper::get_no_reviews();

		// Show add review button when there's a lot of reviews
		if ( $count > 5 )
			echo '<p class="add-review"><a href="#comment-form" class="button large full" data-toggle="modal">' . sprintf( __( 'Add your review for %s', '7listings' ), get_the_title() ) . '</a></p>';

		echo '<div class="comments-title"><h3>' . sprintf( __('%d reviews for %s', '7listings'), $count, get_the_title() ) . '</h3></div>';

		$title_reply = '';

		if ( have_comments() )
		{
			echo '<ol class="commentlist">';
			$comments = get_comments( array( 'post_id' => get_the_ID() ) );
			wp_list_comments( array( 'callback' => 'solar_comment', 'per_page'=>10 ), $comments );
			echo '</ol>';
			echo '<div class="animation_image" style="display:none" align="center"><img src="/wp-content/themes/solar-listings/images/ajax-loader.gif" /></div>';
			echo '<div class="load-more-reviews">See more reviews</div>';

			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) )
			{
				?>
				<div class="navigation">
					<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', '7listings' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', '7listings' ) ); ?></div>
				</div>
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

	</div>

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
					'author' => '<p class="horizontal comment-form-author">' . '<label for="author">' . __( 'Name', '7listings' ) . '</label> ' . '<span class="required">*</span>' .
								'<input required id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" /></p>',
					'email'  => '<p class="horizontal comment-form-email"><label for="email">' . __( 'Email', '7listings' ) . '</label> ' . '<span class="required">*</span>' .
								'<input required id="email" name="email" type="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" /></p>',
				),
				'label_submit'  => __( 'Submit Review', '7listings' ),
				'logged_in_as'  => '',
				'comment_field' => '
					<hr class="light">
					<div class="vertical comment-form-location">
						<div class="suburb-city">
							<label>' . __( 'City/Suburb', '7listings' ) . '</label>
							<input type="text" name="suburb" id="comment-suburb">
						</div>
						<div class="state">
							<label>' . __( 'State', '7listings' ) . '</label>
							<select name="state" id="comment-state">
								<option>ACT - Canberra</option>
								<option>New South Wales</option>
								<option>Northern Territory</option>
								<option>Queensland</option>
								<option>South Australia</option>
								<option>Tasmania</option>
								<option>Victoria</option>
								<option>Western Australia</option>
							</select>
						</div>
					</div>
					<hr class="light">
					<div class="comment-rates">
						<span class="detailed-rating">
							<label for="rating_sales">' . __( 'Sales Rep', '7listings' ) .'</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_sales" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
							</select>
						</span>
						<span class="detailed-rating">
							<label for="rating_service">' . __( 'Service', '7listings' ) . '</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_service" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
							</select>
						</span>
						<span class="detailed-rating">
							<label for="rating_installation">' . __( 'Installation', '7listings' ) . '</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_installation" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
							</select>
						</span>
						<span class="detailed-rating">
							<label for="rating_quality">' . __( 'Quality Of System', '7listings' ) .'</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_quality" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
							</select>
						</span>
						<span class="detailed-rating">
							<label for="rating_timelyness">' . __( 'Timelyness', '7listings' ) .'</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_timelyness" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
							</select>
						</span>
						<span class="detailed-rating">
							<label for="rating_price">' . __( 'Price', '7listings' ) .'</label>' .
							sl_star_rating( '', 'type=select&echo=0' ) . '
							<select name="rating_price" class="rating-select hidden">
								<option value="">'.__('Rate...', '7listings').'</option>
								<option value="5" selected>'.__('Perfect', '7listings').'</option>
								<option value="4">'.__('Good', '7listings').'</option>
								<option value="3">'.__('Average', '7listings').'</option>
								<option value="2">'.__('Not that bad', '7listings').'</option>
								<option value="1">'.__('Very Poor', '7listings').'</option>
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
							<label>' . __( 'How much did you spend?', '7listings' ) . '</label>
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
					<div class="review-text"><label for="comment">' . __( 'Your Review', '7listings' ) . '</label><span class="required">*</span><textarea aria-required="true" id="comment" name="comment" cols="45" rows="8"></textarea></div>'
			));

			?>

		</div><!-- .modal-body -->

		<div class="modal-footer">
			<button class="button primary" id="comment-form-submit"><?php _e( 'Submit', '7listings' ); ?></button>
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Close', '7listings' ); ?></button>
		</div>

	</div>

</aside>

<script type="text/javascript">
$(document).ready(function() {
	var page = 2;
	$(".load-more-reviews").on("click", function () {
        var id = '<?php echo get_the_ID(); ?>';
		var data = {page:page, id:id};
		$('.animation_image').show();
		$('.load-more-reviews').hide();
		$.ajax({
            type: "POST",
            dataType: 'json',
            url: 'https://www.australiansolarquotes.com.au/wp-admin/admin-ajax.php?action=get_review_of_company',
            data: data,
            cache: false,
            success: function (response) {
                if (response['error_code'] === 0) {
                	html = response['comments'];
                	if(html != null && html != '') {
                		$('.commentlist').append(html);
	                	page++;
	                    $('.animation_image').hide();
	                    $('.load-more-reviews').show();
                	} else {
                		$('.load-more-reviews').hide();
                		$('.animation_image').hide();
                		return false;
                	}

                } else {
                    console.log('fail');
                }
                
                return true;
            }
        });
        return false;
    });
});
</script>
