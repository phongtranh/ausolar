<?php
/**
 * This file handles all Ajax actions related to common things in the theme
 * Ajax actions related to each module will be handle by the module itself
 */

add_action( 'wp_ajax_sl_location_autocomplete', 'sl_ajax_location_autocomplete' );
add_action( 'wp_ajax_nopriv_sl_location_autocomplete', 'sl_ajax_location_autocomplete' );

/**
 * Ajax callback for autocomplete location
 *
 * @return void
 * @since  4.12.1
 */
function sl_ajax_location_autocomplete()
{
	if ( ! check_ajax_referer( 'location-autocomplete', 'nonce', false ) || empty( $_POST['term'] ) )
		wp_send_json_error();

	$search = sanitize_text_field( $_POST['term'] );
	$terms  = get_terms( 'location', array(
		'hide_empty' => 0,
		'name__like' => $search,
	) );

	/**
	 * Filter by level
	 *
	 * State = Top level = Level 1
	 * City = Level 2
	 * Suburb/Area = Level 3
	 */
	$level = isset( $_POST['level'] ) ? absint( $_POST['level'] ) : 0;
	if ( $level )
	{
		// For 1st level, we can check easily
		if ( 1 == $level )
		{
			// Remove terms level > 1
			foreach ( $terms as $k => $term )
			{
				if ( $term->parent )
					unset( $terms[$k] );
			}
		}
		else
		{
			foreach ( $terms as $k => $term )
			{
				// Remove terms level = 1
				// They're simple to be checked so we do that to avoid getting 'level' for these terms
				if ( ! $term->parent )
					unset( $terms[$k] );

				// Check term level
				$ancestors   = get_ancestors( $term->term_id, 'location' );
				if ( count( $ancestors ) + 1 != $level )
					unset( $terms[$k] );
			}
		}
	}

	$result = array();
	foreach ( $terms as $term )
	{
		$names = array( '<strong>' . $term->name . '</strong>' );
		while ( $term->parent )
		{
			$parent  = get_term( $term->parent, 'location' );
			$names[] = $parent->name;
			$term    = $parent;
		}
		$result[] = array(
			'label' => implode( ', ', $names ),
			'value' => strip_tags( $names[0] ),
		);
	}
	$result = apply_filters( 'sl_ajax_location_autocomplete', $result );
	wp_send_json_success( $result );
}

add_action( 'wp_ajax_sl_change_featured', 'wp_ajax_sl_change_featured' );

/**
 * Ajax callback for changing featured on listing page
 *
 * @return void
 */
function wp_ajax_sl_change_featured()
{
	check_admin_referer( 'change-featured' );
	$post_id = ( int ) $_POST['post_id'];
	$current = ( int ) $_POST['current'];
	$current = 2 == $current ? 0 : ( $current + 1 );

	update_post_meta( $post_id, 'featured', $current );
	wp_send_json_success();
}

add_action( 'wp_ajax_sl_delete_sidebar', 'sl_ajax_delete_sidebar' );

/**
 * Delete custom sidebar
 *
 * @return void
 */
function sl_ajax_delete_sidebar()
{
	check_admin_referer( 'delete-sidebar' );

	if ( empty( $_POST['sidebar'] ) )
		wp_send_json_error( __( 'No sidebar selected', '7listings' ) );

	$sidebar  = $_POST['sidebar'];
	$settings = get_option( THEME_SETTINGS );
	if ( empty( $settings['sidebars'] ) || ! is_array( $settings['sidebars'] ) || ! in_array( $sidebar, $settings['sidebars'] ) )
		wp_send_json_error( __( 'Invalid sidebar', '7listings' ) );

	$settings['sidebars'] = array_diff( $settings['sidebars'], array( $sidebar ) );

	update_option( THEME_SETTINGS, $settings );
	wp_send_json_success();
}

add_action( 'wp_ajax_sl_contact_current_time', 'sl_ajax_contact_current_time' );
add_action( 'wp_ajax_nopriv_sl_contact_current_time', 'sl_ajax_contact_current_time' );

/**
 * Get current time for contact page
 *
 * @return void
 * @since 4.17.1
 */
function sl_ajax_contact_current_time()
{
	wp_send_json_success( array(
		'day'  => current_time( 'l' ),
		'time' => current_time( get_option( 'time_format' ) ),
	) );
}


add_action( 'wp_ajax_sl_contact_submit', 'sl_ajax_contact_submit' );
add_action( 'wp_ajax_nopriv_sl_contact_submit', 'sl_ajax_contact_submit' );

/**
 * Send contact message in contact page
 *
 * @return void
 * @since 5.1.1
 */
function sl_ajax_contact_submit()
{
	// Validate ajax request
	$check = check_ajax_referer( 'contact-send', false, false );
	if ( ! $check )
		wp_send_json_error( '<p>' . __( 'Invalid request', '7listings' ) . '</p>' );

	$errors = array();

	// Check required fields
	if ( empty( $_POST['first'] ) )
		$errors[] = __( 'Please enter your <strong>first name</strong>.', '7listings' );
	if ( empty( $_POST['last'] ) )
		$errors[] = __( 'Please enter your <strong>last name</strong>.', '7listings' );
	if ( empty( $_POST['email'] ) )
		$errors[] = __( 'Please enter your <strong>email</strong>.', '7listings' );
	elseif ( ! is_email( $_POST['email'] ) )
		$errors[] = __( 'Invalid <strong>email address</strong>.', '7listings' );
	if ( empty( $_POST['subject'] ) )
		$errors[] = __( 'Please enter a <strong>subject</strong>.', '7listings' );
	if ( empty( $_POST['message'] ) )
		$errors[] = __( 'Please send a <strong>message</strong>.', '7listings' );

	if ( $errors )
		wp_send_json_error( '<p>' . implode( '</p><p>', $errors ) . '</p>' );

	$email   = $_POST['email'];
	$phone   = ! empty( $_POST['phone'] ) ? strip_tags( $_POST['phone'] ) : '';
	$subject = ! empty( $_POST['subject'] ) ? strip_tags( $_POST['subject'] ) : '';
	$message = ! empty( $_POST['message'] ) ? $_POST['message'] : '';

	$settings = get_option( THEME_SETTINGS );
	$number   = empty( $settings['contact_number'] ) ? 0 : $settings['contact_number'];
	$number ++;
	$settings['contact_number'] = $number;
	update_option( THEME_SETTINGS, $settings );

	$replacements = array(
		'[first]'           => $_POST['first'],
		'[last]'            => $_POST['last'],
		'[customer-email]'  => $email,
		'[customer-phone]'  => $phone,
		'[subject]'         => $subject,
		'[message]'         => $message,
		'[message_counter]' => $number,
	);

	// Send email with all details to admin
	$admin_email = sl_email_admin_email( 'contact_admin_email' );
	$subject     = sl_email_replace( sl_setting( 'contact_admin_subject' ), $replacements );
	$body        = sl_email_content( '', 'contact-admin', $replacements );
	wp_mail( $admin_email, $subject, $body, "Reply-To: $email" );

	// Send notification email to user
	$subject = sl_email_replace( sl_setting( 'contact_subject' ), $replacements );
	$body    = sl_email_content( 'contact_message', 'contact', $replacements );
	wp_mail( $email, $subject, $body );

	wp_send_json_success( '<p>' . __( 'Thank you, our friendly staff will follow up with you shortly.', '7listings' ) . '</p>' );
}
