<?php
/**
 * Email Header
 *
 * @author        7Listings
 * @package       7Listings/Templates/Emails
 * @version       1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

// Variables
$background = sl_setting( 'emails_background' );

$header_background  = sl_setting( 'emails_base_color' );
$header_color       = sl_setting( 'emails_header_color' );
$header_text_shadow = sl_light_or_dark( $header_color, '0 1px 1px rgba(0,0,0,.2)', '0 1px 0 rgba(255,255,255,.3)' );

$body_background = sl_setting( 'emails_body_background' );
$heading_color   = sl_setting( 'emails_heading_color' );
$body_text_color = sl_setting( 'emails_body_text_color' );


$heading_color_10 = sl_hex_lighter( $heading_color, 7 );
$heading_color_20 = sl_hex_lighter( $heading_color, 13 );
$heading_color_30 = sl_hex_lighter( $heading_color, 20 );

$text_darker  = sl_hex_darker( $body_text_color, 20 );
$text_lighter = sl_hex_lighter( $body_text_color, 40 );


$bg_darker_10    = sl_hex_darker( $background, 10 );
$base_lighter_10 = sl_hex_lighter( $header_background, 10 );
$base_lighter_20 = sl_hex_lighter( $header_background, 20 );
$base_lighter_30 = sl_hex_lighter( $header_background, 30 );
$text_lighter_20 = sl_hex_lighter( $body_text_color, 20 );


// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline. !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
$wrapper                = '
	background-color: ' . esc_attr( $background ) . ';
	width:100%;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 70px 0 70px 0;
';
$template_container     = '
	-webkit-box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	box-shadow:0 0 0 3px rgba(0,0,0,0.025) !important;
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
	background-color: ' . esc_attr( $body_background ) . ';
	border: 1px solid $bg_darker_10;
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
';
$template_header        = '
	background-color: ' . esc_attr( $header_background ) . ';
	-webkit-border-top-left-radius:6px !important;
	-webkit-border-top-right-radius:6px !important;
	border-top-left-radius:6px !important;
	border-top-right-radius:6px !important;
	border-bottom: 0;
	font-family:Helvetica, Arial;
	font-weight:bold;
	line-height:100%;
	vertical-align:middle;
';
$header_content_h1      = '
	color: ' . esc_attr( $header_color ) . ";
	margin:0;
	padding: 28px 24px;
	text-shadow: $header_text_shadow;
	display:block;
	font-family:Helvetica, Arial;
	font-size:30px;
	font-weight:bold;
	text-align:left;
	line-height: 100%;
";
$header_content_h1_link = '
	color: ' . esc_attr( $header_color ) . ';
	text-decoration:none;
';

$body_content       = '
	background-color: ' . esc_attr( $body_background ) . ';
	-webkit-border-radius:6px !important;
	border-radius:6px !important;
';
$body_content_inner = "
	color: $body_text_color;
	font-family:Helvetica, Arial;
	font-size:14px;
	line-height:150%;
	text-align:left;
";


$style = "
	<style type='text/css'>
		#emailContent h1 {
			color:$heading_color_30;
			font-size:32px;
		}
		#emailContent h2 {
			color:$heading_color_20;
			font-size:26px;
		}
		#emailContent h3 {
			color:$heading_color_10;
			font-size:22px;
		}
		#emailContent h4,
		#emailContent h5,
		#emailContent h6{
			color:$heading_color;
		}
		#emailContent p {
			$body_content_inner
		}
		@media only screen and (max-width:480px){
			#emailContent p {
				font-size:16px !important;
			}
		}
	</style>
";

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo get_bloginfo( 'name' ); ?></title>
	<?php echo $style; ?>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: <?php echo $background; ?>;">
<div style="<?php echo $wrapper; ?>">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top">
				<?php
				if ( $img = sl_setting( 'emails_header_image' ) )
				{
					echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name' ) . '"></p>';
				}
				?>
				<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container" style="<?php echo $template_container; ?>" bgcolor="<?php echo $body_background; ?>">
					<tr>
						<td align="center" valign="top">
							<!-- Header -->
							<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header" style="<?php echo $template_header; ?>" bgcolor="<?php echo $header_background; ?>">
								<tr>
									<td>
										<h1 style="<?php echo $header_content_h1; ?>">
											<a href="[url]" target="_blank" style="<?php echo $header_content_h1_link; ?>?utm_campaign=Website Notification&utm_medium=email&utm_source=WebsiteEmail">[site-title]</a>
										</h1>
									</td>
								</tr>
							</table>
							<!-- End Header -->
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<!-- Body -->
							<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
								<tr>
									<td valign="top" style="<?php echo $body_content; ?>">
										<!-- Content -->

										<table border="0" cellpadding="20" cellspacing="0" width="100%">
											<tr>
												<td valign="top">
													<div id="emailContent" style="<?php echo $body_content_inner; ?>">
