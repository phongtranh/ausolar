<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo "{$post_type}_featured"; ?>">
	<span class="add-md toggle"></span>
	<span class="heading"><?php echo $title; ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display featured listings<br>in a slideshow', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></span>
	<span class="on-off">
		<?php
		_e( 'Display: ', '7listings' );
		Sl_Form::checkbox( "{$prefix}active" );
		?>
	</span>

	<section class="widget-settings hidden">
		<div class="sl-row">
			<div class="column-2">
				
				<div class="sl-settings sl-widget-title">
					<div class="sl-label">
						<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter a heading for the slider', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							<span class="warning-sm required right"></span></label>
					</div>
					<div class="sl-input">
						<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>" class="sl-input-large">
						<?php Sl_Form::select_heading_style( "{$prefix}title" ) ?>
					</div>
				</div>
				
				<br>
				
				<?php include THEME_TABS . 'homepage/checkboxes.php'; ?>
				<?php include THEME_TABS . 'homepage/excerpt.php'; ?>
				
				<br>
				
				<div class="sl-settings">
					<div class="sl-label">
						<label>
							<?php _e( 'Transition', '7listings' ); ?>
							<span class="warning-sm required right"></span>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Choose a transition type for the slider', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
						</label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}transition]"; ?>">
							<option value="-1"><?php _e( 'Select', '7listings' ); ?></option>
							<?php
							Sl_Form::options( sl_setting( "{$prefix}transition" ), array(
								'fade'       => __( 'Fade', '7listings' ),
								'scrollHorz' => __( 'Scroll Horizontally', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>
				<div class="sl-settings additional-inputs">
					<div class="sl-label">
						<label>
							<?php _e( 'Delay', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Enter the delay before slideshow starts', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							<span class="warning-sm required right"></span>
						</label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount large" min="0" name="<?php echo THEME_SETTINGS . "[{$prefix}transition_delay]"; ?>" value="<?php echo sl_setting( "{$prefix}transition_delay" ); ?>">
							<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
						</span>
					</div>
				</div>
				<div class="sl-settings additional-inputs">
					<div class="sl-label">
						<label>
							<?php _e( 'Speed', '7listings' ); ?>
							<?php echo do_shortcode( '[tooltip content="' . __( 'Enter the duration of the transition between slides', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
							<span class="warning-sm required right"></span>
						</label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount large" min="0" name="<?php echo THEME_SETTINGS . "[{$prefix}transition_speed]"; ?>" value="<?php echo sl_setting( "{$prefix}transition_speed" ); ?>">
							<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
						</span>
					</div>
				</div>
		
			</div>
			<div class="column-2">

				<div class="sl-settings additional-inputs">
					<div class="sl-label">
						<label><?php _e( 'Display', '7listings' ); ?>
							<span class="warning-sm required right"></span>
						</label>
					</div>
					<div class="sl-input">
						<span class="input-append">
							<input type="number" class="amount" name="<?php echo THEME_SETTINGS . "[{$prefix}total]"; ?>" value="<?php echo sl_setting( "{$prefix}total" ); ?>">
							<span class="add-on"><?php _e( 'slides', '7listings' ); ?></span>
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
							'orderby'         => 'NAME',
							'order'           => 'ASC',
						) );
						?>
					</div>
				</div>
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Priority', '7listings' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}priority]"; ?>">
							<?php
							Sl_Form::options( sl_setting( "{$prefix}priority" ), array(
								0 => __( 'All', '7listings' ),
								2 => __( 'Star', '7listings' ),
								1 => __( 'Featured', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>
				
				<div class="sl-settings">
					<div class="sl-label">
						<label><?php _e( 'Sort By', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select the order in which listings are displayed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
					</div>
					<div class="sl-input">
						<select name="<?php echo THEME_SETTINGS . "[{$prefix}orderby]"; ?>">
							<option value="-1"><?php _e( 'Select', '7listings' ); ?></option>
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
		
			</div>
		</div>
	</section>
</div>
