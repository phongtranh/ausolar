<div class="sl-settings">
	<div class="sl-label">
		<label for="new_sidebar"><?php _e( 'Create New Sidebar', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="text" name="<?php echo THEME_SETTINGS; ?>[new_sidebar]">
		<?php submit_button( __( 'Add', '7listings' ), 'primary', 'save', false ); ?>
	</div>
</div>

<?php if ( sl_setting( 'sidebars' ) ): ?>

	<hr>
    
    <h3><?php _e( 'My Sidebars', '7listings' ); ?></h3>
	<ul class="sl-sidebar-list">
		<?php
		foreach ( sl_setting( 'sidebars' ) as $sidebar )
		{
			printf(
				'<li class="sl-sidebar"><span class="title sl-sidebar-title">%s</span> <a href="#" class="button delete" rel="%s">%s</a></li>',
				$sidebar,
				$sidebar,
				__( 'Delete', '7listings' )
			);
		}
		?>
	</ul>
<?php endif; ?>
