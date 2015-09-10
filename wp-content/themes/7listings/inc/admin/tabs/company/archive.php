<?php $post_type = 'company'; ?>
<div class="sl-row">
	<div class="column-2">
		<h2><?php _e( 'Titles', '7listings' ); ?></h2>
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Main Archive', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" id="<?php echo $post_type; ?>_archive_main_title" name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_archive_main_title]" value="<?php echo sl_setting( "{$post_type}_archive_main_title" ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
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
		
		<hr class="light">
		
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Location', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_location_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_location_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Brand', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_brand_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_brand_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Product', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_product_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_product_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Service', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" class="sl-input-large" name="<?php echo THEME_SETTINGS . "[{$post_type}_service_title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$post_type}_service_title" ) ); ?>">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For page title<br>use SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
	</div>
	<div class="column-2">
		
		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>
		
		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category or taxonomy description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $post_type . '_archive_cat_desc' ); ?>
			</div>
		</div>
		
		<br><br>
		
		<h3><?php _e( 'Main', '7listings' ); ?></h3>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Companies', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter amount of companies<br>to display per page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_archive_num]" value="<?php echo sl_setting( "{$post_type}_archive_num" ); ?>" class="sl-input-small">
					<span class="add-on"><?php _e( ' / page', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Priority Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable display order:<br>Gold > Silver > Bronze > None', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( $post_type . '_archive_priority' ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting type for companies', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_archive_orderby]">
					<?php
					$items = array(
						'date'           => __( 'Recent', '7listings' ),
						'views'          => __( 'Popular', '7listings' ),
						'alphabetically' => __( 'Alphabetically (A-Z)', '7listings' ),
						'city'           => __( 'City', '7listings' ),
					);
					$items = apply_filters( 'company_edit_archive_main_sorting_items', $items );
					Sl_Form::options( sl_setting( "{$post_type}_archive_orderby" ), $items );
					?>
				</select>
			</div>
		</div>
		
		<hr class="light">
		
		<br>
		
		<div class="sl-settings sidebar layout">
			<div class="sl-label">
				<label><?php _e( 'Sidebar', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sidebar layout<br>for archive pages', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::sidebar_layout( "{$post_type}_archive_sidebar_layout" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Widget Area', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select widget area<br>for sidebar', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::sidebar_select( "{$post_type}_archive_sidebar" ); ?>
				</div>
			</div>
		</div>
	</div>
</div>