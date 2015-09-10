<?php
if ( !class_exists( 'Peace' ) ):

	/**
	 * Framework loader
	 */
	class Peace
	{
		/**
		 * Constructor
		 * Load files in 'after_setup_theme' to allow theme adds supports
		 *
		 * @return Peace
		 */
		function __construct()
		{
			$this->constants();
			$this->helpers();

			add_action( 'after_setup_theme', array( $this, 'functions' ), 20 );

			if ( !is_admin() )
				add_action( 'after_setup_theme', array( $this, 'frontend' ), 30 );

			add_action( 'after_setup_theme', array( $this, 'extensions' ), 50 );
		}

		/**
		 * Define framework constants
		 *
		 * @return void
		 */
		function constants()
		{
			define( 'HOME_URL', trailingslashit( home_url() ) );

			define( 'THEME_DIR', trailingslashit( get_template_directory() ) );
			define( 'THEME_URL', trailingslashit( get_template_directory_uri() ) );

			define( 'CHILD_DIR', trailingslashit( get_stylesheet_directory() ) );
			define( 'CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );

			define( 'PEACE_DIR', trailingslashit( THEME_DIR . basename( dirname( __FILE__ ) ) ) );
			define( 'PEACE_URL', trailingslashit( THEME_URL . basename( dirname( __FILE__ ) ) ) );
		}

		/**
		 * Load helper functions
		 *
		 * @return void
		 */
		function helpers()
		{
			$dir = trailingslashit( PEACE_DIR . 'helpers' );

			// Helper classes / functions in general
			require_once $dir . 'include.php';
			require_once $dir . 'upload.php';

			// Helper classes / functions for admin area
			if ( is_admin() )
				require_once $dir . 'admin/post-management.php';
		}

		/**
		 * Load theme functions
		 *
		 * @return void
		 */
		function functions()
		{
			require_once PEACE_DIR . 'functions/core.php';
		}

		/**
		 * Load frontend files
		 *
		 * @return void
		 */
		function frontend()
		{
			$dir = trailingslashit( PEACE_DIR . 'frontend' );

			require_once $dir . 'bootstrap-menu.php';
			require_once $dir . 'pagination.php';
		}

		/**
		 * Load extensions
		 *
		 * @return void
		 */
		function extensions()
		{
			$dir = trailingslashit( PEACE_DIR . 'extensions' );
			require_if_theme_supports( 'peace-entry-views', $dir . 'entry-views.php' );
		}
	}

endif;
