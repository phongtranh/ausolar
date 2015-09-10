<?php
add_action( 'wp_ajax_solar_email_company', 'solar_email_company' );
add_action( 'wp_ajax_nopriv_solar_email_company', 'solar_email_company' );

/**
 * Send email to company in single company page
 *
 * @return void
 */
function solar_email_company()
{
	check_ajax_referer( 'send' );

	$id      = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
	$name    = isset( $_POST['name'] ) ? strip_tags( $_POST['name'] ) : '';
	$email   = isset( $_POST['email'] ) ? strip_tags( $_POST['email'] ) : '';
	$phone   = isset( $_POST['phone'] ) ? strip_tags( $_POST['phone'] ) : '';
	$message = isset( $_POST['message'] ) ? strip_tags( $_POST['message'] ) : '';

	if ( ! $id || ! $name || ! $email || ! $phone || ! $message )
		wp_send_json_error( __( 'Error sending email, please try again!' ) );

	$to = get_post_meta( $id, 'email', true );

	if ( ! $to )
		wp_send_json_error( __( 'Error sending email, please try again!' ) );

	$body = __( 'Dear %s,<br><br>
		You have got a new message sent via your company page.<br><br>
		<b>Name:</b> %s<br>
		<b>Email:</b> %s<br>
		<b>Phone:</b> %s<br>
		<b>Message:</b> %s', '7listings' );
	$body = sprintf( $body, get_the_title( $id ), $name, $email, $phone, $message );
	wp_mail( $to, __( 'New message sent via company page', '7listings' ), $body, array( "Reply-To: $email", 'Bcc: installer@australiansolarquotes.com.au' ) );

	wp_send_json_success( __( 'Your message has been sent to company. We will be in touch!', '7listings' ) );
}

add_action( 'wp_ajax_autocomplete_companies', 'ajax_autocomplete_companies' );
add_action( 'wp_ajax_nopriv_autocomplete_companies', 'ajax_autocomplete_companies' );

function ajax_autocomplete_companies()
{
	global $wpdb;

	ob_clean();

	if ( empty( $_GET['term'] ) )
		wp_send_json_error();

	$term = trim( $_GET['term'] );

	$companies = get_posts( array(
		's'              => $term,
		'post_type'      => 'company',
		'post_status'    => 'publish',
		'posts_per_page' => 10,
		'meta_query'     => array(
			'relation' => 'AND',
			// Company haven't user assigned before
			array(
				'key'     => 'user',
				'compare' => 'NOT EXISTS'
			),
			array(
				'key'     => 'leads_enable',
				'value'   => 1,
				'compare' => 'NOT EXISTS',
			),
		)
	) );

	$companies_array = array();

	foreach ( $companies as $company )
	{
		$companies_array[] = $company->post_title;
	}

	if ( ! empty( $companies_array ) )
		wp_send_json_success( $companies_array );

	wp_send_json_error();
}

add_action( 'wp_ajax_solar_widget_email', function ()
{
	$template = 'G\'day web developer,

[first_name] from [company] has asked that you take a few minutes out of your day to install the following script on to their site [company_link]

<strong>1. Copy this code and paste before <code>&amp;lt;/body&amp;gt;</code> tag of your websites\' HTML</strong>

<pre>&amp;lt;script defer src="https://www.australiansolarquotes.com.au/rating-widget.js"&amp;gt;&amp;lt;/script&amp;gt;</pre>

<strong>2. Copy and paste this code into the HTML of your website where you\'d like the widget to appear</strong>

<pre>[widget]</pre>

Note:

1. You can change "data-width" and "data-height" to specify widget\'s width and height to match your website style
2. You can also change widget theme by changing "data-theme" value to "white" or "dark"

Thanking you in advance,

The team at [site]';

	$user    = get_current_user();
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );
	if ( empty( $company ) )
		wp_send_json_error( 'Error: no companies.' );

	$company  = current( $company );
	$site     = get_post_meta( $company->ID, 'website', true );
	$template = strtr( $template, [
		'[first_name]'   => $user->user_firstname,
		'[company]'      => $company->post_title,
		'[id]'           => $company->ID,
		'[company_link]' => $site ? '<a href="' . esc_url( $site ) . '">' . esc_html( $site ) . '</a>' : '',
		'[site]'         => '<a href="' . HOME_URL . '">' . get_bloginfo( 'name' ) . '</a>',
		'[widget]'       => str_replace( '&', '&amp;', esc_html( stripslashes( $_POST['widget'] ) ) ),
	] );
	$template = wpautop( $template );

	wp_mail( $_POST['email'], 'Review Widget Integration Instruction', $template );
	wp_send_json_success( 'Great work! Your developer has been emailed the instructions.' );
} );
