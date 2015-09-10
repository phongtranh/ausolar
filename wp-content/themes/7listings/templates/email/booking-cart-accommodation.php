<?php
/**
 * Tour Cart Booking Component
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
		<td class="bodyContent" style="border-collapse: collapse;">
			<div style="color: #505050; font-size: 14px; line-height: 150%; text-align: left;" align="left">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="2" style="border-collapse: collapse; padding-top: 12px; padding-right: 0; padding-bottom: 12px; padding-left: 4px;">
							<strong style="color: <?php echo $text_darker; ?>;">[title]</strong> - [resource]
						</td>
					</tr>
					<tr>
						<td valign="top" style="border-collapse: collapse;">
							<table width="100%" border="0" cellspacing="0" cellpadding="6">
								<tr>
									<td style="border-collapse: collapse;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td style="border-collapse: collapse;">Check In</td>
												<td align="right" style="color: <?php echo $text_darker; ?>; border-collapse: collapse;">
													<strong>[checkin]</strong></td>
											</tr>
											<tr>
												<td style="border-collapse: collapse;">Check Out</td>
												<td align="right" style="color: <?php echo $text_darker; ?>; border-collapse: collapse;">
													<strong>[checkout]</strong></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td bgcolor="<?php echo $row_background; ?>" style="border-top:1px solid <?php echo $row_border; ?>; border-collapse: collapse;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td valign="top" style="border-collapse: collapse;">Guests</td>
												<td align="right" valign="top" style="color: <?php echo $text_darker; ?>; border-collapse: collapse;">
													<strong>[guests]</strong></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="border-top:1px solid <?php echo $row_border; ?>; border-collapse: collapse;">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td style="border-collapse: collapse;">TOTAL</td>
												<td align="right" style="color: <?php echo $text_darker; ?>; border-collapse: collapse;">
													<strong>[total] AUD</strong></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td width="190" align="right" valign="top" style="padding-right: 24px; border-collapse: collapse;">[resource_photo]</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td class="bodyContent" align="left">[booking_message]
			</div></td>
	</tr>
	<tr>
		<td style="border-collapse: collapse;">
			<h4 style="color: <?php echo $heading_color; ?>; display: block; font-size: 20px; font-weight: bold; line-height: 100%; text-align: left; margin-top: 20px; margin-right: 0; margin-bottom: 0px; margin-left: 0;" align="left">Guest Details</h4>
		</td>
	</tr>
	<tr>
		<td class="bodyContent" style="border-collapse: collapse;" align="left"> [guests_info]<br>
			<br>
			[message]
		</td>
	</tr>
	<tr>
		<td style="border-collapse: collapse;">
			<h4 style="color: <?php echo $heading_color; ?>; display: block; font-size: 20px; font-weight: bold; line-height: 100%; text-align: left; margin-top: 20px; margin-right: 0; margin-bottom: 0px; margin-left: 0;" align="left">Our Policies</h4>
		</td>
	</tr>
	<tr>
		<td class="bodyContent" style="border-collapse: collapse;" align="left"> [payment_policy]<br>
			<br>
			[cancellation_policy]<br>
			<br>
			[terms_conditions]
		</td>
	</tr>
</table>
