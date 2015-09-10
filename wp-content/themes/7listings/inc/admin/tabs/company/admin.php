<h2><?php _e( 'User Account', '7listings' ); ?></h2>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Account', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<select name="membership_type">
			<?php
			$user_id = get_post_meta( get_the_ID(), 'user', true );
			Sl_Form::options( get_user_meta( $user_id, 'membership', true ), array(
				''       => __( 'None', '7listings' ),
				'gold'   => __( 'Gold', '7listings' ),
				'silver' => __( 'Silver', '7listings' ),
				'bronze' => __( 'Bronze', '7listings' ),
			) );
			?>
		</select>
	</div>
</div>
<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Renewal Frequency', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<span class="results">
			<?php
			$freq = get_user_meta( $user_id, 'membership_time', true );
			if ( 'month' == $freq )
				echo __( 'Monthly', '7listings' );
			elseif ( 'year' == $freq )
				echo __( 'Yearly', '7listings' );
			else
				echo __( 'None', '7listings' );
			?>
		</span>
	</div>
</div>
<div class="sl-sub-settings stat-info">
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Member Since', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<span class="results">
				<?php
				if ( $user_id )
				{
					$user_data  = get_userdata( $user_id );
					$registered = $user_data->user_registered;
					echo date( 'd/m/Y H:i', strtotime( $registered ) );
				}
				?>
			</span>
		</div>
	</div>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Last Payment', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<span class="result">
				<?php
				$time = get_user_meta( $user_id, 'membership_paid', true );
				if ( ! $time )
					_e( 'Not paid', '7listings' );
				else
					echo date( 'd/m/Y H:i', $time );
				?>
			</span>
		</div>
	</div>
</div>
<hr class="light">

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Operating', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<?php
		$operating = get_post_meta( get_the_ID(), 'operating', true );
		if ( '' === $operating )
		{
			$operating = 1;
		}
		Sl_Form::checkbox_general( 'operating', $operating );
		?>
	</div>
</div>

<div class="sl-settings">
	<div class="sl-label">
		<label><?php _e( 'Accounting number', '7listings' ); ?></label>
	</div>
	<div class="sl-input">
		<input type="number" name="accounting_number" value="<?php echo get_post_meta( get_the_ID(), 'accounting_number', true ); ?>">
	</div>
</div>

<?php do_action( 'company_edit_tab_admin_after' ); ?>
