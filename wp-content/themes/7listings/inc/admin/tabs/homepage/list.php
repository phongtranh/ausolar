<?php
$prefix = "homepage_{$post_type}_listings_";
if ( ! isset( $elements ) )
	$elements = array();
$elements = array_merge( array(
	'price'   => __( 'Price', '7listings' ),
	'booking' => __( 'Booking Button', '7listings' ),
	'rating'  => __( 'Review Rating', '7listings' ),
), $elements );
?>
<div class="box">
	<input type="hidden" name="<?php echo THEME_SETTINGS; ?>[homepage_order][]" value="<?php echo "{$post_type}_listings"; ?>">
	<span class="add-md toggle"></span>
	<span class="heading"><?php echo $title; ?></span>
	<span class="on-off">
		<?php _e( 'Display:', '7listings' ); ?>
		<?php Sl_Form::checkbox( "{$prefix}active" ); ?>
	</span>

	<section class="widget-settings hidden">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?> <span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<input type="text" class="widget-title" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo sl_setting( "{$prefix}title" ); ?>">
			</div>
		</div>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Display', '7listings' ); ?><span class="warning-sm required right"></span></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="amount" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}display]"; ?>" value="<?php echo sl_setting( "{$prefix}display" ); ?>">
					<span class="add-on"><?php _e( 'Listings', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings homepage columns layout">
			<div class="sl-label">
				<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select columns layout', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				$value     = sl_setting( "{$prefix}columns" );
				$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols', 5 => 'five-cols', 6 => 'six-cols' );
				for ( $cols = 2; $cols <= 6; $cols ++ )
				{
					$checked = checked( $value, $cols, false );
					$id      = uniqid();
					printf( '
							<span>
								<input type="radio" name="%s[%s]" id="%s" value="%s"%s>
								<label class="%s%s" for="%s">&nbsp;</label>
							</span>',
						THEME_SETTINGS, "{$prefix}columns", $id, $cols, $checked,
						$cols_name[$cols], $checked ? ' active' : '', $id
					);
				}
				?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Image Size', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}image_size]", sl_setting( "{$prefix}image_size" ) ); ?>
			</div>
		</div>
		<?php
		foreach ( $elements as $element => $title )
		{
			?>
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php echo $title; ?></label>
				</div>
				<div class="sl-input">
					<?php Sl_Form::checkbox( "{$prefix}{$element}" ); ?>
				</div>
			</div>
		<?php
		}
		?>
		
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
				<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select the order in which listings are displayed', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
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
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Types', '7listings' ); ?></label>
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
				) );
				?>
			</div>
		</div>
	</section>
</div>
