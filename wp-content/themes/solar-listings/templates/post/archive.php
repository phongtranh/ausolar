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

		<?php
			if ( have_posts() ) :
				$count = 1;
				while ( have_posts() ) : the_post(); ?>

			<article <?php post_class('post'); ?>>

				<?php
				if ( sl_setting( 'post_archive_featured' ) )
					echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

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

			<?php if($count == 5):?>
				<?php if ( !wp_is_mobile() && url_contains( array( '/news/', '/environment/', '/politics/', '/technology/', '/transport/', '/international-solar-news/', '/category/blog/' ) ) ): ?>
				<div class="adrotate_newsroom_pc" id="adrotate_newsroom_pc">
					<span class='close-adrotate'>x</span>
					<?php
						if(url_contains( array( '/news/') ))
						{
							echo adrotate_group(10);
						} elseif (url_contains( array( '/environment/') )) {
							echo adrotate_group(13);
						} elseif (url_contains( array( '/politics/') )) {
							echo adrotate_group(15);
						} elseif (url_contains( array( '/technology/') )) {
							echo adrotate_group(14);
						} elseif (url_contains( array( '/transport/') )) {
							echo adrotate_group(11);
						} elseif (url_contains( array( '/international-solar-news/') )) {
							echo adrotate_group(12);
						} else {
							echo adrotate_group(16);//for blog
						}
						
					?>
				</div>
				<?php endif; ?>
			<?php endif;?>

			<?php if($count == 10):?>
				<?php if ( !wp_is_mobile() && url_contains( array( '/news/', '/environment/', '/politics/', '/technology/', '/transport/', '/international-solar-news/', '/category/blog/' ) ) ): ?>
				<div class="adrotate_newsroom_pc" id="adrotate_newsroom_pc">
					<span class='close-adrotate'>x</span>
					<?php echo adrotate_group(5);?>
				</div>
				<?php endif; ?>
			<?php endif;?>

		<?php
			$count++;
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
