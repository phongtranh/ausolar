<?php

class Sl_Company_Admin
{
	/**
	 * Class constructor
	 *
	 * @return Sl_Company_Admin
	 */
	function __construct()
	{
		add_action( 'admin_init', array( $this, 'redirect' ) );
		add_action( 'admin_footer', array( $this, 'show_new_companies' ) );
	}

	/**
	 * Redirect company owner to dashboard when trying to access admin area
	 * Note need to allow ajax requests
	 *
	 * @return void
	 * @since  4.12
	 */
	function redirect()
	{
		/**
		 * Allow ajax requests
		 * @since 5.0.1
		 */
		if ( ! current_user_can( 'company_owner' ) || current_user_can( 'administrator' ) || defined( 'DOING_AJAX' ) )
			return;

		$dashboard = sl_setting( 'company_page_dashboard' );
		$url       = $dashboard ? get_permalink( $dashboard ) : HOME_URL;
		wp_redirect( $url );
	}

	/**
	 * Show number (number badge) of new companies in the sidebar, like number of new comments
	 *
	 * @return void
	 */
	function show_new_companies()
	{
		$companies = get_posts( array(
			'post_type'      => 'company',
			'fields'         => 'ids',
			'post_status'    => 'pending',
			'posts_per_page' => - 1,
		) );
		$num       = count( $companies );
		echo '
		<script>
		jQuery( function( $ )
		{
			$( "#menu-posts-company .wp-menu-name" ).append( " <span class=\'awaiting-mod count-' . $num . '\'><span class=\'pending-count\'>' . $num . '</span></span>" );
		} );
		</script>
		';
	}
}

new Sl_Company_Admin;
