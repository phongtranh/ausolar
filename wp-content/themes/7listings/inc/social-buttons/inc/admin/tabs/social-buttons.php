<?php $options = sl_setting( 'social_buttons' ); ?>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Buttons', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$tpl     = '<label><input type="checkbox" value="%s" name="' . THEME_SETTINGS . '[social_buttons][buttons][]"%s> %s</label><br>';
		$checked = $options['buttons'];
		$buttons = array(
			'facebook'    => 'Facebook',
			'twitter'     => 'Twitter',
			'google'      => 'Google+',
			'linkedin'    => 'Linkedin',
			'pinterest'   => 'Pinterest',
			'reddit'      => 'Reddit',
			'stumbleupon' => 'StumbleUpon',
			'email'       => __( 'Email', '7listings' ),
		);
		foreach ( $buttons as $k => $v )
		{
			printf( $tpl, $k, checked( in_array( $k, $checked ), 1, false ), $v );
		}
		?>
	</div>
</div>
<hr class="light">
<div class="sl-settings">
	<div class="sl-label">
		<label for="social-buttons-position"><?php _e( 'Position', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php $position = $options['position']; ?>
		<select id="social-buttons-position" name="<?php echo THEME_SETTINGS; ?>[social_buttons][position]">
			<option value="both" <?php selected( 'both', $position ); ?>><?php _e( 'Before &amp; after the post', '7listings' ); ?></option>
			<option value="top" <?php selected( 'top', $position ); ?>><?php _e( 'Before the post', '7listings' ); ?></option>
			<option value="bottom" <?php selected( 'bottom', $position ); ?>><?php _e( 'After the post', '7listings' ); ?></option>
		</select>
	</div>
</div>
<hr class="light">
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Show on', '7listings' ); ?></label>
	</div>
	<div class="sl-input" id="social-page-options">
		<?php
		$tpl     = '<label><input type="checkbox" value="%s" name="' . THEME_SETTINGS . '[social_buttons][show_on][]"%s> %s</label>';
		$checked = $options['show_on'];
		printf( $tpl, 'home', checked( in_array( 'home', $checked ), 1, false ), __( 'Home', '7listings' ) );
		printf( $tpl, 'archive_page', checked( in_array( 'archive_page', $checked ), 1, false ), __( 'Archive', '7listings' ) );
		printf( $tpl, 'archive', checked( in_array( 'archive', $checked ), 1, false ), __( 'Archive Posts', '7listings' ) );
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $post_type )
		{
			if ( 'attachment' == $post_type )
				continue;
			printf(
				$tpl,
				$post_type,
				checked( in_array( $post_type, $checked ), 1, false ),
				ucwords( $post_type )
			);
		}
		?>
	</div>
</div>