<?php

/**
 * This class will hold all things for edit page
 */
abstract class Sl_Core_Edit
{
	/**
	 * Post type: used for post type slug and some checks (prefix or suffix)
	 *
	 * @var string
	 */
	public $post_type;

	/**
	 * Class constructor
	 *
	 * @param string $post_type Post type
	 */
	public function __construct( $post_type )
	{
		$this->post_type = $post_type;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add' ) );
		add_action( 'save_post', array( $this, 'save' ) );

		// Add help tab
		add_action( 'load-post.php', array( $this, 'help' ) );
		add_action( 'load-post-new.php', array( $this, 'help' ) );

		/**
		 * Don't validate form when submit
		 * This makes sure type="url" works when entered URL like mywebsite.com in the input
		 */
		add_action( 'post_edit_form_tag', array( $this, 'no_form_validate' ) );
	}

	/**
	 * Enqueue scripts and styles for editing page
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{
		$screen = get_current_screen();

		if ( 'post' !== $screen->base || $this->post_type !== $screen->post_type )
			return;

		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_style( 'jquery-ui-timepicker' );

		sl_enqueue_photo_script();

		wp_enqueue_script( "sl-{$this->post_type}", sl_locate_url( "js/admin/{$this->post_type}.js" ), array( 'sl-meta-box', 'sl-choose-image', 'sl-location-autocomplete', 'jquery-ui-timepicker' ) );
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	public function add()
	{
		add_meta_box( $this->post_type . '-meta-box', ucwords( $this->post_type ), array( $this, 'render' ), $this->post_type, 'advanced', 'high' );
		add_meta_box( 'listing-owner', __( 'Owner', '7listings' ), array( $this, 'box_owner' ), $this->post_type, 'side' );
		remove_meta_box( 'locationdiv', $this->post_type, 'side' );
	}

	/**
	 * Show meta box
	 *
	 * @return void
	 */
	public function render()
	{
		wp_nonce_field( "save-post-{$this->post_type}", "sl_nonce_save_{$this->post_type}" );

		echo '<div class="tabs"><ul>';

		echo '
			<li>' . __( 'Media', '7listings' ) . '</li>
			<li>' . __( 'Booking Resources', '7listings' ) . '</li>
			<li>' . __( 'Information', '7listings' ) . '</li>
			<li>' . __( 'Policies', '7listings' ) . '</li>
		';
		if ( 'attraction' != $this->post_type )
		{
			echo '<li>' . __( 'Scheduling', '7listings' ) . '</li>';
		}

		do_action( 'sl_edit_tab' );
		do_action( $this->post_type . '_edit_tab' );

		echo '</ul>';

		// Load tab content
		$tabs = array( 'photos', 'booking', 'info', 'policies' );
		if ( 'attraction' != $this->post_type )
			$tabs[] = 'scheduling';

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
	 * Show meta box for listing owner
	 *
	 * @return void
	 */
	public function box_owner()
	{
		locate_template( array(
			"inc/admin/tabs/{$this->post_type}/owner.php",
			'inc/admin/tabs/parts/owner.php',
		), true );
	}

	/**
	 * Save custom fields for posts
	 * Update custom fields when they have values and delete them when possible to save database space.
	 *
	 * Note:
	 * - Edit listing can be done in the frontend (like company) where the edit page may not contains all fields
	 *   So using delete_post_meta() only when that field presents
	 * - If unchecked, checkboxes don't present in $_POST
	 *
	 * @param int $post_id Post ID
	 *
	 * @return void
	 */
	public function save( $post_id )
	{
		$type = $this->post_type;

		if (
			empty( $_POST["sl_nonce_save_{$type}"] )
			|| ! wp_verify_nonce( $_POST["sl_nonce_save_$type"], "save-post-$type" )
			|| defined( 'DOING_AUTOSAVE' )
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
		)
		{
			return;
		}

		do_action( 'sl_save_post_before', $post_id, $type );
		do_action( "{$this->post_type}_save_post_before", $post_id, $type );

		// Simple fields
		$fields = array(
			sl_meta_key( 'logo', $type ),
			'address', 'address2', 'postcode', // Information tab
			'latitude', 'longitude',
			'email', 'phone', 'tripadvisor', 'paypal_email',
			'paymentpol', 'cancellpol', 'terms', 'booking_message', // Policies tab
			'season', 'schedule', 'allocations', // Scheduling tab
			'user', // Owner meta box
		);
		foreach ( $fields as $field )
		{
			/**
			 * Ignore field to save previous data if field does not present
			 * In case of editing in the frontend (like company), the edit page contains only some specific fields
			 */
			if ( ! isset( $_POST[$field] ) )
				continue;

			if ( $_POST[$field] )
				update_post_meta( $post_id, $field, $_POST[$field] );
			else
				delete_post_meta( $post_id, $field );
		}

		// Sanitize website address, make sure it's a correct URL
		if ( isset( $_POST['website'] ) )
		{
			if ( $_POST['website'] )
				update_post_meta( $post_id, 'website', esc_url_raw( $_POST['website'] ) );
			else
				delete_post_meta( $post_id, 'website' );
		}

		/**
		 * Locations:
		 * - City and area/suburb: data is sent as term names and they save term IDs in post meta
		 * - State: data is send as term ID and it saves term IDs in post meta
		 *
		 * After saving in post meta, they're also set as post terms
		 */
		$fields   = array( 'city', 'area', 'state' );
		$term_ids = array();
		foreach ( $fields as $field )
		{
			/**
			 * Ignore field to save previous data if field does not present
			 * In case of editing in the frontend (like company), the edit page contains only some specific fields
			 */
			if ( ! isset( $_POST[$field] ) )
				continue;

			if ( ! $_POST[$field] )
			{
				delete_post_meta( $post_id, $field );
				continue;
			}

			$value = $_POST[$field];

			/**
			 * Get location term to get its ID (for city and area) and check for state
			 * Note: state is passed as ID, we get term just for checking its existence
			 */
			$term = 'state' == $field ? get_term( $value, 'location' ) : get_term_by( 'name', $value, 'location' );

			if ( ! empty( $term ) && ! is_wp_error( $term ) )
			{
				update_post_meta( $post_id, $field, $term->term_id );
				$term_ids[] = $term->term_id;
			}
		}
		wp_set_post_terms( $post_id, $term_ids, 'location', false ); // Save and replace all current taxonomies

		// Checkboxes
		$fields = array( 'location_marker' );
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, 1 );
			else
				delete_post_meta( $post_id, $field );
		}

		// Save photos
		$name   = sl_meta_key( 'photos', $type );
		$photos = empty( $_POST['post_photo_ids'] ) ? array() : $_POST['post_photo_ids'];
		$old    = get_post_meta( $post_id, $name, false );
		$photos = array_merge( $old, $photos );

		$photos = array_unique( array_filter( $photos ) );
		delete_post_meta( $post_id, $name );
		foreach ( $photos as $id )
		{
			add_post_meta( $post_id, $name, $id, false );
		}

		// Save movies
		$name = sl_meta_key( 'movies', $type );
		// If Youtube URL is entered
		if ( ! empty( $_POST[$name] ) )
		{
			update_post_meta( $post_id, $name, $_POST[$name] );
		}
		// If user uploaded movie
		else
		{
			$allowed_file_types = array_keys( get_allowed_mime_types() );
			$extensions         = explode( ',', str_replace( '|', ',', implode( ',', $allowed_file_types ) ) );
			$movie              = peace_handle_upload( $name, array(
				'extensions' => $extensions,
			) );
			if ( $movie )
			{
				update_post_meta( $post_id, $name, $movie );
			}
			else
			{
				delete_post_meta( $post_id, $name );
			}
		}

		$this->save_post( $post_id );

		do_action( 'sl_save_post_after', $post_id, $type );
		do_action( "{$this->post_type}_save_post_after", $post_id, $type );
	}

	/**
	 * Additional save post actions
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	public function save_post( $post_id )
	{
	}

	/**
	 * Add help tab
	 *
	 * @return void
	 */
	public function help()
	{
		$screen = get_current_screen();
		if ( $this->post_type != $screen->post_type )
			return;
		sl_add_help_tabs( 'edit-' . $this->post_type );
	}

	/**
	 * Don't validate form when submit
	 * This makes sure type="url" works when entered URL like mywebsite.com in the input
	 *
	 * @return void
	 */
	public function no_form_validate()
	{
		echo ' novalidate';
	}
}
