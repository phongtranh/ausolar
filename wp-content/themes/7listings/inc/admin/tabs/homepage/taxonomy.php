<?php $prefix = "homepage_{$post_type}_{$taxonomy}_"; ?>
<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo "{$post_type}_{$taxonomy}"; ?>">
	<span class="add-md toggle"></span>
	<span class="heading"><?php echo $title; ?></span>
	<span class="on-off">
		<?php _e( 'Display:', '7listings' ); ?>
		<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
	</span>

	<section class="widget-settings hidden">
		<div class="sl-row">
			<div class="column-2">
		
				<div class="sl-settings sl-widget-title">
					<div class="sl-label">
						<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
						<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
					</div>
				</div>
		
			</div>
			<div class="column-2">
		
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Display', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
							<span class="add-on"><?php _e( 'Features', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				
			</div>
		</div>
	</section>
</div>
