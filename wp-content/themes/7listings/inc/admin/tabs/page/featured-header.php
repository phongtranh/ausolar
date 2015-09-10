<div class="sl-settings header-setting title">
	<div class="sl-label">
		<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the page title<br>in featured area', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		global $hook_suffix;
		$title = get_post_meta( get_the_ID(), 'featured_header_title', true );
		if ( 'post-new.php' == $hook_suffix || '' === $title )
		{
			$title = 1;
		}
		Sl_Form::checkbox_general( 'featured_header_title', $title )
		?>
	</div>
</div>
<div class="sl-settings header-setting custom-text">
	<div class="sl-label">
		<label><?php _e( 'Subtitle', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter a custom subtitle<br>in the featured area', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox_general( 'featured_header_text', get_post_meta( get_the_ID(), 'featured_header_text', true ) ) ?>
		</span>
		<div class="extra-input">
			<?php wp_editor( get_post_meta( get_the_ID(), 'featured_header_text_content', true ), 'featured_header_text_content' ); ?>
		</div>
	</div>
</div>
<div class="sl-settings header-setting slideshow">
	<div class="sl-label">
		<label><?php _e( 'Slideshow', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert a slideshow<br>in featured area', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox_general( 'featured_header_slideshow', get_post_meta( get_the_ID(), 'featured_header_slideshow', true ) ) ?>
		</span>
		<p id="featured_header_slideshow_id" class="extra-input">
			<?php
			$slideshows = get_posts( 'post_type=slideshow' );
			if ( empty( $slideshows ) )
			{
				_e( 'No slideshows', '7listings' );
			}
			else
			{
				echo '<select name="featured_header_slideshow_id">';
				echo '<option value="">' . __( 'Select', '7listings' ) . '</option>';
				$option_tpl = '<option value="%s"%s>%s</option>';
				$selected   = get_post_meta( get_the_ID(), 'featured_header_slideshow_id', true );
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
		</p>
	</div>
</div>
<div class="sl-settings header-setting header-image">
	<div class="sl-label">
		<label><?php _e( 'Featured Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display the featured image<br>as a full size background', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'featured_image', get_post_meta( get_the_ID(), 'featured_image', true ) ) ?>
	</div>
</div>
<div class="sl-settings header-setting header-height">
	<div class="sl-label">
		<label><?php _e( 'Custom height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Change default height of featured header<br>Note: this input will not result in a responsive height change on smaller screens<br>You can change the default height for the whole site in design settings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle">
			<?php Sl_Form::checkbox_general( 'featured_header_height_enable', get_post_meta( get_the_ID(), 'featured_header_height_enable', true ) ) ?>
		</span>
		<p class="extra-input">
			<br />
			<select name="featured_header_height">
				<?php
				$sizes = array(
					'tiny'  => __( 'Tiny', '7listings' ),
					'small' => __( 'Small', '7listings' ),
					''      => __( 'Medium', '7listings' ),
					'large' => __( 'Large', '7listings' ),
					'huge'  => __( 'Huge', '7listings' ),
				);
				Sl_Form::options( get_post_meta( get_the_ID(), 'featured_header_height', true ), $sizes );
				?>
			</select>
		</p>
	</div>
</div>
