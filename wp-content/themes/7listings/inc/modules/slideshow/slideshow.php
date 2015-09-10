<?php

/**
 * This class registers new listing post type and load files needed for this module
 */
class Sl_Slideshow
{
	/**
	 * Post type: used for post type slug and some checks (prefix or suffix)
	 *
	 * @var string
	 */
	static $post_type = 'slideshow';

	/**
	 * Constructor
	 * Add hooks
	 */
	function __construct()
	{
		$this->load_files();

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Load files
	 *
	 * @return void
	 */
	function load_files()
	{
		$type = self::$post_type;
		$dir  = THEME_MODULES . $type;
		if ( ! is_admin() )
		{
			require "$dir/frontend.php";
		}
		elseif ( ! defined( 'DOING_AJAX' ) && ! is_network_admin() )
		{
			require "$dir/edit.php";
			require "$dir/management.php";
		}

		// Widgets
		require THEME_INC . "widgets/$type.php";
	}

	/**
	 * Register custom post type
	 * Use peace framework to do quickly
	 *
	 * @return void
	 */
	function register_post_type()
	{
		$labels = array(
			'name'               => _x( 'Slideshows', 'Post Type General Name', '7listings' ),
			'singular_name'      => _x( 'Slideshow', 'Post Type Singular Name', '7listings' ),
			'menu_name'          => __( 'Slideshows', '7listings' ),
			'parent_item_colon'  => __( 'Parent Slideshow:', '7listings' ),
			'all_items'          => __( 'All Slideshows', '7listings' ),
			'view_item'          => __( 'View Slideshow', '7listings' ),
			'add_new_item'       => __( 'Add New Slideshow', '7listings' ),
			'add_new'            => __( 'New Slideshow', '7listings' ),
			'edit_item'          => __( 'Edit Slideshow', '7listings' ),
			'update_item'        => __( 'Update Slideshow', '7listings' ),
			'search_items'       => __( 'Search slideshows', '7listings' ),
			'not_found'          => __( 'No slideshows found', '7listings' ),
			'not_found_in_trash' => __( 'No slideshows found in Trash', '7listings' ),
		);
		$args   = array(
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'rewrite'             => false,
			'query_var'           => false,
		);
		register_post_type( 'slideshow', $args );
	}

	/**
	 * Display post updated messages
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	function post_updated_messages( $messages )
	{
		global $post, $post_ID;
		$messages[self::$post_type] = array(
			0  => '',
			1  => sprintf( __( 'Slideshow updated. <a href="%s">View slideshow</a>', '7listings' ), get_permalink( $post_ID ) ),
			2  => __( 'Custom field updated.', '7listings' ),
			3  => __( 'Custom field deleted.', '7listings' ),
			4  => __( 'Slideshow updated.', '7listings' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Slideshow restored to revision from %s', '7listings' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Slideshow published. <a href="%s">View slideshow</a>', '7listings' ), get_permalink( $post_ID ) ),
			7  => __( 'Slideshow saved.', '7listings' ),
			8  => sprintf( __( 'Slideshow submitted. <a target="_blank" href="%s">Preview slideshow</a>', '7listings' ), add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
			9  => sprintf( __( 'Slideshow scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview slideshow</a>', '7listings' ), date_i18n( __( 'M j, Y @ G:i', '7listings' ), strtotime( $post->post_date ) ), get_permalink( $post_ID ) ),
			10 => sprintf( __( 'Slideshow draft updated. <a target="_blank" href="%s">Preview slideshow</a>', '7listings' ), add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ),
		);

		return $messages;
	}

	/**
	 * Register widgets
	 *
	 * @return void
	 */
	function register_widgets()
	{
		register_widget( 'Sl_Widget_Slideshow' );
	}
}

new Sl_Slideshow;
