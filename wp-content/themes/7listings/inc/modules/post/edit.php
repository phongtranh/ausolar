<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Post_Edit
{
	/**
	 * Post type: used for post type slug and some checks (prefix or suffix)
	 *
	 * @var string
	 */
	public $post_type = 'post';

	/**
	 * Constructor
	 *
	 * Add hooks
	 */
	function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function add()
	{
		if ( sl_setting( 'sidebars' ) )
			add_meta_box( 'sidebar', __( 'Sidebar', '7listings' ), array( $this, 'sidebar' ), $this->post_type, 'side' );
	}

	/**
	 * Show meta box
	 *
	 * @return void
	 */
	function sidebar()
	{
		$type = $this->post_type;
		wp_nonce_field( "save-post-{$type}", "sl_nonce_save_{$type}" );
		include THEME_TABS . 'page/sidebar.php';
	}

	/**
	 * Save meta boxes
	 *
	 * @param $post_id
	 */
	function save( $post_id )
	{
		$type = $this->post_type;

		if (
			empty( $_POST["sl_nonce_save_{$type}"] )
			|| ! wp_verify_nonce( $_POST["sl_nonce_save_{$type}"], "save-post-{$type}" )
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
		)
		{
			return;
		}

		$fields = array(
			'layout',
			'custom_sidebar',
		);
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, $_POST[$field] );
			else
				delete_post_meta( $post_id, $field );
		}
	}
}

new Sl_Post_Edit;
