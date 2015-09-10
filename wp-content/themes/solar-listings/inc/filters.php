<?php

/**
 * Filter the value submitted by Address box
 */
if ( ! empty ( $_POST['gform_submit'] ) && in_array( $_POST['gform_submit'], array( 1, 63 ) ) )
{
	$states = array(
		'QLD' 	=> 'Queensland',
		'VIC' 	=> 'Victoria',
		'SA' 	=> 'South Australia',
		'TAS' 	=> 'Tasmania',
		'ACT' 	=> 'Australian Capital Territory',
		'WA'	=> 'Western Australia',
		'NT'	=> 'Northern Territory',
		'NSW'   => 'New South Wales'
	);

	$prefix = ( $_POST['gform_submit'] == 1 ) ? '17' : '91'; 

	$city 	= $_POST["input_{$prefix}_3"];
	$state 	= $_POST["input_{$prefix}_4"];

	$address = explode( ',', $_POST["input_{$prefix}_1"] );

	if ( count( $address ) > 1 )
	{
		for ( $i = count( $address ); $i > 0; $i-- )
		{
			if ( in_array( trim( $address[$i] ), array( $city, $states[$state], 'Australia' ) ) )
				unset( $address[$i] );
		}
	}

	$_POST["input_{$prefix}_1"] = implode( ',', $address );

	if ( empty( $_POST['input_57'] ) )
		$_POST['input_57'] = 'I';
}

/**
 * Disable Emoji feature since ASQ isn't using it
 * 
 * @return void
 */
add_action( 'init', function() 
{
	// all actions related to emojis
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

	// filter to remove TinyMCE emojis
	//add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
} );

add_filter( 'gform_mailchimp_field_value', 'solar_gf_filter_mailchimp_value', 10, 5 );

function solar_gf_filter_mailchimp_value( $field_value, $form_id, $field_id, $entry, $name )
{
	if ( $form_id == 1 && $name == 'COMPANIES' )
	{
		$companies_id = gform_get_meta( $entry['id'], 'companies' );

		if ( empty( $companies_id ) )
			return;

		$companies_id = explode( ',', $companies_id );

		$field_value = count( $companies_id );
	}

	return $field_value;
}

add_filter( 'post_row_actions', 'asq_add_row_action_switch_to', 10, 2 );

/**
 * Add Switch to Row actions
 * 
 * @param  Array $actions WP Row Actions
 * @param  Post $post    WP Post Object
 * 
 * @return Array $actions
 */
function asq_add_row_action_switch_to( $actions, $post )
{
	// Only add switch to to company post type
	if ( $post->post_type != 'company' )
		return $actions;

	$user_id = get_post_meta( $post->ID, 'user', true );

	if ( isset( $user_id ) && intval( $user_id ) > 0 ) 
	{
		$switch_to_user_url = wp_nonce_url( add_query_arg( array(
			'action'  => 'switch_to_user',
			'user_id' => $user_id
		), wp_login_url() ), "switch_to_user_{$user_id}" );
		
		$actions['switch'] = '<a href="' . $switch_to_user_url . '">Switch to</a>';
	}

    return $actions;
}