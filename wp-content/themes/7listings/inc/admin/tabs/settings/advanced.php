<div class="normal">

	<h3>Google Analytics</h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Google Analytics ID', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Enter Google Analytics tracking ID<br>and code is inserted in header', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<input type="text" id="ga" name="<?php echo THEME_SETTINGS; ?>[ga]" value="<?php echo sl_setting( 'ga' ); ?>">
			<span class="description">UA-XXXXX-X</span>
		</div>
	</div>

	<br><br>

	<h3>CSS</h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Recompile CSS', '7listings' ); ?> <?php echo do_shortcode( '[tooltip content="' . __( 'Recompile less files to css<br>and clear less cache', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php submit_button( __( 'Update', '7listings' ), 'secondary', 'fix_less', false ); ?>
		</div>
	</div>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Query String', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Add a query string to CSS file url<br>Use when you update the design of your website frequently', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'css_no_var' ); ?>
		</div>
	</div>

	<br><br>

	<h3>Javascript</h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'jQuery in header', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Insert jQuery in header<br>Some plugins like gravityforms require this setting for AJAX', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php Sl_Form::checkbox( 'jquery_on_top' ); ?>
		</div>
	</div>

	<br><br>

	<h3>.htaccess<?php echo do_shortcode( '[tooltip content="' . __( 'Insert server side settings to optimize website<br>Warning: may cause conflict with some cache plugins', '7listings' ) . '" type="warning"]<span class="icon"></span>[/tooltip]' ); ?></h3>
	<pre>Header unset ETag
FileETag None
ExpiresActive On
ExpiresByType text/html "access plus 1 day"
ExpiresByType image/gif "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/png "access plus 1 year"
ExpiresByType text/css "access plus 1 year"
ExpiresByType text/javascript "access plus 1 year"
ExpiresByType application/x-javascript "access plus 1 year"
SetOutputFilter DEFLATE
&lt;FilesMatch ".(js|css|html|htm|php|xml)$"&gt;
SetOutputFilter DEFLATE
&lt;/FilesMatch&gt;</pre>
	<?php submit_button( __( 'Insert', '7listings' ), 'secondary', 'htaccess', false ); ?>

	<br><br>

	<h3><?php _e( 'General', '7listings' ); ?></h3>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Fix Reviews', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<?php submit_button( __( 'Update', '7listings' ), 'secondary', 'fix_reviews', false ); ?>
		</div>
	</div>

	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Prices', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Remove .00 from prices', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php submit_button( __( 'Clean Up', '7listings' ), 'secondary', 'cleanup', false ); ?>
		</div>
	</div>

	<hr class="light">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Booking Counter', '7listings' ); ?><?php echo do_shortcode( '[tooltip content="' . __( 'Reset booking #IDs to start from 1 (existing bookings are included)', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?></label>
		</div>
		<div class="sl-input">
			<?php submit_button( __( 'Reset', '7listings' ), 'secondary', 'fix_counter', false ); ?>
		</div>
	</div>

	<?php do_action( 'sl_settings_advanced_page_after' ); ?>
</div>
