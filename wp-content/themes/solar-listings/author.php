<?php get_header(); ?>
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

		<?php if(is_author()) {author_detail_box(); echo '<h2 class="title-list-post-author">Press release by: ' .get_the_author() .'</h2>';}  ?>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<article <?php post_class('author-page-post'); ?>>

				<?php
					if ( sl_setting( 'post_archive_featured' ) )
						echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

					echo '<div class="details">';

						echo sl_listing_element( 'post_title', array( 'title_tag' => 'h2' ) );
						echo sl_listing_element( 'date' );
						echo ' | by <a href="' .get_author_posts_url( get_the_author_meta( 'ID' ) ) .'">' .get_the_author() .'</a>';

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

</div><!-- #main-wrapper -->

<?php get_footer(); ?>

<?php
	function author_detail_box()
	{
?>
	<section id="blog-author-details">
		<?php echo sl_avatar( get_the_author_meta( 'ID' ), 200 ); ?>
		<div class="details">
			<h2 class="entry-meta author name" id="author-name">
				<?php the_author(); ?>
			</h2>
			<p class="entry-content bio" id="author-bio"><?php the_author_meta( 'description' ); ?></p>
			<?php author_social_links(); ?>
		</div>
	</section>
<?php
	}

	function author_social_links()
	{
		$classes = 'author';
		if ( sl_setting( 'design_social_icon_color_scheme' ) )
			$classes .= ' ' . sl_setting( 'design_social_icon_color_scheme' );
		$shortcode = '[social_links class="' . $classes . '"';
		$networks  = array(
			'facebook',
			'twitter',
			'googleplus',
			'pinterest',
			'linkedin',
			'instagram',
			'rss',
		);
		$has_link  = false;
		foreach ( $networks as $network )
		{
			if ( $link = get_the_author_meta( $network ) )
			{
				$shortcode .= ' ' . $network . '="' . $link . '"';
				$has_link = true;
			}
		}
		$shortcode .= ']';
		if ( $has_link )
			echo do_shortcode( $shortcode );
	}
?>