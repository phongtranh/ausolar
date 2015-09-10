<h2><?php _e( 'Reports', '7listings' ); ?></h2>
<table width="100%">
	<tr>
		<th scope="col"><?php _e( 'ID', '7listings' ); ?></th>
		<th scope="col"><?php _e( 'Description', '7listings' ); ?></th>
		<th scope="col"><?php _e( 'Total', '7listings' ); ?></th>
		<th scope="col"><?php _e( 'Paid On', '7listings' ); ?></th>
	</tr>
	<?php
	$log = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership_log', true );
	if ( empty( $log ) )
		$log = array();
	$log = array_reverse( $log );

	$tpl     = '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
	$counter = 1;
	foreach ( $log as $entry )
	{
		$range = '';
		if ( 'month' == $entry[2] )
			$range .= __( 'Monthly: ', '7listings' );
		if ( 'year' == $entry[2] )
			$range .= __( 'Yearly: ', '7listings' );
		$range .= date( 'd/m/Y', $entry[1] ) . ' - ' . date( 'd/m/Y', strtotime( "+1 {$entry[2]}", $entry[1] ) );

		$desc = ucwords( $entry[0] );
		if ( ( 'upgrade' == $entry[0] || 'change' == $entry[0] ) && isset( $entry[4] ) && isset( $entry[5] ) )
			$desc .= ': ' . ucwords( $entry[5] ) . ' &rarr; ' . ucwords( $entry[4] );
		if ( 'close' != $entry[0] )
			$desc .= '<br>' . $range;

		printf(
			$tpl,
			$counter ++,
			$desc,
			$entry[3] . ' ' . sl_setting( 'currency' ),
			date( 'd/m/Y H:i', $entry[1] )
		);
	}
	?>
</table>

<?php do_action( 'company_edit_tab_reports_after' ); ?>
