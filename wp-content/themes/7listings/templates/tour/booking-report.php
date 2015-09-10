<?php
global $wpdb;

$resource_post  = sl_booking_meta( 'post_id' );
$resources      = get_post_meta( $resource_post, sl_meta_key( 'booking', 'tour' ), true );
$resource_title = sl_booking_meta( 'resource' );

$found = false;
if ( ! empty( $resources ) )
{
	$sanitized_title = sanitize_title( $resource_title );
	foreach ( $resources as $resource )
	{
		if ( $sanitized_title == sanitize_title( $resource['title'] ) )
		{
			$found = true;
			break;
		}
	}
}
if ( ! $found )
	return;

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );

$invoice_code = sl_setting( 'invoice_code' ) ? sl_setting( 'invoice_code' ) : get_option( 'blogname' );
?>
<!DOCTYPE html>
<html>
<head>
	<?php wp_head(); ?>
	<style>
		@media print {
			#adminmenuwrap, #footer, .tablenav, .button-primary.export, .add-new-h2, .check-column, .search-box, #screen-meta-links, .row-actions, #icon-edit, .column-paid, #adminmenuwrap, #adminmenuback, #total-overview .total, #wpwrap, #wpcontent, #wpbody {
				display: none;
			}
		}
	</style>
</head>

<body <?php body_class(); ?> onload="javascript:window.print()">

<div id="booking-wrapper">

<div id="booking-single">

<div id="booking-header">Booking <?php echo $invoice_code . ' #' . sl_booking_meta( 'booking_id' ); ?>
	<div id="header-datetime"><?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ); ?></div>
</div>

<div id="item-description" class="section-title">
	<h2>
		<label>Tour</label>
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
	<?php
	$guests = sl_booking_meta( 'guests' );
	$name   = sl_booking_meta( 'card_holders_name' );
	// Get name from Guests
	if ( ! $name )
	{
		$name = array();
		if ( ! empty( $guests[0]['first'] ) )
			$name[] = $guests[0]['first'];
		if ( ! empty( $guests[0]['last'] ) )
			$name[] = $guests[0]['last'];

		$name = empty( $name ) ? '' : implode( ' ', $name );
	}

	if ( $name )
		echo "{$name}";
	?>
	<hr />
	<div id="payment-date-amount">
		<?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ); ?>
		<br>
		<strong><?php echo Sl_Currency::symbol() . sl_booking_meta( 'amount' ); ?></strong>
	</div>
</div>

<table width="360" border="0" cellspacing="0" cellpadding="6">
	<tr>
		<td>
			<table width="360" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="140">Depart</td>
					<td width="204" align="right"><p style="text-align:right">
							<strong><?php echo sl_booking_meta( 'day' ); ?></strong></p></td>
				</tr>
				<tr>
					<td colspan="2" align="right"><p style="text-align:right">From:
							<strong><?php echo sl_booking_meta( 'depart_time' ); ?></strong> To:
							<strong><?php echo sl_booking_meta( 'arrival_time' ); ?></strong></p></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #ECECEC">
			<table width="360" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="113" valign="top">Passengers</td>
					<td width="231" align="right" valign="top">
						<p style="text-align:right"><strong>
								<?php
								$num_guests      = array();
								$subtotal_guests = 0;
								$types           = array(
									'adults'   => 'Adults',
									'children' => 'Children',
									'seniors'  => 'Seniors',
									'families' => 'Families',
									'infants'  => 'Infants',
								);
								$price_types     = array(
									'adults'   => 'adult',
									'children' => 'child',
									'seniors'  => 'senior',
									'families' => 'family',
									'infants'  => 'infant',
								);
								foreach ( $types as $type => $label )
								{
									$num = sl_booking_meta( $type );
									if ( empty( $num ) || - 1 == $num )
										continue;

									$num_guests[] = "{$num} {$label}";

									if ( ! empty( $resource["price_{$price_types[$type]}"] ) )
										$subtotal_guests += $num * $resource["price_{$price_types[$type]}"];
								}

								if ( ! empty( $num_guests ) )
								{
									echo implode( ' - ', $num_guests );
									echo ' - $' . $subtotal_guests;
								}
								?>
							</strong></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #ECECEC">
			<table width="360" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>Options</td>
					<td align="right">
						<strong>
							<?php
							$upsells          = sl_booking_meta( 'upsells' );
							$upsells_text     = array();
							$subtotal_upsells = 0;
							if ( is_array( $upsells ) && ! empty( $upsells ) ):
								$no_guests = count( $num_guests );
								?>
								<p style="text-align:right">

									<?php
									foreach ( $upsells as $upsell )
									{
										if ( ! isset( $upsell['name'] ) || ! isset( $upsell['num'] ) )
											continue;

										$upsells_text[] = "{$upsell['num']} {$upsell['name']}";

										if ( isset( $resource['upsell_items'] ) && is_array( $resource['upsell_items'] ) )
										{
											$k = array_search( $upsell['name'], $resource['upsell_items'] );
											if ( ! empty( $resource['upsell_prices'][$k] ) )
											{
												$price = $resource['upsell_prices'][$k];
												if ( sl_setting( 'tour_multiplier' ) && ! empty( $resource['upsell_multipliers'][$k] ) )
													$price *= $no_guests;
												$subtotal_upsells += $upsell['num'] * $price;
											}
										}
									}

									echo implode( ' - ', $upsells_text );
									echo ' - ' . Sl_Currency::symbol() . $subtotal_upsells;
									?>
								</p>
							<?php endif; ?>
						</strong>
					</td>
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
	$row    = '
					<tr>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
					</tr>
				';
	$guests = sl_booking_meta( 'guests' );
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
