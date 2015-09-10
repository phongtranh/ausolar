<?php
/**
 * Email Header
 *
 * @author        7Listings
 * @package       7Listings/Templates/Emails
 * @version       1.0
 */

if ( !defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

// Load colours
//$base_text = sl_light_or_dark( $base, '#202020', '#ffffff' );
//$text = sl_setting( 'emails_body_text_color' );
//
//$text_darker = sl_hex_darker( $text, 20 );
//$text_lighter = sl_hex_lighter( $text, 40 );

$body_background = sl_setting( 'emails_body_background' );
$heading_color = sl_setting( 'emails_heading_color' );
$body_text_color = sl_setting( 'emails_body_text_color' );


$heading_lighter_20 = sl_hex_lighter( $heading_color, 30 );

$text_darker = sl_hex_darker( $body_text_color, 20 );
$text_lighter = sl_hex_lighter( $body_text_color, 40 );

$row_border = sl_hex_darker( $body_background, 10 );
$row_background = sl_hex_darker( $body_background, 3 );

?>

<span style="font-size: 11px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">Hi [first_name],</span></span>

<span style="font-size: 11px;"> <span style="font-size: 11px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">Welcome aboard!</span></span></span>

<span style="font-size: 11px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">Get started by logging in to our Wholesaler Portal by clicking on the below link:</span></span>

<strong><span style="font-size: 11px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;"><a href="http://www.australiansolarquotes.com.au/affiliates/">Wholesaler Portal</a></span></span></strong>

&nbsp;

<span style="font-size: 11px;"><span style="font-family: lucida sans unicode,lucida grande,sans-serif;">We look forward to working with you</span></span>
<img class="wp-image-17355 alignnone" src="http://www.australiansolarquotes.com.au/wp-content/uploads/2014/07/Australian-Solar-Quotes-Logo-13.png" alt="Australian Solar Quotes Logo 13" width="242" height="89" />