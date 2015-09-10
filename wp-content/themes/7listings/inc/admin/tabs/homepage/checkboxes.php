<?php
foreach ( $checkboxes as $k => $v )
{
	?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php echo $v; ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}{$k}" ); ?>
		</div>
	</div>
<?php
}
