<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

	<div class="container content listings">
		<?php
		if ( have_posts() ) :

			global $query_string;

			$args                   = wp_parse_args( $query_string );
			$args['post_type']      = 'any';
			$args['post_status']    = 'publish';
			$args['posts_per_page'] = - 1;
			$query                  = new WP_Query( $args );

			$ids = array();
			while ( $query->have_posts() )
			{
				$query->the_post();
				$ids[] = get_the_ID();
			}
			wp_reset_postdata();

			$ids = array_unique( array_filter( $ids ) );

			echo '<div class="results-count">' . sprintf( __( 'Showing all %d results', '7listings' ), count( $ids ) ) . '</div>';

			// Common query args
			$args                = wp_parse_args( $query_string );
			$args['orderby']     = 'meta_value_num';
			$args['order']       = 'ASC';
			$args['meta_key']    = 'price_from';
			$args['post_status'] = 'publish';

			$types = sl_setting( 'listing_types' );

			if ( in_array( 'accommodation', $types ) )
			{
				$args['post_type'] = 'accommodation';
				sl_query_with_priority( $args, 'sl_search_results' );
			}

			if ( in_array( 'tour', $types ) )
			{
				$args['post_type'] = 'tour';
				sl_query_with_priority( $args, 'sl_search_results' );
			}

			if ( in_array( 'rental', $types ) )
			{
				$args['post_type'] = 'rental';
				sl_query_with_priority( $args, 'sl_search_results' );
			}

			$args                   = wp_parse_args( $query_string );
			$args['posts_per_page'] = - 1;

			if ( in_array( 'product', $types ) )
			{
				$args['post_type'] = 'product';
				sl_search_results_normal( $args );
			}

			$args['post_type'] = 'post';
			sl_search_results_normal( $args );

			$args['post_type'] = 'page';
			sl_search_results_normal( $args );

		else :

			echo '<p>' . __( 'No results match your search query', '7listings' ) . '</p>';

		endif;
		?>
	</div>

<?php get_footer(); ?>

<?php
/**
 * Show list of posts
 *
 * @param WP_Query $query
 *
 * @return void
 */
function sl_search_results( $query )
{
	while ( $query->have_posts() ): $query->the_post();
		?>
		<div class="listing">

			<article <?php post_class( 'row' ); ?>>

				<?php if (has_post_thumbnail()) : ?>
				<div class="span2">
					<?php
					sl_broadcasted_thumbnail( 'sl_thumb_tiny', array(
						'alt'   => the_title_attribute( 'echo=0' ),
						'title' => the_title_attribute( 'echo=0' ),
					) );
					?>
				</div>

				<div class="span8 info">

					<?php else : ?>

					<div class="span10 info">

						<?php endif; ?>

						<h2 class="title entry-title">
							<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', '7listings' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
						</h2>

						<?php
						if ( 'accommodation' == get_post_type() )
						{
							$star = get_the_terms( get_the_ID(), 'stars' );
							if ( is_array( $star ) )
							{
								echo '<div class="hotel-rating">';
								$star = array_pop( $star );
								list( $star ) = explode( ' ', $star->name );
								$star = floatval( $star );
								echo '<div class="rates" style="width: ' . intval( $star * 16 ) . 'px;"></div>';
								echo '</div>';
							}
						}
						?>

						<?php get_template_part( 'templates/parts/featured-ribbon' ); ?>

						<?php
						$author = in_array( get_post_type(), array( 'accommodation', 'tour' ) ) ? get_the_author() : get_bloginfo( 'name' );
						printf(
							__( '<time class="updated entry-date" datetime="%s" style="display:none">%s</time><span class="author vcard" style="display:none"><a class="url fn" href="%s" title="%s" rel="author">%s</a></span>', '7listings' ),
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() ),
							sl_setting( 'googleplus' ) ? sl_setting( 'googleplus' ) : get_author_posts_url( get_the_author_meta( 'ID' ) ),
							esc_attr( sprintf( __( 'View all posts by %s', '7listings' ), $author ) ),
							esc_html( $author )
						);
						?>

						<?php echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( get_post_type() . '_archive_desc' ) ) ); ?>

					</div>
					<!-- .info -->

			</article>

		</div>
	<?php
	endwhile;

	wp_reset_postdata();
}

/**
 * Show normal search results
 *
 * @param array $args Query arguments
 *
 * @return void
 */
function sl_search_results_normal( $args )
{
	$query = new WP_Query( $args );
	if ( ! $query->have_posts() )
		return;

	while ( $query->have_posts() ) : $query->the_post();
		?>
		<div class="listing">

			<article <?php post_class( 'row' ); ?>>

				<?php if (has_post_thumbnail()) : ?>
				<div class="span2">
					<?php
					sl_broadcasted_thumbnail( 'sl_thumb_tiny', array(
						'alt'   => the_title_attribute( 'echo=0' ),
						'title' => the_title_attribute( 'echo=0' ),
					) );
					?>
				</div>

				<div class="span8 info">

					<?php else : ?>

					<div class="span10 info">

						<?php endif; ?>

						<h2 class="title entry-title">
							<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', '7listings' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
						</h2>

						<?php
						$author = get_bloginfo( 'name' );
						printf(
							__( '<time class="updated entry-date" datetime="%s" style="display:none">%s</time><span class="author vcard" style="display:none"><a class="url fn" href="%s" title="%s" rel="author">%s</a></span>', '7listings' ),
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() ),
							sl_setting( 'googleplus' ) ? sl_setting( 'googleplus' ) : get_author_posts_url( get_the_author_meta( 'ID' ) ),
							esc_attr( sprintf( __( 'View all posts by %s', '7listings' ), $author ) ),
							esc_html( $author )
						);
						?>

						<?php echo sl_listing_element( 'excerpt' ); ?>

					</div>
					<!-- .info -->

			</article>

		</div>
	<?php
	endwhile;

	wp_reset_postdata();
}
