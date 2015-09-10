<?php /*?>
 * Site Header Template
 * last edit: 5.1.1
 *
 * @package WordPress
 * @subpackage 7Listings
<?php */ ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="designer" content="7listings.net">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?><?php peace_action( 'body' ); ?>>

<?php
$class = '';
switch ( sl_setting( 'layout_option' ) )
{
	case 'no-box':
		$class = 'container-fluid full-width-layout';
		break;
	case 'one-box':
		$class = 'container one-box-layout';
		break;
	case 'boxes':
		$class = 'container boxes-layout';
		break;
}
$site_title = esc_attr( get_bloginfo( 'name' ) );
?>
<div class="<?php echo $class; ?>" id="wrapper" data-role="page">
	<header id="branding" class="site-header" role="banner" data-mobile-nav="<?php echo esc_attr( sl_setting( 'design_mobile_nav_break_point' ) ); ?>">
		<div class="container">
			<a href="<?php echo HOME_URL; ?>" title="<?php echo $site_title; ?>" rel="home" class="home-link">
				<?php
				if ( sl_setting( 'logo_display' ) && sl_setting( 'logo' ) )
				{
					if ( '0' == sl_setting( 'design_mobile_logo_display' ) || ! sl_setting( 'design_mobile_logo' ) )
					{
						if( sl_setting( 'svg_display' ) )
						{
							echo sl_setting( 'logo_svg' );
						}
						else
						{
							printf(
								'<img src="%s" alt="%s" width="%s" height="%s" id="logo-desktop" class="logo site">',
								wp_get_attachment_url( sl_setting( 'logo' ) ),
								$site_title,
								sl_setting( 'logo_width' ) ? sl_setting( 'logo_width' ) : '',
								sl_setting( 'logo_height' ) ? sl_setting( 'logo_height' ) : ''
							);
						}
					}
					else
					{
						if( !sl_setting( 'svg_display' ) )
						{
							printf(
								'<img src="%s" alt="%s" width="%s" height="%s" id="logo-desktop" class="logo site">',
								wp_get_attachment_url( sl_setting( 'logo' ) ),
								$site_title,
								sl_setting( 'logo_width' ) ? sl_setting( 'logo_width' ) : '',
								sl_setting( 'logo_height' ) ? sl_setting( 'logo_height' ) : ''
							);
						}
					}
				}

				if ( sl_setting( 'design_mobile_logo_display' ) && sl_setting( 'design_mobile_logo' ) )
				{
					if( sl_setting( 'svg_display' ) )
					{
						echo sl_setting( 'logo_svg' );
					}
					else
					{
						printf(
							'<img src="%s" alt="%s" width="%s" height="%s" id="logo-mobile" class="logo site">',
							wp_get_attachment_url( sl_setting( 'design_mobile_logo' ) ),
							$site_title,
							sl_setting( 'design_mobile_logo_width' ) ? sl_setting( 'design_mobile_logo_width' ) : '',
							sl_setting( 'design_mobile_logo_height' ) ? sl_setting( 'design_mobile_logo_height' ) : ''
						);
					}
				}
				?>

				<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>
				<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
			</a>

			<?php
			if ( sl_setting( 'design_header_phone' ) && ( $phone = sl_setting( 'phone' ) ) )
			{
				$class = '';
				if ( sl_setting( 'design_header_phone_color_scheme' ) )
				{
					$class = sl_setting( 'design_header_phone_color_scheme' );
				}
				echo '<div id="header-phone-number" class="' . $class . '"><a href="tel:' . $phone . '">' . $phone . '</a></div>';
			}
			?>

			<?php peace_action( 'header_bottom' ); ?>

		</div><!-- .container -->

		<nav class="navbar">
			<div class="container">
				<a id="nav-open-btn" href="#nav">&#9776;</a>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav',
					'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
					'walker'         => new FitWP_Bootstrap_Nav_Walker,
					'fallback_cb'    => 'sl_page_menu',
				) );
				?>
				<?php do_action( 'sl_show_mini_cart' ); ?>
				<?php
				if ( sl_setting( 'design_header_search' ) )
				{
					get_template_part( 'searchform', 'top' );
				}
				?>
			</div>
		</nav>
	</header><!-- #branding -->

	<?php sl_breadcrumbs(); ?>

	<main id="main" role="main">

