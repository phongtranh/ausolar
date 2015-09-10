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

// Load colours
//$base = sl_setting( 'emails_base_color' );
//$base_text = sl_light_or_dark( $base, '#202020', '#ffffff' );

$body_background = sl_setting( 'emails_body_background' );
$heading_color   = sl_setting( 'emails_heading_color' );
$body_text_color = sl_setting( 'emails_body_text_color' );


$heading_lighter_20 = sl_hex_lighter( $heading_color, 30 );

$text_darker  = sl_hex_darker( $body_text_color, 20 );
$text_lighter = sl_hex_lighter( $body_text_color, 40 );

$row_border     = sl_hex_darker( $body_background, 10 );
$row_background = sl_hex_darker( $body_background, 3 );
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="center" valign="top" style="border-collapse: collapse;"><!-- // Begin Template Body \\ -->

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="58%" valign="top" class="bodyContent" style="padding-top: 22px; border-collapse: collapse;">
						<h2 style="color: <?php echo $text_darker; ?>; display: block; font-size: 28px; font-weight: bold; line-height: 100%;">Dear [name]</h2>
						<p>Thank You, for your message.<br>
							Our friendly staff will follow up with you shortly.</p>
						<p>If you have any questions or special requests, <br>
							do not hesitate to contact us.</p>
						<p>Kind regards,<br>
							<strong style="font-size: 1.1em; font-weight: bold; margin-bottom: 2em; display:block;">The [site-title] Team</strong>
						</p></td>
					<td width="42%" valign="top" style="padding-left: 32px; border-collapse: collapse;" class="sidebarContent">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td align="right" style="padding-bottom: 12px; border-collapse: collapse;">
									<table border="0" cellpadding="0" cellspacing="4">
										<tr>
											<td align="left" valign="top" style="border-collapse: collapse;">
												<a href="[facebook]"><img src="http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_facebook.png" alt="" style="height: auto; line-height: 100%; outline: none; text-decoration: none; display: inline; margin-top: 0; margin-right: 0; margin-bottom: 0; margin-left: 0; border-top-width: 0; border-right-width: 0; border-bottom-width: 0; border-left-width: 0;"></a>
											</td>
										</tr>
										<tr>
											<td align="left" valign="top" style="border-collapse: collapse;">
												<a href="[twitter]"><img src="http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_twitter.png" alt="" style="height: auto; line-height: 100%; outline: none; text-decoration: none; display: inline; margin-top: 0; margin-right: 0; margin-bottom: 0; margin-left: 0; border-top-width: 0; border-right-width: 0; border-bottom-width: 0; border-left-width: 0;"></a>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="color: <?php echo $text_lighter; ?>; border-collapse: collapse; font-size: 12px; line-height: 150%; text-align: right;" align="right">
									<a href="mailto:[email]" style="color: #336699; font-weight: normal; text-decoration: none;">[email]</a><br>
									[phone]<br><br>
									[address]<br>
									[city] [postcode]
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="12">
				<tr>
					<td>
						<h3 style=" color: <?php echo $heading_lighter_20; ?>; display: block; font-size: 48px; font-weight: bold; line-height: 100%; text-align: left; margin-top: 20px; margin-right: 0; margin-bottom: 0px; margin-left: 0;" align="left">Your Message</h3>
					</td>
				</tr>
				<tr>
					<td class="bodyContent" style="border-collapse: collapse;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">
									<table width="100%" border="0" cellspacing="0" cellpadding="6">
										<tr>
											<td bgcolor="<?php echo $row_background; ?>" style="border-top:1px solid <?php echo $row_border; ?>; border-bottom:1px solid <?php echo $row_border; ?>;">[message]</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="bodyContent" style="border-collapse: collapse;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">Page URL</td>
								<td valign="top">[page-url]</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<!-- // End Template Body \\ --></td>
	</tr>
</table>
