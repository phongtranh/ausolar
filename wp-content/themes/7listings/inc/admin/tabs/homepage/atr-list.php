<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo "{$post_type}_listings"; ?>">
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
				
				<br>
				
				<?php include THEME_TABS . 'homepage/thumbnail.php'; ?>
				<?php include THEME_TABS . 'homepage/checkboxes.php'; ?>
				<?php include THEME_TABS . 'homepage/excerpt.php'; ?>
				
				<br>
				
				<div class="sl-settings checkbox-toggle">
					<div class="sl-label">
						<label><?php _e( 'See more listings', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}more_listings" ); ?>
					</div>
				</div>
				<div class="sl-sub-settings">
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Text', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_text]"; ?>" value="<?php echo sl_setting( "{$prefix}more_listings_text" ); ?>">
						</div>
					</div>
					<div class="sl-settings">
						<div class="sl-label">
							<label><?php _e( 'Style', '7listings' ); ?></label>
						</div>
						<div class="sl-input">
							<select class="input-small" name="<?php echo THEME_SETTINGS . "[{$prefix}more_listings_style]"; ?>">
								<option value="button"<?php selected( 'button', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Button', '7listings' ); ?></option>
								<option value="text"<?php selected( 'text', sl_setting( "{$prefix}more_listings_style" ) ); ?>><?php _e( 'Text', '7listings' ); ?></option>
							</select>
						</div>
					</div>
				</div>
				
			</div>
			<div class="column-2">

				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Amount', '7listings' ); ?><span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
							<span class="add-on"><?php _e( 'listings', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				
				<br>
				
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Type', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php
						wp_dropdown_categories( array(
							'show_option_all' => __( 'All', '7listings' ),
							'taxonomy'        => sl_meta_key( 'tax_type', $post_type ),
							'hide_empty'      => 1,
							'name'            => THEME_SETTINGS . "[{$prefix}type]",
							'selected'        => sl_setting( "{$prefix}type" ),
							'id'              => "{$prefix}type",
							'orderby'         => 'NAME',
							'order'           => 'ASC',
						) );
						?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Location', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<?php
						wp_dropdown_categories( array(
							'show_option_all' => __( 'All', '7listings' ),
							'taxonomy'        => 'location',
							'hide_empty'      => 1,
							'name'            => THEME_SETTINGS . "[{$prefix}location]",
							'selected'        => sl_setting( "{$prefix}location" ),
							'id'              => "{$prefix}location",
							'orderby'         => 'NAME',
							'order'           => 'ASC',
						) );
						?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Priority Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable priority sorting:<br>star > featured > none', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<?php Sl_Form::checkbox( "{$prefix}priority" ); ?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Sort By', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select the order in which listings are displayed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							<span class="warning-sm required right"></span>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
							<?php
							Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
								'date'       => __( 'Newest', '7listings' ),
								'views'      => __( 'Most Viewed', '7listings' ),
								'price-asc'  => __( 'Price (low-high)', '7listings' ),
								'price-desc' => __( 'Price (high-low)', '7listings' ),
								'rand'       => __( 'Random', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>
				
				<br>
				
				<?php include THEME_TABS . 'homepage/layout.php'; ?>
				
			</div>
		</div>
	</section>
</div>
