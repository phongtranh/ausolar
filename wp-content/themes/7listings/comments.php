<?php
/**
 * Comments & Reviews Template
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

if ( post_password_required() )
{
	return;
}
?>

<section id="comments" class="comments">

	<?php
	// Show add comment button when there's a lot of reviews
	if ( get_comments_number() > 5 )
	{
		printf(
			'<p class="add-review"><a href="#comment-form" class="button"%s>%s</a></p>',
			sl_setting( 'comments_style' ) ? '' : ' data-toggle="modal"',
			sprintf( __( 'Add your comment', '7listings' ), get_the_title() )
		);
	}

	echo '<h3>';
	printf( _n( '1 Comment on &ldquo;%2$s&rdquo;', '%1$s Comments on &ldquo;%2$s&rdquo;', get_comments_number(), '7listings' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
	echo '</h3>';

	$title_reply = '';

	if ( have_comments() )
	{
		echo '<ol class="commentlist">';
		wp_list_comments( 'callback=sl_comment' );
		echo '</ol>';

		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) )
		{
			?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Previous', '7listings' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Next <span class="meta-nav">&rarr;</span>', '7listings' ) ); ?></div>
			</div>
		<?php
		}

		if ( comments_open() )
		{
			$title_reply = __( 'Add a comment', '7listings' );
		}
	}
	else
	{
		$title_reply = __( 'Be the first to comment', '7listings' ) . ' &ldquo;' . get_the_title() . '&rdquo;';
		echo '<p>' . esc_html__( 'There are no comments yet', '7listings' ) . '</p>';
	}
    if ( ! sl_setting( 'comments_style' ) )
	    echo '<p class="add-review"><a href="#comment-form" class="button" data-toggle="modal">' . __( 'Add your comment', '7listings' ) . '</a></p>';
	?>

	<?php if ( comments_open() ) : ?>

		<?php if ( ! sl_setting( 'comments_style' ) ) : ?>

			<div id="comment-form" class="modal hide fade">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo esc_html( $title_reply ); ?></h3>
				</div>

				<div class="modal-body">

		<?php else: ?>

			<div id="comment-form" class="comment-body">
				<h4><?php _e( 'Add a comment', '7listings' ); ?></h4>

		<?php endif; ?>

			<?php
			comment_form( array(
				'title_reply'          => '',
				'comment_notes_before' => '',
				'comment_notes_after'  => '',
				'fields'               => sl_comment_fields(),
				'logged_in_as'         => '',
				'label_submit'         => __( 'Submit', '7listings' ),
				'comment_field'        => sprintf( '
					<p class="error error-comment hidden">%s</p>
					<p>
						<label for="comment">%s</label>
						<textarea id="comment" name="comment" class="message"></textarea>
					</p>',
					__( 'Please write a comment', '7listings' ),
					__( 'Comment', '7listings' )
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

	<?php endif; // comments_open() ?>

</section>
