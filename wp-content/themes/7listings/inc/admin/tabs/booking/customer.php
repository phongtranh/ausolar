<?php
if ( 'cart' == get_post_meta( get_the_ID(), 'type', true ) )
{
	$items = get_post_meta( get_the_ID(), 'bookings', true );
	foreach ( $items as $key_item => $item )
	{
		printf(
			'<span class="%s cart-item">
					<span class="listing-title"><a href="%s" target="_blank">%s</a></span><br>
					<span class="resource-title">%s</span>
			</span>',
			get_post_type( $item['post'] ),
			get_permalink( $item['post'] ),
			get_the_title( $item['post'] ),
			$item['data']['resource']
		);
		?>
		<table class="widefat customer-details">
			<thead>
			<tr>
				<th><?php _e( 'First Name', '7listings' ); ?></th>
				<th><?php _e( 'Last Name', '7listings' ); ?></th>
				<th><?php _e( 'Email', '7listings' ); ?></th>
				<th><?php _e( 'Phone', '7listings' ); ?></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $item['data']['guests'] as $key_guest => $guest )
			{
				$key = $key_item . $key_guest;
				?>
				<tr>
					<td class="sl-first">
						<div class="view"><?php echo $guest['first'] ?></div>
						<div class="edit hidden">
							<input type="text" name="_first_name_customer-<?php echo $key; ?>" value="<?php echo $guest['first'] ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-last">
						<div class="view"><?php echo $guest['last']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_last_name_customer-<?php echo $key; ?>" value="<?php echo $guest['last']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-email">
						<div class="view"><?php echo $guest['email']; ?></div>
						<div class="edit hidden">
							<input type="email" name="_email_customer-<?php echo $key; ?>" value="<?php echo $guest['email']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-phone">
						<div class="view"><?php echo $guest['phone']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_phone_customer-<?php echo $key; ?>" value="<?php echo $guest['phone']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-customer-action">
						<div class="view">
							<a class="sl-edit dashicons dashicons-edit" href="#"></a>
						</div>
					</td>
				</tr>
			<?php
			}
			?>
			</tbody>
		</table>
		<?php
	}
}
else
{
	?>
	<table class="widefat customer-details">
		<thead>
		<tr>
			<th><?php _e( 'First Name', '7listings' ); ?></th>
			<th><?php _e( 'Last Name', '7listings' ); ?></th>
			<th><?php _e( 'Email', '7listings' ); ?></th>
			<th><?php _e( 'Phone', '7listings' ); ?></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$guests = get_post_meta( get_the_ID(), 'guests', true );
		if ( ! empty( $guests ) && is_array( $guests ) )
		{
			foreach ( $guests as $key => $guest )
			{
				?>
				<tr>
					<td class="sl-first">
						<div class="view"><?php echo $guest['first']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_first_name_customer-<?php echo $key; ?>" value="<?php echo $guest['first']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-last">
						<div class="view"><?php echo $guest['last']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_last_name_customer-<?php echo $key; ?>" value="<?php echo $guest['last']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-email">
						<div class="view"><?php echo $guest['email']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_email_customer-<?php echo $key; ?>" value="<?php echo $guest['email']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-phone">
						<div class="view"><?php echo $guest['phone']; ?></div>
						<div class="edit hidden">
							<input type="text" name="_phone_customer-<?php echo $key; ?>" value="<?php echo $guest['phone']; ?>" autocomplete="off">
						</div>
					</td>
					<td class="sl-customer-action">
						<div class="view">
							<a class="sl-edit dashicons dashicons-edit" href="#"></a>
						</div>
					</td>
				</tr>
			<?php
			}
		}
		?>
		</tbody>
	</table>
<?php
}
