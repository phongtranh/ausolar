<?php
/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_post_modules( $id )
{
	if ( 'post_listings' != $id )
		return;

	// Common query args
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => sl_setting( 'homepage_post_listings_display' ),
		'ignore_sticky_posts' => true,
	);

	switch ( sl_setting( 'homepage_post_listings_orderby' ) )
	{
		case 'rand':
			$args['orderby'] = 'rand';
			break;
		case 'views':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = 'views';
			break;
		case 'date':
		default:
	}

	if ( $cat = sl_setting( 'homepage_post_listings_category' ) )
		$args['cat'] = $cat;

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() )
		return;

	switch ( sl_setting( 'homepage_post_listings_layout' ) )
	{
		case 'magazine':
			sl_homepage_posts_listings_magazine( $query );
			break;
		default:
			sl_homepage_posts_listings_list( $query );
	}
}

/**
 * Display posts in list/grid layout
 *
 * @param WP_Query $query
 *
 * @return void
 */
function sl_homepage_posts_listings_list( $query )
{
	if ( $title = sl_setting( 'homepage_post_listings_title' ) )
	{
		$heading_style  = sl_heading_style( 'homepage_post_listings_title' );
		$title          = "<" . $heading_style . " class='title section'>$title</" . $heading_style . ">";
	}
	?>

	<section id="posts" class="list posts">
		<div class="container">

			<?php
			$sidebar_layout = sl_setting( 'homepage_post_listings_sidebar_layout' );
			$classes        = 'sl-list sl-hp-content archive';
			if ( 'grid' == sl_setting( 'homepage_post_listings_layout' ) )
				$classes .= ' columns-' . sl_setting( 'homepage_post_listings_columns' );
			$classes .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
			$classes = $classes ? ' class="' . $classes . '"' : '';
			?>

			<section id="post-wrapper" <?php echo $classes; ?>>
				<?php echo $title; ?>

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

					<article <?php post_class(); ?>>

						<?php
						if ( sl_setting( 'homepage_post_listings_featured' ) )
							echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

						echo '<div class="details">';

							echo sl_listing_element( 'post_title', array( 'title_tag' => 'h3' ) );
							sl_post_meta();

							echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( 'homepage_post_listings_desc' ) ) );

							if ( sl_setting( 'homepage_post_listings_readmore' ) )
							{
								echo sl_listing_element( 'more_link', array(
									'more_link_type' => sl_setting( 'homepage_post_listings_readmore_type' ),
									'more_link_text' => sl_setting( 'homepage_post_listings_readmore_text' ),
								) );
							}
						echo '</div>'; // .details
						?>

					</article>
				<?php
				endwhile;

				/**
				 * Add 'View more listings' links
				 * Link to term archive page and fallback to post type archive page
				 * If the archive page does not have more listing, then don't show this link
				 */
				if ( sl_setting( 'homepage_post_listings_more_listings' ) )
				{
					$show = true;

					// Get blog page
					$link = '';
					if ( 'page' == get_option( 'show_on_front' ) && ( $blog_page = get_option( 'page_for_posts' ) ) )
						$link = get_permalink( $blog_page );

					// If set category, get link to that category page
					if ( sl_setting( 'homepage_post_listings_category' ) )
					{
						$term = get_term( sl_setting( 'homepage_post_listings_category' ), 'category' );
						if ( ! is_wp_error( $term ) )
						{
							// Don't show view more listings if the term doesn't have more listings
							if ( $term->count <= sl_setting( 'homepage_post_listings_display' ) )
								$show = false;

							$term_link = get_term_link( $term, 'category' );
							if ( ! is_wp_error( $term_link ) )
								$link = $term_link;
						}
					}

					if ( $show && $link )
						echo '<a class="' . sl_setting( 'homepage_post_listings_more_listings_style' ) . '" href="' . $link . '">' . sl_setting( 'homepage_post_listings_more_listings_text' ) . '</a>';
				}
				?>
			</section>

			<?php if ( 'none' != $sidebar_layout ) : ?>
				<aside id="sidebar" class="sl-hp-sidebar <?php echo $sidebar_layout; ?>">
					<?php dynamic_sidebar( sl_setting( 'homepage_post_listings_sidebar' ) ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</section><!-- .posts.list -->
<?php
}

/**
 * Display posts in magazine layout
 *
 * @param WP_Query $query
 *
 * @return void
 */
function sl_homepage_posts_listings_magazine( $query )
{
	// Store number of displayed posts
	$display = 0;

	if ( $title = sl_setting( 'homepage_post_listings_title' ) )
		$title = "<h3 class='title section'>$title</h3>";
	?>

	<section id="posts" class="list posts">
		<div class="container">

			<?php echo $title; ?>

			<?php
			$sidebar_layout = sl_setting( 'homepage_post_listings_sidebar_layout' );
			$classes        = 'sl-hp-content';
			$classes .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
			$classes = $classes ? ' class="' . $classes . '"' : '';
			?>

			<section id="post-wrapper" <?php echo $classes; ?>>

				<?php
				$show_thumb = sl_setting( 'homepage_post_listings_featured' );

				// First post
				$query->the_post();
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

							echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( 'homepage_post_listings_desc' ) ) );

							if ( sl_setting( 'homepage_post_listings_readmore' ) )
							{
								echo sl_listing_element( 'more_link', array(
									'more_link_type' => sl_setting( 'homepage_post_listings_readmore_type' ),
									'more_link_text' => sl_setting( 'homepage_post_listings_readmore_text' ),
								) );
							}

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
					if ( $display == sl_setting( 'homepage_post_listings_display' ) || $break )
						break;

					echo "<div class='sl-list archive posts grid columns-$col'>";

					$count = 0;
					while ( $count < $col )
					{
						// No posts remaining in the queue
						if ( $display == sl_setting( 'homepage_post_listings_display' ) )
						{
							$break = true;
							break;
						}

						$query->the_post();
						?>

						<article <?php post_class(); ?>>

							<?php
							if ( $show_thumb )
								echo sl_listing_element( 'thumbnail', array( 'image_size' => 'sl_pano_medium' ) );

							echo '<div class="details">';

							echo sl_listing_element( 'post_title', array( 'title_tag' => 'h3' ) );

							sl_post_meta();

							echo sl_listing_element( 'excerpt', array( 'excerpt_length' => sl_setting( 'homepage_post_listings_desc' ) ) );

							if ( sl_setting( 'homepage_post_listings_readmore' ) )
							{
								echo sl_listing_element( 'more_link', array(
									'more_link_type' => sl_setting( 'homepage_post_listings_readmore_type' ),
									'more_link_text' => sl_setting( 'homepage_post_listings_readmore_text' ),
								) );
							}

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
				if ( $query->have_posts() )
				{
					echo "<div class='sl-list archive posts grid columns-4'>";

					while ( $query->have_posts() )
					{
						$query->the_post();
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

							echo '</div>';
							?>

						</article>

						<?php
					}

					echo '</div>'; // .sl-list
				}

				wp_reset_postdata();

				/**
				 * Add 'View more listings' links
				 * Link to term archive page and fallback to post type archive page
				 * If the archive page does not have more listing, then don't show this link
				 */
				if ( sl_setting( 'homepage_post_listings_more_listings' ) )
				{
					$show = true;

					// Get blog page
					$link = '';
					if ( 'page' == get_option( 'show_on_front' ) && ( $blog_page = get_option( 'page_for_posts' ) ) )
						$link = get_permalink( $blog_page );

					// If set category, get link to that category page
					if ( sl_setting( 'homepage_post_listings_category' ) )
					{
						$term = get_term( sl_setting( 'homepage_post_listings_category' ), 'category' );
						if ( ! is_wp_error( $term ) )
						{
							// Don't show view more listings if the term doesn't have more listings
							if ( $term->count <= sl_setting( 'homepage_post_listings_display' ) )
								$show = false;

							$term_link = get_term_link( $term, 'category' );
							if ( ! is_wp_error( $term_link ) )
								$link = $term_link;
						}
					}

					if ( $show && $link )
						echo '<a class="' . sl_setting( 'homepage_post_listings_more_listings_style' ) . '" href="' . $link . '">' . sl_setting( 'homepage_post_listings_more_listings_text' ) . '</a>';
				}
				?>

			</section>

			<?php if ( 'none' != $sidebar_layout ) : ?>
				<aside id="sidebar" class="<?php echo $sidebar_layout; ?>">
					<?php dynamic_sidebar( sl_setting( 'homepage_post_listings_sidebar' ) ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</section>
<?php
}
