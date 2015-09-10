<?php $post_type = 'product'; ?>

<div class="section">

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Featured Graphics', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$post_type}_featured_graphics" ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Pages', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Assign your WooCommerce store pages<br>and configure your Product page templates.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php printf( __( '<a href="%s" title="Assign WooCommerce pages">WooCommerce Pages</a>', '7listings' ), admin_url( 'admin.php?page=wc-settings&tab=products&section=display' ) ); ?>
			<br>
			<?php printf( __( '<a href="%s">Archive settings</a>', '7listings' ), admin_url( 'edit.php?post_type=page&page=product#archive' ) ); ?>
			<br>
			<?php printf( __( '<a href="%s">Single settings</a>', '7listings' ), admin_url( 'edit.php?post_type=page&page=product#single' ) ); ?>
		</div>
	</div>

	<br><br>

	<h3><?php _e( 'Advanced', '7listings' ); ?></h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Label', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Displayed in admin and front; enter in singular. Used when you want to use the tour listings as lessons.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
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
	<br>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'eCommerce/Cart', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable online booking functionality. If OFF listings display without a booking button like a catalog.', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( $post_type . '_cart' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If ON, please fill Payment details', '7listings' ) . '" type="message"]<span class="icon"></span>[/tooltip]' ); ?>
		</div>
	</div>

	<br><br>

	<h3><?php _e( 'Product Feed For Google Merchant', '7listings' ); ?></h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Condition', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php $selected = sl_setting( "{$post_type}_feed_condition" ); ?>
			<span class="product-condition-select">
			<input type="radio" name="<?php echo THEME_SETTINGS . "[{$post_type}_feed_condition]"; ?>" value="new"<?php checked( $selected, 'new' ); ?>>
				<?php _e( 'New', '7listings' ); ?><br>
			<input type="radio" name="<?php echo THEME_SETTINGS . "[{$post_type}_feed_condition]"; ?>" value="old"<?php checked( $selected, 'used' ); ?>>
				<?php _e( 'Used', '7listings' ); ?><br>
			<input type="radio" name="<?php echo THEME_SETTINGS . "[{$post_type}_feed_condition]"; ?>" value="refurbished"<?php checked( $selected, 'refurbished' ); ?>>
				<?php _e( 'Refurbished', '7listings' ); ?><br>
			</span>
		</div>
	</div>

	<br>

	<?php
	Sl_Form::input(
		__( 'Brand', '7listings' ),
		"{$post_type}_feed_brand",
		sprintf( __( ' Leave empty to use your product brands.', '7listings' ) )
	);
	Sl_Form::input(
		__( 'Google Product Category', '7listings' ),
		"{$post_type}_feed_google_product_category",
		sprintf( __( 'Select from <a href="%s" target="_blank">list of supported categories ONLY</a>.', '7listings' ), 'http://www.google.com/basepages/producttype/taxonomy.en-US.txt' )
	);
	?>

	<p><?php printf( __( 'Link to product XML feed: <a href="%1s">%1$s</a>', '7listings' ), home_url( 'products.xml' ) ); ?></p>

</div>
