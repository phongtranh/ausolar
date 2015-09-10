<?php
$resource_post = sl_booking_meta( 'post_id' );

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );

$invoice_code = sl_setting( 'invoice_code' ) ? sl_setting( 'invoice_code' ) : get_option( 'blogname' );
?>
<!DOCTYPE html>
<html>
<head>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> onload="javascript:window.print()">

<div id="booking-wrapper">

	<div id="booking-single">

		<div id="booking-header">Booking <?php echo $invoice_code . ' #' . sl_booking_meta( 'booking_id' ); ?>
			<div id="header-datetime"><?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ); ?></div>
		</div>

		<div id="item-description" class="section-title">
			<h2>
				<label>Accommodation</label>
				<a href="<?php echo get_permalink( $resource_post ); ?>" target="_blank"><?php echo get_the_title( $resource_post ); ?></a>
			</h2>
			<h3>
				<label>Resource</label>
				<?php echo sl_booking_meta( 'resource' ); ?>
			</h3>
		</div>

		<div id="payment-details">
			<span id="payment-title">Payment Details</span>
			<hr />
			<span class="<?php echo sl_booking_meta( 'card_type' ); ?>"></span><br>
			<?php echo sl_booking_meta( 'card_holders_name' ); ?>
			<hr />
			<div id="payment-date-amount">
				<?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ); ?>
				<br>
				<strong><?php echo Sl_Currency::symbol() . sl_booking_meta( 'amount' ); ?></strong></div>
		</div>

		<table width="360" border="0" cellspacing="0" cellpadding="6">
			<tr>
				<td>
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="140">Checkin</td>
							<td width="204" align="right"><p style="text-align:right">
									<strong><?php echo sl_booking_meta( 'checkin' ); ?></strong></p></td>
						</tr>
						<tr>
							<td width="140">Checkout</td>
							<td width="204" align="right"><p style="text-align:right">
									<strong><?php echo sl_booking_meta( 'checkout' ); ?></strong></p></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php $guests = sl_booking_meta( 'guests' ); ?>
			<tr>
				<td style="border-top:1px solid #CCC">
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>Guests</td>
							<td align="right"><strong><?php echo count( $guests ); ?></strong></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:1px solid #CCC">
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td>TOTAL</td>
							<td align="right"><p style="text-align:right">
									<strong><?php echo Sl_Currency::symbol() . sl_booking_meta( 'amount' ); ?></strong>
								</p></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<h3 class="section-title">Guest Information</h3>

		<table class="widefat">
			<thead>
			<tr>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
				<th>Phone</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$row = '
				<tr>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
				</tr>
				';
			if ( ! empty( $guests ) && is_array( $guests ) )
			{
				foreach ( $guests as $guest )
				{
					if ( $guest['email'] )
						$mailto = '<a href="mailto:' . antispambot( $guest['email'] ) . '">' . antispambot( $guest['email'] ) . '</a>';
					else
						$mailto = '';

					if ( empty( $guest['first'] ) && empty( $guest['last'] ) )
						continue;
					printf( $row, $guest['first'], $guest['last'], $mailto, $guest['phone'] );
				}
			}
			?>
			</tbody>
		</table>

		<?php if ( $customer_message = sl_booking_meta( 'customer_message' ) ): ?>
			<h3>Customer Message</h3>
			<p><?php echo nl2br( $customer_message ); ?></p>
		<?php endif; ?>

	</div>
</div>
</body>
</html>
