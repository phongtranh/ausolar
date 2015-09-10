<div class="sl-field departure date">
	<label class="sl-label" for="departure-date"><?php _e( 'Depart', '7listings' ); ?></label>
	<div class="sl-input">
		<input type="text" name="day" value="<?php echo esc_attr( $data['day'] ); ?>" id="departure-date" class="datepicker" readonly>
	</div>
</div>

<div class="sl-field departure time required hidden">
	<label class="sl-label" for="hour-input"><?php _e( 'Time', '7listings' ); ?></label>
	<div class="sl-input">
		<?php // Display 2 select dropdowns for hour and minute ?>
		<span class="time-input hour">
			<select id="hour-input" class="hour-input" required>
				<option value="">-</option>
				<?php
				list( $hour, $minute ) = explode( ':', $data['custom_depart'] . ':' );
				for ( $i = 0; $i < 24; $i ++ )
				{
					// Add padding zero
					$j = sprintf( '%02d', $i );
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $j ),
						selected( $j, $hour, false ),
						esc_html( $j )
					);
				}
				?>
			</select>
			<label for="hour-input" class="sl-label-below"><?php _e( 'Hours', '7listings' ); ?></label>
		</span>
		<span class="time-input minute">
			<select id="minute-input" class="minute-input" required>
				<?php
				for ( $i = 0; $i < 12; $i ++ )
				{
					// Add padding zero
					$j = sprintf( '%02d', $i * 5 );
					printf(
						'<option value="%02d"%s>%02d</option>',
						esc_attr( $j ),
						selected( $j, $minute, false ),
						esc_html( $j )
					);
				}
				?>
			</select>
			<label for="minute-input" class="sl-label-below"><?php _e( 'Minutes', '7listings' ); ?></label>
		</span>

		<?php // Hidden input to store time value ?>
		<input type="hidden" name="custom_depart" value="<?php echo esc_attr( $data['custom_depart'] ); ?>">
	</div>
</div>
