<?php $post_type = 'company'; ?>

<div class="section">

	<h3><?php _e( 'Memberships', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'To disable a payment method (e.g: monthly) - leave input empty. To make a membership type free enter 0.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Gold', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<span>
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$post_type}_membership_gold" ); ?>
				</span>
				<span>
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_gold]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_gold" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/month', '7listings' ); ?></span>
					</span>
					&nbsp;&nbsp;&nbsp;
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_year_gold]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_year_gold" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/year', '7listings' ); ?></span>
					</span>
				</span>
			</span>
		</div>
	</div>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Silver', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<span>
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$post_type}_membership_silver" ); ?>
				</span>
				<span>
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_silver]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_silver" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/month', '7listings' ); ?></span>
					</span>
					&nbsp;&nbsp;&nbsp;
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_year_silver]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_year_silver" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/year', '7listings' ); ?></span>
					</span>
				</span>
			</span>
		</div>
	</div>
	
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Bronze', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<span>
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$post_type}_membership_bronze" ); ?>
				</span>
				<span>
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_bronze]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_bronze" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/month', '7listings' ); ?></span>
					</span>
					&nbsp;&nbsp;&nbsp;
					<span class="input-prepend input-append">
						<span class="add-on">$</span>
						<input type="number" step="any" min="0" name="<?php echo THEME_SETTINGS . "[{$post_type}_membership_price_year_bronze]"; ?>" value="<?php echo sl_setting( "{$post_type}_membership_price_year_bronze" ); ?>" class="membership-price">
						<span class="add-on"><?php _e( '/year', '7listings' ); ?></span>
					</span>
				</span>
			</span>
		</div>
	</div>

	<hr class="light">

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Display Membership', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display graphics for different memberships', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$post_type}_membership_display" ); ?>
		</div>
	</div>
	
	<br><br>
	
	<h3><?php _e( 'Menu', '7listings' ); ?></h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Menu item text', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_menu_title]"; ?>" value="<?php echo sl_setting( "{$post_type}_menu_title" ); ?>" class="sl-input-medium">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Dropdown', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Sorting options for dropdown<br>in main nav.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<select type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_menu_dropdown]"; ?>" class="sl-input-small">
				<?php
				Sl_Form::options( sl_setting( "{$post_type}_menu_dropdown" ), array(
					'locations' => __( 'Locations', '7listings' ),
					'services'  => __( 'Services', '7listings' ),
					'products'  => __( 'Products', '7listings' ),
					'brands'    => __( 'Brands', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	
	<br><br>

	<div class="sl-settings">
		<div class="sl-label">
			<h3><?php _e( 'Pages', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Configure your Company pages templates.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h3>
		</div>
		<div class="sl-input">
			<?php printf( __( '<a href="%s">Archive settings</a>', '7listings' ), admin_url( 'edit.php?post_type=page&page=company#archive' ) ); ?><br>
			<?php printf( __( '<a href="%s">Single settings</a>', '7listings' ), admin_url( 'edit.php?post_type=page&page=company#single' ) ); ?>
		</div>
	</div>
	
	<br>

	<?php
	$pages = array(
		'signup'    => __( 'Signup Page', '7listings' ),
		'dashboard' => __( 'Dashboard', '7listings' ),
		'edit'      => __( 'Edit Listing', '7listings' ),
		'profile'   => __( 'Edit User', '7listings' ),
		'posts'     => __( 'Company Posts', '7listings' ),
		'account'   => __( 'Account', '7listings' ),
	);
	foreach ( $pages as $k => $v )
	{
		echo '<div class="sl-settings">';
		echo '<div class="sl-label">';
		echo "<label>$v</label>";
		echo '</div>';
		echo '<div class="sl-input">';
		wp_dropdown_pages( array(
			'selected' => sl_setting( "{$post_type}_page_{$k}" ),
			'name'     => THEME_SETTINGS . "[{$post_type}_page_{$k}]",
		) );
		echo '</div>';
		echo '</div>';
	}
	?>

	<?php do_action( 'company_settings_page_after' ); ?>
	
	<br><br>

	<h3><?php _e( 'Advanced', '7listings' ); ?></h3>
	
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Label', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Used & displayed in admin menu, on front end, in singular form', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" name="<?php echo THEME_SETTINGS . "[{$post_type}_label]"; ?>" value="<?php echo sl_setting( "{$post_type}_label" ); ?>" class="sl-input-medium">
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Permalinks', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Set your url structure for tours.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php printf( __( '<a href="%s">Permalink Settings</a>', '7listings' ), admin_url( 'options-permalink.php' ) ); ?>
		</div>
	</div>
	
</div>
