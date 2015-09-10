<?php $post_type = 'tour'; ?>
<div class="sl-row">
	<div class="column-2">
		<h2><?php _e( 'Titles', '7listings' ); ?></h2>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Main', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for main archive', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_archive_main_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_archive_main_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert text, html or shortcodes', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<textarea name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$post_type}_archive_main_description"; ?>]" cols="40" rows="5" placeholder="<?php _e( 'Main archive description', '7listings' ); ?>"><?php echo esc_textarea( sl_setting( "{$post_type}_archive_main_description" ) ); ?></textarea>
				</div>
			</div>
		</div>

		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Type', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for tour type archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_type_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_type_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Feature', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Page title for tour features archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_features_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_features_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Location', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Paget title for location archives', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_location_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_location_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>

		<h2><?php _e( 'Tours', '7listings' ); ?></h2>

		<?php include THEME_TABS . 'parts/archive-atr-listings.php'; ?>
	</div>
	<div class="column-2">
		<?php include THEME_TABS . 'parts/archive-layout.php'; ?>
	</div>
</div>