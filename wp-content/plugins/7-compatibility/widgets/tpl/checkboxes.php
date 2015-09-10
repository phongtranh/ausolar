<?php
foreach ( $checkboxes as $k => $v )
{
	?>
	<p>
		<?php Sl_Form::checkbox_general( $this->get_field_name( $k ), $instance[$k] ); ?>
		<label><?php echo $v; ?></label>
	</p>
<?php
}
