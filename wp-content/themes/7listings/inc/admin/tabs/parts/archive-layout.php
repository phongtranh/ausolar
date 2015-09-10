<?php $prefix = "{$post_type}_archive_"; ?>
<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>

<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Description', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category or taxonomy description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}cat_desc" ); ?>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display category or taxonomy image', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}cat_image" ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<div class="sl-settings sub-input">
		<div class="sl-label">
			<label><?php _e( 'Style', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}cat_image_type"; ?>]">
				<?php
				Sl_Form::options( sl_setting( "{$prefix}cat_image_type" ), array(
					'thumbnail'  => __( 'Thumbnail', '7listings' ),
					'background' => __( 'Background', '7listings' ),
				) );
				?>
			</select>
		</div>
	</div>
	<div class="sl-settings sub-input">
		<div class="sl-label">
			<label><?php _e( 'Size', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}cat_image_size]", sl_setting( "{$prefix}cat_image_size" ) ); ?>
		</div>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Map', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display map with listing markers as background', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}map" ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<h4><?php _e( 'Map Popup', '7listings' ); ?></h4>
	<div class="sl-settings checkbox-toggle">
		<div class="sl-label">
			<label><?php _e( 'Image', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable featured image', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}map_image" ); ?>
		</div>
	</div>
	<div class="sl-sub-settings">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Size', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::image_sizes_select( THEME_SETTINGS . "[{$prefix}map_image_size]", sl_setting( "{$prefix}map_image_size" ) ); ?>
			</div>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Price', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display listing price', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}map_price" ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Button', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display booking button', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}map_booking" ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display customer review rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( "{$prefix}map_rating" ); ?>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Excerpt', '7listings' ) ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display excerpt or<br>XX words from description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<span class="checkbox-toggle" data-effect="fade">
				<?php Sl_Form::checkbox( "{$prefix}map_excerpt" ); ?>
			</span>
			<span class="input-append supplementary-input">
				<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$prefix}map_excerpt_length]"; ?>]" value="<?php echo esc_attr( sl_setting( "{$prefix}map_excerpt_length" ) ); ?>">
				<span class="add-on"><?php _e( 'Words', '7listings' ); ?></span>
			</span>
		</div>
	</div>
</div>

<div class="sl-settings checkbox-toggle">
	<div class="sl-label">
		<label><?php _e( 'Search', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enable search widget', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<?php Sl_Form::checkbox( "{$prefix}search_widget" ); ?>
	</div>
</div>
<div class="sl-sub-settings">
	<?php
	Sl_Form::input( __( 'Title', '7listings' ), "{$prefix}search_widget_title" );
	$checkboxes = array(
		'keyword'  => __( 'Keyword', '7listings' ),
		'location' => __( 'Location', '7listings' ),
		'type'     => __( 'Type', '7listings' ),
		'feature'  => __( 'Feature', '7listings' ),
		//'date'        => __( 'Date', '7listings' ),
		'rating'   => __( 'Rating', '7listings' ),
	);
	if ( 'accommodation' == $post_type )
	{
		$checkboxes['star_rating'] = __( 'Stars', '7listings' );
	}
	foreach ( $checkboxes as $k => $v )
	{
		?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php echo $v; ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}search_widget_{$k}" ); ?>
			</div>
		</div>
		<?php
	}
	?>
</div>

<br>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If not enter, default settings in Design page will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_height]"; ?>]" class="sl-input-small">
			<?php
			$sizes = array(
				'tiny'  => __( 'Tiny', '7listings' ),
				'small' => __( 'Small', '7listings' ),
				''      => __( 'Medium', '7listings' ),
				'large' => __( 'Large', '7listings' ),
				'huge'  => __( 'Huge', '7listings' ),
			);
			Sl_Form::options( sl_setting( "{$prefix}featured_title_height" ), $sizes );
			?>
		</select>
	</div>
</div>

<br><br>

<h3><?php _e( 'Main', '7listings' ); ?></h3>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Listings', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter amount of listings<br>to display on a page', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="input-append">
			<input type="number" min="0" class="amount" name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}num"; ?>]" value="<?php echo sl_setting( "{$prefix}num" ); ?>">
			<span class="add-on"><?php _e( '/ page', '7listings' ); ?></span>
		</span>
	</div>
</div>

<br>

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
		<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting for listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="<?php echo THEME_SETTINGS; ?>[<?php echo "{$prefix}orderby"; ?>]">
			<?php
			Sl_Form::options( sl_setting( "{$prefix}orderby" ), array(
				'date'       => __( 'Recent', '7listings' ),
				'views'      => __( 'Popular', '7listings' ),
				'price-asc'  => __( 'Price (low-high)', '7listings' ),
				'price-desc' => __( 'Price (high-low)', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>

<br>
<br>

<?php include THEME_TABS . 'parts/archive-sidebar-layout.php'; ?>
