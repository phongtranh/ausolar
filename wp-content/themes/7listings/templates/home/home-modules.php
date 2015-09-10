<?php

/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_module( $id )
{
	$prefix = 'homepage_' . $id . '_';

	switch ( $id )
	{
		// Custom content
		case 'custom_content':
			if ( ! sl_setting( 'homepage_heading' ) && ! sl_setting( 'homepage_content' ) )
				return;
			?>

			<section id="hp-content" class="section main">
				<div class="container">

					<?php
					$sidebar_layout = sl_setting( 'homepage_custom_content_sidebar_layout' );
					$classes        = 'entry-content';
					$classes .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
					$classes = $classes ? ' class="' . $classes . '"' : '';
					?>

					<article id="content"<?php echo $classes; ?>>
						<?php
							if ( $heading = sl_setting( 'homepage_heading' ) )
							{
								$heading_style = sl_heading_style( 'homepage' );
								echo '<' . $heading_style .  ' class="title main">' . esc_html( $heading ) . '</' . $heading_style . '>';
							}
						?>
						<?php
						if ( $content = sl_setting( 'homepage_content' ) )
						{
							// Make it similar to a page content
							$content = apply_filters( 'the_content', $content );
							$content = str_replace( ']]>', ']]&gt;', $content );

							// Clean shortcodes
							$content  = str_replace( array( '<br>', '<br />', '<p></p>' ), '', $content );
							$patterns = array(
								'#^\s*</p>#',
								'#<p>\s*$#',
							);
							$content  = preg_replace( $patterns, '', $content );

							echo apply_filters( 'sl_homepage_custom_content', $content );
						}
						?>
					</article>

					<?php if ( 'none' != $sidebar_layout ) : ?>
						<aside id="sidebar" class="<?php echo $sidebar_layout; ?>">
							<?php dynamic_sidebar( sl_setting( 'homepage_custom_content_sidebar' ) ); ?>
						</aside>
					<?php endif; ?>

				</div>
			</section><!-- #hp-content .section main -->

			<?php
			break;

		// Featured Area
		case 'featured_area':
			if ( ! sl_setting( 'homepage_featured_area_heading' ) && ! sl_setting( 'homepage_featured_area_custom_text' ) )
				return;
			get_template_part( 'templates/parts/featured-title' );
			break;

		// Custom HTML
		case 'custom_html':
			if ( sl_setting( 'homepage_custom_html' ) )
				echo '<section class="section custom">' . do_shortcode( sl_setting( 'homepage_custom_html' ) ) . '</section>';
			break;

		// Listings Search
		case 'listings_search':
			$bg = '';
			if ( $img = sl_setting( "{$prefix}background" ) )
			{
				$bg = wp_get_attachment_url( $img );
			}

			$post_types = sl_setting( "{$prefix}post_types" );

			$available_post_types = sl_setting( 'listing_types' );
			foreach ( $post_types as $k => $post_type )
			{
				if ( ! in_array( $post_type, $available_post_types ) )
					unset( $post_types[$k] );
			}
			?>
			<section id="featured-search" class="full" style="background-image: url(<?php echo esc_url( $bg ); ?>);">
				<div class="container">

					<?php
					$heading_style = sl_heading_style( 'homepage_listings_search_title' );

					echo '<' . $heading_style . ' class="title">' . esc_html( sl_setting( "{$prefix}title" ) ) . '</' . $heading_style . '>';

					$class = 'search-wrapper';
					if ( sl_setting( "{$prefix}type" ) )
						$class .= ' has-taxonomies';
					?>
					<div class="<?php echo esc_attr( $class ); ?>">
						<?php
						if ( 1 < count( $post_types ) )
						{
							echo '<div class="type-wrapper">';

							foreach ( $post_types as $post_type )
							{
								$svg = '';
								switch ( $post_type )
								{
									case 'accommodation':
										$svg = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="52" height="52" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve" class="accommodations-icon">
  <path opacity="0.75" d="M43.46 35.86V45h4.76V31.35H8.24v-19.77c0-1.33-1.05-2.41-2.37-2.41 -1.31 0-2.37 1.08-2.37 2.41V45h4.74v-9.14H43.46z"/>
  <path d="M15.52 21.21c2.37 0 4.28-1.95 4.28-4.36 0-2.39-1.91-4.32-4.28-4.32 -2.37 0-4.3 1.93-4.3 4.32C11.22 19.26 13.16 21.21 15.52 21.21z"/>
  <path d="M48.29 27.74l-0.02-5.97c0-1.89-1.59-3.19-3.41-3.42L24.54 14.96c-0.06-0.02-0.12-0.06-0.18-0.06 -1.17 0-2.11 0.98-2.11 2.15v6.43h-8.82c-1.17 0-2.11 0.96-2.11 2.13 0 1.2 0.93 2.13 2.11 2.13H48.29L48.29 27.74z"/></svg>';
										break;
									case 'tour':
										$svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" width="52" height="52" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve" class="tours-icon">
  <path d="M19.34 35.59l12.33-6.17V16.41l-12.33 6.15V35.59zM22.43 24.45l6.15 3.07 -6.15 3.09V24.45z"/>
  <path opacity="0.75" d="M25.5 7.99c-10.1 0-17.86 7.81-17.86 18.01 0 10.18 7.76 18.01 17.86 18.01 10.68 0 18.85-7.83 18.85-18.01C44.35 15.8 36.19 7.99 25.5 7.99zM25.5 39.07c-7.19 0-12.92-5.86-12.92-13.07s5.73-13.07 12.92-13.07c7.36 0 13.91 5.86 13.91 13.07S32.86 39.07 25.5 39.07z"/></svg>';
										break;
									case 'rental':
										$svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0" y="0" width="52" height="52" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve" class="rentals-icon">
  <path d="M47 29.28c0 0.34-0.1 0.64-0.32 0.88l-7 8.34c-0.3 0.34-0.66 0.5-1.08 0.5 -0.42 0-0.78-0.16-1.06-0.5l-7-8.34c-0.24-0.24-0.34-0.54-0.34-0.88 0-0.38 0.14-0.7 0.42-0.98C30.9 28.02 31.22 27.88 31.6 27.88h4.2v-8.32h-12.6c-0.24 0-0.42-0.1-0.54-0.26l-3.5-4.18C19.06 15 19 14.86 19 14.7c0-0.2 0.08-0.36 0.2-0.5C19.34 14.06 19.52 14 19.7 14h18.42c3.26 0 3.28 3.56 3.28 3.56v1.3 9.02h4.2c0.38 0 0.72 0.14 0.98 0.42C46.86 28.58 47 28.9 47 29.28z"/>
  <path opacity="0.75" d="M33 38.3c0 0.2-0.06 0.36-0.2 0.5S32.48 39 32.3 39H14.24c-3.62 0-3.64-3.46-3.64-3.46v-1.4V25.1H6.4c-0.38 0-0.7-0.12-0.98-0.4C5.14 24.42 5 24.1 5 23.72c0-0.34 0.1-0.64 0.32-0.88l7-8.34c0.28-0.32 0.64-0.48 1.08-0.48 0.44 0 0.8 0.16 1.08 0.48l7 8.34c0.22 0.24 0.32 0.54 0.32 0.88 0 0.38-0.14 0.7-0.42 0.98C21.1 24.98 20.78 25.1 20.4 25.1h-4.2v8.34h12.6c0.24 0 0.42 0.08 0.54 0.24l3.5 4.16C32.94 38 33 38.16 33 38.3z"/></svg>';
										break;
									case 'attraction':
										$svg = '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" x="0" y="0" width="52" height="52" viewBox="0 0 52 52" enable-background="new 0 0 52 52" xml:space="preserve" class="attractions-icon"><path d="M19 22c0-3.9 3.1-7 7-7 3.9 0 7 3.1 7 7 0 3.9-3.1 7-7 7C22.1 29 19 25.9 19 22zM13 22c0 8.6 13 23 13 23s13-14.4 13-23C39 14.8 33.2 9 26 9S13 14.8 13 22z"/></svg>';
								}
								$post_type_object = get_post_type_object( $post_type );
								printf( '<a href="#" class="%1$ss-search" data-post_type="%1$s">%2$s %3$s</a>',
									$post_type,
									$svg,
									$post_type_object->labels->name );
							}
							echo '</div>';
						}
						?>
						<form action="<?php echo HOME_URL; ?>" method="get" role="form" class="search-inputs">
							<?php wp_nonce_field( 'widget-search', 'sl_widget_search', false ); ?>
							<input type="hidden" name="post_type" value="<?php echo 1 == count( $post_types ) ? esc_attr( $post_types[0] ) : ''; ?>">

							<div class="main-inputs">
								<input type="search" name="s" placeholder="<?php esc_attr_e( 'Keywords...', '7listings' ); ?>" class="keyword" value="">
								<?php
								if ( sl_setting( "{$prefix}location" ) )
								{
									global $wpdb;
									foreach ( $post_types as $post_type )
									{
										echo "<div class='location-inputs' data-post_type='$post_type'>";

										/**
										 * Get all location terms for current post type only
										 * We know that terms are saved as post meta, so we query from post meta
										 */
										$term_ids = $wpdb->get_col( "
											SELECT DISTINCT meta_value FROM $wpdb->postmeta AS m
											INNER JOIN $wpdb->posts AS p ON p.ID = m.post_id
											WHERE p.post_type='$post_type' AND m.meta_key IN ('state', 'city', 'area')
										" );

										$select = wp_dropdown_categories( array(
											'taxonomy'        => 'location',
											'orderby'         => 'name',
											'hierarchical'    => true,
											'name'            => "sl_location_$post_type",
											'show_option_all' => __( 'All locations', '7listings' ),
											'echo'            => false,
											'include'         => $term_ids,
											'id'              => "location-$post_type",
											'class'           => 'sl-location',
										) );

										// Add select2 options for width and placeholder
										$select = str_replace( '<select', '<select data-placeholder="' . esc_attr__( 'Location', '7listings' ) . '"', $select );

										// Add empty <option> for placeholder
										$select = preg_replace( '@(<select[^>]*>)@', '\1<option></option>', $select );

										echo $select;

										echo '</div>';
									}
								}
								?>
							</div>

							<?php if ( in_array( 'accommodation', $post_types ) && sl_setting( "{$prefix}star_rating" ) ) : ?>
								<div class="ratings-inputs">
									<div class="stars star-5"></div>
									<div class="stars star-4"></div>
									<div class="stars star-3"></div>
									<div class="stars star-2"></div>
									<div class="stars star-1"></div>
								</div>
								<input type="hidden" name="star_rating">
							<?php endif; ?>
							<?php
							if ( sl_setting( "{$prefix}type" ) )
							{
								foreach ( $post_types as $post_type )
								{
									$terms = get_terms( sl_meta_key( 'tax_type', $post_type ), array(
										'orderby' => sl_setting( "{$prefix}type_orderby" ),
										'order'   => 'name' == sl_setting( "{$prefix}type_orderby" ) ? 'ASC' : 'DESC',
										'number'  => sl_setting( "{$prefix}type_number" ),
									) );
									if ( $terms )
									{
										echo "<div class='taxonomy-inputs' data-post_type='$post_type'>";
										foreach ( $terms as $term )
										{
											printf(
												'<input type="checkbox" name="sl_type_%4$s[]" value="%1$s" id="taxonomy-%1$s">
												<label for="taxonomy-%1$s">%2$s%3$s</label>',
												$term->term_id,
												$term->name,
												sl_setting( "{$prefix}type_counter" ) ? '<span class="counter">' . $term->count . '</span>' : '',
												$post_type
											);
										}
										echo '</div>';
									}
								}
							}
							?>
							<button type="submit" class="button search"><?php _e( 'Search', '7listings' ); ?></button>
						</form>
					</div>
				</div>
			</section>
			<?php
			break;

		default:
			do_action( 'sl_homepage_show_module', $id );
	}
}
