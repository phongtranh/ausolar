<h2><?php _e( 'Activate functionality', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Save settings to see additional options', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h2>

<?php do_action( 'sl_settings_modules' ); ?>

<?php if ( sl_setting( 'listing_types' ) ): ?>

	<h3 class="nav-tab-wrapper sl-tabs">
		<?php do_action( 'sl_settings_listings_tab' ); ?>
	</h3>
	<div class="sl-tabs-content">
		<?php do_action( 'sl_settings_listings_tab_content' ); ?>
	</div>

<?php endif; ?>
