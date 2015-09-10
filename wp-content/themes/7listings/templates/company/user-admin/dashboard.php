<?php
if ( ! is_user_logged_in() )
{
	get_template_part( 'templates/company/user-admin/form-login' );

	return;
}

$user_id = get_current_user_id();
$company = get_posts( array(
	'post_type'      => 'company',
	'post_status'    => 'any',
	'posts_per_page' => 1,
	'meta_key'       => 'user',
	'meta_value'     => $user_id,
) );

if ( empty( $company ) )
{
	get_template_part( 'templates/company/user-admin/no-company' );

	return;
}
?>

<div id="company-admin">

	<h2><?php _e( 'Company views', '7listings' ); ?></h2>

	<p><?php _e( 'Total views:', '7listings' ); ?> <span id="total-views"></span><p>
	<div id="views-chart"></div>

	<?php
	global $post, $sl_is_company_account_page;
	$post = current( $company );
	setup_postdata( $post );

	$sl_is_company_account_page = true;
	comments_template();
	wp_reset_postdata();
	?>

</div><!-- #company-admin -->
