<style>
	/* Hide post content. It's empty div but have margin. */
	#post-body-content {
		display: none;
	}
</style>
<table class="form-table booking-details">
	<tr class="total">
		<th><?php _e( 'Total', '7listings' ); ?></th>
		<td>
			<?php
			$total = get_post_meta( get_the_ID(), 'amount', true );
			echo Sl_Currency::format( $total, 'type=plain' );
			?>
		</td>
	</tr>
	<tr class="booking_on">
		<th><?php _e( 'Booked on', '7listings' ); ?></th>
		<td>
			<?php
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );

			echo '<span class="date booking-date">' . get_the_time( $date_format, get_the_ID() ) . '</span> - <span class="time booking-time">' . get_the_time( $time_format, get_the_ID() ) . '</span>';
			?>
		</td>
	</tr>
	<?php if ( $ip_address = get_post_meta( get_the_ID(), 'ip_address', true ) ) : ?>
		<tr class="customer_ip">
			<th><?php _e( 'Customer IP', '7listings' ); ?></th>
			<td>
				<a href="http://www.ip2location.com/<?php echo $ip_address; ?>" title=" <?php _e( 'View location data based on IP address', '7listings' ); ?>" target="blank"><?php echo $ip_address; ?></a>
			</td>
		</tr>
	<?php endif; ?>
	<tr class="device">
		<th><?php _e( 'Device', '7listings' ); ?></th>
		<td>
			<?php
			switch ( get_post_meta( get_the_ID(), 'device', true ) )
			{
				case 'phone':
					echo '<span class="device-phone">' . __( 'Phone', '7listings' ) . '</span>';
					break;
				case 'tablet':
					echo '<span class="device-tablet">' . __( 'Tablet', '7listings' ) . '</span>';
					break;
				default:
					echo '<span class="device-desktop">' . __( 'Desktop', '7listings' ) . '</span>';
			}
			?>
		</td>
	</tr>
	<tr class="payment_type">
		<th><?php _e( 'Payment', '7listings' ); ?></th>
		<td>
			<div class="view">
				<?php
				$payment_gateway = get_post_meta( get_the_ID(), 'payment_gateway', true );
				$display         = str_replace( '-', ' ', $payment_gateway );
				echo esc_html( ucwords( $display ) );
				?>
				<a class="sl-edit dashicons dashicons-edit" href="#"></a>
			</div>
			<div class="edit hidden">
				<select name="payment_gateway">
					<?php
					printf( '
						<optgroup label="%s">
							<option value="eway"%s>%s</option>
							<option value="paypal"%s>%s</option>
						</optgroup>
						<optgroup label="%s">
							<option value="credit-card"%s>%s</option>
							<option value="cash"%s>%s</option>
						</optgroup>',
						esc_attr__( 'Website', '7listings' ),
						selected( $payment_gateway, 'eway', false ),
						esc_html__( 'eWay', '7listings' ),
						selected( $payment_gateway, 'paypal', false ),
						esc_html__( 'PayPal', '7listings' ),
						esc_attr( 'Office', '7listings' ),
						selected( $payment_gateway, 'credit-card', false ),
						esc_html__( 'Credit Card', '7listings' ),
						selected( $payment_gateway, 'cash', false ),
						esc_html__( 'Cash', '7listings' )
					);
					?>
				</select>
			</div>
		</td>
	</tr>
	<tr class="order_status">
		<th><?php _e( 'Order status', '7listings' ); ?></th>
		<td>
			<div class="view">
				<?php
				$paid    = get_post_meta( get_the_ID(), 'paid', true );
				$display = $paid ? __( 'Paid', '7listings' ) : __( 'Unpaid', '7listings' );
				echo esc_html( $display );
				?>
				<a class="sl-edit dashicons dashicons-edit" href="#"></a>
			</div>
			<div class="edit hidden">
				<select name="payment_status">
					<?php
					Sl_Form::options( get_post_meta( get_the_ID(), 'paid', true ), array(
						0 => __( 'Unpaid', '7listings' ),
						1 => __( 'Paid', '7listings' ),
					) );
					?>
				</select>
			</div>
		</td>
	</tr>
</table>
