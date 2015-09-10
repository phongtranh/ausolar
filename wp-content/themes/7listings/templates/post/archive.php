<?php
/**
 * The Template for displaying POST ARCHIVES as list
 * LIST DESIGN
 *
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'sl-list archive posts';

	if ( 'list' == sl_setting( 'post_archive_layout' ) )
		$content_class .= ' list';
	else
		$content_class .= ' columns-' . sl_setting( 'post_archive_columns' );
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	$content_class = $content_class ? ' class="' . $content_class . '"' : '';
	?>

	<div id="content"<?php echo $content_class; ?>>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<article <?php post_class(); ?>>

				<?php
				if ( sl_setting( 'post_archive_featured' ) )
				{
					$img = sl_listing_element( 'thumbnail', array( 'image_size' => sl_setting( 'post_archive_cat_image_size' ) ) );
					echo $img;
				}

				echo '<div class="details">';

					echo sl_listing_element( 'post_title', array( 'title_tag' => 'h2' ) );
					sl_post_meta();

					if ( 'content' == sl_setting( 'post_archive_display' ) )
					{
						echo '<div class="entry-content">';
						the_content( '' );
						echo '</div>';
					}
					else
					{
						echo sl_listing_element( 'excerpt' );
					}

					if ( sl_setting( 'post_archive_readmore' ) )
					{
						echo sl_listing_element( 'more_link', array(
							'more_link_type' => sl_setting( 'post_archive_readmore_type' ),
							'more_link_text' => sl_setting( 'post_archive_readmore_text' ),
						) );
					}

					echo apply_filters( 'sl_archive_post', '' );

				echo '</div>'; // .details
				?>

			</article>
		<?php
		endwhile;

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
