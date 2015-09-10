<?php $prefix = "{$post_type}_archive_"; ?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select image size', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display excerpt or<br>XX words from description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="checkbox-toggle" data-effect="fade">
			<?php Sl_Form::checkbox( "{$prefix}desc_enable" ); ?>
		</span>
		<span class="input-append">
			<input type="number" class="small-text" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}desc]"; ?>]" value="<?php echo sl_setting( "{$prefix}desc" ); ?>">
			<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display customer review rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}rating" ); ?>
	</div>
</div>
<?php if ( 'accommodation' == $post_type ) : ?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Star Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display accommodation star rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}star_rating" ); ?>
		</div>
	</div>
<?php endif; ?>
<div class="price-book">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}price" ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display booking button', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}booking" ); ?>
		</div>
	</div>
</div>
<div class="sl-settings sub-input checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Read More', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display read more link<br>below description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}readmore" ); ?>
	</div>
</div>
<div class="sl-sub-settings extra-input-toggle">
	<div class="sl-settings sub-input">
		<div class="sl-label">
			<label><?php _e( 'Style', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Link design', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="<?php echo THEME_SETTINGS . "[{$prefix}readmore_type]"; ?>]" class="sl-input-small">
				<?php
				Sl_Form::options( sl_setting( "{$prefix}readmore_type" ), array(
					'text'   => __( 'Text', '7listings' ),
					'button' => __( 'Button', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<?php Sl_Form::input( __( 'Text', '7listings' ), "{$prefix}readmore_text" ); ?>
</div>

<br>

<div class="booking-resources">
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label><?php _e( 'Booking Resources', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display booking resources with booking buttons with the listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$post_type}_book_in_archive" ); ?>
		</div>
	</div>
	<div class="sl-settings sub-input">
		<div class="sl-label">
			<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter description length', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<span class="input-append">
				<input type="number" class="small-text" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}resource_desc]"; ?>]" value="<?php echo sl_setting( "{$prefix}resource_desc" ); ?>">
				<span class="add-on"><?php _e( 'words', '7listings' ); ?></span>
			</span>
		</div>
	</div>
</div>
