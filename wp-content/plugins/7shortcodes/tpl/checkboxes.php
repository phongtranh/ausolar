<?php
foreach ( $checkboxes as $k => $v )
{
	?>
	<div class="control-group">
		<?php Sls_Helper::checkbox_angular( "sls_$shortcode.$k", "sls-$shortcode-$k" ); ?>
		<label class="control-label"><?php echo $v; ?></label>
	</div>
<?php
}
