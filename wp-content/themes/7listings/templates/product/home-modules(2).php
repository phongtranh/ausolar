<?php
/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_product_modules( $id )
{
	$prefix = "homepage_{$id}_";
	switch ( $id )
	{
		// Product Featured
		case 'product_featured':

			// Common query args
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => get_option( 'woo_featured_product_limit' ),
				'meta_query'     => array(
					array(
						'meta_key'   => '_featured',
						'meta_value' => 'yes',
					),
				),
			);
			switch ( sl_setting( "{$prefix}orderby" ) )
			{
				case 'rand':
					$args['orderby'] = 'rand';
					break;
				case 'views':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'views';
					break;
				case 'price-asc':
					$args['meta_key'] = '_regular_price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'ASC';
					break;
				case 'price-desc':
					$args['meta_key'] = '_regular_price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					break;
				case 'date':
				default:
			}

			global $post;
			$query = new WP_Query( $args );
			if ( ! $query->have_posts() )
				return;

			$cols = sl_setting( "{$prefix}columns" );

			if ( $title = sl_setting( "{$prefix}title" ) )
			{
				$heading_style  = sl_heading_style( 'homepage_product_featured_title' );
				$title          = "<" . $heading_style . " class='title section'>$title</" . $heading_style . ">";
			}
			?>

			<section id="product-slider" class="slider products">
				<div class="container">
					<?php echo $title; ?>

					<div class="columns-<?php echo $cols; ?>">
						<ul class="slides products">
							<?php
							$count = 0;
							while ( $query->have_posts() ) : $query->the_post();
								$_product = wc_get_product( get_the_ID() );
								$count ++;

								$class = 'post product featured-listing';
								if ( 1 == $count )
									$class .= ' first';
								if ( $cols == $count )
								{
									$class .= ' last';
									$count = 0;
								}
								?>
								<li <?php post_class( $class ); ?>>
									<div class="front">
										<?php woocommerce_show_product_sale_flash( $post, $_product ); ?>
										<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
											<?php woocommerce_template_loop_product_thumbnail(); ?>
										</a>
									</div>
									<!--/.front-->

									<div class="back">
										<h3>
											<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
										</h3>
										<span class="price"><?php echo $_product->get_price_html(); ?></span>
										<?php woocommerce_template_loop_add_to_cart(); ?>
									</div>
									<!--/.back-->
								</li>
							<?php
							endwhile;
							wp_reset_postdata();
							?>
						</ul>
					</div>
				</div>
			</section><!-- .product-slider.section -->

			<?php
			break;

		// Product Listings
		case 'product_listings':

			// Common query args
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => sl_setting( "{$prefix}display" ),
			);
			switch ( sl_setting( "{$prefix}orderby" ) )
			{
				case 'rand':
					$args['orderby'] = 'rand';
					break;
				case 'views':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'views';
					break;
				case 'price-asc':
					$args['meta_key'] = '_regular_price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'ASC';
					break;
				case 'price-desc':
					$args['meta_key'] = '_regular_price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					break;
				case 'date':
				default:
			}

			if ( sl_setting( "{$prefix}category" ) )
			{
				$args['tax_query']   = array();
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => sl_setting( "{$prefix}category" ),
				);
			}

			$query = new WP_Query( $args );
			if ( ! $query->have_posts() )
				return;

			$cols = sl_setting( "{$prefix}columns" );

			// Columns
			add_filter( 'loop_shop_columns', 'sl_homepage_product_listings_columns' );

			// Thumbnail
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
			add_action( 'woocommerce_before_shop_loop_item_title', 'sl_homepage_product_listings_thumbnail' );

			if ( $title = sl_setting( "{$prefix}title" ) )
			{
				$heading_style  = sl_heading_style( 'homepage_product_listings_title' );
				$title          = "<" . $heading_style . " class='title section'>$title</" . $heading_style . ">";
			}
			?>

			<section id="products" class="sl-list columns-<?php echo $cols; ?> products">
				<div class="container">
					<?php echo $title; ?>

					<ul class="products">
						<?php
						while ( $query->have_posts() ) : $query->the_post();

							wc_get_template_part( 'content', 'product' );

						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				</div>
				<?php
				/**
				 * Add 'View more products' links
				 * Link to term archive page and fallback to post type archive page
				 * If the archive page does not have more listing, then don't show this link
				 */
				if ( sl_setting( 'homepage_product_listings_more_listings' ) )
				{
					$show = true;

					// Get link shop
					$link = '';
					if ( class_exists( 'WC' ) )
						$link = apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) );

					// If set category, get link to that category page
					if ( sl_setting( 'homepage_product_listings_category' ) )
					{
						$term = get_term( sl_setting( 'homepage_product_listings_category' ), 'product_cat' );
						if ( ! is_wp_error( $term ) )
						{
							// Don't show view more listings if the term doesn't have more listings
							if ( $term->count <= sl_setting( 'homepage_product_listings_display' ) )
								$show = false;

							$term_link = get_term_link( $term, 'product_cat' );
							if ( ! is_wp_error( $term_link ) )
								$link = $term_link;
						}
					}

					if ( $show && $link )
						echo '<a class="' . sl_setting( 'homepage_product_listings_more_listings_style' ) . '" href="' . $link . '">' . sl_setting( 'homepage_product_listings_more_listings_text' ) . '</a>';
				}
				?>
			</section><!-- .products.list -->

			<?php
			// Thumbnail
			remove_action( 'woocommerce_before_shop_loop_item_title', 'sl_homepage_product_listings_thumbnail' );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );

			// Columns
			remove_filter( 'loop_shop_columns', 'sl_homepage_product_listings_columns' );
			break;

		// Product Categories
		case 'product_categories':
			$args = array(
				'number' => sl_setting( "{$prefix}display" ),
			);

			switch ( sl_setting( "{$prefix}orderby" ) )
			{
				case 'name':
					$args['orderby'] = 'name';
					break;
				case 'listings-asc':
					$args['orderby'] = 'count';
					$args['order']   = 'ASC';
					break;
				case 'listings-desc':
					$args['orderby'] = 'count';
					$args['order']   = 'DESC';
					break;
				case 'none':
					$args['orderby'] = 'none';
					break;
				default:
			}

			$parent = 0;
			if ( ! sl_setting( "{$prefix}sub" ) )
				$args['parent'] = $parent;
			else
				$args['child_of'] = $parent;

			$tax  = 'product_cat';
			$cats = get_terms( $tax, $args );

			$image_size = sl_setting( "{$prefix}image_size" );

			$class = 'sl-list categories products columns-' . sl_setting( "{$prefix}columns" );
			?>
			<section id="product-categories" class="<?php echo $class; ?>">
				<div class="container">
					<?php
						if ( sl_setting( "{$prefix}title" ) )
						{
							$heading_style = sl_heading_style( 'homepage_product_categories_title' );
							echo '<' . $heading_style . ' class="title section">' . sl_setting( "{$prefix}title" ) .'</' . $heading_style . '>';
						}

						if ( ! empty( $cats ) ) :
						$i = 0;
						foreach ( $cats as $cat ) :
							$i ++;
							?>
							<article class="post category<?php
							if ( $i % sl_setting( "{$prefix}columns" ) == 0 )
								echo ' last';
							elseif ( ( $i - 1 ) % sl_setting( "{$prefix}columns" ) == 0 )
								echo ' first';

							if ( $cat->parent != $parent )
								echo ' child';
							?>">
								<?php
								if ( sl_setting( "{$prefix}thumb" ) )
								{
									echo '<a href="' . get_term_link( $cat, $tax ) . '" rel="bookmark">';
									echo '<figure class="thumbnail">';
									$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
									if ( $thumbnail_id )
									{
										echo wp_get_attachment_image( $thumbnail_id, $image_size, 0, array(
											'alt'   => $cat->description,
											'title' => $cat->description,
										) );
									}
									else
									{
										echo wc_placeholder_img( $image_size );
									}
									echo '</figure>';
									echo '</a>';
								}
								echo '<div class="details">';
								if ( sl_setting( "{$prefix}category_title" ) )
									echo '<a class="title" href="' . get_term_link( $cat, $tax ) . '" rel="bookmark">' . $cat->name . '</a>';

								if ( sl_setting( "{$prefix}count" ) )
									echo '<span class="category-count amount">' . $cat->count . '</span>';
								echo '</div>';
								?>
							</article>
						<?php endforeach; ?>

					<?php endif; ?>

				</div>
				<!-- .container -->
			</section><!-- #product-categories .section -->
			<?php
			break;
	}
}

/**
 * Change number of columns on homepage for listings widget
 *
 * @param int $cols
 *
 * @return int
 */
function sl_homepage_product_listings_columns( $cols )
{
	return sl_setting( 'homepage_product_listings_columns' );
}

/**
 * Display product thumbnail on homepage for listing widget
 *
 * @return void
 */
function sl_homepage_product_listings_thumbnail()
{
	$size = sl_setting( 'homepage_product_listings_image_size' );
	if ( has_post_thumbnail() )
	{
		the_post_thumbnail( $size, array(
			'alt'   => the_title_attribute( 'echo=0' ),
			'title' => the_title_attribute( 'echo=0' ),
		) );
	}
	elseif ( wc_placeholder_img_src() )
	{
		echo wc_placeholder_img( $size );
	}
}
