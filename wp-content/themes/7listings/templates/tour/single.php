<?php
/**
 * The Template for displaying SINGLE TOURS
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
		$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
		?>

		<article id="content" <?php post_class( $content_class ); ?>>

			<?php the_post(); ?>

			<?php peace_action( 'entry_top' ); ?>

			<?php sl_photo_slider(); ?>

			<div id="description" class="entry-content" itemprop="description">
				<?php
				peace_action( 'entry_content_top' );
				the_content();
				peace_action( 'entry_content_bottom' );
				?>
			</div>

			<?php
			if ( sl_setting( get_post_type() . '_single_features' ) )
			{
				echo '<section id="features">';
				the_terms( get_the_ID(), 'features', '<h3>' . __( 'Tour Features', '7listings' ) . '</h3>', ', ', '' );
				echo '</section>';
			}
			?>

			<?php get_template_part( 'templates/parts/address' ); ?>

			<?php get_template_part( 'templates/parts/terms' ); ?>

			<?php get_template_part( 'templates/parts/map' ); ?>

			<?php peace_action( 'entry_bottom' ); ?>

			<?php get_template_part( 'templates/booking/resources' ); ?>

			<?php
			edit_post_link( __( 'Edit Listing', '7listings' ), '<span class="edit-link button small">', '</span>' );
			if ( current_user_can( 'manage_options' ) )
			{
				echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=tour#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
			}
			?>

			<?php
			if ( sl_setting( get_post_type() . '_comment_status' ) )
			{
				comments_template( '', true );
			}
			?>

			<?php get_template_part( 'templates/parts/similar-listings' ); ?>

		</article><!-- #content -->

		<?php if ( 'none' != $sidebar_layout ) : ?>
			<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
				<?php get_sidebar(); ?>
			</aside>
		<?php endif; ?>

	</div><!-- #main-wrapper -->

<?php get_footer(); ?>

<?php

/**
 * Rebuild query args
 *
 * @param array $not_duplicated
 * @param array $args
 * @param array $type_args
 * @param array $price_args
 *
 * @return void
 */
function sl_tour_rebuild_args( $not_duplicated, &$args, &$type_args, &$price_args )
{
	$args['post__not_in']       = array_merge( $args['post__not_in'], $not_duplicated );
	$type_args['post__not_in']  = array_merge( $type_args['post__not_in'], $not_duplicated );
	$price_args['post__not_in'] = array_merge( $price_args['post__not_in'], $not_duplicated );
}

/**
 * Display similar tours
 *
 * @param     $query
 * @param int $limit
 *
 * @return void
 */
function sl_similar_tour( $query, $limit = 3 )
{
	$prefix = get_post_type() . '_similar_';
	$args   = array(
		'image_size'     => sl_setting( "{$prefix}image_size" ),
		'rating'         => sl_setting( "{$prefix}rating" ),
		'price'          => sl_setting( "{$prefix}price" ),
		'booking'        => sl_setting( "{$prefix}booking" ),
		'excerpt'        => sl_setting( "{$prefix}excerpt" ),
		'excerpt_length' => sl_setting( "{$prefix}excerpt_length" ),
	);
	while ( 0 < $limit && $query->have_posts() )
	{
		$query->the_post();
		$limit --;
		echo sl_post_list_single( $args );
	}
}
