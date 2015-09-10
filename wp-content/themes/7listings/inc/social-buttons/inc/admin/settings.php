<?php

/**
 * Create settings page under 7listings menu and handle all settings for social buttons
 * This class does not handle design settings. Those settings are handled in 'design-settings.php'
 */
class Sl_Settings_Social extends Sl_Settings_Page
{
	/**
	 * Display main settings content
	 *
	 * @return void
	 */
	public function page_content()
	{
		?>
		<h2 class="nav-tab-wrapper sl-tabs">
			<a href="#settings" class="nav-tab"><?php _e( 'Profiles', '7listings' ); ?></a>
			<a href="#sharing-buttons" class="nav-tab"><?php _e( 'Sharing Buttons', '7listings' ); ?></a>
		</h2>
		<div class="sl-tabs-content">
			<div>
				<?php include SSB_DIR . 'inc/admin/tabs/social.php'; ?>
			</div>
			<div>
				<?php include SSB_DIR . 'inc/admin/tabs/social-buttons.php'; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Sanitize options, remove spaces for social links
	 *
	 * @param array $options_new
	 * @param array $options
	 *
	 * @return array
	 */
	public function sanitize( $options_new, $options )
	{
		// Social networks supported
		$networks = array(
			'facebook',
			'twitter',
			'googleplus',
			'pinterest',
			'linkedin',
			'instagram',
			'rss',
		);
		foreach ( $networks as $network )
		{
			if ( isset( $options_new[$network] ) )
			{
				$options_new[$network] = trim( $options_new[$network] );
			}
		}

		return $options_new;
	}
}
