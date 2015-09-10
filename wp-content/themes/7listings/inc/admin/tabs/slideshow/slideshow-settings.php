<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Animation', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $selected = get_post_meta( get_the_ID(), 'animation', true ); ?>
		<select name="animation">
			<option value="fade"<?php selected( 'fade', $selected ); ?>><?php _e( 'Fade', '7listings' ); ?></option>
			<option value="slide"<?php selected( 'slide', $selected ); ?>><?php _e( 'Slide', '7listings' ); ?></option>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Loop', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$value = get_post_meta( get_the_ID(), 'loop', true );
		if ( '' === $value )
			$value = 1;
		Sl_Form::checkbox_general( 'loop', $value );
		?>
	</div>
</div>
<hr class="light">
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Slideshow Speed', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $value = get_post_meta( get_the_ID(), 'slideshow_speed', true ) ? get_post_meta( get_the_ID(), 'slideshow_speed', true ) : 3000; ?>
		<span class="input-append">
			<input type="number" class="small-text" name="slideshow_speed" value="<?php echo $value; ?>">
			<span class="add-on">ms</span>
		</span>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Animation Speed', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $value = get_post_meta( get_the_ID(), 'animation_speed', true ) ? get_post_meta( get_the_ID(), 'animation_speed', true ) : 300; ?>
		<span class="input-append">
			<input type="number" class="small-text" name="animation_speed" value="<?php echo $value; ?>">
			<span class="add-on">ms</span>
		</span>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Fixed Height', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'fixed_height', get_post_meta( get_the_ID(), 'fixed_height', true ) ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label>&nbsp;</label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" class="small-text" name="height" value="<?php echo get_post_meta( get_the_ID(), 'height', true ); ?>">
			<span class="add-on">px</span>
		</span>
	</div>
</div>

<h2><?php _e( 'Navigation', '7listings' ); ?></h2>
<div class="sl-settings">
	<?php
	$value = get_post_meta( get_the_ID(), 'nextprev', true );
	if ( '' === $value )
		$value = 1;
	?>
	<div class="sl-label">
		<label><?php _e( 'Next/Prev', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'nextprev', $value ); ?>
	</div>
</div>
<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Pagination', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'pagination', get_post_meta( get_the_ID(), 'pagination', true ) ); ?>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label>&nbsp;</label>
	</div>
	<div class="sl-input">
		<?php $selected = get_post_meta( get_the_ID(), 'pagination_type', true ); ?>
		<select name="pagination_type">
			<option value="buttons"<?php selected( 'buttons', $selected ); ?>><?php _e( 'Buttons', '7listings' ); ?></option>
			<option value="thumbnails"<?php selected( 'thumbnails', $selected ); ?>><?php _e( 'Thumbnails', '7listings' ); ?></option>
		</select>
	</div>
</div>
