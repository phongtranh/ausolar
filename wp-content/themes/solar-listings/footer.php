<?php global $settings; ?>

	</main><!-- #main -->

	<footer id="colophon">
	
		<?php if ( !is_front_page() || $settings['homepage_footer'] || get_query_var( 'news' ) ) : ?>

			<div id="top">
				<div class="container">
					<?php get_sidebar( 'footer' ); ?>
				</div>
			</div><!-- #top -->

		<?php endif; ?>

		<?php
		if ( Sl_License::license_type() == '7Network' )
			do_action( '7listings_footer_middle' );
		?>

		<div id="bottom">
			<div class="container">
				&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> - <?php bloginfo( 'description' ); ?>
				<div id="sl-back-link"><a href="/" target="_blank" title="Wordpress business directory theme by 7 Listings"><span id="power">Powered by</span><span id="seven">7 Listings</span></a></div>

				<?php do_action( '7listings_footer_bottom_bottom' ); ?>
			</div>

		</div><!-- #bottom -->

		<?php do_action( '7listings_after_footer_bottom' ); ?>

	</footer><!-- #colophon -->
</div><!-- #wrapper -->

<?php if ( wp_is_mobile() && is_single() && in_category(array('environment', 'politics', 'technology', 'transport', 'international-solar-news')) ): ?>
<div class="adrotate_bottom" id="adrotate_bottom">
	<span class='close-adrotate'>x</span>
	<?php echo adrotate_group(8); ?>
</div>
<?php elseif ( wp_is_mobile() && is_page(18033) ): ?>
<div class="adrotate_bottom" id="adrotate_bottom">
	<span class='close-adrotate'>x</span>
	<?php echo adrotate_group(21); ?>
</div>
<?php elseif ( wp_is_mobile() && !url_contains( array( '/my-account/', '/support/', '/intranet/', '/solar-quotes/', '/solar-quotes-split/' ) ) ): ?>
<div class="adrotate_bottom" id="adrotate_bottom">
	<span class='close-adrotate'>x</span>
	<?php echo adrotate_group(6); ?>
</div>
<?php endif; ?>

<nav id="nav-mobile">

	<?php get_search_form(); ?>

	<?php
	if ( function_exists( 'peace_nav' ) )
	{
		$args = array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'slide-nav',
			'walker'         => new Peace_Bootstrap_Nav_Walker,
		);
		if ( sl_setting( 'design_mobile_custom_menu' ) && sl_setting( 'design_mobile_menu' ) )
		{
			$args['theme_location'] = '';
			$args['menu'] = sl_setting( 'design_mobile_menu' );
		}
		peace_nav( $args );
	}
	else
	{
		$args = array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'slide-nav',
			'walker'         => new FitWP_Bootstrap_Nav_Walker,
			'fallback_cb'    => 'fitwp_bootstrap_menu_callback',
		);
		if ( sl_setting( 'design_mobile_custom_menu' ) && sl_setting( 'design_mobile_menu' ) )
		{
			$args['theme_location'] = '';
			$args['menu'] = sl_setting( 'design_mobile_menu' );
		}

		if(is_user_logged_in() && current_user_can( 'company_owner' ))
		{
			$args['theme_location'] = 'primary_mobile';
			$args['menu'] = 'None';
		}
		wp_nav_menu( $args );
	}
	?>

	<?php get_search_form(); ?>

</nav>

<div class="ajax-loading"></div>

<?php wp_footer(); ?>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-WVR9JX"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WVR9JX');</script>
<!-- End Google Tag Manager -->

<?php if ( false !== strpos( $_SERVER['REQUEST_URI'], '/my-account' ) ) : ?>
	<script>
	var __lc = {};
	__lc.license = 3434152;
	</script>
	<script defer async src="//cdn.livechatinc.com/tracking.js"></script>
<?php endif; ?>

<?php 
if ( is_singular() ) : 
	$custom_script = get_post_meta( get_the_ID(), 'custom_script_footer', true );
	if ( ! empty( $custom_script ) ) 
		echo $custom_script;
endif;

if ( ! url_contains( array( '/intranet', '/my-account') ) )
	ASQ\Bubble\Bubble::make();
?>
<script>
	window.onload = function(){
		$(".close-adrotate").on("click", function () {
			this.parentNode.parentNode
	        .removeChild(this.parentNode);
	        return false;
		});
	};
</script>
</body>
</html>
