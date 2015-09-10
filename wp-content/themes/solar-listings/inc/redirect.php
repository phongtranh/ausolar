<?php
add_filter( 'login_redirect', 'solar_login_redirect', 10, 3 );

/**
 * Redirect users after login regarding user role
 *
 * @param string $redirect_to
 *
 * @return string
 */
function solar_login_redirect( $redirect_to, $request, $user )
{
	$roles = array(
		'editor'        => home_url( '/intranet/' ),
		'company_owner' => home_url( '/my-account/' ),
		'customer' 		=> '/',
	);

	foreach ( $roles as $role => $url )
	{
		if ( is_array( $user->roles ) && in_array( $role, $user->roles ) )
		{
			return $url;
		}
	}
	return $redirect_to;
}

add_action( 'admin_init', 'solar_admin_redirect' );

/**
 * Redirect user to pages regarding user role
 *
 * @return void
 */
function solar_admin_redirect()
{
	// Allow ajax requests
	if ( current_user_can( 'administrator' ) || defined( 'DOING_AJAX' ) )
		return;

	$dashboard = sl_setting( 'company_page_dashboard' );
	$url       = $dashboard ? get_permalink( $dashboard ) : HOME_URL;

	$roles = array(
		'company_owner'   	=> $url,
		'subscriber'      	=> '/',
		'customer'      	=> '/',
		'wholesale_owner' 	=> home_url( '/affiliates/' ),
	);

	foreach ( $roles as $role => $url )
	{
		if ( current_user_can( $role ) )
		{
			wp_redirect( $url );

			die;
		}
	}
}

add_action( 'template_redirect', 'solar_redirect_company' );

/**
 * Todo: Remove it because we use .htaccess
 *
 * Redirect user to new page company
 *
 * @return void
 */

function solar_redirect_company()
{
	$installers = sl_setting( 'company_base_url' );

	$mappings = array(
		'solar-link-gold-coast' => 'solar-link-brisbane',
		'solar-link-melbourne'  => 'solar-link-australia-vic',
		'solar-link-sydney'     => 'solar-link-australia-nsw',
		'breaze'                => 'breaze-energy-solutions'
	);

	foreach ( $mappings as $old => $new )
	{
		if ( is_single( $old ) )
		{
			$url = home_url( $installers . '/' . $new );

			wp_redirect( $url, 301 );

			exit();
		}
	}
}

add_action( 'wp_print_scripts', 'solar_force_https' );

/**
 * Force SSL on whole site
 *
 * @return void
 */
function solar_force_https()
{
	?>
	<script>
		if ( document.location.protocol != "https:" )
		{
			document.location = document.URL.replace( /^http:/i, "https:" );
		}
	</script>
<?php
}

// Redirect deleted tags to homepage
add_action( 'template_redirect', function ()
{
	global $wp;
	if ( ! empty( $wp->query_vars['tag'] ) && is_404() )
	{
		wp_safe_redirect( HOME_URL, 301 );
		exit;
	}
} );
