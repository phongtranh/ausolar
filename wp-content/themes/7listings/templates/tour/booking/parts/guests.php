<div class="sl-field guests hidden">
	<label class="sl-label" for="guest-adult"><?php _e( 'Passengers', '7listings' ); ?></label>
	<div class="sl-input">
		<?php
		if ( $data['guests'] )
		{
			$max = Sl_Tour_Helper::get_max_allocation( get_the_ID(), $resource['title'], $data['day'] );
			echo Sl_Tour_Helper::format_allocation_select( $max, $resource, $data );
		}
		?>
	</div>
</div>
