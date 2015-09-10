<?php
if ( 'company_logos' != $id )
	return;

$prefix = "homepage_{$id}_";
?>

<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="company_logos">
	<span class="add-md toggle"></span> <span class="heading"><?php _e( 'Company Logos', '7listings' ); ?></span>
	<span class="on-off">
		<?php _e( 'Display:', '7listings' ); ?>
		<?php Sl_Form::checkbox( 'homepage_company_logos_active' ); ?>
	</span>

	<section class="widget-settings hidden">
		<div class="sl-row">
			<div class="column-2">
		
				<div class="sl-settings sl-widget-title">
					<div class="sl-label">
						<label><?php _e( 'Title', '7listings' ); ?><span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
						<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
					</div>
				</div>
				<br>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter logo height', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" name="<?php echo THEME_SETTINGS . "[{$prefix}height]"; ?>" value="<?php echo sl_setting( "{$prefix}height" ); ?>" class="amount height">
							<span class="add-on">px</span>
						</span>
					</div>
				</div>
				<br>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Pause', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter time between slides', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" name="<?php echo THEME_SETTINGS . "[{$prefix}speed]"; ?>" value="<?php echo sl_setting( "{$prefix}speed" ); ?>" class="amount speed">
							<span class="add-on">ms</span>
						</span>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Transition', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter time for transition', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" name="<?php echo THEME_SETTINGS . "[{$prefix}transition_speed]"; ?>" value="<?php echo sl_setting( "{$prefix}transition_speed" ); ?>" class="amount speed">
							<span class="add-on">ms</span>
						</span>
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
							<input type="number" name="<?php echo THEME_SETTINGS . "[{$prefix}total]"; ?>" value="<?php echo sl_setting( "{$prefix}total" ); ?>" class="amount posts">
							<span class="add-on"><?php _e( 'Companies', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				<br>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Featured Only', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Turn on to display only featured companies', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}featured" ); ?>
					</div>
				</div>
				<div class="sl-settings sl-membership-options">
					<div class="sl-label">
						<label><?php _e( 'Membership', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select company types to display in slider', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<span>
							<?php Sl_Form::checkbox( "{$prefix}none" ); ?>
							<?php _e( 'None', '7listings' ); ?>
						</span>
						<span>
							<?php Sl_Form::checkbox( "{$prefix}bronze" ); ?>
							<?php _e( 'Bronze', '7listings' ); ?>
						</span>
						<span>
							<?php Sl_Form::checkbox( "{$prefix}silver" ); ?>
							<?php _e( 'Silver', '7listings' ); ?>
						</span>
						<span>
							<?php Sl_Form::checkbox( "{$prefix}gold" ); ?>
							<?php _e( 'Gold', '7listings' ); ?>
						</span>
					</div>
				</div>
		
			</div>
		</div>
	</section>
</div>
