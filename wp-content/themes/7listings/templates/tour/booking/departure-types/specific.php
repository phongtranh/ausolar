<div class="sl-field departure date">
	<label class="sl-label" for="departure-date"><?php _e( 'Depart', '7listings' ); ?></label>
	<div class="sl-input">
		<?php
		$days         = array( 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' );
		$enabled_days = array();
		foreach ( $days as $k => $day )
		{
			if ( ! empty( $resource["{$day}_depart"] ) )
			{
				$enabled_days[] = $k;
			}
		}
		$enabled_days = implode( ',', $enabled_days );
		?>
		<input type="text" name="day" value="<?php echo esc_attr( $data['day'] ); ?>" id="departure-date" data-days="<?php echo esc_attr( $enabled_days ); ?>" class="datepicker" readonly>
	</div>
</div>

<div class="sl-field departure time required hidden">
	<label class="sl-label" for="hour-input"><?php _e( 'Time', '7listings' ); ?></label>
	<div class="sl-input">
		<?php
		foreach ( $days as $i => $day )
		{
			if ( empty( $resource["{$day}_depart"] ) )
			{
				continue;
			}

			echo "<select name='{$day}_depart' data-day='$i' class='specific-day-depart time-select'>";
			echo '<option value="">-</option>';
			foreach ( $resource["{$day}_depart"] as $k => $depart )
			{
				if ( empty( $depart ) )
				{
					continue;
				}

				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $k ),
					selected( $k, $data["{$day}_depart"], false ),
					esc_html( Sl_Helper::time_format( $depart ) )
				);
			}
			echo '</select>';
		}
		?>
	</div>
</div>
