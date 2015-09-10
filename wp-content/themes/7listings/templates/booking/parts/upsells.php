<?php
if ( empty( $resource['upsells'] ) || empty( $resource['upsell_items'] ) )
{
	return;
}

/**
 * Check to make sure there are upsells
 */
$found = false;
foreach ( $resource['upsell_items'] as $k => $item )
{
	if ( ! empty( $item ) && ! empty( $resource['upsell_prices'][$k] ) )
	{
		$found = true;
		break;
	}
}

if ( ! $found )
{
	return;
}
?>

<section class="sl-field upsells hidden">
	<label class="sl-label"><?php _e( 'Options', '7listings' ); ?></label>
	<div class="sl-input">
		<?php
		foreach ( $resource['upsell_items'] as $k => $item )
		{
			if ( empty( $item ) || empty( $resource['upsell_prices'][$k] ) )
			{
				continue;
			}

			$selected_upsell = wp_list_filter( $data['upsells'], array( 'name' => $item ) );
			$selected_upsell = $selected_upsell ? $selected_upsell[0]['num'] : 0;
			?>
			<div class="upsell">
				<select id="upsell_<?php echo esc_attr( $k ); ?>" name="upsell_<?php echo esc_attr( $k ); ?>" class="quantity">
					<option value="-1">-</option>
					<?php
					for ( $i = 1; $i <= 10; $i ++ )
					{
						printf(
							'<option value="%d"%s>%d</option>',
							esc_attr( $i ),
							selected( $i, $selected_upsell, false ),
							esc_html( $i )
						);
					}
					?>
				</select>
				<label for="upsell_<?php echo esc_attr( $k ); ?>" class="sl-label-inline description"><?php echo esc_html( $item ); ?></label>
				<?php echo Sl_Currency::format( $resource['upsell_prices'][$k] ); ?>
			</div>
		<?php
		}
		?>
	</div>
</section>
