<div id="homepage-widgets">
	<?php
	foreach ( sl_setting( 'homepage_order' ) as $id )
	{
		sl_homepage_settings_box( $id );
	}
	?>
</div>
<div class="sl-settings homepage-footer">
	<div class="sl-label">
		<label><?php _e( 'Footer', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display footer area with widgets<br>on the homepage', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( 'homepage_footer' ); ?>
	</div>
</div>

<?php
/**
 * Display box content, based on id
 *
 * @param string $id Box ID
 *
 * @return void
 */
function sl_homepage_settings_box( $id )
{
	$prefix = 'homepage_' . $id . '_';

	if ( 'custom_content' == $id )
	{
		?>
		<div class="box">
			<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="custom_content">
			<span class="add-md toggle"></span>
			<span class="heading"><?php _e( 'Main Content', '7listings' ); ?></span>
			<span class="on-off">
				<?php _e( 'Display:', '7listings' ); ?>
				<?php Sl_Form::checkbox( 'homepage_custom_content_active' ); ?>
			</span>

			<section class="widget-settings hidden">
				<div class="sl-settings sl-widget-title">
					<div class="sl-label">
						<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<input type="text" name="<?php echo THEME_SETTINGS; ?>[homepage_heading]" value="<?php echo sl_setting( 'homepage_heading' ); ?>" class="sl-input-large">
						<?php Sl_Form::select_heading_style( 'homepage' ) ?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label>&nbsp;</label>
					</div>
					<div class="sl-input sl-block">
						<?php
						$args = array(
							'textarea_name' => THEME_SETTINGS . '[homepage_content]',
						);
						wp_editor( sl_setting( 'homepage_content' ), 'homepage_content', $args );
						?>
					</div>
				</div>
				<br />
				<div class="sl-settings sidebar layout visible">
					<div class="sl-label">
						<label><?php _e( 'Sidebar Layout', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::sidebar_layout( 'homepage_custom_content_sidebar_layout' ); ?>
					</div>
				</div>
				<div class="sl-sub-settings sl-sidebar-options">
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Sidebar', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<?php Sl_Form::sidebar_select( 'homepage_custom_content_sidebar' ); ?>
						</div>
					</div>
				</div>
			</section>
		</div>
		<?php
		return;
	}

	if ( 'featured_area' == $id )
	{
		?>
		<!-- Featured Area -->
		<div class="box">
			<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="featured_area">
			<span class="add-md toggle"></span>
			<span class="heading"><?php _e( 'Featured Title', '7listings' ); ?></span>
			<span class="on-off">
				<?php _e( 'Display:', '7listings' ); ?>
				<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
			</span>

			<section class="widget-settings hidden">
				<div class="sl-settings sl-widget-title">
					<div class="sl-label">
						<label><?php _e( 'Title', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}heading]"; ?>" value="<?php echo sl_setting( "{$prefix}heading" ); ?>" class="sl-input-large">
						<?php Sl_Form::select_heading_style( "{$prefix}heading" ) ?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Custom Text', '7listings' ); ?></label>
					</div>
					<div class="sl-input sl-block">
						<?php
						wp_editor( sl_setting( "{$prefix}custom_text" ), "{$prefix}custom_text", array(
							'textarea_name' => THEME_SETTINGS . '[homepage_featured_area_custom_text]',
						) );
						?>
					</div>
				</div>
				<br />
				<div class="sl-settings sl-slideshow">
					<div class="sl-label">
						<label><?php _e( 'Slideshow', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php
						$slideshows = get_posts( 'post_type=slideshow' );
						if ( empty( $slideshows ) )
						{
							_e( 'No slideshows', '7listings' );
						}
						else
						{
							echo '<select name="' . THEME_SETTINGS . "[{$prefix}slideshow]" . '">';
							echo '<option value="">' . __( 'None', '7listings' ) . '</option>';
							$option_tpl = '<option value="%s"%s>%s</option>';
							$selected   = sl_setting( "{$prefix}slideshow" );
							foreach ( $slideshows as $slideshow )
							{
								printf(
									$option_tpl,
									$slideshow->ID,
									selected( $selected, $slideshow->ID, false ),
									esc_html( $slideshow->post_title )
								);
							}
							echo '</select>';
						}
						?>
						<a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=slideshow' ); ?>" title="<?php _e( 'Edit Slideshows', '7listings' ); ?>" class="edit-icon-md"><?php _e( 'Edit Slideshows', '7listings' ); ?></a>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Featured Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the featured image<br>as a full size background', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input upload">
						<?php
						$src = '';
						if ( $value = intval( sl_setting( "{$prefix}image" ) ) )
						{
							list( $src ) = wp_get_attachment_image_src( $value, 'sl_thumb_tiny' );
						}
						?>
						<input type="hidden" name="<?php echo THEME_SETTINGS . '[homepage_featured_area_image]'; ?>" value="<?php echo $value; ?>">
						<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
						<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
						<img src="<?php echo $src; ?>" class="<?php echo $src ? '' : ' hidden'; ?>">
					</div>
				</div>
				<div class="sl-settings sl-widget-height">
					<div class="sl-label">
						<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Default height is set in:<br>Design > Featured', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS; ?>[homepage_featured_area_height]" class="sl-input-small">
							<?php
							$sizes = array(
								'tiny'  => __( 'Tiny (50%)', '7listings' ),
								'small' => __( 'Small (75%)', '7listings' ),
								''      => __( 'Default', '7listings' ),
								'large' => __( 'Large (150%)', '7listings' ),
								'huge'  => __( 'Huge (200%)', '7listings' ),
							);
							Sl_Form::options( sl_setting( "{$prefix}height" ), $sizes );
							?>
						</select>
					</div>
				</div>
			</section>
		</div>
		<?php
		return;
	}

	if ( 'custom_html' == $id )
	{
		?>
		<div class="box">
			<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="custom_html">
			<span class="add-md toggle"></span>
			<span class="heading"><?php _e( 'Custom HTML', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert any html', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></span>
			<span class="on-off">
				<?php _e( 'Display:', '7listings' ); ?>
				<?php Sl_Form::checkbox( 'homepage_custom_html_active' ); ?>
			</span>

			<section class="widget-settings hidden">
				<div class="sl-settings">
					<div class="sl-label">
						<label>&nbsp;</label>
					</div>
					<div class="sl-input sl-block">
						<textarea id="custom-html" cols="120" rows="5" name="<?php echo THEME_SETTINGS . '[homepage_custom_html]'; ?>"><?php echo esc_textarea( sl_setting( 'homepage_custom_html' ) ); ?></textarea>
					</div>
				</div>
			</section>
		</div>
		<?php
		return;
	}

	if ( 'listings_search' == $id )
	{
		$name = THEME_SETTINGS . "[$prefix";

		$post_types = sl_setting( 'listing_types' );

		// Ignore 'product' and 'company' post types
		$post_types = array_diff( $post_types, array( 'product', 'company' ) );

		if ( empty( $post_types ) )
			return;
		?>
		<div class="box">
			<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo $id; ?>">
			<span class="add-md toggle"></span>
			<span class="heading"><?php _e( 'Listings Search', '7listings' ); ?></span>
			<span class="on-off">
				<?php _e( 'Display:', '7listings' ); ?>
				<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
			</span>

			<section class="widget-settings hidden">
				<div class="sl-row">
					<div class="column-2">

						<div class="sl-settings sl-widget-title title">
							<div class="sl-label">
								<label><?php _e( 'Title', '7listings' ) ?></label>
							</div>
							<div class="sl-input">
								<input type="text" name="<?php echo esc_attr( "{$name}title]" ); ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}title" ) ); ?>" class="sl-input-large">
								<?php Sl_Form::select_heading_style( 'homepage_listings_search_title' ) ?>
							</div>
						</div>
						<?php if ( 1 < count( $post_types ) ) : ?>
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Post Types', '7listings' ); ?></label>
								</div>
								<div class="sl-input">&nbsp;</div>
							</div>
							<div class="sl-sub-settings">
								<?php
								foreach ( $post_types as $post_type )
								{
									$post_type_object = get_post_type_object( $post_type );
									$id               = uniqid( mt_rand(), true );
									?>
									<div class="sl-settings">
										<div class="sl-label">
											<label><?php echo esc_html( $post_type_object->labels->singular_name ); ?></label>
										</div>
										<div class="sl-input">
											<span class="checkbox">
												<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( "{$name}post_types][]" ); ?>" value="<?php echo esc_attr( $post_type ); ?>"<?php checked( in_array( $post_type, sl_setting( "{$prefix}post_types" ) ) ); ?>>
												<label for="<?php echo esc_attr( $id ); ?>">&nbsp;</label>
											</span>
										</div>
									</div>
								<?php
								}
								?>
							</div>
							<hr class="light">
						<?php else : ?>
							<input type="hidden" name="<?php echo esc_attr( "{$name}post_types][]" ); ?>" value="<?php echo esc_attr( $post_types[0] ); ?>">
						<?php endif; ?>

						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Location', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<?php Sl_Form::checkbox_general( "{$name}location]", sl_setting( "{$prefix}location" ) ); ?>
							</div>
						</div>
						<div class="sl-settings checkbox-toggle">
							<div class="sl-label">
								<label><?php _e( 'Features', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<?php Sl_Form::checkbox_general( "{$name}type]", sl_setting( "{$prefix}type" ) ); ?>
							</div>
						</div>
						<div class="sl-sub-settings">
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Sorting', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<select name="<?php echo esc_attr( "{$name}type_orderby]" ); ?>" class="sl-input-small">
										<?php
										Sl_Form::options( sl_setting( "{$prefix}type_orderby" ), array(
											'name'  => __( 'Alphabetical', '7listings' ),
											'count' => __( 'Amount', '7listings' ),
										) );
										?>
									</select>
								</div>
							</div>
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Max taxonomies', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<input type="number" class="small-text" name="<?php echo esc_attr( "{$name}type_number]" ); ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}type_number" ) ); ?>">
								</div>
							</div>
							<div class="sl-settings">
								<div class="sl-label">
									<label><?php _e( 'Counter', '7listings' ); ?></label>
								</div>
								<div class="sl-input">
									<?php Sl_Form::checkbox_general( "{$name}type_counter]", sl_setting( "{$prefix}type_counter" ) ); ?>
								</div>
							</div>
						</div>

					</div>
					<div class="column-2">

						<div class="sl-settings">
							<div class="sl-label">
								<label><?php _e( 'Background Image', '7listings' ); ?></label>
							</div>
							<div class="sl-input">
								<?php
								$src = '';
								if ( $img = sl_setting( "{$prefix}background" ) )
								{
									// Show thumb in admin for faster load
									list( $src ) = wp_get_attachment_image_src( $img, 'sl_thumb_tiny' );
								}
								?>
								<img src="<?php echo $src; ?>"<?php echo $src ? '' : ' class="hidden"'; ?>">
								<input type="hidden" name="<?php echo esc_attr( "{$name}background]" ); ?>" value="<?php echo esc_attr( $img ); ?>">
								<a href="#" class="button choose-image"><?php _e( 'Choose Image', '7listings' ); ?></a>
								<a href="#" class="button delete-image<?php echo $src ? '' : ' hidden'; ?>"><?php _e( 'Delete', '7listings' ); ?></a>
							</div>
						</div>

					</div>
				</div>
			</section>
		</div>
		<?php
		return;
	}

	do_action( 'sl_homepage_settings_box', $id );
}
