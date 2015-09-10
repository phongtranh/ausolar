<?php
/**
 * The Template for single POSTS
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

get_header(); ?>

<?php the_post(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'sl-single';
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<?php
		peace_action( 'entry_top' );

		/**
		 * Get image for news article
		 * It is required from Google Structure Markup
		 * @link https://developers.google.com/structured-data/rich-snippets/articles
		 */
		$thumb = sl_broadcasted_image_src( '_thumbnail_id', 'full' );
		echo "<img class='hidden' itemprop='image' src='$thumb' alt='thumbnail'>";

		echo '<div id="description" class="entry-content" itemprop="articleBody">';
		the_content();
		echo '</div>';

		wp_link_pages( array(
			'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
			'after'  => '</p>',
		) );

		if ( comments_open() && sl_setting( 'post_comment_status' ) && ! sl_setting( 'comments_style' ) )
			echo '<span class="comments-link"><a href="#comment-form" data-toggle="modal">' . __( 'Add Comment', '7listings' ) . '</a></span>';

		edit_post_link( __( 'Edit Post', '7listings' ), '<span class="edit-link button small">', '</span>' );
		if ( current_user_can( 'manage_options' ) )
			echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=post#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';

		peace_action( 'entry_bottom' );

		if (
			( comments_open() || get_comments_number() ) &&
			( sl_setting( 'post_comment_status' ) || sl_setting( 'post_ping_status' ) )
		)
		{
			comments_template();
		}
		?>

	</article>

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
