<section class="panel contact">
	<h2 class="panel-title"><?php _e( 'Contact Info', '7listings' ); ?></h2>
	<div class="panel-content">

		<div class="sl-field error-box hidden">
			<div class="sl-input">
				<div class="error error-passenger"></div>
			</div>
		</div>

		<div class="passenger">
			<div class="sl-field required">
				<label class="sl-label" for="customer_name_first"><?php _e( 'Name', '7listings' ); ?></label>

				<div class="sl-input">
					<span class="sl-input-inline-wrap"><input type="text" id="customer_name_first" name="first[]" placeholder="<?php esc_attr_e( 'First', '7listings' ); ?>" class="name first" tabindex="201" required autocomplete="given-name"><label class="sl-input-warning"></label></span><span class="sl-input-inline-wrap"><input type="text" name="last[]" placeholder="<?php esc_attr_e( 'Last', '7listings' ); ?>" class="name last" tabindex="202" required autocomplete="family-name">
					<label class="sl-input-warning"></label></span>
				</div>
			</div>

			<div class="sl-field required">
				<label class="sl-label" for="customer_email"><?php _e( 'Email', '7listings' ); ?></label>
				<div class="sl-input">
					<input type="email" id="customer_email" name="email[]" class="email" tabindex="203" required autocomplete="email">
					<label class="sl-input-warning"></label>
				</div>
			</div>

			<div class="sl-field">
				<label class="sl-label" for="customer_phone"><?php _e( 'Phone', '7listings' ); ?></label>
				<div class="sl-input">
					<input type="text" id="customer_phone" name="phone[]" class="phone" tabindex="204" autocomplete="tel">
				</div>
			</div>

			<?php if ( 'rental' != get_post_type() ) : ?>
				<div class="sl-field more-guests">
					<div class="sl-input">
						<a href="#" class="guests-show" tabindex="205" title="<?php esc_attr_e( 'Optional', '7listings' ); ?>"><?php _e( '+ Enter contact details for all guests.', '7listings' ); ?></a>
						<a href="#" class="guests-hide hidden"><?php _e( '- My details are enough.', '7listings' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div><!-- .passenger -->

		<?php if ( 'rental' != get_post_type() ) : ?>
			<div class="other-passengers hidden"></div>
		<?php endif; ?>

		<div class="sl-field message-box">
			<label class="sl-label" for="customer_message"><?php _e( 'Message', '7listings' ); ?></label>
			<div class="sl-input">
				<textarea id="customer_message" name="customer_message" cols="20" rows="3" class="message" placeholder="<?php esc_attr_e( 'Let us know of any special requests, dietary requirements or other instructions...' ); ?>" tabindex="206"></textarea>
			</div>
		</div>

		<?php sl_get_template( 'templates/booking/parts/terms' ); ?>

		<?php
		// Do not show payment gateways for cart
		if ( ! $is_cart )
		{
			sl_get_template( 'templates/booking/parts/gateways', $params );
		}
		?>

		<?php sl_get_template( 'templates/booking/parts/buttons', $params ); ?>

	</div><!-- .panel-content -->

	<?php sl_get_template( 'templates/booking/parts/terms-modal' ); ?>
</section><!-- .panel -->
