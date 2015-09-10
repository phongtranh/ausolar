<?php
/*
 * Template name: Thank You Booking
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	the_post();

	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'entry-content';
	$content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );
	?>

	<article id="content" <?php post_class( $content_class ); ?>>

		<?php
		if ( ! empty( $_SESSION['name'] ) || ! empty( $_SESSION['email'] ) || ! empty( $_SESSION['invoice'] ) )
		{
			echo '<h3>' . __( 'Booking information', '7listings' ) . '</h3>';

			printf(
				'<div id="booking-details">%s%s%s</div>',
				! empty( $_SESSION['name'] ) ? '<p class="name">' . $_SESSION['name'] . '</p>' : '',
				! empty( $_SESSION['email'] ) ? '<p class="email">' . $_SESSION['email'] . '</p>' : '',
				! empty( $_SESSION['invoice'] ) ? '<p class="invoice">' . __( 'Invoice:', '7listings' ) . ' ' . $_SESSION['invoice'] . '</p>' : ''
			);
		}
		?>

		<p><?php _e( 'If you have any further questions or special requests,<br>please do not hesitate to contact us.<br><br>Our friendly staff will gladly assist You.', '7listings' ); ?></p>

		<div id="contact-info" class="contact-info">
			<h3><?php bloginfo( 'name' ); ?></h3>

			<?php
			if ( sl_setting( 'phone' ) || sl_setting( 'fax' ) )
			{
				printf(
					'<p class="numbers">%s%s</p>',
					sl_setting( 'phone' ) ? '<span class="phone">' . sl_setting( 'phone' ) . '</span>' : '',
					sl_setting( 'fax' ) ? '<span class="fax">' . sl_setting( 'fax' ) . '</span>' : ''
				);
			}

			if ( sl_setting( 'address' ) || sl_setting( 'general_city' ) || sl_setting( 'state' ) || sl_setting( 'country' ) )
			{
				printf(
					'<p class="address">%s%s%s%s</p>',
					sl_setting( 'address' ) ? '<span class="street">' . nl2br( sl_setting( 'address' ) ) . '</span><br>' : '',
					sl_setting( 'general_city' ) ? '<span class="city">' . sl_setting( 'general_city' ) . '</span>' : '',
					sl_setting( 'state' ) ? '<span class="state">' . sl_setting( 'state' ) . '</span>' : '',
					sl_setting( 'country' ) ? '<br><span class="country">' . sl_setting( 'country' ) . '</span>' : ''
				);
			}
			?>
		</div>

	</article>

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div>

<?php get_footer(); ?>
