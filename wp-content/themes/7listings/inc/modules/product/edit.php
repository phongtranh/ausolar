<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Product_Edit extends SL_Core_Edit
{
	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function add()
	{
		add_meta_box( $this->post_type . '-meta-box', __( 'Product Photos', '7listings' ), array( $this, 'render' ), $this->post_type, 'advanced', 'high' );
	}

	/**
	 * Show meta box
	 *
	 * @return void
	 */
	function render()
	{
		wp_nonce_field( "save-post-{$this->post_type}", "sl_nonce_save_{$this->post_type}" );

		echo '<div class="tabs"><ul>';

		echo '
			<li>' . __( 'Media', '7listings' ) . '</li>
		';

		do_action( 'sl_edit_tab' );
		do_action( $this->post_type . '_edit_tab' );

		echo '</ul>';

		// Load tab content
		$tabs = array( 'photos' );
		foreach ( $tabs as $tab )
		{
			echo '<div id="' . $this->post_type . '-' . $tab . '" class="' . $tab . '">';
			locate_template( array(
				"inc/admin/tabs/{$this->post_type}/$tab.php",
				"inc/admin/tabs/parts/$tab.php",
			), true );
			echo '</div>';
		}

		do_action( 'sl_edit_tab_content' );
		do_action( $this->post_type . '_edit_tab_content' );

		echo '</div>';
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

		// Save photos
		$name   = sl_meta_key( 'photos', $type );
		$photos = empty( $_POST['post_photo_ids'] ) ? array() : $_POST['post_photo_ids'];

		// WooCommerce Gallery
		if ( isset( $_POST['product_image_gallery'] ) )
		{
			$attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
			foreach ( $attachment_ids as $id )
			{
				$photos[] = $id;
			}
		}

		// Get featured image
		if ( $featured = get_post_thumbnail_id( $post_id ) )
			$photos[] = $featured;

		$photos = array_unique( array_filter( $photos ) );

		// Update to our database
		$old    = get_post_meta( $post_id, $name, false );
		$values = $old;
		foreach ( $photos as $id )
		{
			if ( ! in_array( $id, $old ) )
			{
				$values[] = $id;
				add_post_meta( $post_id, $name, $id, false );
			}
		}

		// Update to WooCommerce Gallery
		$values = array_unique( array_filter( $values ) );
		update_post_meta( $post_id, '_product_image_gallery', implode( ',', $values ) );

		// Save movies
		$name = sl_meta_key( 'movies', $type );
		// If Youtube URL is entered
		if ( ! empty( $_POST['movie_url'] ) )
		{
			update_post_meta( $post_id, $name, $_POST['movie_url'] );
		}

		// If Youtube URL is edited
		elseif ( ! empty( $_POST['movie'] ) )
		{
			update_post_meta( $post_id, $name, $_POST['movie'] );
		}

		// If user uploaded movie
		elseif ( $movie = peace_handle_upload( 'movie', false, $post_id ) )
		{
			update_post_meta( $post_id, $name, $movie );
		}
	}
}

new Sl_Product_Edit( 'product' );
