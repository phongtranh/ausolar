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
	$address  = implode( ', ', array_filter( compact( 'address', 'address2' ) ) );

	$area     = get_post_meta( get_the_ID(), 'area', true );
	$state    = get_post_meta( get_the_ID(), 'state', true );
	$postcode = get_post_meta( get_the_ID(), 'postcode', true );
}

if ( $show_website )
	$website = get_post_meta( get_the_ID(), 'website', true );
if ( $show_phone )
	$phone = get_post_meta( get_the_ID(), 'phone', true );
	$pattern = '/(\\d{2})(\\d{4})(\\d{4})/';
	$phone = preg_replace( $pattern, '$1 $2 $3', $phone );

if ( $show_email )
	$email = get_post_meta( get_the_ID(), 'email', true );

$info  = array( 'phone', 'website', 'email', 'address', 'area', 'state', 'postcode', 'tripadvisor' );
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
	echo "<span class='phone'>$phone</span>";
if ( ! empty( $website ) )
	echo '<span class="website"><a itemprop="sameAs" href="' . esc_url( $website ) . '" rel="nofollow" target="_blank">Visit website</a></span>';
if ( ! empty( $email ) )
{
	echo '<span class="email"><a href="#modal-email" data-toggle="modal">' . __( 'Email this business', '7listings' ) . '</a></span>';
	echo "<meta itemprop='email' content='$email'>";
}

$address_block = array();

if ( ! empty( $address ) )
	$address_block[] = "<span class='street'>$address</span>";
if ( ! empty( $area ) || ! empty( $state ) || ! empty( $postcode ) )
{
	$region = array();

	// Area
	if ( ! empty( $area ) )
		$region[] = "<span class='area'>$area</span>";

	// State
	if ( ! empty( $state ) )
	{
		$state = asq_states_format( $state );
		$region[] = "<span class='state'>$state</span>";
	}

	// Post code
	if ( ! empty( $postcode ) )
		$region[] = "<span class='postcode'>$postcode,</span><span class='country' itemprop='country'>Australia</span>";

	$address_block[] = '<span class="region">' . implode( ', ', $region ) . '</span>';
}
if ( ! empty( $address_block ) )
	echo '<span class="address">' . implode( '', $address_block ) . '</span>';
if ( ! empty( $tripadvisor ) )
	echo '<span class="tripadvisor"><a href="' . esc_url( $tripadvisor ) . '" rel="nofollow" target="_blank">' . $tripadvisor . '</a></span>';
echo '</section>';

if ( ! empty( $email ) )
{
	?>
	<div id="modal-email" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<form action="" method="post" class="email-company" id="email-company">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php printf( __( 'Email %s', '7listings' ), get_the_title() ); ?></h3>
			</div>
			<div class="modal-body">

				<div id="status"></div>

				<label><?php _e( 'Your name', '7listings' ); ?> <span class="required">*</span></label>
				<input type="text" name="name" required>
				<label><?php _e( 'Email', '7listings' ); ?> <span class="required">*</span></label>
				<input type="text" name="email" required>
				<label><?php _e( 'Contact number', '7listings' ); ?></label>
				<input type="text" name="phone">
				<label><?php _e( 'Your message', '7listings' ); ?></label>
				<textarea name="message" required rows="5"></textarea>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Close', '7listings' ); ?></button>
				<input type="submit" class="button green" value="<?php _e( 'Submit', '7listings' ); ?>">
			</div>
		</form>
	</div>
<?php
}
