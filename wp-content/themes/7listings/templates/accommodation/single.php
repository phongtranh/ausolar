<?php
/**
 * The Template for displaying SINGLE ACCOMMODATIONS
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

			<?php get_template_part( 'templates/parts/address' ); ?>

			<?php
			if ( sl_setting( get_post_type() . '_single_features' ) )
			{
				echo '<section id="features">';
				the_terms( get_the_ID(), 'amenity', '<h3>' . __( 'Amenities', '7listings' ) . '</h3>', ', ', '' );
				echo '</section>';
			}
			?>

			<section id="times">
				<?php if ( $checkin = get_post_meta( get_the_ID(), 'checkin', true ) ) : ?>
					<p>
						<span class="label check-times"><?php _e( 'Check-In:', '7listings' ); ?></span>
						<?php
						$checkin   = intval( $checkin ) - 1;
						$check_arr = array( '7:30 am', '8:00 am', '8:30 am', '9:00 am', '9:30 am', '10:00 am', '10:30 am', '11:00 am', '11:30 am', 'Noon', '1:00 pm', '2:00 pm', '3:00 pm', '4:00 pm', '5:00 pm' );
						echo $check_arr[$checkin];
						?>
					</p>
				<?php endif; ?>
				<?php if ( $checkout = get_post_meta( get_the_ID(), 'checkout', true ) ) : ?>
					<p>
						<span class="label check-times"><?php _e( 'Check-Out:', '7listings' ); ?></span>
						<?php
						$checkout  = intval( $checkout ) - 1;
						$check_arr = array( '7:30 am', '8:00 am', '8:30 am', '9:00 am', '9:30 am', '10:00 am', '10:30 am', '11:00 am', '11:30 am', 'Noon', '1:00 pm', '2:00 pm', '3:00 pm', '4:00 pm', '5:00 pm' );
						echo $check_arr[$checkout];
						?>
					</p>
				<?php endif; ?>
			</section><!-- #times -->

			<?php get_template_part( 'templates/parts/terms' ); ?>

			<?php get_template_part( 'templates/parts/map' ); ?>

			<?php peace_action( 'entry_bottom' ); ?>

			<?php get_template_part( 'templates/booking/resources' ); ?>

			<?php edit_post_link( __( 'Edit Listing', '7listings' ), '<span class="edit-link button small">', '</span>' );
			if ( current_user_can( 'manage_options' ) )
				echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=accommodation#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
			?>

			<?php
			if ( sl_setting( get_post_type() . '_comment_status' ) )
				comments_template( '', true );
			?>

			<?php get_template_part( 'templates/parts/similar-listings' ); ?>

		</article>
		<!-- #content -->

		<?php if ( 'none' != $sidebar_layout ) : ?>
			<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
				<?php get_sidebar(); ?>
			</aside>
		<?php endif; ?>

	</div><!-- .container -->

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
function sl_accommodation_rebuild_args( $not_duplicated, &$args, &$type_args, &$price_args )
{
	$args['post__not_in']       = array_merge( $args['post__not_in'], $not_duplicated );
	$type_args['post__not_in']  = array_merge( $type_args['post__not_in'], $not_duplicated );
	$price_args['post__not_in'] = array_merge( $price_args['post__not_in'], $not_duplicated );
}

/**
 * Display similar accommodation
 *
 * @param     $query
 * @param int $limit
 *
 * @return void
 */
function sl_similar_accommodation( $query, $limit = 3 )
{
	$prefix = get_post_type() . '_similar_';
	$args   = array(
		'image_size'     => sl_setting( "{$prefix}image_size" ),
		'star_rating'    => sl_setting( "{$prefix}star_rating" ),
		'rating'         => sl_setting( "{$prefix}rating" ),
		'price'          => sl_setting( "{$prefix}price" ),
		'booking'        => sl_setting( "{$prefix}booking" ),
		'excerpt'        => sl_setting( "{$prefix}excerpt" ),
		'excerpt_length' => sl_setting( "{$prefix}excerpt_length" ),
		'elements'       => array( 'post_title', 'star_rating', 'rating', 'address', 'excerpt', 'price', 'booking' ),
	);
	while ( 0 < $limit && $query->have_posts() )
	{
		$query->the_post();
		$limit --;
		echo sl_post_list_single( $args );
	}
}
