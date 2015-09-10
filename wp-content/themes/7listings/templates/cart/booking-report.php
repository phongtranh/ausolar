<?php
global $wpdb, $data;

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );

$invoice_code = sl_setting( 'invoice_code' ) ? sl_setting( 'invoice_code' ) : get_option( 'blogname' );

$data = get_post_meta( get_the_ID(), 'bookings', true );
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
<div id="booking-header">Booking <?php echo $invoice_code . ' #' . get_post_meta( get_the_ID(), 'booking_id', true ); ?>
	<div id="header-datetime"><?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ); ?></div>
</div>

<?php
foreach ( $data as $item ) :
	$resource_post = $item['post'];

	$post = get_post( $resource_post );

	$resources      = get_post_meta( $resource_post, sl_meta_key( 'booking', $post->post_type ), true );
	$resource       = $resources[$item['resource']];
	$resource_title = $resource['title'];

	if ( 'tour' == $post->post_type ) :

		$date   = sl_booking_meta( 'day', null, $item );
		$prices = Sl_Tour_Helper::get_prices( get_the_ID(), $resource_title, $date );
		?>

		<div id="item-description" class="section-title">
			<h2>
				<label><?php _e( 'Tour', '7listings' ); ?></label>
				<a href="<?php echo get_permalink( $resource_post ); ?>" target="_blank"><?php echo get_the_title( $resource_post ); ?></a>
			</h2>
			<h3>
				<label><?php _e( 'Resource', '7listings' ); ?></label>
				<?php echo sl_booking_meta( 'resource', null, $item ); ?>
			</h3>
		</div>

		<div id="payment-details">
			<span id="payment-title"><?php _e( 'Payment Details', '7listings' ); ?></span>
			<hr />
			<span class="<?php echo sl_booking_meta( 'card_type', null, $item ); ?>"></span><br>
			<?php
			$guests = sl_booking_meta( 'guests', null, $item );
			$name   = sl_booking_meta( 'card_holders_name', null, $item );
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
				<strong><?php echo Sl_Currency::symbol() . sl_booking_meta( 'amount', null, $item ); ?></strong>
			</div>
		</div>

		<table width="360" border="0" cellspacing="0" cellpadding="6">
			<tr>
				<td>
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="140"><?php _e( 'Depart', '7listings' ); ?></td>
							<td width="204" align="right"><p style="text-align:right">
									<strong><?php echo $date; ?></strong></p></td>
						</tr>
						<tr>
							<td colspan="2" align="right"><p style="text-align:right">From:
									<strong><?php echo sl_booking_meta( 'depart_time', null, $item ); ?></strong> To:
									<strong><?php echo sl_booking_meta( 'arrival_time', null, $item ); ?></strong></p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:1px solid #ECECEC">
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="113" valign="top"><?php _e( 'Passengers', '7listings' ); ?></td>
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

										$prices = Sl_Tour_Helper::get_prices( $resource_post, $resource_title, $date );
										foreach ( $types as $type => $label )
										{
											$num = sl_booking_meta( $type, null, $item );
											if ( empty( $num ) || - 1 == $num )
												continue;

											$num_guests[] = "{$num} {$label}";

											$price_type = $price_types[$type];
											$price      = isset( $prices["price_{$price_type}"] ) ? $prices["price_{$price_type}"] : 0;

											$subtotal_guests += $num * $price;
										}

										if ( ! empty( $num_guests ) )
										{
											echo implode( ' - ', $num_guests );
											echo ' - ' . Sl_Currency::symbol() . $subtotal_guests;
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
							<td><?php _e( 'Options', '7listings' ); ?></td>
							<td align="right">
								<strong>
									<?php
									$upsells          = sl_booking_meta( 'upsells', null, $item );
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
							<td><?php _e( 'TOTAL', '7listings' ); ?></td>
							<td align="right"><p style="text-align:right">
									<strong><?php echo Sl_Currency::symbol() . sl_booking_meta( 'amount' ); ?></strong>
								</p></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<h3 class="section-title"><?php _e( 'Guest Information', '7listings' ); ?></h3>

		<table class="widefat">
			<thead>
			<tr>
				<th><?php _e( 'First Name', '7listings' ); ?></th>
				<th><?php _e( 'Last Name', '7listings' ); ?></th>
				<th><?php _e( 'Email', '7listings' ); ?></th>
				<th><?php _e( 'Phone', '7listings' ); ?></th>
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
			$guests = sl_booking_meta( 'guests', null, $item );
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

		<?php if ( $customer_message = sl_booking_meta( 'customer_message', null, $item ) ): ?>
		<h3><?php _e( 'Customer Message', '7listings' ); ?></h3>
		<p><?php echo nl2br( $customer_message ); ?></p>
	<?php endif; ?>


	<?php elseif ( 'accommodation' == $post->post_type ) : ?>

		<div id="item-description" class="section-title">
			<h2>
				<label><?php _e( 'Accommodation', '7listings' ); ?></label>
				<a href="<?php echo get_permalink( $resource_post ); ?>" target="_blank"><?php echo get_the_title( $resource_post ); ?></a>
			</h2>
			<h3>
				<label><?php _e( 'Resource', '7listings' ); ?></label>
				<?php echo sl_booking_meta( 'resource', null, $item ); ?>
			</h3>
		</div>

		<div id="payment-details">
			<span id="payment-title"><?php _e( 'Payment Details', '7listings' ); ?></span>
			<hr />
			<span class="<?php echo sl_booking_meta( 'card_type', null, $item ); ?>"></span><br>
			<?php echo sl_booking_meta( 'card_holders_name', null, $item ); ?>
			<hr />
			<div id="payment-date-amount">
				<?php echo get_the_time( "{$date_format} {$time_format}", get_the_ID() ) ?>
				<br>
				<strong><?php echo '$' . sl_booking_meta( 'amount', null, $item ) . ' ' . sl_setting( 'currency' ); ?></strong>
			</div>
		</div>

		<table width="360" border="0" cellspacing="0" cellpadding="6">
			<tr>
				<td>
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="140"><?php _e( 'Checkin', '7listings' ); ?></td>
							<td width="204" align="right"><p style="text-align:right">
									<strong><?php echo sl_booking_meta( 'checkin', null, $item ); ?></strong></p></td>
						</tr>
						<tr>
							<td width="140"><?php _e( 'Checkout', '7listings' ); ?></td>
							<td width="204" align="right"><p style="text-align:right">
									<strong><?php echo sl_booking_meta( 'checkout', null, $item ); ?></strong></p></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php $guests = sl_booking_meta( 'guests', null, $item ); ?>
			<tr>
				<td style="border-top:1px solid #CCC">
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><?php _e( 'Guests', '7listings' ); ?></td>
							<td align="right"><strong><?php echo count( $guests ); ?></strong></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="border-top:1px solid #CCC">
					<table width="360" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><?php _e( 'TOTAL', '7listings' ); ?></td>
							<td align="right"><p style="text-align:right">
									<strong><?php echo '$' . sl_booking_meta( 'amount', null, $item ) . ' ' . sl_setting( 'currency' ); ?></strong>
								</p></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<h3 class="section-title"><?php _e( 'Guest Information', '7listings' ); ?></h3>

		<table class="widefat">
			<thead>
			<tr>
				<th><?php _e( 'First Name', '7listings' ); ?></th>
				<th><?php _e( 'Last Name', '7listings' ); ?></th>
				<th><?php _e( 'Email', '7listings' ); ?></th>
				<th><?php _e( 'Phone', '7listings' ); ?></th>
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

	<?php endif; ?>

<?php endforeach; ?>
</div>
</body>
</html>
