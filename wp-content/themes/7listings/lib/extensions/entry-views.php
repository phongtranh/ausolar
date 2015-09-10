<?php
/**
 * Entry Views - A WordPress script for counting post views.
 * Based on Entry Views scripts of Hybrid Core framework by Justin Tadlock
 */
if ( !class_exists( 'Peace_Entry_Views' ) ) :

	class Peace_Entry_Views
	{
		/**
		 * Meta key for saving entry views
		 * Default is 'views', and can be change by filter
		 *
		 * @var string
		 */
		public $key;

		/**
		 * Class constructor
		 * Add 'entry-views' support for 'post' and hooks
		 *
		 * @return Peace_Entry_Views
		 */
		function __construct()
		{
			$this->key = apply_filters( 'peace_entry_views_key', 'views' );

			// Add post type support for 'entry-views'
			add_action( 'init', array( $this, 'support' ) );

			// Load JavaScript in the footer on singular pages that supports 'entry-views'
			add_action( 'template_redirect', array( $this, 'load' ) );

			// Updates the entry views
			add_action( 'wp_ajax_peace_entry_views', array( $this, 'update' ) );
			add_action( 'wp_ajax_nopriv_peace_entry_views', array( $this, 'update' ) );
		}

		/**
		 * Add 'entry-views' support for 'post'
		 *
		 * @return void
		 */
		function support()
		{
			add_post_type_support( 'post', array( 'entry-views' ) );
		}

		/**
		 * Load JavaScript in the footer on singular pages that supports 'entry-views'
		 *
		 * @return void
		 */
		function load()
		{
			global $_entry_views_post_id;

			// Check on singular posts and support of 'entry-views'
			if ( !is_singular() || !post_type_supports( get_post_type(), 'entry-views' ) )
				return;

			// Set the post ID for later use because we wouldn't want a custom query to change this
			$_entry_views_post_id = get_queried_object_id();

			wp_enqueue_script( 'jquery' );

			// Load the JavaScript in the footer
			add_action( 'wp_footer', array( $this, 'script' ), 1000 );
		}

		/**
		 * Displays a small script that sends an AJAX request to update entry views
		 *
		 * @return void
		 */
		function script()
		{
			global $_entry_views_post_id;

			$nonce = wp_create_nonce( 'peace-update-entry-views' );

			// Display the JavaScript needed
			echo '<script>jQuery(document).ready(function($){$.post("' . admin_url( 'admin-ajax.php' ) . '",{action:"peace_entry_views",_ajax_nonce:"' . $nonce . '",post_id: ' . $_entry_views_post_id . ' })})</script>';
		}

		/**
		 * Callback function for updating entry views
		 *
		 * @return void
		 */
		function update()
		{
			check_ajax_referer( 'peace-update-entry-views' );

			if ( isset( $_POST['post_id'] ) )
				$post_id = (int) $_POST['post_id'];

			if ( empty( $post_id ) )
				exit;

			$views = get_post_meta( $post_id, $this->key, true );
			$views = absint( $views ) + 1;

			update_post_meta( $post_id, $this->key, $views );
			exit;
		}
	}

	new Peace_Entry_Views;

endif;
