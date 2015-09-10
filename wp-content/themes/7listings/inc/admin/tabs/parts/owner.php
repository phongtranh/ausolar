<?php
$user = intval( get_post_meta( get_the_ID(), 'user', true ) );

/**
 * Revert to use wp_dropdown_users() for HUGE performance improvements
 *
 * @since 5.0.8
 */
wp_dropdown_users( array(
	'selected'          => $user,
	'show_option_none'  => __( 'None', '7listings' ),
	'option_none_value' => '',
) );
?>
<a href="<?php echo admin_url( 'user-new.php' ); ?>" target="_blank" class="dashicons dashicons-businessman" title="<?php esc_attr_e( 'Create New User to manage this listing', '7listings' ); ?>"></a>

<?php if ( $user && -1 != $user ) : ?>

	<?php echo get_avatar( $user, 32 ); ?>

	<div class="owner-info">
		<?php
		$user = get_userdata( $user );
		$name = $user->display_name;
		if ( $user->first_name && $user->last_name )
			$name = $user->first_name . ' ' . $user->last_name;
		echo '<span class="name">' . $name . '</span>';
		echo '<span class="email"><a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a></span>';
		if ( $user->phone )
			echo '<span class="phone">' . $user->phone . '</span>';
		?>
	</div>

<?php endif; ?>
