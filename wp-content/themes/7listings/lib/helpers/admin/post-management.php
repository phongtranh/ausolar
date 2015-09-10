<?php
if ( ! class_exists( 'Peace_Post_Management' ) ):

	/**
	 * This class controls which columns and their content are displayed in post management page
	 */
	class Peace_Post_Management
	{
		/**
		 * Post type
		 *
		 * @var string
		 */
		public $post_type = 'post';

		/**
		 * Constructor
		 *
		 * Load hooks on 'edit.php' page
		 */
		function __construct()
		{
			add_action( 'load-edit.php', array( $this, 'execute' ) );
		}

		/**
		 * Call this method in subclass to start
		 *
		 * @return void
		 */
		function execute()
		{
			if ( ! $this->check() )
				return;

			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'show' ), 10, 2 );
			add_filter( "manage_edit-{$this->post_type}_sortable_columns", array( $this, 'sortable_columns' ) );

			add_action( 'restrict_manage_posts', array( $this, 'show_filters' ) );
			add_filter( 'parse_query', array( $this, 'filter' ) );

			// Special method to sort by taxonomy and custom fields
			if ( method_exists( $this, 'posts_clauses' ) )
				add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 10, 2 );

			// Special method to sort by taxonomy and custom fields
			if ( method_exists( $this, 'row_actions' ) )
				add_action( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );

			// Special method to sort by taxonomy and custom fields
			if ( method_exists( $this, 'enqueue' ) )
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

			// Custom hooks
			if ( method_exists( $this, 'hooks' ) )
				$this->hooks();
		}

		/**
		 * Check if we in right page in admin area
		 * Use a separated function allow child class to rewrite the conditions
		 *
		 * @return bool
		 */
		function check()
		{
			if ( ! is_admin() )
				return false;

			$screen = get_current_screen();

			return ( 'edit' == $screen->base && $this->post_type == $screen->post_type );
		}

		/**
		 * Get list of columns
		 *
		 * @param array $columns Default WordPress columns
		 *
		 * @return array
		 */
		function columns( $columns )
		{
			return $columns;
		}

		/**
		 * Show column content
		 * Must be defined in subclass
		 *
		 * @param string $column  Column ID
		 * @param int    $post_id Post ID
		 */
		function show( $column, $post_id )
		{
		}

		/**
		 * Make columns sortable
		 *
		 * @param array $columns
		 *
		 * @return array
		 */
		function sortable_columns( $columns )
		{
			return $columns;
		}

		/**
		 * Show dropdown filters of taxonomies
		 *
		 * @see taxonomy_filters() function
		 *
		 * @return void
		 */
		function show_filters()
		{
			$tax_slugs = get_object_taxonomies( $this->post_type );

			foreach ( $tax_slugs as $tax_slug )
			{
				$tax_obj = get_taxonomy( $tax_slug );

				wp_dropdown_categories( array(
					'show_option_all' => sprintf( __( 'All %s', 'peace' ), $tax_obj->label ),
					'taxonomy'        => $tax_slug,
					'name'            => $tax_obj->name,
					'orderby'         => 'name',
					'selected'        => isset( $_GET[$tax_obj->query_var] ) ? $_GET[$tax_obj->query_var] : '',
					'hierarchical'    => $tax_obj->hierarchical,
					'show_count'      => true,
					'hide_empty'      => false,
				) );
			}
		}

		/**
		 * Change the query to make filters work
		 *
		 * @see taxonomy_filters() function
		 *
		 * @param object $query
		 *
		 * @return object
		 */
		function filter( $query )
		{
			$tax_slugs = get_object_taxonomies( $this->post_type );
			foreach ( $tax_slugs as $tax_slug )
			{
				if ( empty( $query->query_vars[$tax_slug] ) )
					continue;

				$term                         = get_term_by( 'id', $query->query_vars[$tax_slug], $tax_slug );
				$query->query_vars[$tax_slug] = $term->slug;
			}

			return $query;
		}
	}

endif;
