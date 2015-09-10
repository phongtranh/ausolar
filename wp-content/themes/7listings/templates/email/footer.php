<?php
/**
 * Email Footer
 *
 * @author        7Listings
 * @package       7Listings/Templates/Emails
 * @version       1.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

// Load colours
//$bg = sl_setting( 'emails_background' );
//$base = sl_setting( 'emails_base_color' );

$background = sl_setting( 'emails_background' );

$header_background = sl_setting( 'emails_base_color' );


$base_lighter_40   = sl_hex_lighter( $header_background, 40 );
$background_darker = sl_hex_darker( $background, 50 );

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline.
$template_footer = '
	border-top:0;
	-webkit-border-radius:6px;
';

$credit       = "
	border:0;
	color: $base_lighter_40;
	font-family: Helvetica, Arial;
	font-size:12px;
	line-height:125%;
	text-align:center;
";
$footer_links = "
	color: $header_background;
	font-weight: normal;
	text-decoration: none;
";
$footer_text  = "
	color: $background_darker;
	font-family: Helvetica, Arial, sans-serif;
	font-size: 10px;
	line-height: 120%;
";
?>
</div>
</td>
</tr>
</table>
<!-- End Content -->
</td>
</tr>
</table>
<!-- End Body -->
</td>
</tr>
<tr>
	<td align="center" valign="top">
		<!-- Footer -->
		<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="<?php echo $template_footer; ?>">
			<tr>
				<td valign="top">
					<table border="0" cellpadding="10" cellspacing="0" width="100%">
						<tr>
							<td colspan="2" valign="middle" id="credit" style="<?php echo $credit; ?>">
								<?php echo wpautop( wp_kses_post( wptexturize( sl_setting( 'emails_footer_text' ) ) ) ); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<!-- End Footer -->
	</td>
</tr>
</table>
<table border="0" cellpadding="10" cellspacing="0" width="600px" style="<?php echo $footer_text; ?>">
	<tr>
		<td valign="top" width="340" style="border-collapse: collapse;">
			<div style="" align="left">
				Copyright &copy; [current_year]
				<a href="[url]" target="_blank" style="<?php echo $footer_links; ?>?utm_campaign=WebsiteEmail&utm_medium=email&utm_source=Website Notification">[site-title]</a><br>
				All rights reserved.
				<br>
			</div>
		</td>
		<td width="220" align="right" valign="top" style="border-collapse: collapse;">
			<div style="" align="right">
				<a href="[url]" target="_blank" style="<?php echo $footer_links; ?>?utm_campaign=WebsiteEmail&utm_medium=email&utm_source=Website Notification">[url]</a>
			</div>
		</td>
	</tr>
	<tr>
		<td valign="top" style="border-collapse: collapse;">
			<div style="" align="left">
				<strong>[address]<br>
					[city] [state]<br>
					[postcode]</strong>
			</div>
		</td>
		<td valign="top" style="border-collapse: collapse;">
			<div style="text-align: right;" align="right">
				<a href="mailto:[email]" style="<?php echo $footer_links; ?>">[email]</a><br>[phone]
			</div>
		</td>
	</tr>
</table>
</td>
</tr>
</table>
</div>
</body>
</html>
