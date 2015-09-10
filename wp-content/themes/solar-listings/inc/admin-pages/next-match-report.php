<h2>Next Match Records</h2>

<?php

$dir = wp_upload_dir();

$file = $dir['path'] . "/match-log.php";

$logs = array();

if ( file_exists( $file ) )
{
	$logs = file_get_contents( $file );
	$logs = explode( '###', $logs );
}

echo '<!--';
print_r( $logs );
echo '-->';
//$logs[$today->format( 'Y-m-d H:i:s' )][] = $log;
if ( count ( $logs ) > 0 ) :
	?>
	<table class="wp-list-table widefat fixed next-match">
		<thead>
			<tr>
				<th>Lead ID</th>
				<th>Before</th>
				<th>After</th>
				<th>Processed At</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th>Lead ID</th>
				<th>Before</th>
				<th>After</th>
				<th>Processed At</th>
			</tr>
		</tfoot>

		<tbody>
		<?php foreach ( $logs as $log ):
			$log = unserialize( $log );
			// Add styling for recent added companies
			$before = explode( ',', $log['before'] );
			$after 	= explode( ',', $log['after'] );
			$diff 	= array_diff( $after, $before );

			if ( empty ( $diff ) )
				continue;

			$replace = array();
			foreach ( $diff as $new_company )
				$replace[] = '<span style="background: #d54e21; color: #fff;">'.$new_company.'</span>';

			$log['after'] = str_replace( $diff, $replace, $log['after'] );
			?>
			<tr>
				<td><?php echo $log['lead_id'];     ?>
				<td><?php echo $log['before'];      ?>
				<td><?php echo $log['after'];       ?>
				<td><?php echo $log['created_at']; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php
else:
?>
	<div class="warning"><?php _e( 'There are no matched lead on this month', '7listings' ); ?></div>
<?php
endif;