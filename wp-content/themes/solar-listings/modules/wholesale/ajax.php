<?php

add_action( 'wp_ajax_sl_wholesale_signup', 'solar_wholesale_signup' );
add_action( 'wp_ajax_nopriv_sl_wholesale_signup', 'solar_wholesale_signup' );

function solar_wholesale_signup()
{
    if ( empty( $_POST['nonce'] ) || !wp_verify_nonce( $_POST['nonce'], 'signup' ) )
        wp_send_json_error( __( 'Form is not submitted properly.', '7listings' ) );

    parse_str( $_POST['data'], $data );
    unset( $_POST['data'], $_POST['nonce'], $_POST['action'] );
    $_POST = array_merge( $_POST, $data );

    $errors = array();

    if ( is_user_logged_in() )
    {
        $user_id = get_current_user_id();
    }
    else
    {
        // Required fields
        $required = array(
            'username'           => __( 'Please enter username.', '7listings' ),
            'password'           => __( 'Please enter password.', '7listings' ),
            'user_email'         => __( 'Please enter email.', '7listings' ),
            'user_email_confirm' => __( 'Please enter confirmed email.', '7listings' ),
        );
        foreach ( $required as $k => $v )
        {
            if ( empty( $data[$k] ) )
                $errors[] = $v;
        }

        // Check if email valid
        if ( $data['user_email'] != $data['user_email_confirm'] )
            $errors[] = __( 'Please enter same email for confirmation.', '7listings' );
        if ( !is_email( $data['user_email'] ) )
            $errors[] = __( 'Please enter valid email.', '7listings' );
        if ( email_exists( $data['user_email'] ) )
            $errors[] = __( 'Email is used. Please use another email.', '7listings' );

        if ( !empty( $errors ) )
            wp_send_json_error( implode( '<br>', $errors ) );

        // Insert new user
        $user_data = array(
            'user_login' => $data['username'],
            'user_pass'  => $data['password'],
            'user_email' => $data['user_email'],
            'first_name' => isset( $data['first_name'] ) ? $data['first_name'] : '',
            'last_name'  => isset( $data['last_name'] ) ? $data['last_name'] : '',
            'role'       => 'wholesale_owner',
        );

        $user_id = wp_insert_user( $user_data );
        if ( is_wp_error( $user_id ) || !$user_id )
        {
            $errors[] = $user_id->get_error_message();
            wp_send_json_error( implode( '<br>', $errors ) );
        }
    }

    // Required fields
    $required = array(
        'post_title'   => __( 'Please enter company title.', '7listings' ),
        'post_content' => __( 'Please enter company description.', '7listings' ),
    );

    foreach ( $required as $k => $v )
    {
        if ( empty( $data[$k] ) )
            $errors[] = $v;
    }
    if ( !empty( $errors ) )
        wp_send_json_error( implode( '<br>', $errors ) );

    // WordPress Administration File API
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Insert company
    $post_data = array(
        'post_title'   => $data['post_title'],
        'post_content' => $data['post_content'],
        'post_status'  => 'pending',
        'post_type'    => 'wholesale',
        'post_author'  => $user_id,
    );
    $post_id = wp_insert_post( $post_data );
    if ( is_wp_error( $post_id ) || !$post_id )
    {
        $errors[] = __( 'Error while creating new supplier.', '7listings' );
        wp_send_json_error( implode( '<br>', $errors ) );
    }

    // Add user
    update_post_meta( $post_id, 'user', $user_id );

    // Country
    if ( isset( $data['country'] ) )
        update_post_meta( $post_id, 'country', $data['country'] );

    $user_data = get_userdata( $user_id );

    $replacements = array(
        '[first_name]'    => $user_data->first_name,
        '[company-title]' => $data['post_title'],
    );

    // Send email to admin
    // Todo, Fixme: Add Email Template to Setting Page
    $to = sl_email_admin_email( "installer@australiansolarquotes.com.au" );
    $subject = '★ Supplier Signup - ' . $user_data->first_name;
    $body = sl_email_content( '', 'wholesale-membership-admin', $replacements );
    wp_mail( $to, $subject, $body );

    // Email to user
    // Todo, Fixme: Add Email Template to Setting Page
    $to = $user_data->user_email;
    $subject = '★ Supplier Signup - Australian Solar Quotes';
    $body = sl_email_content( '', 'wholesale-membership-user', $replacements );
    wp_mail( $to, $subject, $body );

    wp_send_json_success( array(
        'message'  => __( 'Your information has been added successfully. We will review the company before
        displaying. Thank you!', '7listings' ),
        'redirect' => '/affiliates',
    ) );
}
