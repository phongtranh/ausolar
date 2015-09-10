<?php
/**
 * The Template for ATTRACTION BOOKING PAGES
 *
 * @package    WordPress
 * @subpackage 7Listings
 */

global $post, $resources, $resource, $wpdb;
if ( isset( $_GET['eway-verify'] ) )
{
	include 'eway-verify.php';
	exit;
}

if ( isset( $_GET['ipn'] ) )
{
	include 'ipn.php';
	exit;
}

if ( isset( $_GET['cart'] ) )
{
	include 'cart-edit.php';
	exit;
}

$zero = Sl_Currency::format( '0.00', 'type=plain' );
?>

<?php get_header(); ?>

<?php
$departure_type = isset( $resource['departure_type'] ) ? $resource['departure_type'] : '';
?>

<?php get_template_part( 'templates/parts/featured-title' ); ?>

<div id="main-wrapper" class="container">

	<form action="" method="post" name="booking_form" id="content" class="left booking-form" novalidate>

		<input type="hidden" name="amount" value="">

		<section class="panel booking-details active">
			<h2 class="panel-title"><?php the_title(); ?></h2>

			<?php get_template_part( 'templates/booking/price' ); ?>

			<div class="panel-content">
				<?php get_template_part( 'templates/booking/resource-select' ); ?>

				<section class="sl-field guests hidden">
					<label class="sl-label"><?php _e( 'Passengers', '7listings' ); ?></label>
					<div class="sl-input"></div>
				</section>

				<?php
				if ( ! empty( $resource['upsells'] ) && ! empty( $resource['upsell_items'] ) ):

					$found = false;

					foreach ( $resource['upsell_items'] as $k => $item )
					{
						if ( ! empty( $item ) && ! empty( $resource['upsell_prices'][$k] ) )
						{
							$found = true;
							break;
						}
					}

					if ( $found ):
						?>

						<section class="sl-field upsells hidden">
							<label class="sl-label"><?php _e( 'Options', '7listings' ); ?></label>
							<div class="sl-input">
								<?php
								foreach ( $resource['upsell_items'] as $k => $item )
								{
									if ( empty( $item ) || empty( $resource['upsell_prices'][$k] ) )
										continue;

									$class = '';
									if ( sl_setting( 'attraction_multiplier' ) && ! empty( $resource['upsell_multipliers'][$k] ) )
										$class = ' class="multiply"';

									echo "
										<div class='upsell'>
											<select name='upsell_$k' class='quantity'>
												<option value='-1'>-</option>
									";
									for ( $i = 1; $i <= 10; $i ++ )
									{
										echo "<option value='$i'>$i</option>";
									}
									echo "
											</select>
											<span class='description'>$item</span>
											" . Sl_Currency::format( $resource['upsell_prices'][$k] ) . "
										</div>
									";
								}
								?>
							</div>
						</section>

					<?php
					endif;
				endif;
				?>

				<nav class="sl-field nav hidden">
					<div class="sl-input">
						<a href="#" class="next button" id="to-contact"><?php _e( 'Next', '7listings' ); ?></a>
					</div>
				</nav>

			</div>
		</section>

		<?php get_template_part( 'templates/booking/contact' ); ?>

		<?php get_template_part( 'templates/booking/eway-hosted' ); ?>

	</form>

	<section id="sidebar" class="right summary">
		<h2><?php _e( 'Summary', '7listings' ); ?></h2>

		<h3 class="title listing"><?php the_title(); ?></h3>
		<h4 class="title resource"><?php echo $resource['title']; ?></h4>

		<?php
		if ( ! empty( $resource['photos'] ) )
			echo '<figure class="thumbnail">' . sl_resource_photo( $resource['photos'] ) . '</figure>';
		else
			sl_broadcasted_thumbnail( 'sl_pano_medium' );
		?>

		<div class="row depart hidden">
			<div class="left"><?php _e( 'Depart', '7listings' ); ?></div>
			<div class="right">
				<span class="day"></span>
				<span class="time"></span>
			</div>
		</div>

		<div class="passengers hidden">
			<h4><?php _e( 'Passengers', '7listings' ); ?></h4>
			<div class="row adults hidden">
				<div class="left"><?php _e( '0 Adults', '7listings' ); ?></div>
				<div class="right"></div>
			</div>
			<div class="row children hidden">
				<div class="left"><?php _e( '0 Children', '7listings' ); ?></div>
				<div class="right"></div>
			</div>
			<div class="row seniors hidden">
				<div class="left"><?php _e( '0 Seniors', '7listings' ); ?></div>
				<div class="right"></div>
			</div>
			<div class="row families hidden">
				<div class="left"><?php _e( '0 Families', '7listings' ); ?></div>
				<div class="right"></div>
			</div>
			<div class="row infants hidden">
				<div class="left"><?php _e( '0 Infants', '7listings' ); ?></div>
				<div class="right"></div>
			</div>

			<div class="row total_guests subtotal hidden">
				<div class="left"><?php _e( 'Total', '7listings' ); ?></div>
				<div class="right"></div>
			</div>
		</div>

		<?php
		if ( ! empty( $resource['upsells'] ) && ! empty( $resource['upsell_items'] ) )
		{
			echo '
				<div class="options hidden">
					<h4>' . __( 'Options', '7listings' ) . '</h4>
			';

			foreach ( $resource['upsell_items'] as $k => $item )
			{
				echo "
					<div class='row upsell_$k hidden'>
						<div class='left'>$item</div>
						<div class='right'>$zero</div>
					</div>
				";
			}

			echo '
				</div>
			';
		}
		?>

		<div class="row total">
			<div class="left"><?php _e( 'Total', '7listings' ); ?></div>
			<div class="right"><?php echo $zero; ?></div>
		</div>
	</section>

</div><!-- #main-wrapper -->

<?php get_footer(); ?>
