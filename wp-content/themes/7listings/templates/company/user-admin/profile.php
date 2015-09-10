<?php
if ( ! is_user_logged_in() )
{
	get_template_part( 'templates/company/user-admin/form-login' );

	return;
}
?>
<form action="" method="post" enctype="multipart/form-data" id="company-admin" class="company-form user-edit">

	<?php wp_nonce_field( 'edit-user' ) ?>

	<?php
	global $errors;
	if ( ! empty( $errors ) )
		echo '<div class="alert alert-error">' . implode( '<br>', $errors ) . '</div>';
	elseif ( isset( $_GET['updated'] ) )
		echo '<div class="alert alert-success">' . __( 'Your profile is updated.', '7listings' ) . '</div>';

	global $current_user;
	get_currentuserinfo();
	?>

	<h2><?php _e( 'Login', '7listings' ); ?></h2>

	<div class="row-fluid">
		<div class="span6">
			<label><?php _e( 'Username', '7listings' ); ?></label>
            <span class="input-prepend">
                <span class="add-on"><i class="icon-user"></i></span>
            	<input type="text" value="<?php echo $current_user->user_login; ?>" disabled class=""><span class="help-inline"><?php _e( 'Username cannot be changed.', '7listings' ); ?></span>
            </span> <label class="control-label"><?php _e( 'Email', '7listings' ); ?></label>
            <span class="input-prepend">
                <span class="add-on"><i class="icon-envelope"></i></span>
                <input type="email" name="user_email" value="<?php echo $current_user->user_email; ?>">
            </span>

			<div class="toggle plus password">
				<a href="#"><?php _e( 'Change Password', '7listings' ); ?></a>
				<div class="content">
					<label class="control-label"><?php _e( 'New Password', '7listings' ); ?></label>
                    <span class="input-prepend">
                        <span class="add-on"><i class="icon-lock"></i></span>
                        <input type="password" name="user_pass">
                    </span> <label class="control-label"><?php _e( 'Repeat New Password', '7listings' ); ?></label>
                    <span class="input-prepend">
                        <span class="add-on"><i class="icon-lock"></i></span>
                        <input type="password" name="password_confirm">
                    </span>
				</div>
			</div>
		</div>

		<div class="span6">
			<label><?php _e( 'Mobile', '7listings' ); ?></label>
            <span class="input-prepend">
                <span class="add-on"><i class="icon-phone"></i></span>
            	<input type="text" name="mobile" value="<?php echo $current_user->mobile; ?>">
            </span>
			<label class="control-label"><?php _e( 'Direct Line', '7listings' ); ?></label>
            <span class="input-prepend">
                <span class="add-on"><i class="icon-phone"></i></span>
                <input type="text" name="direct_line" value="<?php echo $current_user->direct_line; ?>">
            </span>
		</div>
	</div>
	<!-- .row-fluid -->

	<h2><?php _e( 'Name', '7listings' ); ?></h2>

	<div class="row-fluid">
		<div class="span12">

			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Name', '7listings' ); ?></label>
					<input type="text" name="first_name" value="<?php echo $current_user->user_firstname; ?>" class="span12">
					<span class="help-block"><?php _e( 'First', '7listings' ); ?></span>
				</div>
				<div class="span6">
					<label>&nbsp;</label>
					<input type="text" name="last_name" value="<?php echo $current_user->user_lastname; ?>" class="span12">
					<span class="help-block"><?php _e( 'Last', '7listings' ); ?></span>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Nickname', '7listings' ); ?></label>
					<input type="text" name="nickname" value="<?php echo $current_user->nickname; ?>" class="span12">
				</div>
				<div class="span6">
					<label class="control-label"><?php _e( 'Display Name', '7listings' ); ?></label>
					<select name="display_name" class="span12">
						<?php
						$display                     = array();
						$display['display_nickname'] = $current_user->nickname;
						$display['display_username'] = $current_user->user_login;

						if ( ! empty( $current_user->first_name ) )
							$display['display_firstname'] = $current_user->first_name;

						if ( ! empty( $current_user->last_name ) )
							$display['display_lastname'] = $current_user->last_name;

						if ( ! empty( $current_user->first_name ) && ! empty( $current_user->last_name ) )
						{
							$display['display_firstlast'] = $current_user->first_name . ' ' . $current_user->last_name;
							$display['display_lastfirst'] = $current_user->last_name . ' ' . $current_user->first_name;
						}

						if ( ! in_array( $current_user->display_name, $display ) ) // Only add this if it isn't duplicated elsewhere
							$display = array( 'display_displayname' => $current_user->display_name ) + $display;

						$display = array_unique( array_map( 'trim', $display ) );

						foreach ( $display as $item )
						{
							echo '<option' . selected( $current_user->display_name, $item, false ) . ">$item</option>";
						}
						?>
					</select>
				</div>
			</div>

		</div>
		<!-- .span12 -->
	</div>
	<!-- .row-fluid -->

	<h2><?php _e( 'Social Info', '7listings' ); ?></h2>

	<div class="row-fluid social-inputs">
		<div class="span4">
			<label class="control-label"><?php _e( 'Website', '7listings' ); ?></label>
            <span class="input-prepend">
                <span class="add-on"><i class="icon-globe"></i></span>
                <input type="text" name="user_url" value="<?php echo $current_user->user_url; ?>" class="span12">
            </span>
		</div>

		<?php
		$tpl     = '
            <div class="span4">
                <label>%2$s</label>
                <span class="input-prepend">
					<span class="add-on"><i class="icon-%4$s"></i></span>
					<input type="text" name="%1$s" value="%3$s" class="span12">
				</span>
            </div>';
		$socials = array(
			'facebook'   => 'Facebook',
			'twitter'    => 'Twitter',
			'googleplus' => 'Google+',
			'pinterest'  => 'Pinterest',
			'linkedin'   => 'LinkedIn',
			'instagram'  => 'Instagram',
			'rss'        => 'RSS',
		);
		foreach ( $socials as $k => $v )
		{
			$icon = $k == 'googleplus' ? 'google-plus' : $k;
			printf( $tpl, $k, $v, $current_user->$k, $icon );
		}
		?>
	</div>

	<h2><?php _e( 'Biography', '7listings' ); ?></h2>
	<textarea name="description" class="span6 description-input"><?php echo esc_textarea( $current_user->description ); ?></textarea>

	<div class="submit">
		<input type="submit" name="submit" class="button booking large" value="<?php _e( 'Update my Info', '7listings' ); ?>">
	</div>
</form>
