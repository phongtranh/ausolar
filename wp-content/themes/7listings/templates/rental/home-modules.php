<?php
/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_rental_modules( $id )
{
	$prefix          = "homepage_{$id}_";
	$module_settings = sl_settings_to_array( $prefix );
	switch ( $id )
	{
		// Rental Featured
		case 'rental_featured':
			// Normalize settings, make sure settings are set
			// We set only some settings here. Some ones don't need normalizing as they'll be set by Sl_Rental_Frontend::slider()
			$module_settings = array_merge( array(
				'title'      => '',
				'total'      => 7,
				'class'      => 'slide featured',
				'image_size' => 'sl_pano_large',
				'container'  => 'div',
			), $module_settings );

			// Change some settings name to match Sl_Rental_Frontend::slider() arguments
			$module_settings['number'] = $module_settings['total'];
			unset( $module_settings['total'] );

			if ( $title = $module_settings['title'] )
			{
				$heading_style  = sl_heading_style( 'homepage_rental_featured_title' );
				$title          = "<" . $heading_style . " class='title section'>{$title}</" . $heading_style . ">";
			}

			echo '<section id="rentals-slider" class="container">' . $title;
			echo Sl_Rental_Frontend::slider( $module_settings );
			echo '</section>';
			break;

		// Rental Listings
		case 'rental_listings':
			// Normalize settings, make sure settings are set
			// We set only some settings here. Some ones don't need normalizing as they'll be set by Sl_Rental_Frontend::post_list()
			$module_settings = array_merge( array(
				'title'     => '',
				'display'   => 12,
				'layout'    => 'grid',
				'container' => 'div',
			), $module_settings );

			// Change some settings name to match Sl_Rental_Frontend::post_list() arguments
			$module_settings['number']    = $module_settings['display'];
			$module_settings['display']   = $module_settings['layout'];
			$module_settings['hierarchy'] = $module_settings['priority'];
			unset( $module_settings['layout'], $module_settings['priority'] );

			if ( $title = $module_settings['title'] )
			{
				$heading_style  = sl_heading_style( 'homepage_rental_listings_title' );
				$title          = "<" . $heading_style . " class='title section'>{$title}</" . $heading_style . ">";
			}

			echo '<section id="rentals-list" class="container">' . $title;
			echo Sl_Rental_Frontend::post_list( $module_settings );
			echo '</section>';
			break;

		// Rental Types
		case 'rental_types':
			$total = sl_setting( "{$prefix}display" ) ? sl_setting( "{$prefix}display" ) : 5;
			$args  = array(
				'number' => $total,
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
				case '':
					$args['orderby'] = 'none';
					break;
			}

			$tax   = sl_meta_key( 'tax_type', 'rental' );
			$types = get_terms( $tax, $args );

			$image_size = sl_setting( "{$prefix}image_size" );

			$class = 'sl-list types rentals columns-' . sl_setting( "{$prefix}columns" );
			?>
			<section id="rental-types" class="<?php echo $class; ?>">
				<div class="container">

					<?php
					if ( sl_setting( "{$prefix}title" ) )
					{
						$heading_style = sl_heading_style( 'homepage_rental_types_title' );
						echo '<' . $heading_style . ' class="title">' . sl_setting( "{$prefix}title" ) . '</' . $heading_style . '>';
					}

					foreach ( $types as $type ): ?>
						<article class="post rental">
							<?php
							if ( sl_setting( "{$prefix}image" ) )
							{
								echo '<a href="' . get_term_link( $type, $tax ) . '" rel="bookmark">';
								echo '<figure class="thumbnail">';
								$thumbnail_id = sl_get_term_meta( $type->term_id, 'thumbnail_id' );
								if ( $thumbnail_id )
								{
									echo wp_get_attachment_image( $thumbnail_id, $image_size, 0, array(
										'alt'   => $type->description,
										'title' => $type->description,
									) );
								}
								else
								{
									echo '<img class="photo">';
								}

								echo '</figure>';
								echo '</a>';
							}
							?>
							<div class="details">
								<h4 class="entry-title">
									<a class="title" href="<?php echo get_term_link( $type, $tax ); ?>" rel="bookmark"><?php echo $type->name; ?></a>
								</h4>
								<?php if ( sl_setting( "{$prefix}desc" ) ): ?>
									<span class="entry-summary excerpt"><?php echo $type->description; ?></span>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>

				</div>
				<!-- .container -->
			</section><!-- #rental-types .section -->
			<?php
			break;

		// Rental Features
		case 'rental_features':
			?>
			<section id="rental-features" class="sl-list rentals features">
				<div class="container">
					<?php
					if ( $title = sl_setting( "{$prefix}title" ) )
					{
						$heading_style = sl_heading_style( 'homepage_rental_features_title' );
						echo '<' . $heading_style . ' class="title">' . $title . '</' . $heading_style . '>';
					}

					wp_tag_cloud( array(
						'number'   => sl_setting( "{$prefix}display" ),
						'taxonomy' => 'feature',
					) );
					?>
				</div>
			</section><!-- #rental-features -->
			<?php
			break;
	}
}
