<hr class="light">
<p>
	<label class="input-label"><?php _e( 'Amount', '7listings' ); ?></label>
	<span class="input-append">
		<input class="amount" type="number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo absint( $instance['number'] ); ?>">
		<span class="add-on"><?php _e( 'listings', '7listings' ); ?></span>
	</span>
</p>
<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>
<hr class="light">
<?php include THEME_INC . 'widgets/tpl/thumbnail.php'; ?>
<?php include THEME_INC . 'widgets/tpl/checkboxes.php'; ?>
<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
<hr class="light">
<p class="checkbox-toggle">
	<?php Sl_Form::checkbox_general( $this->get_field_name( 'more_listings' ), $instance['more_listings'] ); ?>
	<label><?php _e( 'See more listings', '7listings' ); ?></label>
</p>
<div>
	<p>
		<label><?php _e( 'Text', '7listings' ); ?></label><br>
		<input type="text" name="<?php echo $this->get_field_name( 'more_listings_text' ); ?>" value="<?php echo $instance['more_listings_text']; ?>">
	</p>
	<p>
		<label><?php _e( 'Style', '7listings' ); ?></label>
		<select class="input-small" name="<?php echo $this->get_field_name( 'more_listings_style' ); ?>">
			<option value="button"<?php selected( 'button', $instance['more_listings_style'] ); ?>><?php _e( 'Button', '7listings' ); ?></option>
			<option value="text"<?php selected( 'text', $instance['more_listings_style'] ); ?>><?php _e( 'Text', '7listings' ); ?></option>
		</select>
	</p>
</div>
