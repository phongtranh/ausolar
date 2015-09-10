<?php
/* ATR Cart Template
 * last edit: 5.0.5
 *
 * @package WordPress
 * @subpackage 7Listings
 */
?>

<?php get_header(); ?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

	<div id="main-wrapper" class="container">

		<?php
		$cart = SL_Cart::get_instance();
		if ( ! $cart->get_cart_contents_count() ) :
			echo '<div class="alert">' . __( 'Your cart is empty.', '7listings' ) . '</div>';
		else :
			$data  = $cart->get_cart();
			$total = 0;

			$has_all_details = true;
			foreach ( $data as $index => $item )
			{
				if ( empty( $item['data'] ) )
				{
					$has_all_details = false;
					break;
				}
			}

			if ( ! $has_all_details && 1 < count( $data ) )
				echo '<div class="alert">' . __( 'Please enter <strong>all booking details</strong> to proceed', '7listings' ) . '</div>';
			?>
			<div id="cart-wrapper">
				<table class="table bookings">
					<thead>
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th><?php _e( 'Resource', '7listings' ); ?></th>
						<th><?php _e( 'Details', '7listings' ); ?></th>
						<th class="total"><?php _e( 'Price', '7listings' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					$tr = '
						<tr%s>
							<td class="remove"><a href="#" class="remove-from-cart" data-index="%s" title="%s">&#x2716;</a></td>
							<td class="thumb">%s</td>
							<td class="resource"><h3><a href="%s">%s</a></h3><h4>%s</h4></td>
							<td class="details">%s</td>
							<td class="total">%s</td>
						</tr>
					';
					foreach ( $data as $index => $item )
					{
						$post    = get_post( $item['post'] );
						$booking = get_post_meta( $post->ID, sl_meta_key( 'booking', $post->post_type ), true );

						$resource      = $booking[$item['resource']];
						$resource_slug = sanitize_title( $resource['title'] );

						$item_data = isset( $item['data'] ) ? $item['data'] : array();
						$total += floatval( sl_cart_data( 'amount', true, $item_data ) );

						$guests = isset( $item_data['guests'] ) ? count( $item_data['guests'] ) : 0;
						$guest = sl_cart_data( 'guest', true, $item_data );

						if ( 'tour' == $post->post_type )
						{
							$time1 = sl_cart_data( 'day', true, $item_data );
							$time2 = sl_cart_data( 'depart_time', true, $item_data );

							$time1 = $time1 ? __( 'Day: ', '7listings' ) . $time1 : '';
							$time2 = $time2 ? __( 'Time: ', '7listings' ) . $time2 : '';
						}
						elseif ( 'accommodation' == $post->post_type || 'rental' == $post->post_type )
						{
							$time1 = sl_cart_data( 'checkin', true, $item_data );
							$time2 = sl_cart_data( 'checkout', true, $item_data );

							$time1 = $time1 ? __( 'Checkin: ', '7listings' ) . $time1 : '';
							$time2 = $time2 ? __( 'Checkout: ', '7listings' ) . $time2 : '';
						}

						$thumb = sl_resource_thumb( $post, $resource );

						printf(
							$tr,
							$item_data ? ' class="has-details"' : '',
							$index,
							__( 'Delete Item', '7listings' ),
							$thumb,
							get_permalink( $post->ID ), esc_html( $post->post_title ), $resource['title'],
							sprintf(
								'%s
								%s
								%s
								%s
								<a href="%s"%s>%s</a>',
								$time1 ? $time1 . '<br>' : '',
								$time2 ? $time2 . '<br>' : '',
								$guests ? __( '# Guests: ', '7listings' ) . $guests . '<br>' : '',
								$guests ? sprintf(
									__( 'Name: %s<br>Email: %s%s<br>', '7listings' ),
									$guest['first'] . ' ' . $guest['last'],
									$guest['email'],
									$guest['phone'] ? '<br>' . __( 'Phone: ', '7listings' ) . $guest['phone'] : ''
								) : '',
								home_url( "book/{$post->post_name}/{$resource_slug}/?cart" ),
								empty( $item_data ) ? ' class="button booking"' : ' class="button small"',
								empty( $item_data ) ? __( 'Enter Booking Details', '7listings' ) : __( 'Edit', '7listings' )
							),
							empty( $item_data ) ? __( 'N/A', '7listings' ) : Sl_Currency::format( sl_cart_data( 'amount', true, $item_data ), "type=plain" )
						);
					}
					?>
					</tbody>
					<tfoot>
					<tr>
						<th colspan="4" class="total"><?php _e( 'Total', '7listings' ); ?></th>
						<th class="total amount">
							<?php
								if ( empty( $item_data ) )
									_e( 'N/A', '7listings' );
								else
									echo Sl_Currency::format( $total, "type=plain" );
							?>
						</th>
					</tr>
					</tfoot>
				</table>
			</div><!-- #cart-wrapper -->

			<div class="payment-area">
			<?php
				$count      = 0;
				$conditions = array(
					sl_setting( 'paypal' ),
					sl_setting( 'eway' ) && sl_setting( 'eway_shared' ),
				);
				foreach ( $conditions as $condition )
				{
					if ( $condition )
						$count ++;
				}
				$show   = $count > 0;
				$select = $count > 1;

				if ( $show ) :
					?>
						<?php if ( $select ) : ?>
							<div class="sl-field payment-gateway required">
								<label class="sl-label"><?php _e( 'Pay with', '7listings' ); ?></label>
								<div id="payment" class="sl-input gateway-options">
									<ul class="payment_methods">
										<li>
											<input type="radio" id="gateway_eway" name="payment_gateway" value="eway" tabindex="209">
											<label for="gateway_eway" class="eway" title="Pay with eWay">
												<span class="eway logo"><?php esc_attr_e( 'eWay', '7listings' ); ?></span>
												<div class="accepted-cards">
													<span class="visa icon">Visa</span>
													<span class="mastercard icon">Master Card</span>
												</div>
											</label>
											<?php if ( esc_textarea( sl_setting( 'eway_description' ) ) ) : ?>
												<div class="payment_box payment_methode_eway">
													<p><?php echo esc_textarea( sl_setting( 'eway_description' ) ); ?></p>
												</div>
											<?php endif; ?>
										</li>
										<li>
											<input type="radio" id="gateway_paypal" name="payment_gateway" value="paypal" tabindex="210">
											<label for="gateway_paypal" class="paypal" title="Pay with PayPal">
												<span class="paypal logo"><?php esc_attr_e( 'Paypal', '7listings' ); ?></span>
												<div class="accepted-cards">
													<span class="visa icon">Visa</span>
													<span class="mastercard icon">Master Card</span>
													<span class="amex icon">American Express</span>
												</div>
												<a href="https://www.paypal.com/au/webapps/mpp/paypal-popup" class="about_paypal" onclick="javascript:window.open('https://www.paypal.com/au/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;" title="What is PayPal?" rel="nofollow"><?php esc_attr_e( 'What is PayPal?', '7listings' ); ?></a>
											</label>
											<?php if ( esc_textarea( sl_setting( 'paypal_description' ) ) ) : ?>
												<div class="payment_box payment_eway">
													<p><?php echo esc_textarea( sl_setting( 'paypal_description' ) ); ?></p>
												</div>
											<?php endif; ?>
										</li>
									</ul>
									<label class="sl-input-warning"></label>
								</div>
							</div>

							<div class="sl-field error-box hidden">
								<div class="sl-input">
									<div class="error error-gateways not-transform"></div>
								</div>
							</div>
						<?php endif; ?>

						<div class="sl-field nav hidden">
							<div class="sl-input">
								<input type="submit" name="submit" class="button pay booking" value="<?php _e( 'Pay Now', '7listings' ); ?>" tabindex="211">
							</div>
						</div>

				<?php
				endif;
			?>
			</div>
		<?php
		endif;
		?>


	</div><!-- .container -->

<?php get_footer(); ?>
