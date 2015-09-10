<?php

$_GET['year'] 	= isset( $_GET['report_year'] ) ? $_GET['report_year'] : null;
$_GET['month'] 	= isset( $_GET['report_month'] ) ? $_GET['report_month'] : null;

$company_id = $company->ID;

$membership = get_user_meta( get_post_meta( $company_id, 'user', true ), 'membership', true );
if ( !$membership )
	$membership = 'none';

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
$now = current_time( 'timestamp', true );

$year = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm', $now );

$lead_enabled = get_post_meta( $company_id, 'leads_enable', true );

if ( $lead_enabled != 1 )
{
	$year = date( 'Y', $now );
	$month = date( 'm', $now );
}

$key = $month . '-' . $year;
if ( $month < 10 && ! str_contains( $month, '0' ) )
	$key = '0' . $key;

$leads_count = get_post_meta( $company_id, 'leads_count', true );
if ( empty( $leads_count ) )
	$leads_count = array();
if ( empty( $leads_count[$key] ) )
	$leads_count[$key] = '';
$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
$limit = intval( get_post_meta( $company_id, 'leads', true ) );
$report = sl_get_single_by_id( $company_id );
$fields = array(
	'leads_type'         => array(
		'residential' => 'Residential',
		'commercial'  => 'Commercial',
	),
	'leads_type_entry'   => array(
		'Home'     => 'Residential',
		'Business' => 'Commercial',
	),
	'service_type'       => array(
		'solar_pv'        => __( 'Solar PV', '7listings' ),
		'solar_hot_water' => __( 'Solar Hot Water', '7listings' ),
		'solar_ac'        => __( 'Solar A/C', '7listings' ),
	),
	'assessment'         => array(
		'I prefer the installers to visit my property and give a firm price' => 'Onsite',
		'No need for an installer to visit, an estimate via email is fine'   => 'Phone/Email',
		'I have no preference'                                               => 'No preference',
		'I_have_no_preference'                                               => 'No preference',
	),
	'assessment_company' => array(
		'onsite'      => __( 'Onsite', '7listings' ),
		'phone_email' => __( 'Phone/Email', '7listings' ),
	)
);

$leads = $leads_count[$key];
$leads = array_filter( array_unique( explode( ',', $leads . ',' ) ) );
$leads = array_reverse( $leads );

// Show print lead report only when:
// - In previous months
// - If in 1 previous month, current day must be > 7
$show_print = $year != date( 'Y', $now );
if ( !$show_print )
{
	$show_print = intval( date( 'n', $now ) ) - $month > 1;
	if ( !$show_print )
	{
		$current_day = intval( date( 'd', $now ) );
		$show_print = intval( date( 'n', $now ) ) - $month == 1 && $current_day > 7;
	}
}
$company_leads_logs = get_post_meta( $company_id, 'leads_logs', true );
$company_leads_logs = @unserialize( $company_leads_logs );

$logs = array();
foreach ( $company_leads_logs as $log )
	$logs[$log['lead_id']] = $log['created_at'];
?>

<h2><?php _e( 'My Leads', '7listings' ); ?></h2>

<?php if ( get_post_meta( $company_id, 'leads_enable', true ) ) : ?>
<form action="" method="get" class="form-inline">
	<select name="report_year">
		<?php
		$max = intval( date( 'Y', $now ) );
		for ( $i = 2012; $i <= $max; $i++ )
		{
			printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $year, false ), $i );
		}
		?>
	</select>
	<select name="report_month">
		<?php
		for ( $i = 1; $i <= 12; $i++ )
		{
			printf( '<option value="%s"%s>%s</option>', $i, selected( $i, intval( $month ), false ), date( 'M', strtotime( "01-$i-2000" ) ) );
		}
		?>
	</select>

	<?php if ( get_post_meta( $company_id, 'leads_enable', true ) ) : ?>
		<input type="submit" class="button white small" value="<?php _e( 'Go', '7listings' ); ?>">
	<?php else : ?>
		<input type="hidden" name="action" value="print">
		<input type="submit" class="button small" value="<?php esc_attr_e( 'Print Lead Report', '7listings' ); ?>">
	<?php endif; ?>
</form>
<?php endif; ?>

<?php if ( get_post_meta( $company_id, 'leads_enable', true ) || solar_leads_count_total( $company_id, $key ) > 0 ) : ?>

	<h3>
		<span class='member-<?php echo $membership; ?>'></span>
		<?php echo date( 'F', strtotime( "01-$month-2000" ) ) . ' (' . ( $limit - solar_leads_count_total( $company_id, $key ) ) . "/$limit)"; ?>
	</h3>

	<?php if ( empty( $leads ) ) : ?>

		You do not have leads this month yet.<br />
		Maybe consider increasing your service area to be matched with more leads.

	<?php else : ?>
		<h4>Leads can be rejected for the following reasons:</h4>

		<ul class="sl-list custom check-sign gray">
			<li>The lead is a duplicate lead;</li>
			<li>You are not able to establish contact with the prospect;</li>
			<li>The Prospect is outside your elected service area or;</li>
			<li>Any other reasonable grounds.</li>
		</ul>

		<br><br>

		<?php if ( $show_print ) : ?>
			<a class="button small" href="<?php echo add_query_arg( 'action', 'print' ); ?>"><?php _e( 'Print Lead Report', '7listings' ); ?></a>
			<br><br>
		<?php endif; ?>

		<div class="table-responsive">
			<table class="table table-bordered table-striped" id="leads">
				<thead>
					<tr class="row">
						<th class="no" style="width: 45px;"><?php _e( '#', '7listings' ); ?></th>
						<th class="id"><?php _e( 'ID', '7listings' ); ?></th>
						<th class="date"><?php _e( 'Date', '7listings' ); ?></th>
						<th class="name"><?php _e( 'Name', '7listings' ); ?></th>
						<th class="contact"><?php _e( 'Contact', '7listings' ); ?></th>
						<th class="address"><?php _e( 'Address', '7listings' ); ?></th>
						<th class="request"><?php _e( 'Request', '7listings' ); ?></th>
						<th class="action"><?php _e( 'Action', '7listings' ); ?></th>
					</tr>
				</thead>
				
				<tbody>
				<?php
				$rejected_leads = solar_get_rejected_leads( $company );
				$count = count( $leads );

				// Show reject button only for leads within X days from today (X is in admin settings)
				$current_day = strtotime( date( 'Y-m-d', $now ) );
				$min_day = $current_day - 86400 * intval( sl_setting( 'solar_lead_rejection_duration' ) );
				
				foreach ( $leads as $lead )
				{
					$entry = GFFormsModel::get_lead( $lead );

					$time = strtotime( $entry['date_created'] ) + $time_offset;
					if ( ! empty( $logs[$entry['id']] ) )
					{
						$time = strtotime( $logs[$entry['id']] ) + $time_offset;
					}

					$show_reject_button = $min_day <= $time;

					$class = 'row';
					if ( isset( $rejected_leads[$lead] ) )
						$class .= ' rejected';
					if ( $show_reject_button )
						$class .= ' active';
					?>
					<tr class="<?php echo $class; ?>">
						<td class="no"><?php echo $count--; ?></td>
						<td class="id"><?php echo $entry['id']; ?></td>
						<td class="date">
						<?php
							echo date( $date_format, $time );
							if ( empty( $logs[$entry['id']] ) )
								echo '<br>', date( $time_format, $time ); 
						?>
						</td>
						<td class="name"><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></td>
						<td class="contact">
							<?php echo $entry['3']; ?><br>
							<?php echo $entry['33']; ?><br>
							<?php echo "<a href='mailto:{$entry['11']}'>{$entry['11']}</a>"; ?>
						</td>
						<td class="address"><?php echo $entry['17.1'], '<br>', $entry['17.3'], '<br>', implode( ', ', array( $entry['17.4'], $entry['17.5'] ) ); ?></td>
						<td class="request">
							<?php
							echo '<span class="label">' . __( 'Type:', '7listings' ) . '</span> <span class="detail">' . $fields['leads_type_entry'][$entry['30']] . '</span><br>';
							$request_value = array( 'Solar PV' );
							if ( 'Yes' == $entry[56] )
								$request_value[] = 'Solar Hot Water';
							echo '<span class="label">' . __( 'Service:', '7listings' ) . '</span> <span class="detail">' . implode( ', ', $request_value ) . '</span><br>';
							echo '<span class="label">' . __( 'Assessment:', '7listings' ) . '</span> <span class="detail">' . $fields['assessment'][$entry['47']] . '</span>';
							?>
						</td>
						<td class="action">
						<?php
						$time_created = ( $current_day - $time ) / 86400;
						if ( $time_created <= 30 && ! isset( $rejected_leads[$lead] ) )
						{
						?>
							<a href="<?php echo add_query_arg( array( 'action'  =>  'print-request' , 'lead_id' =>  $lead ) ); ?>" role="button" class="button small white" data-lead_id="<?php echo $entry['id']; ?>" target="_blank"><?php _e( 'Print', '7listings' ); ?></a>
						<?php
						}
						?>
							<?php if ( $show_reject_button ) : ?>
								<a href="#modal-reject" role="button" data-toggle="modal" class="button small white reject-lead" data-lead_id="<?php echo $entry['id']; ?>"><?php _e( 'Reject', '7listings' ); ?></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>

	<?php // Reject leads modal
	// Tan: Todo: Remove this because I use action to send mail
	$sent_mail = '{no_send}';
	if( $report['sources']['total'] > 5 )
	{
		$percentage_reject_this_month = ( count( $report['rejected_leads'] ) / $report['sources']['total'] ) * 100;
		if( $percentage_reject_this_month > 30 )
		{
			$sent_mail = '{send_notify_about_percentage_reject}';
		}
	}
	?>

	<div id="modal-reject" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Reject', '7listings' ); ?> # <span class="lead-id"></span> - <span class="system-size"></span></h3>
			<span class="name"></span>, <span class="city"></span>, <span class="state"></span>
		</div>
		<div class="modal-body">
			<?php
			$shortcode = '[gravityforms id="36" title="false" description="false" field_values="leads_company=%CURRENT_COMPANY%&company_percentage_reject=%SEND_MAIL%"]';
			$shortcode = str_replace( '%CURRENT_COMPANY%', $company->post_title, $shortcode );
			$shortcode = str_replace( '%SEND_MAIL%', $sent_mail, $shortcode );
			echo do_shortcode( $shortcode );
			?>
		</div>
	</div>

<?php endif; ?>