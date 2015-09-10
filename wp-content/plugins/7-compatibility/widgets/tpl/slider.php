<hr class="light">
<p>
	<label class="input-label"><?php _e( 'Amount', '7listings' ); ?></label>
	<span class="input-append">
		<input class="amount" type="number" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>">
		<span class="add-on"><?php _e( 'slides', '7listings' ); ?></span>
	</span>
</p>
<hr class="light">
<?php include THEME_INC . 'widgets/tpl/checkboxes.php'; ?>
<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
<hr class="light">
<?php include THEME_INC . 'widgets/tpl/slider-config.php'; ?>
