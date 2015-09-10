<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php do_action( 'wpe_gce_head' ); ?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="designer" content="7istings.net">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Comments Feed" href="<?php bloginfo('comments_rss2_url'); ?>" />

	<?php
	if ( is_singular() ) :
		$custom_script = get_post_meta( get_the_ID(), 'custom_script_header', true );
		if ( ! empty( $custom_script ) )
			echo $custom_script;
	endif;
	?>

	<?php wp_head(); ?>

	<script async type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js"></script>

</head>

<body <?php body_class(); ?><?php peace_action( 'body' ); ?>>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php
global $settings;
$class = '';

switch ( sl_setting( 'layout_option' ) )
{
	case 'no-box':
		$class = 'container-fluid';
		break;
	case 'one-box':
		$class = 'container';
		break;
	case 'boxes':
		$class = 'container boxed';
		break;
}
$site_title = esc_attr( get_bloginfo( 'name' ) );
?>
<div class="<?= $class; ?>" id="wrapper" data-role="page">
	<header id="branding" class="site-header">
		<div class="container">
			<a href="<?php echo HOME_URL; ?>" title="<?php echo $site_title; ?>" rel="home" class="home-link">
				<div id="solar-quotes-logo">
					<img src="https://www.australiansolarquotes.com.au/wp-content/uploads/logo.svg" alt="Australian Solar Quotes Logo">
				</div>
				
				<?php if ( sl_setting( 'display_site_title' ) ) : ?>
					<h2 id="site-title"><?php bloginfo( 'name' ); ?></h2>
				<?php endif; ?>

				<?php if ( sl_setting( 'display_site_description' ) ) : ?>
					<h3 id="site-description"><?php bloginfo( 'description' ); ?></h3>
				<?php endif; ?>
			</a>
			
			<?php if ( ! url_contains( ['my-account', 'intranet', '/quotes/', 'solar-quotes'] ) ) : ?>
			<form name="postcode" id="form-postcode" method="post">
				<img src="https://www.australiansolarquotes.com.au/wp-content/uploads/full_header_form_freeicon.svg" id="free-corner" alt="Free">
				<p>Save Money <span class="small">in <br> only</span> 30 Seconds</p>
				<input type="number" name="sticky_postcode" id="input-postcode" placeholder="Enter postcode...">
				<input id="postcode-form-submit" type="submit" name="submit" value="Get 3 Quotes">
			</form>
			<?php endif; ?>
		</div><!-- .container -->
		<div class="clearfix"></div>

		<nav class="navbar">
			<div class="container">
				<?php
                $location = 'primary';

                if ( is_user_logged_in() )
                {
                    if ( current_user_can( 'company_owner' ) )
                        $location = 'primary_company';
                    elseif ( current_user_can( 'wholesale_owner' ) )
                        $location = 'primary_wholesale';
                    else
                        $location = 'primary';
                }
				if ( function_exists( 'peace_nav' ) )
				{
					peace_nav( array(
						'theme_location'   => $location,
						'container'  => false,
						'menu_class' => 'nav',
						'walker'	 => new Peace_Bootstrap_Nav_Walker,
					) );
				}
				else
				{
					wp_nav_menu( array(
						'theme_location'  => $location,
						'container'       => false,
						'menu_class'      => 'nav',
						'walker'          => new FitWP_Bootstrap_Nav_Walker,
						'fallback_cb'     => 'fitwp_bootstrap_menu_callback',
					) );
				}
				?>

				<a id="nav-open-btn" href="#nav">&#9776;</a>
			</div>
		</nav>
	</header><!-- #branding -->

	<?php sl_breadcrumbs(); ?>

	<main id="main">
