<?php
/**
 * The Template for displaying all single RENTALS
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

global $post; ?>

<?php get_header(); ?>

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

			<?php get_template_part( 'templates/parts/address' ); ?>
			<?php
			$html = '';
			if ( get_post_meta( get_the_ID(), 'open_247', true ) )
			{
				$html .= sprintf(
					'<div class="day">
						<span class="label">%s</span>
						<span class="detail"><time itemprop="openingHours" datetime="Mo-Su">%s</time></span>
					</div>',
					__( 'Monday - Sunday', '7listings' ),
					__( 'All day', '7listings' )
				);
			}
			else
			{
				$days = array(
					'mon' => __( 'Monday', '7listings' ),
					'tue' => __( 'Tuesday', '7listings' ),
					'wed' => __( 'Wednesday', '7listings' ),
					'thu' => __( 'Thursday', '7listings' ),
					'fri' => __( 'Friday', '7listings' ),
					'sat' => __( 'Saturday', '7listings' ),
					'sun' => __( 'Sunday', '7listings' ),
				);
				$open = false;
				foreach ( $days as $k => $v )
				{
					if ( get_post_meta( get_the_ID(), "business_hours_$k", true ) )
					{
						$open = true;
						break;
					}
				}
				if ( $open )
				{
					foreach ( $days as $k => $v )
					{
						if ( ! get_post_meta( get_the_ID(), "business_hours_$k", true ) )
						{
							$html .= sprintf(
								'<div class="day">
									<span class="label">%s</span>
									<span class="detail">%s</span>
								</div>',
								esc_html( $v ), esc_html__( 'Closed', '7listings' )
							);
							continue;
						}

						$time = get_post_meta( get_the_ID(), "business_hours_{$k}_from", true ) . ' - ' . get_post_meta( get_the_ID(), "business_hours_{$k}_to", true );
						$name = substr( ucfirst( $k ), 0, 2 );

						$html .= sprintf(
							'<div class="day">
								<span class="label">%1$s</span>
								<span class="detail"><time itemprop="openingHours" datetime="%2$s %3$s">%3$s</time></span>
							</div>',
							$v, $name, $time
						);
					}
				}
			}

			if ( $html )
			{
				echo '<section id="business-hours" class="company-meta business-hours">';
				echo '<h4>' . __( 'Business Hours', '7listings' ) . '</h4>';
				echo $html;
				echo '</section>';
			}
			?>

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
				the_terms( get_the_ID(), 'feature', '<h3>' . __( 'Features', '7listings' ) . '</h3>', ', ', '' );
				echo '</section>';
			}
			?>

			<?php get_template_part( 'templates/parts/terms' ); ?>

			<?php get_template_part( 'templates/parts/map' ); ?>

			<?php peace_action( 'entry_bottom' ); ?>

			<?php get_template_part( 'templates/booking/resources' ); ?>

			<?php
			edit_post_link( __( 'Edit Listing', '7listings' ), '<span class="edit-link button small">', '</span>' );
			if ( current_user_can( 'manage_options' ) )
				echo '<span class="edit-link button small page-settings ic-only"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=rental#single_settings' ) . '" title="' . __( 'Edit Page Settings', '7listings' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
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
function sl_rental_rebuild_args( $not_duplicated, &$args, &$type_args, &$price_args )
{
	$args['post__not_in']       = array_merge( $args['post__not_in'], $not_duplicated );
	$type_args['post__not_in']  = array_merge( $type_args['post__not_in'], $not_duplicated );
	$price_args['post__not_in'] = array_merge( $price_args['post__not_in'], $not_duplicated );
}

/**
 * Display similar rental
 *
 * @param     $query
 * @param int $limit
 *
 * @return void
 */
function sl_similar_rental( $query, $limit = 3 )
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
