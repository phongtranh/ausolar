<?php
/**
 * Entry Views - A WordPress script for counting post views.
 * Based on Entry Views scripts of Hybrid Core framework by Justin Tadlock
 */
if ( ! class_exists( 'Sl_Company_Views' ) ) :

	class Sl_Company_Views
	{
		/**
		 * Meta key for saving entry views
		 *
		 * @var string
		 */
		public $key;

		/**
		 * Class constructor
		 *
		 * @return Sl_Company_Views
		 */
		function __construct()
		{
			$this->key = 'date_views';

			// Load JavaScript in the footer on singular pages that supports 'entry-views'
			add_action( 'template_redirect', array( $this, 'load' ) );

			// Updates the entry views
			add_action( 'wp_ajax_sl_company_views', array( $this, 'update' ) );
			add_action( 'wp_ajax_nopriv_sl_company_views', array( $this, 'update' ) );
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
			if ( ! is_singular( 'company' ) )
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

			$nonce = wp_create_nonce( 'update-company-date-views' );

			// Display the JavaScript needed
			echo '<script async>jQuery(document).ready(function($){$.post("' . admin_url( 'admin-ajax.php' ) . '",{action:"sl_company_views",_ajax_nonce:"' . $nonce . '",post_id: ' . $_entry_views_post_id . ' })})</script>';
		}

		/**
		 * Callback function for updating entry views
		 *
		 * @return void
		 */
		function update()
		{
			check_ajax_referer( 'update-company-date-views' );

			if ( isset( $_POST['post_id'] ) )
				$post_id = (int) $_POST['post_id'];

			if ( empty( $post_id ) )
				exit;

			$views = get_post_meta( $post_id, $this->key, true );
			if ( empty( $views ) || ! is_array( $views ) )
				$views = array();

			$today       = 'views_' . date( 'Y_m_d' );
			$today_views = isset( $views[$today] ) ? intval( $views[$today] ) : 0;
			$today_views ++;
			$views[$today] = $today_views;

			update_post_meta( $post_id, $this->key, $views );
			die;
		}
	}

	new Sl_Company_Views;

endif;
