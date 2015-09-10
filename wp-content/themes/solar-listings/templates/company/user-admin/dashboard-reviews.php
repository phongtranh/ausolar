<?php
ob_start();
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

				wp_list_comments( array( 'callback' => 'sl_get_comments_of_company' ) );

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
						<div class="comment-questions">
							<div class="control-group">
								<label class="control-label" style="text-align:left">System size purchased</label>
								<div class="controls">
									<input type="text" name="size_system" class="input-xlarge" style="width:97%">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" style="text-align:left">Amount spent</label>
								<div class="controls">
									<input type="text" name="spend" class="input-xlarge" style="width:97%">
								</div>
							</div>
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

		<div id="comment-report-form" class="modal hide fade">
			<?php echo do_shortcode('[gravityform id="61" title="true" description="false"]')?>
		</div>

	</div>
	<!-- .span9 -->
</div>
<?php echo str_replace('class="comment-form"', 'class="comment-form form-horizontal"', ob_get_clean()); ?>