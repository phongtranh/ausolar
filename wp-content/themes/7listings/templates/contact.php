<?php
/*
 * Template name: Contact Us
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<?php
	$sidebar_layout = sl_sidebar_layout();
	$content_class  = 'none' == $sidebar_layout ? 'full' : ( 'right' == $sidebar_layout ? 'left' : 'right' );
	$content_class  = $content_class ? ' class="' . $content_class . '"' : '';
	?>

	<div id="content"<?php echo $content_class; ?> itemscope itemtype="http://schema.org/LocalBusiness">

		<article class="row-fluid entry-content">

			<section class="info span6">

				<?php
				the_post();
				the_content();
				?>

				<?php
				echo '<section class="contact-info" id="contact-info">';
				echo '<meta itemprop="name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
				echo '<meta itemprop="description" content="' . esc_attr( get_bloginfo( 'description' ) ) . '">';

				if ( sl_setting( 'phone' ) || sl_setting( 'fax' ) )
					echo '<p class="numbers">';

				if ( sl_setting( 'phone' ) )
					echo '<span class="phone" itemprop="telephone">' . esc_html( sl_setting( 'phone' ) ) . '</span>';

				if ( sl_setting( 'fax' ) )
					echo '<span class="fax" itemprop="faxNumber">' . esc_html( sl_setting( 'fax' ) ) . '</span>';

				if ( sl_setting( 'phone' ) || sl_setting( 'fax' ) )
					echo '</p>';

				if ( sl_setting( 'email' ) )
					echo '<meta itemprop="email" content="' . esc_attr( sl_setting( 'email' ) ) . '">';

				if ( sl_setting( 'address' ) || sl_setting( 'general_city' ) || sl_setting( 'state' ) || sl_setting( 'country' ) )
					echo '<p class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

				if ( sl_setting( 'address' ) )
					echo '<span class="street" itemprop="streetAddress">' . nl2br( esc_html( sl_setting( 'address' ) ) ) . '</span>';

				if ( sl_setting( 'general_city' ) )
					echo '<span class="city" itemprop="addressLocality">' . esc_html( sl_setting( 'general_city' ) ) . '</span>';

				$state = array();

				if ( sl_setting( 'state' ) )
					$state[] = '<span class="state" itemprop="addressRegion">' . esc_html( sl_setting( 'state' ) ) . '</span>';

				if ( sl_setting( 'postcode' ) )
					$state[] = '<span class="postcode" itemprop="postalCode">' . esc_html( sl_setting( 'postcode' ) ) . '</span>';

				echo implode( ', ', $state );

				if ( sl_setting( 'country' ) )
					echo '<span class="country" itemprop="addressCountry">' . esc_html( sl_setting( 'country' ) ) . '</span>';
				if ( sl_setting( 'address' ) || sl_setting( 'general_city' ) || sl_setting( 'state' ) || sl_setting( 'country' ) )
					echo '</p>';
				echo '</section>';

				if ( sl_setting( 'business_hours' ) )
				{
					$html        = '';
					$time_format = get_option( 'time_format' );
					if ( sl_setting( 'open_247' ) )
					{
						$html = sprintf(
							'<div class="day">
								<span class="label">%s</span>
								<span class="detail"><time itemprop="openingHours" datetime="Mo-Su">%s</time></span>
							</div>',
							__( 'Monday - Sunday', '7listings' ),
							__( 'All day', '7listings' )
						);
					}
					else
					{
						$days = array(
							'mo' => __( 'Monday', '7listings' ),
							'tu' => __( 'Tuesday', '7listings' ),
							'we' => __( 'Wednesday', '7listings' ),
							'th' => __( 'Thursday', '7listings' ),
							'fr' => __( 'Friday', '7listings' ),
							'sa' => __( 'Saturday', '7listings' ),
							'su' => __( 'Sunday', '7listings' ),
						);
						$open = false;
						foreach ( $days as $k => $v )
						{
							if ( sl_setting( "business_hours_{$k}" ) )
							{
								$open = true;
								break;
							}
						}
						// If at least 1 day open, then show hours
						if ( $open )
						{
							foreach ( $days as $k => $v )
							{
								if ( ! sl_setting( "business_hours_{$k}" ) )
								{
									$html .= sprintf(
										'<div class="day">
										<span class="label">%s</span>
										<span class="detail">%s</span>
									</div>',
										esc_html( $v ), esc_html__( 'Closed', '7listings' )
									);
									continue;
								}

								$from = sl_setting( "business_hours_{$k}_from" );
								$to   = sl_setting( "business_hours_{$k}_to" );

								$html .= sprintf(
									'<div class="day">
									<span class="label">%s</span>
									<span class="detail"><time itemprop="openingHours" datetime="%s %s">%s</time></span>
								</div>',
									esc_html( $v ), ucfirst( $k ), esc_attr( "$from-$to" ), date( $time_format, strtotime( $from ) ) . ' - ' . date( $time_format, strtotime( $to ) )
								);
							}
						}

						$html .= '<p class="special-days">' . esc_html( sl_setting( 'special_days' ) ) . '</p>';
					}

					if ( $html )
					{
						echo '<section id="business-hours" class="business-hours">';
						echo '<h2>' . __( 'Business Hours', '7listings' ) . '</h2>' . $html;

						printf(
							'<div class="current day time" id="current-time">
					 <h4>%s</h4>
					<span class="label"></span>
					<span class="detail"></span>
				</div>',
							__( 'Current Time', '7listings' )
						);

						echo '</section>';
					}
				}

				do_action( 'sl_contact_page_before_map' );

				// Google Maps
				if ( sl_setting( 'google_map' ) )
				{
					echo '<section id="location-map">';
					echo '<h2>' . __( 'Location', '7listings' ) . '</h2>';
					$loc = sl_setting( 'address' );
					$loc .= ', ' . sl_setting( 'general_city' );
					$loc .= ', ' . sl_setting( 'country' );
					sl_map( array(
						'type'    => 'address',
						'address' => $loc,
						'height'  => '350px',
						'class'   => 'map',
					) );
					echo '</section>';
				}

				if ( current_user_can( 'manage_options' ) )
					echo '<span class="edit-link button small"><a class="post-edit-link" href="' . admin_url( 'edit.php?post_type=page&page=contact' ) . '">' . __( 'Edit Page', '7listings' ) . '</a></span>';
				?>
			</section>

			<section id="contact-form" class="span6">

				<?php if ( sl_setting( 'contact_custom_contact_form' ) && sl_setting( 'contact_form_shortcode' ) ) : ?>

					<?php echo do_shortcode( sl_setting( 'contact_form_shortcode' ) ); ?>

				<?php else : ?>

					<form action="" method="post" class="sl-form contact">
						<input type="hidden" name="action" value="sl_contact_submit">

						<header class="section">
							<h2 class="title"><?php _e( 'Send A Message', '7listings' ); ?></h2>
							<p class="form-intro"><?php _e( 'We would love to hear from You!<br>Please fill out this form, and we will get in touch with You shortly.', '7listings' ); ?></p>
						</header>
						<div class="section name-wrapper">
							<label for="first" class="name-label"><?php _e( 'Name', '7listings' ); ?>
								<span class="required">*</span></label>

							<div class="one-half left">
								<input type="text" name="first" id="first" class="name first" placeholder="First" required autocomplete="given-name">
							</div>
							<div class="one-half right">
								<input type="text" name="last" id="last" class="name last" placeholder="Last" autocomplete="family-name">
							</div>
						</div>

						<div class="section ">
							<div class="one-half left">
								<label for="email"><?php _e( 'Email', '7listings' ); ?>
									<span class="required">*</span></label>
								<input type="email" name="email" id="email" required autocomplete="email">
							</div>
							<div class="one-half right">
								<label for="phone"><?php _e( 'Phone', '7listings' ); ?></label>
								<input type="text" name="phone" id="phone" class="phone" autocomplete="tel">
							</div>
						</div>

						<div class="section subject">
							<label for="subject"><?php _e( 'Subject', '7listings' ); ?>
								<span class="required">*</span></label> <input type="text" name="subject" id="subject">
						</div>

						<div class="section message">
							<label for="message"><?php _e( 'Message', '7listings' ); ?>
								<span class="required">*</span></label>
							<textarea name="message" id="message" class="message"></textarea>
						</div>
						<p class="section subscribe">
							<input type="checkbox" id="newsletter-subscribe" name="subscribe" value="1" checked="checked">
							<label for="newsletter-subscribe"><?php _e( 'Yes, subscribe me to your newsletter to receive the latest specials and news', '7listings' ); ?></label>
						</p>
						<footer class="section">
							<div class="hide" id="status-error"></div>
							<button name="sl-submit" id="submit" class="button contact large hide-submit"><?php _e( 'Submit', '7listings' ); ?></button>
						</footer>
					</form>
					<div class="hide" id="status-success"></div>
				<?php endif; ?>

			</section>

		</article>

	</div><!-- #content -->

	<?php if ( 'none' != $sidebar_layout ) : ?>
		<aside id="sidebar" class="<?php echo $sidebar_layout ?>">
			<?php get_sidebar(); ?>
		</aside>
	<?php endif; ?>

</div><!-- #main-wrapper -->

<?php get_footer(); ?>
