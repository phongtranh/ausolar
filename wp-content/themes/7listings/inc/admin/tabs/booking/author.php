<select name="post_author" id="post_author">
	<option value="0"><?php _e( 'Guest', '7listings' ); ?></option>
	<optgroup label="<?php _e( 'Users', '7listings' ); ?>">
		<?php
		$users = get_users();
		$post  = get_post();
		foreach ( $users as $user )
		{
			printf( '<option value="%s"%s>%s</option>',
				$user->ID,
				selected( $user->ID, $post->post_author, false ),
				$user->user_login
			);
		}
		?>
	</optgroup>
</select>

