<?php
/**
 * Call back function to display pages in the menu
 * Mimic the wp_page_menu() function but adds more check and auto-generated menu items
 *
 * @see wp_page_menu()
 *
 * @param array $args
 *
 * @return void
 */
function sl_page_menu( $args = array() )
{
	$menu = '';

	// Show Home in the menu on all pages except homepage
	if ( ! is_front_page() )
	{
		$home_label = 'slide-nav' == $args['menu_class'] ? __( 'Homepage', '7listings' ) : __( 'Home', '7listings' );
		$menu .= '<li><a href="' . HOME_URL . '">' . $home_label . '</a></li>';
	}

	// Add listing menu items
	$menu .= sl_listing_menu_items();

	// Exclude page template thank-you-booking.php
	global $wpdb;
	$page_template = 'templates/thank-you-booking.php';
	$exclude       = $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = '$page_template'" );

	// If the front page is a page, add it to the exclude list
	if ( get_option( 'show_on_front' ) == 'page' )
		$exclude[] = get_option( 'page_on_front' );

	$list_args = array(
		'echo'     => false,
		'title_li' => '',
		'exclude'  => implode( ',', $exclude ),
		'walker'   => new FitWP_Bootstrap_Page_Walker,
	);

	$menu .= wp_list_pages( $list_args );
	printf( $args['items_wrap'], esc_attr( $args['menu_id'] ), esc_attr( $args['menu_class'] ), $menu );
}

/**
 * Hard coded adding menu items for listings to menu
 *
 * @return string
 */
function sl_listing_menu_items()
{
	$items = '';
	foreach ( sl_setting( 'listing_types' ) as $post_type )
	{
		// Ignore product and those post types which don't have menu title settings
		if ( 'product' == $post_type || ! post_type_exists( $post_type ) || ! sl_setting( "{$post_type}_menu_title" ) )
			continue;

		// Fix ugly post type archive link
		$link = get_post_type_archive_link( $post_type );
		if ( strpos( $link, '?' ) )
			$link = home_url( sl_setting( $post_type . '_base_url' ) . '/' );

		$li_class  = is_post_type_archive() && $post_type == get_post_type() ? 'active' : '';
		$link_atts = '';
		$ul        = apply_filters( "sl_menu_dropdown_$post_type", '' );
		if ( $ul )
		{
			$li_class .= $li_class ? ' dropdown' : 'dropdown';
			$link_atts = ' class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
		}
		$items .= "<li class='$li_class'><a href='$link'$link_atts>" . sl_setting( "{$post_type}_menu_title" ) . '</a>';
		$items .= $ul;
		$items .= '</li>';
	}

	return $items;
}
