<?php
/**
 * The Template for displaying COMPANIES ARCHIVE
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

global $wpdb, $query_string; ?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

	<div id="main-wrapper" class="container">

		<?php
		$sidebar_layout = sl_sidebar_layout();
		$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
		$content_class  = $content_class ? ' class="' . $content_class . '"' : '';
		?>

		<div id="content"<?php echo $content_class; ?>>

			<section id="locations" class="sl-list columns-6">
				<h3><?php _e( 'Choose Your Area', '7listings' ); ?></h3>

				<?php
				$query     = "SELECT t.term_id FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
				WHERE p.post_type = 'company' AND tt.taxonomy = 'location'
				GROUP BY t.term_id";
				$locations = $wpdb->get_col( $query );

				$states = get_terms( 'location', array(
					'include' => $locations,
					'parent'  => 0, // Only get top level - states
				) );

				$current_location = get_query_var( 'location' );
				if ( $current_location )
					$current_location = get_term_by( 'slug', $current_location, 'location' );

				$current_state = $current_location;
				if ( ! empty( $current_state ) && ! is_wp_error( $current_state ) )
				{
					while ( $current_state->parent )
					{
						$current_state = get_term( $current_state->parent, 'location' );
					}
				}
				else
				{
					$current_state = '';
				}

				foreach ( $states as $term )
				{
					$url = home_url( sl_setting( 'company_base_url' ) . '/area/' . $term->slug );
					if ( isset( $_GET['start'] ) && preg_match( '#^[a-z]$#', $_GET['start'] ) )
						$url = add_query_arg( 'start', $_GET['start'], $url );

					$class = ! empty( $current_state ) && $term->term_id == $current_state->term_id ? ' selected' : '';
					$image = sl_get_term_meta( $term->term_id, 'thumbnail_id' );
					if ( $image )
						list( $image ) = wp_get_attachment_image_src( $image, 'sl_thumb_small' );
					else
						$image = 'http://placehold.it/80x80';
					echo '<article class="post">';
					echo '<a class="location' . $class . '" href="' . $url . '" rel="bookmark">';
					echo '<figure class="thumbnail"><img class="photo" src="' . $image . '"></figure>';
					echo '<h4 class="entry-title">' . $term->name . '</h4>';
					echo '</a>';
					echo '</article>';
				}
				?>
			</section>

			<?php
			if ( is_search() )
			{
				printf(
					__( 'Search results for: <strong>%s</strong>. <a href="%s">Remove</a>', '7listings' ),
					get_search_query(),
					remove_query_arg( 's' )
				);
			}

			// Default query arguments
			$args = wp_parse_args( $query_string );
			unset( $args['paged'] );
			$args['posts_per_page'] = - 1;

			// Order listings by
			switch ( sl_setting( 'company_archive_orderby' ) )
			{
				case 'views':
					$args['orderby']    = 'meta_value_num';
					$args['meta_key']   = 'views';
					$args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'     => 'views',
							'value'   => 1,
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'views',
							'value'   => 1,
							'compare' => 'NOT EXISTS',
						),
					);
					break;
				case 'alphabetically':
					$args['orderby'] = 'title';
					$args['order']   = 'ASC';
					break;
				case 'city':
					$args['orderby']    = 'meta_value';
					$args['meta_key']   = 'city';
					$args['order']      = 'ASC';
					$args['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key'     => 'city',
							'value'   => 1,
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'city',
							'value'   => 1,
							'compare' => 'NOT EXISTS',
						),
					);
					break;
			}

			// Filter by location
			if ( ! empty( $current_location ) && ! is_wp_error( $current_location ) )
			{
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'location',
						'field'    => 'id',
						'terms'    => $current_location->term_id,
					),
				);
			}

			// Filter by alphabet
			if ( isset( $_GET['start'] ) && preg_match( '#^[a-z]$#', $_GET['start'] ) )
				add_filter( 'posts_where', array( 'Sl_Company_Frontend', 'filter_by_alphabet' ) );

			// Filter by taxonomy
			$taxonomies = array( 'brand', 'company_product', 'company_service', 'location' );

			$args['tax_query'] = array( 'relation' => 'AND' );

			foreach ( $taxonomies as $taxonomy )
			{
				if ( isset( $_GET["filter_{$taxonomy}"] ) )
				{
					$field = 'brand' == $taxonomy ? 'slug' : 'id';

					$query_type      = 'and' == isset( $_GET["query_type_{$taxonomy}"] ) ? 'AND' : 'IN';
					$filter_features = isset( $_GET["filter_{$taxonomy}"] ) ? explode( ',', $_GET["filter_{$taxonomy}"] ) : array();

					$args['tax_query'][] =
						array(
							'taxonomy' => $taxonomy,
							'terms'    => $filter_features,
							'field'    => $field,
							'operator' => $query_type
						);
				}
			}

			if ( ! sl_setting( 'company_archive_priority' ) )
			{
				$query = new WP_Query( $args );
				sl_company_archive( $query, 999 );
			}
			else
			{
				sl_query_with_priority( $args, 'sl_company_archive', '', '', true, 0, sl_setting( 'company_archive_num' ) );
			}

			// Remove filter for alphabet
			if ( isset( $_GET['start'] ) && preg_match( '#^[a-z]$#', $_GET['start'] ) )
				remove_filter( 'posts_where', array( 'Sl_Company_Frontend', 'filter_by_alphabet' ) );

			if ( current_user_can( 'manage_options' ) )
				echo '<span class="edit-link button small page-settings"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=company' ) . '">' . __( 'Edit Page Settings', '7listings' ) . '</a></span>';
			?>

		</div>
		<!-- #content -->

		<?php if ( 'none' != $sidebar_layout ) : ?>
			<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
				<?php get_sidebar(); ?>
			</aside>
		<?php endif; ?>

	</div>

<?php get_footer(); ?>

<?php
/**
 * Show company posts
 *
 * @param WP_Query $query
 * @param int      $limit
 *
 * @return void
 */
function sl_company_archive( $query, $limit = 5 )
{
	if ( ! sl_setting( 'company_archive_priority' ) )
	{
		$num_posts  = count( $query->posts );
		$paged      = absint( max( 1, get_query_var( 'paged' ) ) ) - 1;
		$pagination = absint( sl_setting( 'company_archive_num' ) );
		$pagination = $pagination ? $pagination : get_option( 'posts_per_page' );

		$query->max_num_pages = ceil( $num_posts / $pagination );
		$query->posts         = array_slice( $query->posts, $paged * $pagination, $pagination );
		$query->post_count    = count( $query->posts );
	}
	?>
	<div class="content">
		<div id="companies-grid">
			<div class="sorter">
				<div class="entry-title" data-sort_by="title" title="<?php _e( 'Sort by Name', '7listings' ); ?>"><?php _e( 'Name', '7listings' ); ?></div>
				<div class="rating" data-sort_by="rating" title="<?php _e( 'Sort by Rating', '7listings' ); ?>"><?php _e( 'Rating', '7listings' ); ?></div>
				<div class="location" data-sort_by="location" title="<?php _e( 'Sort by City', '7listings' ); ?>"><?php _e( 'City', '7listings' ); ?></div>
			</div>
			<div class="companies">
				<?php
				while ( 0 < $limit && $query->have_posts() ):
					$query->the_post();
					$limit --;

					// Companies with ratings first
					$average = Sl_Company_Helper::get_average_rating();
					$city    = get_term_by( 'name', get_post_meta( get_the_ID(), 'city', true ), 'location' );
					if ( ! empty( $city ) && ! is_wp_error( $city ) && $city->count > 1 )
					{
						$city_output = '<a href="' . home_url( sl_setting( 'company_base_url' ) . '/city/' . $city->slug ) . '">' . $city->name . '</a>';
						$city_name   = $city->name;
					}
					else
					{
						$city_output = $city_name = get_post_meta( get_the_ID(), 'city', true );
					}
					?>
					<article <?php post_class(); ?> data-title="<?php the_title(); ?>" data-rating="<?php echo $average; ?>" data-location="<?php echo $city_name; ?>">
						<div class="entry-title">
							<a class="title" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						</div>
						<?php if ( $average ) : ?>
							<div class="entry-meta rating"><?php sl_star_rating( $average, 'type=rating' ); ?></div>
						<?php else: ?>
							<div class="entry-meta rating none"><?php _e( 'No Reviews', '7listings' ); ?></div>
						<?php endif; ?>
						<div class="entry-meta location"><?php echo $city_output; ?></div>
					</article>
				<?php endwhile; ?>
			</div>
		</div>
	</div>
	<?php
	if ( ! sl_setting( 'company_archive_priority' ) )
		peace_numeric_pagination( $query );
}
