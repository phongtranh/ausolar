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

<table width="100%" border="0" cellspacing="0" cellpadding="12">
	<tr>
		<td style="border-bottom-width: 1px; border-bottom-color: <?php echo $row_border; ?>;; border-bottom-style: solid; border-collapse: collapse;">
			<h3 style="color: <?php echo $heading_color; ?>; display: block; font-family: Helvetica, Arial; font-size: 26px; font-weight: bold; line-height: 100%; text-align: left; margin-top: 20px; margin-right: 0; margin-bottom: 0px; margin-left: 0;" align="left">New message from contact form widget</h3>
			<p>Page: [url] </p></td>
	</tr>
	<tr>
		<td class="bodyContent" style="border-collapse: collapse;">
			<div style="font-family: Helvetica, Arial;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top">
							<table width="100%" border="0" cellspacing="0" cellpadding="6">
								<tr>
									<td style="border-top:1px solid <?php echo $row_border; ?>;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>Name</td>
												<td style="color: <?php echo $text_darker; ?>; border-collapse: collapse;" align="right">
													<strong>[name]</strong></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td bgcolor="<?php echo $row_background; ?>" style="border-top:1px solid <?php echo $row_border; ?>;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td valign="top">Email</td>
												<td align="right" valign="top">
													<strong><a href="mailto:[customer-email]" style="color: #336699; text-decoration: none;">[customer-email]</a></strong>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="border-top:1px solid <?php echo $row_border; ?>;">[message]</td>
								</tr>
								<tr>
									<td style="border-top:1px solid <?php echo $row_border; ?>;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td>Page URL</td>
												<td style="color: <?php echo $text_darker; ?>; border-collapse: collapse;" align="right">
													<strong>[page-url]</strong></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>
<br>
