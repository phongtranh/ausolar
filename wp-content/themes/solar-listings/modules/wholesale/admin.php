<?php

class Sl_Wholesale_Admin
{
    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'redirect' ) );
        add_action( 'admin_footer', array( $this, 'show_new_wholesales' ) );
    }

    public function redirect()
    {
        if ( !current_user_can( 'wholesale_owner' ) || current_user_can( 'administrator' ) || defined( 'DOING_AJAX' ) )
            return;

        $dashboard = sl_setting( 'wholesale_page_dashboard' );
        $url = $dashboard ? get_permalink( $dashboard ) : HOME_URL;
        wp_redirect( $url );
    }

    public function show_new_wholesales()
    {
        $wholesales = get_posts( array(
            'post_type'      => 'wholesale',
            'fields'         => 'ids',
            'post_status'    => 'pending',
            'posts_per_page' => -1,
        ) );
        $num = count( $wholesales );
        echo '
		<script>
		jQuery( function( $ )
		{
			$( "#menu-posts-wholesale .wp-menu-name" ).append( " <span class=\'awaiting-mod count-' . $num . '\'><span class=\'pending-count\'>' . $num . '</span></span>" );
		} );
		</script>
		';
    }
}

new Sl_Wholesale_Admin;