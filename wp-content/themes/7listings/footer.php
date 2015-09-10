<?php /*?>
 * Site Footer Template
 * last edit: 5.0
 *
 * @package WordPress
 * @subpackage 7Listings
<?php */?>

	</main><!-- #main -->

	<footer id="colophon" role="contentinfo">

		<?php
		/**
		 * Hide footer top section if:
		 * - Booking page
		 * - Is 7listings front page and not enable footer for homepage (settings is in 7listings > Pages > Homepage)
		 */
		$hide   = get_query_var( 'book' );
		$bundle = get_query_var( 'book_bundle', false );
		$hide   = $hide || ( is_front_page() && sl_setting( 'homepage_enable' ) && ! sl_setting( 'homepage_footer' ) ) || $bundle;
		if ( ! $hide ) :
		?>

			<section id="top" class="footer footer-section">
				<div class="container">
					<?php get_sidebar( 'footer' ); ?>
				</div>
			</section>

		<?php endif; ?>

		<?php
		/**
		 * Show footer top section only when
		 * - Not in booking page
		 * - License is network
		 */
		if ( ! get_query_var( 'book' ) && Sl_License::license_type() == '7Network' && ! $bundle )
		{
			do_action( '7listings_footer_middle' );
		}
		?>

		<section id="bottom" class="footer-section">
			<div class="container">
				&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo HOME_URL; ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> - <?php bloginfo( 'description' ); ?>
				<div id="sl-back-link"><a href="http://7listings.net" target="_blank" title="<?php esc_attr_e( 'Wordpress themes by 7 Listings', '7listings' ); ?>"><span id="power"><?php _e( 'Powered by', '7listings' ); ?></span><span id="seven">7 Listings</span></a></div>

				<?php do_action( '7listings_footer_bottom_bottom' ); ?>
			</div>

		</section><!-- #bottom -->

		<?php do_action( '7listings_after_footer_bottom' ); ?>

	</footer><!-- #colophon -->
</div><!-- #wrapper -->

<nav id="nav-mobile">

	<?php get_search_form(); ?>

	<?php
	$args = array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'slide-nav',
		'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
		'walker'         => new FitWP_Bootstrap_Nav_Walker,
		'fallback_cb'    => 'sl_page_menu',
	);
	if ( sl_setting( 'design_mobile_custom_menu' ) && sl_setting( 'design_mobile_menu' ) )
	{
		$args['theme_location'] = '';
		$args['menu'] = sl_setting( 'design_mobile_menu' );
	}
	wp_nav_menu( $args );
	?>

	<?php get_search_form(); ?>

</nav>

<div class="ajax-loading"></div>

<?php wp_footer(); ?>
</body>
</html>
