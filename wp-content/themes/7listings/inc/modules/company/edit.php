<?php

/**
 * This class will hold all things for edit page
 */
class Sl_Company_Edit extends Sl_Core_Edit
{
	/**
	 * Enqueue scripts and styles for editing page
	 *
	 * @return void
	 */
	function enqueue_scripts()
	{
		$screen = get_current_screen();
		
		wp_enqueue_script( 'sl-meta-box' );
		wp_enqueue_script( 'sl-choose-image' );
		sl_enqueue_photo_script();

		if ( 'post' == $screen->base && $this->post_type == $screen->post_type )
		{
			wp_enqueue_script( 'sl-utils' );
			wp_enqueue_script( 'sl-location-autocomplete' );
		}

		if ( 'post' == $screen->base || 'post' == $screen->post_type )
		{
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'sl-company-post', sl_locate_url( 'js/admin/company-post.js' ), array( 'jquery', 'select2' ), '', true );
		}
	}

	/**
	 * Add meta boxes
	 *
	 * @return void
	 */
	function add()
	{
		parent::add();

		add_meta_box( 'sl-post-company', __( 'Company', '7listings' ), array( $this, 'box_company' ), 'post', 'side' );
	}

	/**
	 * Show meta box for link post to company
	 *
	 * @return void
	 */
	function box_company()
	{
		wp_nonce_field( 'save-post-company', 'sl_nonce_save_post_company' );
		
		global $wpdb;

		$query = "SELECT ID, post_title
					FROM {$wpdb->posts}
					WHERE post_type = 'company'
					AND post_status = 'publish'
					ORDER BY post_title ASC";

		$results = $wpdb->get_results( $query );

		$companies = array();
		foreach ( $results as $company )
		{
			$companies[$company->ID] = $company->post_title;
		}
		
		echo '<select name="company" data-placeholder="' . __( 'Select a company', '7listings' ) . '">';
		echo '<option></option>';
		Sl_Form::options( get_post_meta( get_the_ID(), 'company', true ), $companies );
		echo '</select>';
	}

	/**
	 * Show meta box
	 *
	 * @return void
	 */
	function render()
	{
		$dir = "inc/admin/tabs/{$this->post_type}/";
		$dir_tab = "inc/admin/tabs/";
		wp_nonce_field( "save-post-{$this->post_type}", "sl_nonce_save_{$this->post_type}" );

		echo '<div class="tabs"><ul>';

		echo '
			<li>' . __( 'Media', '7listings' ) . '</li>
			<li>' . __( 'Location', '7listings' ) . '</li>
			<li>' . __( 'Contact Info', '7listings' ) . '</li>
			<li>' . __( 'Contacts', '7listings' ) . '</li>
			<li>' . __( 'Admin', '7listings' ) . '</li>
			<li>' . __( 'Reports', '7listings' ) . '</li>
		';

		do_action( 'company_edit_tab' );

		echo '</ul>';

		// Load tab content
		
		echo '<div>';
		locate_template( $dir_tab . 'parts/photos.php', true );
		echo '</div>';
		
		$tabs = array( 'location', 'contact', 'payment', 'admin', 'reports' );
		foreach ( $tabs as $tab )
		{
			echo '<div>';
			locate_template( $dir . $tab . '.php', true );
			echo '</div>';
		}

		do_action( 'company_edit_tab_content' );

		echo '</div>';
	}

	/**
	 * Save posts
	 *
	 * @param $post_id
	 *
	 * @return void
	 */
	function save( $post_id )
	{
		parent::save( $post_id );

		if (
			empty( $_POST['sl_nonce_save_post_company'] )
			|| ! wp_verify_nonce( $_POST['sl_nonce_save_post_company'], 'save-post-company' )
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
		)
		{
			return;
		}

		if ( ! empty( $_POST['company'] ) )
			update_post_meta( $post_id, 'company', $_POST['company'] );
	}

	/**
	 * Save custom fields for posts
	 * Update custom fields when they have values and delete them when possible to save database space.
	 *
	 * Note:
	 * - Edit listing can be done in the frontend where the edit page may not contains all fields
	 *   So using delete_post_meta() only when that field presents
	 * - If unchecked, checkboxes don't present in $_POST
	 *
	 * @param int $post_id Post ID
	 * @return void
	 */
	function save_post( $post_id )
	{
		if ( $logo = peace_handle_upload( sl_meta_key( 'logo', $this->post_type ) ) )
			set_post_thumbnail( $post_id, $logo );

		// Text and Select fields
		$fields = array(
			'account',
			'facebook', 'twitter', 'googleplus', 'pinterest', 'linkedin', 'instagram', 'rss',
			'invoice_name', 'invoice_email', 'invoice_phone',
			'service_radius', 'service_postcodes', 'leads_service_radius',
			'accounting_number',
		);
		$days   = array( 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' );
		foreach ( $days as $day )
		{
			$fields[] = "business_hours_{$day}_from";
			$fields[] = "business_hours_{$day}_to";
		}
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

		// Checkboxes
		$fields = array( 'open_247', 'featured', 'operating', 'service_area' );
		foreach ( $days as $day )
		{
			$fields[] = "business_hours_$day";
		}
		foreach ( $fields as $field )
		{
			if ( ! empty( $_POST[$field] ) )
				update_post_meta( $post_id, $field, 1 );
			else
				delete_post_meta( $post_id, $field );
		}

		// Locations
		$locations = array( 'state', 'city', 'area' );
		$term_ids  = array();
		foreach ( $locations as $location )
		{
			if ( empty( $_POST[$location] ) )
			{
				delete_post_meta( $post_id, $location );
				continue;
			}

			$value = trim( $_POST[$location] );

			$type = ( $location == 'area' ) ? 'suburb' : $location;

			// If location is passed as ID, store turn back to name
			// Todo: Validate location by postcode
			// If entered postcode, then all other location fields must based on that postcode
			if ( is_numeric( $value ) )
			{
				$object = \ASQ\Location\Location::find( array( 'id' => $value, 'type' => $type ), true );
			}
			else
			{
				$name 	= $value;
				$object = \ASQ\Location\Location::find( compact( 'name', 'type' ), true );
			}

			$value = empty( $object ) ? '' : $object['name'];

			update_post_meta( $post_id, $location, $value );
		}

		// Edit membership type in admin only
		if ( isset( $_POST['membership_type'] ) )
		{
			$user_id = get_post_meta( $post_id, 'user', true );
			$type    = $_POST['membership_type'];
			$prev    = get_user_meta( $user_id, 'membership', true );

			if ( $type != $prev )
			{
				update_user_meta( $user_id, 'membership', $type );

				$time = get_user_meta( $user_id, 'membership_time', true );
				do_action( "{$this->post_type}_account_change", $user_id, get_post( $post_id ), $type, $prev, $time, $time );
			}
		}

		do_action( "{$this->post_type}_save_post", $post_id );
	}
}

new Sl_Company_Edit( 'company' );