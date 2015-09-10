<h2><?php _e( 'Login', '7listings' ); ?></h2>

<form method="post" action="">

	<?php wp_nonce_field( 'login' ); ?>

	<?php
	global $errors;
	if ( ! empty( $errors ) )
		echo '<div class="alert alert-error">' . implode( '<br>', $errors ) . '</div>';
	?>

	<div class="row-fluid">
		<label for="username"><?php _e( 'Username', '7listings' ); ?> <span class="required">*</span></label>
		<input type="text" name="username" id="username">
	</div>
	<div class="row-fluid">
		<label for="password"><?php _e( 'Password', '7listings' ); ?> <span class="required">*</span></label>
		<input type="password" name="password" id="password">
	</div>

	<div class="clear"></div>

	<div class="row-fluid">
		<input type="submit" class="button" name="submit_login" value="<?php esc_attr_e( 'Login', '7listings' ); ?>">
		<a class="lost_password" href="<?php
		$dashboard = sl_setting( 'company_page_dashboard' );
		$url       = $dashboard ? get_permalink( $dashboard ) : HOME_URL;
		echo wp_lostpassword_url( $url );
		?>"><?php _e( 'Lost Password?', '7listings' ); ?></a>
	</div>
</form>
