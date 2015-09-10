<div class="row-fluid">
	<div class="span6">
		<h2><?php _e( 'Accounts Contact', '7listings' ); ?></h2>
		<span class="input-hint" style="margin: -1.6em 0 2em 0;display: block;"><?php _e( 'Leave empty to use same details as Company Owner.', '7listings' ); ?></span>

		<p>
			<label><?php _e( 'Name', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" name="invoice_name" value="<?php echo get_post_meta( get_the_ID(), 'invoice_name', true ); ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Position', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" name="invoice_position" value="<?php echo get_post_meta( get_the_ID(), 'invoice_position', true ); ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Email', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-envelope-alt"></i></span>
				<input type="email" name="invoice_email" value="<?php echo get_post_meta( get_the_ID(), 'invoice_email', true ); ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Mobile', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-phone"></i></span>
				<input type="text" name="invoice_phone" value="<?php echo get_post_meta( get_the_ID(), 'invoice_phone', true ); ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Direct Line', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-phone"></i></span>
				<input type="text" name="invoice_direct_line" value="<?php echo get_post_meta( get_the_ID(), 'invoice_direct_line', true ); ?>">
			</span>
		</p>

		<hr class="light">

		<p class="checkbox-toggle">
			<label><?php _e( 'Business uses Paypal?', '7listings' ); ?></label>
			<?php SL_Form::checkbox_general( 'paypal_enable', get_post_meta( get_the_ID(), 'paypal_enable', true ) ); ?>
		</p>
		<p>
			<label><?php _e( 'Paypal Email', '7listings' ); ?></label>
			<input type="email" name="paypal_email" value="<?php echo get_post_meta( get_the_ID(), 'paypal_email', true ); ?>">
		</p>
	</div>
	<div class="span6">
		<h2><?php _e( 'User', '7listings' ); ?></h2>

		<?php
		$user = get_userdata( get_post_meta( get_the_ID(), 'user', true ) );
		$name = $user->display_name;
		if ( $user->first_name && $user->last_name )
			$name = "{$user->first_name} {$user->last_name}";
		?>
		<p>
			<label><?php _e( 'Name', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" disabled value="<?php echo $name; ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Username', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-user"></i></span>
				<input type="text" disabled value="<?php echo $user->user_login; ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Email', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-envelope-alt"></i></span>
				<input type="email" name="owner_email" value="<?php echo $user->user_email; ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Mobile', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-phone"></i></span>
				<input type="text" name="owner_mobile" value="<?php echo $user->mobile; ?>">
			</span>
		</p>
		<p>
			<label><?php _e( 'Direct Line', '7listings' ); ?></label>
			<span class="input-prepend">
				<span class="add-on"><i class="icon-phone"></i></span>
				<input type="text" name="owner_direct_line" value="<?php echo $user->direct_line; ?>">
			</span>
		</p>
	</div>
</div>
