<?php
/**
 * The Template for the Featured Title Area
 *
 * @package Listings
 */
?>
<header id="featured"
	<?php
	/**
	 * Get featured title CSS class, includes:
	 * - Design background type
	 * - Page header height
	 * - 7listings homepage height
	 *
	 * And apply filters to allow modules to change
	 */
	$class = array();

	// Design background type (only when it has background image)
	if ( sl_setting( 'design_featured_background_image' ) && sl_setting( 'design_featured_background_image_id' ) && sl_setting( 'design_featured_background_type' ) )
		$class[] = sl_setting( 'design_featured_background_type' );

	// Page
	if ( is_page() && get_post_meta( get_the_ID(), 'featured_header_height_enable', true ) )
	{
		$height = get_post_meta( get_the_ID(), 'featured_header_height', true );
		if ( $height && 'medium' != $height )
			$class[] = $height;
	}

	// 7listings homepage
	if ( is_front_page() && sl_setting( 'homepage_enable' ) )
	{
		$height = sl_setting( 'homepage_featured_area_height' );
		if ( $height && 'medium' != $height )
			$class[] = $height;
	}

	// Tan: Default height is tiny
	if ( count( $class ) === 1 )
		$class[] = 'tiny';

	// Filters to add more classes
	$class = peace_filters( 'featured_title_class', $class );

	$class = array_unique( array_filter( $class ) );

	if ( ! empty( $class ) )
		echo ' class="' . esc_attr( implode( ' ', $class ) ) . '"';

	/**
	 * Get inline CSS style, includes:
	 * - Custom background for page
	 * - Custom background for 7listings homepage
	 *
	 * And apply filters to allow modules to change
	 */
	$style = '';

	// Page custom background
	if ( is_page() && get_post_meta( get_the_ID(), 'featured_image', true ) && has_post_thumbnail() )
	{
		$image = get_post_thumbnail_id();
		list( $src ) = wp_get_attachment_image_src( $image, sl_setting( 'design_featured_background_size' ) );
		$style .= 'background-image: url(' . $src . ');';
	}

	// 7listings homepage custom background
	if ( is_front_page() && sl_setting( 'homepage_enable' ) && ( $image = sl_setting( 'homepage_featured_area_image' ) ) )
	{
		list( $src ) = wp_get_attachment_image_src( $image, sl_setting( 'design_featured_background_size' ) );
		$style .= 'background-image: url(' . $src . ');';
	}

	$style = peace_filters( 'featured_title_style', $style );
	if ( $style )
		echo ' style="' . esc_attr( $style ) . '"';
	?>>

	<?php peace_action( 'featured_title_before' ); ?>

	<div class="container">
		<?php
		peace_action( 'featured_title_top' );

		// Show title or not
		$show_title = true;
		if ( is_page() )
		{
			$show_title = get_post_meta( get_the_ID(), 'featured_header_title', true );
			if ( '' === $show_title )
				$show_title = true;
		}
		$show_title = peace_filters( 'featured_title_show_title', $show_title );
		if ( $show_title )
		{
			// Default title
			$title = __( 'Archives', '7listings' );

			// Check if singular post/page is enabled the title area
			if ( is_singular() )
			{
				$title = get_the_title();
			}
			elseif ( is_search() )
			{
				$title = sprintf( __( 'Search results for &quot;%s&quot;', '7listings' ), get_search_query() );
			}
			elseif ( is_404() )
			{
				$title = __( 'Page not found', '7listings' );
			}
			elseif ( is_author() )
			{
				the_post();
				$title = sprintf( __( 'Author Archives: %s', '7listings' ), get_the_author() );
				rewind_posts();
			}
			elseif ( is_day() )
			{
				$title = sprintf( __( 'Daily Archives: %s', '7listings' ), get_the_date() );
			}
			elseif ( is_month() )
			{
				$title = sprintf( __( 'Monthly Archives: %s', '7listings' ), get_the_date( 'F Y' ) );
			}
			elseif ( is_year() )
			{
				$title = sprintf( __( 'Yearly Archives: %s', '7listings' ), get_the_date( 'Y' ) );
			}
			elseif ( is_tax() || is_category() || is_tag() )
			{
				if ( is_tax( 'location' ) )
				{
					$term                  = get_queried_object();
					$replacement['%TERM%'] = $term->name;
					$title                 = sl_setting( 'post_location_title' );
					$title                 = strtr( $title, $replacement );
				}
				else
				{
					$title = single_term_title( '', false );
				}
			}

			// Allow modules filter this
			$title = peace_filters( 'featured_title_title', $title );
			if ( $title )
			{
				$heading_style = is_front_page() ? sl_heading_style( 'homepage_featured_area_heading' ) : 'h1';
				$item_prop = 'itemprop="' . ( is_single() ? 'headline' : 'name' ) . '"';
				echo '<' . $heading_style . ' class="title" ' . $item_prop . '>' . do_shortcode( $title ) . '</' . $heading_style . '>';
			}
		}

		// Subtitle
		$subtitle = '';
		if ( is_page() && get_post_meta( get_the_ID(), 'featured_header_text', true ) )
			$subtitle = get_post_meta( get_the_ID(), 'featured_header_text_content', true );

		// Allow modules filter this
		$subtitle = peace_filters( 'featured_title_subtitle', $subtitle );

		if ( $subtitle )
			echo '<div class="subtitle">' . do_shortcode( $subtitle ) . '</div>';

		if ( is_page() && get_post_meta( get_the_ID(), 'featured_header_slideshow', true ) && get_post_meta( get_the_ID(), 'featured_header_slideshow_id', true ) )
			echo do_shortcode( '[slideshow id="' . get_post_meta( get_the_ID(), 'featured_header_slideshow_id', true ) . '"]' );

		peace_action( 'featured_title_bottom' );
		?>
	</div>

	<?php peace_action( 'featured_title_after' ); ?>

</header>
