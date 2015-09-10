<?php locate_template( 'inc/admin/tabs/parts/info.php', true ); ?>

<h2><?php _e( 'Business Hours', '7listings' ); ?></h2>

<div class="sl-settings checkbox-toggle" data-reverse="1">
	<div class="sl-label">
		<label><?php _e( 'Open 24/7', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox_general( 'open_247', get_post_meta( get_the_ID(), 'open_247', true ) ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<?php
	$days = array(
		'mon' => __( 'Monday', '7listings' ),
		'tue' => __( 'Tuesday', '7listings' ),
		'wed' => __( 'Wednesday', '7listings' ),
		'thu' => __( 'Thursday', '7listings' ),
		'fri' => __( 'Friday', '7listings' ),
		'sat' => __( 'Saturday', '7listings' ),
		'sun' => __( 'Sunday', '7listings' ),
	);
	foreach ( $days as $k => $v )
	{
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php echo $v; ?></label>
			</div>
			<div class="sl-input">
				<span>
					<span class="checkbox-toggle" data-effect="fade">
						<?php Sl_Form::checkbox_general( "business_hours_$k", get_post_meta( get_the_ID(), "business_hours_$k", true ) ); ?>
					</span>
					<span>
						<span class="input-prepend">
							<span class="add-on"><?php _e( 'From', '7listings' ); ?></span>
							<input type="text" name="business_hours_<?php echo $k; ?>_from" class="hours timepicker" value="<?php echo get_post_meta( get_the_ID(), "business_hours_{$k}_from", true ); ?>">
						</span>&nbsp;&nbsp;
						<span class="input-prepend">
							<span class="add-on"><?php _e( 'To', '7listings' ); ?></span>
							<input type="text" name="business_hours_<?php echo $k; ?>_to" class="hours timepicker" value="<?php echo get_post_meta( get_the_ID(), "business_hours_{$k}_to", true ); ?>">
						</span>
					</span>
				</span>
			</div>
		</div>
	<?php
	}
	?>
</div>
