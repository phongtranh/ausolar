<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Slideshow_Edit
{
	/**
	 * @var string Post type: used for post type slug and some checks (prefix or suffix)
	 */
	public $post_type = 'slideshow';

	/**
	 * Constructor
	 *
	 * Add hooks
	 */
	function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );
		add_filter( 'get_media_item_args', array( $this, 'media_args' ) );
	}

	/**
	 * Enqueue scripts and styles for editing page
	 *
	 * @return void
	 */
	function enqueue_scripts()
	{
		$screen = get_current_screen();

		if ( 'post' !== $screen->base || $this->post_type !== $screen->post_type )
			return;

		wp_enqueue_media();
		wp_enqueue_script( 'sl-choose-image' );
		sl_enqueue_photo_script();
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function add()
	{
		add_meta_box( $this->post_type . '-images', __( 'Photos', '7listings' ), array( $this, 'render_photos' ), $this->post_type, 'advanced', 'high' );
		add_meta_box( $this->post_type . '-settings', __( 'Settings', '7listings' ), array( $this, 'render_settings' ), $this->post_type, 'advanced', 'high' );
		add_meta_box( $this->post_type . '-usage', __( 'Usage', '7listings' ), array( $this, 'render_usage' ), $this->post_type, 'side' );
	}

	/**
	 * Show meta box 'images'
	 *
	 * @return void
	 */
	function render_photos()
	{
		echo '<div class="photos">';
		include THEME_TABS . $this->post_type . '/photos.php';
		echo '</div>';
	}

	/**
	 * Show meta box 'settings'
	 *
	 * @return void
	 */
	function render_settings()
	{
		include THEME_TABS . $this->post_type . '/slideshow-settings.php';
	}

	/**
	 * Show meta box 'usage'
	 *
	 * @return void
	 */
	function render_usage()
	{
		_e( 'To use this slideshow in your posts or pages use the following shortcode:', '7listings' );
		echo '<br><code>[slideshow id="' . get_the_ID() . '"]</code>';
	}

	/**
	 * Save meta boxes
	 *
	 * @param $post_id
	 */
	function save( $post_id )
	{
		// Get proper post type. @link http://www.deluxeblogtips.com/forums/viewtopic.php?id=161
		$post_type = '';
		$post      = get_post( $post_id );

		if ( $post )
			$post_type = $post->post_type;
		elseif ( isset( $_POST['post_type'] ) && post_type_exists( $_POST['post_type'] ) )
			$post_type = $_POST['post_type'];

		if (
			defined( 'DOING_AJAX' )
			|| $post_type != $this->post_type
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
		)
		{
			return;
		}

		// Save photos
		$name   = sl_meta_key( 'photos', $this->post_type );
		$photos = empty( $_POST['post_photo_ids'] ) ? array() : $_POST['post_photo_ids'];
		$photos = array_unique( array_filter( $photos ) );
		foreach ( $photos as $id )
		{
			add_post_meta( $post_id, $name, $id, false );
		}

		$fields = array( 'loop', 'pagination', 'nextprev', 'fixed_height' );
		foreach ( $fields as $field )
		{
			$value = empty( $_POST[$field] ) ? 0 : 1;
			update_post_meta( $post_id, $field, $value );
		}

		$fields = array( 'animation', 'slideshow_speed', 'animation_speed', 'height', 'pagination_type' );
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, $_POST[$field] );
			else
				delete_post_meta( $post_id, $field );
		}
	}

	/**
	 * For media upload popup to show "Insert Into Post" button
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function media_args( $args )
	{
		$args['send'] = true;

		return $args;
	}
}

new Sl_Slideshow_Edit;
