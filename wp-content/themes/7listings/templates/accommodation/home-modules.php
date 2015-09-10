<?php
/**
 * Display module content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_show_accommodation_modules( $id )
{
	$prefix          = "homepage_{$id}_";
	$module_settings = sl_settings_to_array( $prefix );
	switch ( $id )
	{
		// Accommodation Featured
		case 'accommodation_featured':
			// Normalize settings, make sure settings are set
			// We set only some settings here. Some ones don't need normalizing as they'll be set by Sl_Accommodation_Frontend::slider()
			$module_settings = array_merge( array(
				'title'      => '',
				'total'      => 7,
				'class'      => 'slide featured',
				'image_size' => 'sl_pano_large',
				'container'  => 'div',
			), $module_settings );

			// Change some settings name to match Sl_Accommodation_Frontend::slider() arguments
			$module_settings['number'] = $module_settings['total'];
			unset( $module_settings['total'] );

			if ( $title = $module_settings['title'] )
			{
				$heading_style  = sl_heading_style( 'homepage_accommodation_featured_title' );
				$title          = "<" . $heading_style . " class='title section'>{$title}</" . $heading_style . ">";
			}

			echo '<section id="accommodations-slider" class="container">' . $title;
			echo Sl_Accommodation_Frontend::slider( $module_settings );
			echo '</section>';
			break;

		// Accommodation Listings
		case 'accommodation_listings':
			// Normalize settings, make sure settings are set
			// We set only some settings here. Some ones don't need normalizing as they'll be set by Sl_Accommodation_Frontend::post_list()
			$module_settings = array_merge( array(
				'title'     => '',
				'display'   => 12,
				'layout'    => 'grid',
				'container' => 'div',
			), $module_settings );

			// Change some settings name to match Sl_Accommodation_Frontend::post_list() arguments
			$module_settings['number']    = $module_settings['display'];
			$module_settings['display']   = $module_settings['layout'];
			$module_settings['hierarchy'] = $module_settings['priority'];
			unset( $module_settings['layout'], $module_settings['priority'] );

			if ( $title = $module_settings['title'] )
			{
				$heading_style  = sl_heading_style( 'homepage_accommodation_listings_title' );
				$title          = "<" . $heading_style . " class='title section'>{$title}</" . $heading_style . ">";
			}

			echo '<section id="accommodations-list" class="container">' . $title;
			echo Sl_Accommodation_Frontend::post_list( $module_settings );
			echo '</section>';
			break;

		// Accommodation Types
		case 'accommodation_types':
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

			$tax   = sl_meta_key( 'tax_type', 'accommodation' );
			$types = get_terms( $tax, $args );

			$image_size = sl_setting( "{$prefix}image_size" );

			$class = 'sl-list types accommodations columns-' . sl_setting( "{$prefix}columns" );
			?>
			<section id="accommodation-types" class="<?php echo $class; ?>">
				<div class="container">

					<?php
					if ( sl_setting( "{$prefix}title" ) )
					{
						$heading_style = sl_heading_style( 'homepage_accommodation_types_title' );
						echo '<' . $heading_style . ' class="title">' . sl_setting( "{$prefix}title" ) . '</' . $heading_style . '>';
					}
					foreach ( $types as $type ): ?>
						<article class="post accommodation">
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
			</section><!-- #accommodation-types .section -->
			<?php
			break;

		// Accommodation Amenities
		case 'accommodation_amenities':
			?>
			<section id="accommodation-amenities" class="sl-list accommodations features amenities">
				<div class="container">
					<?php
					if ( $title = sl_setting( "{$prefix}title" ) )
					{
						$heading_style = sl_heading_style( 'homepage_accommodation_amenities_title' );
						echo '<' . $heading_style . ' class="title">' . $title . '</' . $heading_style . '>';
					}
					wp_tag_cloud( array(
						'number'   => sl_setting( "{$prefix}display" ),
						'taxonomy' => 'amenity',
					) );
					?>
				</div>
			</section><!-- #accommodation-amenities -->
			<?php
			break;
	}
}
