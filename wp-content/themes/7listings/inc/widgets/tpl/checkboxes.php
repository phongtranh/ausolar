<?php
foreach ( $checkboxes as $k => $v )
{
	?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php echo $v; ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox_general( $this->get_field_name( $k ), $instance[$k] ); ?>
		</div>
	</div>
	<?php
}
