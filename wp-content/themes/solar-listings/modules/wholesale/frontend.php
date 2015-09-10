<?php

class Solar_Wholesale_Frontend extends Sl_Core_Frontend
{
    private $special_pages;

    public function __construct( $post_type )
    {
        parent::__construct( $post_type );

        $this->special_pages = array(
            'sign-up'  => 'signup'
        );

        add_filter( 'the_content', array( $this, 'special_pages_content' ) );
        add_filter( 'show_admin_bar', array( $this, 'no_admin_bar' ) );

        if ( ! empty( $_POST['submit_login'] ) && ! is_user_logged_in() )
        {
            $this->process_login();
        }
    }

    function no_admin_bar( $show )
    {
        if ( !current_user_can( 'publish_posts' ) )
            $show = false;
        return $show;
    }

    public function special_pages_content( $content )
    {

        global $post;

        $page = get_page_by_path( 'affiliates' );

        if ( $post->post_name === 'sign-up' )
            $page = get_page_by_path( "affiliates/{$post->post_name}", OBJECT );

        if ( empty ( $page ) || strpos( $_SERVER['REQUEST_URI'], '/affiliates/' ) === false )
            return $content;

        if ( is_page( 'sign-up' ) )
        {
            ob_start();

            do_action( 'wholesale_special_pages_content_before' );
            //do_action( 'sl_notification', 'all' );
            get_template_part( "templates/wholesale/signup" );
            $content .= ob_get_clean();

            do_action( 'wholesale_special_pages_content_after' );

        }
        else
        {
            if ( ! current_user_can( 'wholesale_owner' ) && $page->post_parent == 17826 )
            {
                wp_redirect( '/', 301 );
                exit;
            }
        }

        return $content;
    }

    public function enqueue_scripts()
    {
        if ( is_page( 'sign-up' ) )
        {
            wp_enqueue_style( 'jquery-ui' );

            wp_enqueue_script( "sl-wholesale-signup", sl_locate_url( "js/wholesale-signup.js" ),
                array( ), '', true );

            wp_localize_script( "sl-wholesale-signup", 'SolarWholesale', array(
                'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'signup' ),
                'nonceSave' => wp_create_nonce( "save-post-wholesale" ),
            ) );

            wp_enqueue_script( 'sl-highlight', sl_locate_url( 'js/highlight.js' ), array( 'jquery' ), '', true );
        }

        if ( is_page( 'affiliates' ) )
        {
            asq_admin_enqueue_scripts();

	        wp_register_script( 'google', 'https://www.google.com/jsapi', '', '', true );

	        wp_enqueue_script( 'solar-wholesale-report', CHILD_URL . 'js/wholesale-leads-report.js', array( 'google',
		        'jquery' ), '', true );
        }
    }

    /**
     * Process login
     *
     * @return string
     * @since  4.12
     */
    function process_login()
    {
        if ( empty( $_POST['submit_login'] ) )
            return;

        if ( empty( $_POST['_wpnonce'] ) || !wp_verify_nonce( $_POST['_wpnonce'], 'login' ) )
            die( __( 'Form is not properly submitted. Please try again!', '7listings' ) );

        global $errors;
        if ( !is_array( $errors ) )
            $errors = array();

        if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) )
        {
            $errors[] = __( 'Please enter username and password', '7listings' );
            return;
        }

        $user = wp_signon( array(
            'user_login'    => $_POST['username'],
            'user_password' => $_POST['password'],
            'remember'      => true,
        ), false );

        if ( is_wp_error( $user ) )
        {
            $errors[] = __( 'Username and password do not match. Please try again.', '7listings' );
            return;
        }

        wp_redirect( add_query_arg( 'logged-in', 1 ) );
        exit;
    }
}

new Solar_Wholesale_Frontend( 'wholesale' );