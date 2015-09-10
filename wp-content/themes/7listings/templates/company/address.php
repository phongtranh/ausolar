<?php
$post_type = get_post_type();

$user_id    = get_post_meta( get_the_ID(), 'user', true );
$membership = get_user_meta( $user_id, 'membership', true );
if ( ! $membership )
	$membership = 'none';

$show_address = sl_setting( "{$post_type}_single_address_{$membership}" );
$show_phone   = sl_setting( "{$post_type}_single_phone_{$membership}" );
$show_website = sl_setting( "{$post_type}_single_url_{$membership}" );
$show_email   = sl_setting( "{$post_type}_single_email_{$membership}" );

if ( ! $show_address && ! $show_email && ! $show_phone && ! $show_website )
	return;

if ( $show_address )
{
	$tripadvisor = get_post_meta( get_the_ID(), 'tripadvisor', true );

	$address  = get_post_meta( get_the_ID(), 'address', true );
	$address2 = get_post_meta( get_the_ID(), 'address2', true );
	$address  = implode( ',', array_filter( compact( 'address', 'address2' ) ) );

	$city     = get_post_meta( get_the_ID(), 'city', true );
	$state    = get_post_meta( get_the_ID(), 'state', true );
	$postcode = get_post_meta( get_the_ID(), 'postcode', true );
}

if ( $show_website )
	$website = get_post_meta( get_the_ID(), 'website', true );
if ( $show_phone )
	$phone = get_post_meta( get_the_ID(), 'phone', true );
if ( $show_email )
	$email = get_post_meta( get_the_ID(), 'email', true );

$info  = array( 'phone', 'website', 'email', 'address', 'city', 'state', 'postcode', 'tripadvisor' );
$found = false;
foreach ( $info as $k )
{
	if ( ! empty( $$k ) )
	{
		$found = true;
		break;
	}
}
if ( ! $found )
	return;

echo '<section id="contact-info" class="company-meta contact-info">';

if ( ! empty( $phone ) )
	echo "<span class='phone' itemprop='telephone'>$phone</span>";
if ( ! empty( $website ) )
	echo '<span class="website"><a itemprop="sameAs" href="' . esc_url( $website ) . '" rel="nofollow" target="_blank">' . $website . '</a></span>';
if ( ! empty( $email ) )
	echo '<span class="email"><a itemprop="email" href="mailto:' . antispambot( $email ) . '">' . antispambot( $email ) . '</a></span>';

$address_block = array();

if ( ! empty( $address ) )
	$address_block[] = "<span class='street' itemprop='streetAddress'>$address</span>";
if ( ! empty( $city ) || ! empty( $state ) || ! empty( $postcode ) )
{
	// City
	if ( ! empty( $city ) )
		$address_block[] = "<span class='city' itemprop='addressLocality'>$city</span>";

	// State
	if ( ! empty( $state ) )
		$address_block[] = "<span class='state' itemprop='addressRegion'>$state</span>";

	// Post code
	if ( ! empty( $postcode ) )
		$address_block[] = "<span class='postcode' itemprop='postalCode'>$postcode</span>";
}
if ( ! empty( $address_block ) )
	echo '<span class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">' . implode( '', $address_block ) . '</span>';
if ( ! empty( $tripadvisor ) )
	echo '<span class="tripadvisor"><a href="' . esc_url( $tripadvisor ) . '" rel="nofollow" target="_blank">' . $tripadvisor . '</a></span>';
echo '</section>';
