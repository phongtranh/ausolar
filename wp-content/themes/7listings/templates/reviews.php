<?php
if ( post_password_required() )
	return;
?>

<section id="comments">

	<div id="comment-list">

		<?php
		list( $count, $average ) = sl_get_average_rating();

		// Show add review button when there's a lot of reviews
		if ( $count > 5 )
		{
			printf(
				'<p class="add-review"><a href="#comment-form" class="button"%s>%s</a></p>',
				sl_setting( 'comments_style' ) ? '' : ' data-toggle="modal"',
				sprintf( __( 'Add your review for %s', '7listings' ), get_the_title() )
			);
		}

		if ( $count > 0 )
		{
			echo '<div class="comments-title">';
			sl_star_rating( $average, array(
				'count' => $count,
				'item'  => get_the_title(),
			) );
			echo '<h3>' . sprintf( _n( '%s Review for &ldquo;%s&rdquo;', '%s Reviews for &ldquo;%s&rdquo;', $count, '7listings' ), $count, $post->post_title ) . '</h3>';
			echo '</div>';
		}
		else
		{
			echo '<h3>' . __( 'Reviews', '7listings' ) . '</h3>';
		}

		$title_reply = '';

		if ( have_comments() )
		{
			echo '<ol class="commentlist">';
			wp_list_comments( 'callback=sl_review' );
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

        if ( ! sl_setting( 'comments_style' ) )
		    echo '<p class="add-review"><a href="#comment-form" class="button" data-toggle="modal">' . sprintf( __( 'Add your review for %s', '7listings' ), get_the_title() ) . '</a></p>';
		?>

	</div>

    <?php if ( ! sl_setting( 'comments_style' ) ) : ?>

		<div id="comment-form" class="modal hide fade">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo esc_html( $title_reply ); ?></h3>
			</div>

			<div class="modal-body">

	<?php else : ?>

		<div id="comment-form" class="comment-body">
			<h4><?php _e( 'Add a review', '7listings' ); ?></h4>

	<?php endif; ?>

		<?php
		comment_form( array(
			'title_reply'          => '',
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'fields'               => sl_comment_fields(),
			'logged_in_as'         => '',
			'comment_field'        => sprintf( '
					<p class="error error-rating hidden">%s</p>
					<p>
						<label for="rating">%s</label>' . ( is_singular( 'product' ) ? '' : sl_star_rating( '', 'type=select&echo=0' ) ) . '
						<select id="rating" name="rating" class="rating-select hidden">
							<option value="">%s</option>
							<option value="5">%s</option>
							<option value="4">%s</option>
							<option value="3">%s</option>
							<option value="2">%s</option>
							<option value="1">%s</option>
						</select>
					</p>
					<p class="error error-comment hidden">%s</p>
					<p>
						<label for="comment">%s</label>
						<textarea id="comment" name="comment" class="message"></textarea>
					</p>
					%s',
				__( 'Please rate the product', '7listings' ),
				__( 'Rating', '7listings' ),
				__( 'Rate...', '7listings' ),
				__( 'Perfect', '7listings' ),
				__( 'Good', '7listings' ),
				__( 'Average', '7listings' ),
				__( 'Not that bad', '7listings' ),
				__( 'Very Poor', '7listings' ),
				__( 'Please write a review', '7listings' ),
				__( 'Review', '7listings' ),
				class_exists( 'Woocommerce' ) ? wp_nonce_field( 'woocommerce-comment_rating', '_wpnonce', true, false ) : ''
			),
		) );
		?>

		<?php if ( ! sl_setting( 'comments_style' ) ) : ?>

			</div><!-- .modal-body -->

			<div class="modal-footer">
				<button class="button primary" id="comment-form-submit"><?php _e( 'Submit Comment', '7listings' ); ?></button>
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			</div>

		<?php endif; ?>

	</div>

</section><!-- #comments -->
