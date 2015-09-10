<?php
/**
 * The Template for POST ARCHIVES
 * MAGAZINE DESIGN
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
		// Store number of displayed posts
		$display = 0;

		if ( have_posts() ) :

			$show_thumb = sl_setting( 'post_archive_featured' );

			// First post
			the_post();
			?>
			<div class="sl-list archive posts grid columns-1">
				<article <?php post_class( 'row-fluid' ); ?>>

					<?php
					if ( $show_thumb )
					{
						echo '<div class="span6">';
						echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );
						echo '</div>';
					}

					echo '<div class="' . ( $show_thumb ? 'span6' : 'span12' ) . '">';

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

					echo '</div>'; // .span12 or .span6
					?>

				</article>
			</div>
			<?php
			$display ++;

			// This variable detects that we displayed all posts, even in the middle of loop
			$break = false;

			// Show 2-columns and 3-columns
			for ( $col = 2; $col <= 3; $col ++ )
			{
				// Stop if there's no post remaining to display
				if ( $display == get_option( 'posts_per_page' ) || $break )
					break;

				echo "<div class='sl-list archive posts grid columns-$col'>";

				$count = 0;

				while ( $count < $col )
				{
					// No posts remaining in the queue
					if ( $display == get_option( 'posts_per_page' ) )
					{
						$break = true;
						break;
					}

					the_post();
					?>

					<article <?php post_class(); ?>>

						<?php
						if ( $show_thumb )
							echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

						echo '<div class="details">';

							echo sl_listing_element( 'post_title', array( 'title_tag' => 'h3' ) );

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
					$count ++;
					$display ++;
				}

				echo '</div>'; // .sl-list
			}

			// Repeat 4-columns
			// We have to add check of $display, because the number of queried posts might be different than in 'posts_per_page' option
			// because of sticky posts (they always are added to the query regardless 'posts_per_page')
			if ( have_posts() && $display < get_option( 'posts_per_page' ) )
			{
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
			}

			peace_numeric_pagination();

		endif;
		?>

	</div><!-- #content -->

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
