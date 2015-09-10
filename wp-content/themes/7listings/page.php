<?php
/* WP Page Template
 * last edit: 5.6.6
 *
 * @package WordPress
 * @subpackage 7Listings
 */
get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">
	<?php
	the_post();

	$is_wc_pages = class_exists( 'Sl_Product_Frontend' ) && in_array( get_the_ID(), Sl_Product_Frontend::woocommerce_page_ids() );

	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'entry-content';
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<?php
		if ( ! $is_wc_pages )
		{
			peace_action( 'entry_top' );
		}

		the_content( __( 'Continue reading &rarr;', '7listings' ) );
		wp_link_pages( array(
			'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
			'after'  => '</p>',
		) );
		edit_post_link( __( 'Edit Page', '7listings' ), '<span class="edit-link button small">', '</span>' );

		if ( ! $is_wc_pages )
		{
			peace_action( 'entry_bottom' );
		}

		if ( ! $is_wc_pages && sl_setting( 'comments_page' ) )
		{
			if ( comments_open() || ( get_post_meta( get_the_ID(), 'show_old_comments', true ) && get_comments_number() ) )
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
