<div class="sl-row">
	<div class="column-2">
		<?php include THEME_TABS . 'parts/address.php'; ?>
	</div>
	<div class="column-2">
		<?php include THEME_TABS . 'parts/map.php'; ?>
	</div>
</div>

<?php include THEME_TABS . 'parts/contact.php'; ?>

<hr class="light">
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Tripadvisor', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="url" name="tripadvisor" class="sl-input-large tripadvisor" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'tripadvisor', true ) ); ?>" placeholder="<?php esc_attr_e( 'Tripadvisor page URL', '7listings' ); ?>">
	</div>
</div>
<?php
$key  = sl_meta_key( 'logo', get_post_type() );
$logo = get_post_meta( get_the_ID(), $key, true );
$src  = '';
if ( $logo )
	$src = wp_get_attachment_url( $logo );
?>

<hr>

<div class="sl-settings logo upload">
	<div class="sl-label">
		<label><?php _e( 'Logo', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<img src="<?php echo esc_attr( $src ); ?>"<?php echo $src ? '' : ' class="hidden"'; ?>>
		<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $logo ); ?>">
		<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
		<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
	</div>
</div>
