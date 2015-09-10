<div class="sl-field departure date">
	<label class="sl-label" for="departure-date"><?php _e( 'Depart', '7listings' ); ?></label>
	<div class="sl-input">
		<input type="text" name="day" value="<?php echo esc_attr( $data['day'] ); ?>" id="departure-date" class="datepicker" readonly>
	</div>
</div>

<div class="sl-field departure time required hidden">
	<label class="sl-label" for="hour-input"><?php _e( 'Time', '7listings' ); ?></label>
	<div class="sl-input">
		<?php
		$departs = ! empty( $resource['depart'] ) ? $resource['depart'] : array();
		echo '<select name="daily_depart" id="daily-depart" class="time-select">';
		echo '<option value="">-</option>';
		foreach ( $departs as $k => $depart )
		{
			if ( empty( $depart ) )
			{
				continue;
			}

			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $k ),
				selected( $k, $data['daily_depart'], false ),
				esc_html( Sl_Helper::time_format( $depart ) )
			);
		}
		echo '</select>';
		?>
	</div>
</div>
