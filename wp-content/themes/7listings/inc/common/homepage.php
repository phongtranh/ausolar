<?php
/**
 * Get heading style for homepage widgets title
 *
 * @param string $name
 *
 * @return string
 */
function sl_heading_style( $name )
{
	$suffix = '_heading_style';

	$default = array(
		'homepage_featured_area_heading' . $suffix         => 'h1', // featured
		'homepage' . $suffix                               => 'h1', // main content
		'homepage_listings_search_title' . $suffix         => 'h1', // listings search
		'homepage_post_listings_title' . $suffix           => 'h3', // posts
		'homepage_product_featured_title' . $suffix        => 'h2', // product slider
		'homepage_product_categories_title' . $suffix      => 'h3', // product categories
		'homepage_product_listings_title' . $suffix        => 'h3', // products
		'homepage_company_logos_title' . $suffix           => 'h2', // company
		'homepage_accommodation_featured_title' . $suffix  => 'h3', // accommodation slider
		'homepage_accommodation_types_title' . $suffix     => 'h3', // accommodation types
		'homepage_accommodation_listings_title' . $suffix  => 'h3', // accommodation listings
		'homepage_accommodation_amenities_title' . $suffix => 'h3', // accommodation features
		'homepage_tour_featured_title' . $suffix           => 'h3', // tour slider
		'homepage_tour_types_title' . $suffix              => 'h3', // tour types
		'homepage_tour_listings_title' . $suffix           => 'h3', // tour listings
		'homepage_tour_features_title' . $suffix           => 'h3', // tour features
		'homepage_rental_featured_title' . $suffix         => 'h3', // rental featured
		'homepage_rental_types_title' . $suffix            => 'h3', // rental types
		'homepage_rental_listings_title' . $suffix         => 'h3', // rental listings
		'homepage_rental_features_title' . $suffix         => 'h3', // rental features
	);

	$name .= $suffix;

	return sl_setting( $name ) ? sl_setting( $name ) : $default[$name];
}
