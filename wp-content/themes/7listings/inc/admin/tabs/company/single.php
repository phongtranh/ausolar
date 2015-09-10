<?php
$post_type = 'company';
$prefix    = "{$post_type}_single_";
?>
<div class="sl-row">
	<div class="column-2">
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Title for company', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS . "[{$prefix}title]"; ?>" value="<?php echo esc_attr( sl_setting( "{$prefix}title" ) ); ?>" class="sl-input-large">
				<?php echo do_shortcode( '[tooltip content="' . __( 'For title<br>use a SEO plugin like: Yoast', '7listings' ) . '" type="seo"]SEO[/tooltip]' ); ?>
			</div>
		</div>
		
		<br>

		<table id="memberhip-display">
			<tr>
				<th width="210"><?php _e( 'Membership', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Show/hide elements based on membership type', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></th>
				<td width="70"><?php _e( 'None', '7listings' ); ?></td>
				<td width="70"><?php _e( 'Bronze', '7listings' ); ?></td>
				<td width="70"><?php _e( 'Silver', '7listings' ); ?></td>
				<td><?php _e( 'Gold', '7listings' ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Logo', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}logo_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}logo_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}logo_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}logo_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Address', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}address_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}address_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}address_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}address_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Phone', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}phone_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}phone_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}phone_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}phone_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Email', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}email_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}email_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}email_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}email_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Website', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}url_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}url_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}url_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}url_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Social Links', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$prefix}social_media_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}social_media_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}social_media_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$prefix}social_media_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Map', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$post_type}_google_maps_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_google_maps_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_google_maps_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_google_maps_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Service Area', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$post_type}_service_area_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_service_area_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_service_area_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_service_area_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Reviews', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$post_type}_comment_status_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_comment_status_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_comment_status_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_comment_status_gold" ); ?></td>
			</tr>
			<tr>
				<th>
					<?php
					printf(
						'<a href="%s" target="_blank">%s</a>',
						'http://codex.wordpress.org/Introduction_to_Blogging#Managing_Comments',
						__( 'Trackbacks &amp; Pingbacks', '7listings' )
					);
					?>
				</th>
				<td><?php Sl_Form::checkbox( "{$post_type}_ping_status_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_ping_status_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_ping_status_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_ping_status_gold" ); ?></td>
			</tr>
			<tr>
				<th><?php _e( 'Similar Listings', '7listings' ); ?></th>
				<td><?php Sl_Form::checkbox( "{$post_type}_similar_none" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_similar_bronze" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_similar_silver" ); ?></td>
				<td><?php Sl_Form::checkbox( "{$post_type}_similar_gold" ); ?></td>
			</tr>
			<?php do_action( 'company_edit_single_membership_main' ); ?>
		</table>

		<br>

		<h3><?php _e( 'Similar Listings', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Settings for similar listings<br>you can display similar listings based on membership settings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></h3>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Title', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter heading<br>above similar listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<input type="text" name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_similar_title]" value="<?php echo sl_setting( "{$post_type}_similar_title" ); ?>" class="title related">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Sorting', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select sorting for similar listings', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_similar_by]" class="sl-input-small">
					<?php
					Sl_Form::options( sl_setting( "{$post_type}_similar_by" ), array(
						'location' => __( 'Location', '7listings' ),
						'brands'   => __( 'Brands', '7listings' ),
						'services' => __( 'Services', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings single columns layout">
			<div class="sl-label">
				<label><?php _e( 'Columns', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select similar listings layout', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				$value     = sl_setting( "{$post_type}_similar_columns" );
				$cols_name = array( 2 => 'two-cols', 3 => 'three-cols', 4 => 'four-cols', 5 => 'five-cols', 6 => 'six-cols' );
				for ( $cols = 2; $cols <= 6; $cols ++ )
				{
					$checked = checked( $value, $cols, false );
					printf( '
							<span>
								<input type="radio" name="%1$s[%2$s_similar_columns]" id="%2$s-similar-columns-%3$s" value="%3$s" %4$s>
								<label class="%5$s%6$s" for="%2$s-similar-columns-%3$s">&nbsp;</label>
							</span>',
						THEME_SETTINGS,
						$post_type,
						$cols,
						$checked,
						$cols_name[$cols],
						$checked ? ' active' : ''
					);
				}
				?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Amount', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Select amount of similiar listings<br>displayed in columns', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="amount" name="<?php echo THEME_SETTINGS; ?>[<?php echo $post_type; ?>_similar_display]" value="<?php echo sl_setting( "{$post_type}_similar_display" ); ?>">
					<span class="add-on"><?php _e( 'Listings', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Rating', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display customer review rating', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$post_type}_similar_rating" ); ?>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Excerpt', '7listings' ) ?><?php echo do_shortcode( '[tooltip content="' . __( 'Display excerpt or<br>XX words from description', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="checkbox-toggle" data-effect="fade">
					<?php Sl_Form::checkbox( "{$post_type}_similar_excerpt" ); ?>
				</span>
				<span class="input-append supplementary-input">
					<input type="number" class="amount" max="<?php echo absint( sl_setting( 'excerpt_limit' ) ); ?>" min="1" name="<?php echo THEME_SETTINGS . "[{$post_type}_similar_excerpt_length]"; ?>]" value="<?php echo esc_attr( sl_setting( "{$post_type}_similar_excerpt_length" ) ); ?>">
					<span class="add-on"><?php _e( 'Words', '7listings' ); ?></span>
				</span>
			</div>
		</div>
	</div>
	<div class="column-2">

		<h2><?php _e( 'Page Layout', '7listings' ); ?></h2>

		<h3><?php _e( 'Featured Header', '7listings' ); ?></h3>

		<div class="sl-settings checkbox-toggle">
			<div class="sl-label">
				<label><?php _e( 'Map', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox( "{$prefix}featured_title_map" ); ?>
			</div>
		</div>
		<div class="sl-sub-settings">
			<div class="sl-settings">
				<div class="sl-label">
					<label><?php _e( 'Zoom Level', '7listings' ); ?></label>
				</div>
				<div class="sl-input">
					<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_map_zoom]"; ?>" class="sl-input-tiny">
						<?php
						$value = sl_setting( "{$prefix}featured_title_map_zoom" );
						for ( $i = 1; $i <= 16; $i ++ )
						{
							printf( '<option value="%d"%s>%d</option>', $i, selected( $i, $value, false ), $i );
						}
						?>
					</select>
				</div>
			</div>
		</div>

		<br>

		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Height', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'If not enter, default settings in Design page will be used', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo THEME_SETTINGS . "[{$prefix}featured_title_height]"; ?>" class="sl-input-small">
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

		<?php include THEME_TABS . 'parts/single-sidebar-layout.php'; ?>
	</div>
</div>
