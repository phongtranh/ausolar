<?php
/**
 * The Template for POST ARCHIVES
 * MAGAZINE DESIGN
 * Page: 2, 3, ...
 *
 * last edit: 5.1.1
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
	$content_class  = $content_class ? ' class="' . $content_class . '"' : '';
	?>

	<div id="content"<?php echo $content_class; ?>>

		<?php
		if ( have_posts() ) :

			$show_thumb = sl_setting( 'post_archive_featured' );

			echo "<div class='sl-list archive posts grid columns-4'>";
			while ( have_posts() )
			{
				the_post();
				?>

				<article <?php post_class(); ?>>

					<?php
					if ( $show_thumb )
						echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

					echo '<div class="details">';

						echo sl_listing_element( 'post_title', array( 'title_tag' => 'h4' ) );

						echo '<div class="entry-meta-wrapper">';
							echo sl_listing_element( 'date' );
							echo sl_listing_element( 'author' );
						echo '</div>';

						echo apply_filters( 'sl_archive_post', '' );

					echo '</div>';
					?>

				</article>

				<?php
			}
			echo '</div>';

			peace_numeric_pagination();

		endif;
		?>

	</div>
	<!-- #content -->

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

	<?php
	if ( current_user_can( 'manage_options' ) )
		echo '<span class="edit-link button small page-settings"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=post' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
	?>
</div><!-- #main-wrapper -->

<?php get_footer(); ?>
