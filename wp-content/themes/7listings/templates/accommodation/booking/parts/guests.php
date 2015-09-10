<div class="sl-field passenger-type">
	<label class="sl-label" for="guests"><?php _e( 'Guests', '7listings' ); ?></label>
	<div class="sl-input">
		<select name="guests" id="guests">
			<?php
			$num_guests = count( $data['guests'] );
			for ( $i = 1; $i <= $resource['max_occupancy']; $i ++ )
			{
				printf(
					'<option value="%d"%s>%d</option>',
					$i,
					selected( $i, $num_guests, false ),
					$i
				);
			}
			?>
		</select>

		<div class="right occupancy">
			<div class="guests">
				<span class="title desc"><?php _e( 'Occupancy', '7listings' ); ?></span>
				<span class="value"><?php echo $resource['occupancy']; ?></span>
			</div>
			<div class="guests max">
				<span class="title desc"><?php _e( 'Max. Guests', '7listings' ); ?></span>
				<span class="value max"><?php echo $resource['max_occupancy']; ?></span>
			</div>
			<div class="rate hidden">
				<span class="desc"><?php _e( 'Rate:', '7listings' ); ?></span> $<strong><?php echo $resource['price']; ?></strong><?php _e( '/day', '7listings' ); ?>
			</div>
		</div>

	</div>
</div>
