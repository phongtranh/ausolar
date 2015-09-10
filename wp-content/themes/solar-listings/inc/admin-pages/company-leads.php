<?php
if ( empty( $_GET['company_id'] ) )
{
	_e( '<p>Invalid request</p>', '7listings' );

	return;
}

$sources = solar_get_source_with_title();

$convert_sources = $sources;

$company_id = intval( $_GET['company_id'] );
$company    = get_post( $company_id );

$membership = get_user_meta( get_post_meta( $company_id, 'user', true ), 'membership', true );
if ( ! $membership )
	$membership = 'none';

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
$now         = time() + $time_offset;

$year  = isset( $_GET['year'] ) 	? $_GET['year'] 	: date( 'Y', $now );
$month = isset( $_GET['month'] ) 	? $_GET['month'] 	: date( 'n', $now );
$key   = sprintf( '%02d', $month ) . '-' . $year;

$leads_count = get_post_meta( $company_id, 'leads_count', true );

if ( empty( $leads_count ) )
	$leads_count = array();
if ( empty( $leads_count[$key] ) )
	$leads_count[$key] = '';


$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
$limit  = intval( get_post_meta( $company_id, 'leads', true ) );

$report = Solar_Report::single();

$title = "<span class='member-$membership'></span>" . get_the_title( $company_id ) . ' (' . ( $limit - solar_leads_count_total( $company, $key ) ) . "/$limit)";
echo "<h2><a href='/wp-admin/post.php?action=edit&post={$company_id}'>$title</a></h2>";
printf( __( '<p>Buying since: %s</p>', '7listings' ), date( "$date_format $time_format", get_post_meta( $company_id, 'leads_paid', true ) ) );

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

$lead_statuses = array(
	'all'      => 'All Leads',
	'approved' => 'Approved',
	'rejected' => 'Rejected'
);
$sources       = array_values( solar_get_sources() );
$sources[]     = 'total';

foreach ( $sources as $source )
{
	if ( ! isset( $report['incomes'][$source] ) || ! is_numeric( $report['incomes'][$source] ) )
		$report['incomes'][$source] = 0;

	if ( ! isset( $report['sources'][$source] ) || ! is_numeric( $report['sources'][$source] ) )
		$report['sources'][$source] = 0;
}
$payment_method 	= solar_get_company_payment_method( $company_id );

$direct_debit_application_saved = '';

if ( $payment_method == 'Direct Debit' && get_post_meta( $company_id, 'leads_direct_debit_saved', true ) )
	$direct_debit_application_saved = 'Direct Debit application saved';
?>

<h4>Payment Method: <?php echo $payment_method; ?> </h4>
<h5><?php echo $direct_debit_application_saved ?></h5>

<form action="<?php menu_page_url( 'leads' ); ?>" method="get">
	<input type="hidden" name="page" value="leads">
	<input type="hidden" name="action" value="view_company_leads">
	<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
	<div class="tablenav">
		<div class="alignleft actions">
			<select name="year" ng-model="year">
				<?php
				$max = intval( date( 'Y' ) );
				for ( $i = 2012; $i <= $max; $i++ )
				{
					printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $year, false ), $i );
				}
				?>
			</select>
			<select name="month" ng-model="month">
				<?php
				for ( $i = 1; $i <= 12; $i++ )
				{
					printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $month, false ), date( 'M', strtotime( "01-$i-2000" ) ) );
				}
				?>
				<option value="all" <?php selected( 'all', $month ); ?>><?php _e( '- All', '7listings' ); ?></option>
			</select>

            <select name="status">
            	<?php
				$selected = isset( $_GET['status'] ) ? $_GET['status'] : '';
				SL_Form::options( $selected, $lead_statuses );
            	?>
			</select>

			<button class="button" type="submit">
                <?php _e( 'Go', '7listings' ); ?>
            </button>
		</div>

		<p class="search-box">
			<input type="search" name="leads_search" value="<?php echo isset( $_GET['leads_search'] ) ? $_GET['leads_search'] : ''; ?>">
			<input type="submit" name="" class="button" value="Search">
		</p>
	</div>
</form>

<hr>

<div id="single-report">
	<section class="section">
		<label class="label"><h3>Income</h3></label>
		<span class="total big">$ <?php echo number_format( $report['incomes']['total'], 0 ) ?></span>
	</section>

	<section class="section">
		<label class="label">Leads</label>
		<span class="total big"><?php echo $report['total_leads'] ?></span>
	</section>

	<section class="section">
		<label class="label">Approved Leads</label>
		<span class="total big"><?php echo $report['sources']['total'] ?></span>
	</section>

	<br>

    <!-- Todo: Match CSS classes same as variable names to reuse -->
	<section class="section">
		<label class="label"><h3>Sources</h3></label>
		<div class="span1 website"><span class="asq-website">Website</span></div>
		<div class="span1 internal"><span class="asq-internal">Internal</span></div>
		<div class="span1 phone"><span class="asq-phone">Phone</span></div>
		<div class="span1 energysmart"><span class="energy-smart">Energy Smart</span></div>
		<div class="span1 your-solar-quotes"><span class="icon your-solar-quotes">Your Solar Quotes</span></div>
		<div class="span1 solar-lead-factory"><span class="icon solar-lead-factory">Solar Lead Factory</span></div>
		<div class="span1 exclusive-leads"><span class="icon exclusive-leads">Exclusive Leads</span></div>
		<div class="span1 jack-media"><span class="icon jack-media">Jack Media</span></div>
		<div class="span1 solar-leads"><span class="icon solar-leads">Solar Leads</span></div>
		<div class="span1 ocere"><span class="icon ocere">Ocere</span></div>
		<div class="span1 green-utilities"><span class="icon green-utilities">Green Utilities</span></div>
		<div class="span1 solar-power-today"><span class="icon solar-power-today">Solar Power Today</span></div>
		<div class="span1 cleantechnia"><span class="icon cleantechnia">Cleantechnia</span></div>
	</section>

	<section class="section">
		<label class="label">Income</label>
        <?php foreach ( solar_get_sources() as $key => $source ): ?>
            <div class="span1 <?php echo $source ?>">
                $ <?php echo number_format( $report['incomes'][$source], 0 ) ?>
            </div>
        <?php endforeach; ?>
	</section>

	<section class="section">
		<label class="label">Leads</label>
        <?php foreach ( solar_get_sources() as $key => $source ): ?>
            <div class="span1 <?php echo $source ?>"><?php echo $report['sources'][$source] ?></div>
        <?php endforeach; ?>
	</section>

	<?php
	if ( isset( $report['incomes']['total'] ) && $report['incomes']['total'] > 0 ) :
		$bar_percent = array();
		foreach ( $report['incomes'] as $key => $value )
		{
			$bar_percent[$key] = floor( $value / $report['incomes']['total'] * 100 * 100 ) / 100;
			if ( $bar_percent[$key] < 0.3 )
				$bar_percent[$key] = 0;
		}
		$bar_percent['your-solar-quotes'] = 0;
		$bar_percent['your-solar-quotes'] = 200 - array_sum( $bar_percent );
    ?>
		<section class="section overall total">
			<label class="label"></label>
			<div class="chart-container">
				<div class="bar-chart sources">
					<?php foreach ( solar_get_sources() as $key => $source ): ?>
						<div title="# <?php echo $source ?> Leads - Total income $<?php echo
						$report['incomes'][$source] ?>"
							 style="width: <?php echo $bar_percent[$source] ?>%"
							 class="part <?php echo $source ?>"></div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

	<?php endif; ?>

    <br><br>

	<?php
	foreach ( solar_get_rejection_reasons() as $reason => $title )
	{
		if ( ! isset( $report['reasons'][$reason] ) || ! is_numeric( $report['reasons'][$reason] ) )
			$report['reasons'][$reason] = 0;
	}
    ?>

	<section class="section">
		<label class="label"><h3><?php _e( 'Rejections', '7listings' ); ?></h3></label>
		<span class="total big"><?php echo count( $report['rejected_leads'] ) ?></span>
	</section>

	<section class="section rejections-breakdown">
		<label class="label"></label>
        <?php foreach( solar_get_rejection_reasons() as $reason => $title ): ?>
            <div class="span2"><span class="<?php echo $reason ?>"></span>
                <?php echo $report['reasons'][$reason]; ?>
            </div>
        <?php endforeach; ?>
	</section>

	<?php
	if ( count( $report['rejected_leads'] ) > 0 ):
		$bar_percent = array();
		foreach ( $report['reasons'] as $key => $value )
		{
			$bar_percent[$key] = number_format( $value / count( $report['rejected_leads'] ) * 100, 2 );
		}
        $bar_percent['other'] = 0;
        $bar_percent['other'] = 100 - array_sum( $bar_percent );
		?>
		<section class="section overall rejections">
			<label class="label"></label>
			<div class="chart-container">
				<div class="bar-chart reasons">
                    <?php foreach ( solar_get_rejection_reasons() as $reason => $title ): ?>
                        <div title="# <?php echo $title ?>: <?php echo $bar_percent[$reason] ?>%"
                             style="width:<?php echo $bar_percent[$reason] ?>%"
                             class="part bar-<?php echo $reason ?>">
                        </div>
                    <?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
</div>

<br><br><br><br>
<a target="_blank" class="button small" href="/?action=admin-leads-print&amp;company_id=<?php echo $company_id ?>&amp;report_year=<?php echo $year ?>&amp;report_month=<?php echo $month ?>"><?php _e( 'Print Lead Report', '7listings' ); ?></a>
<br><br>

<div class="data-grid" id="leads">
	
	<?php if ( !empty( $_GET['leads_search'] ) ): ?>
		<h3>All leads under the search: <code><?php echo $_GET['leads_search']; ?></code></h3>
	<?php endif; ?>

	<div class="header">
		<div class="no"><?php _e( '#', '7listings' ); ?></div>
		<div class="id"><?php _e( 'ID', '7listings' ); ?></div>
		<div class="date"><?php _e( 'Date', '7listings' ); ?></div>
		<div class="source"><?php _e( 'Source', '7listings' ); ?></div>
		<div class="name"><?php _e( 'Name', '7listings' ); ?></div>
		<div class="contact"><?php _e( 'Contact', '7listings' ); ?></div>
		<div class="address"><?php _e( 'Address', '7listings' ); ?></div>
		<div class="request"><?php _e( 'Request', '7listings' ); ?></div>
		<div class="action"><?php _e( 'Action', '7listings' ); ?></div>
	</div>

	<?php
	$rejected_leads = solar_get_rejected_leads( $company );

	$by_month = true;
	if ( 'all' == $month )
		$by_month = false;
	if ( $month < 10 )
		$month = '0' . $month;
	//$key = "$month-$year";

	$yyyy = date( 'Y' );
	$mm   = date( 'm' );

	if ( ! empty( $_GET['year'] ) && ! empty( $_GET['month'] ) )
	{
		$yyyy = trim( $_GET['year'] );
		$mm   = trim( $_GET['month'] );
	}

	// Generate mm-yyyy string
	$key = sprintf( '%02d', $mm ) . '-' . $yyyy;

	$count = 0;

	foreach ( $leads_count as $time => $leads )
	{
		if ( $by_month && $time != $key )
			continue;
		$leads = array_filter( array_unique( explode( ',', $leads . ',' ) ) );
		$count += count( $leads );
	}

	foreach ( $leads_count as $time => $leads )
	{
		if ( $by_month && $time != $key )
			continue;

		$leads = array_filter( array_unique( explode( ',', $leads . ',' ) ) );

		$leads = array_reverse( $leads );

		foreach ( $leads as $lead )
		{
			if ( isset( $_GET['status'] ) && $_GET['status'] === 'approved' && isset( $rejected_leads[$lead] ) )
				continue;

			if ( isset( $_GET['status'] ) && $_GET['status'] === 'rejected' && $rejected_leads[$lead] === null )
				continue;

			$entry = GFFormsModel::get_lead( $lead );

			if ( !empty ( $_GET['leads_search'] ) )
			{
				$continue = true;
				
				foreach ( $entry as $field => $value )
				{
					if ( $value === trim( $_GET['leads_search'] ) )
					{
						$continue = false;
					}
				}

				if ( $continue ) continue;
			}

			$time = strtotime( $entry['date_created'] ) + $time_offset;

			$class = 'row';
			if ( isset( $rejected_leads[$lead] ) )
			{
				$class .= " rejected {$rejected_leads[$lead]}";
			}
			?>
			<div class="<?php echo $class; ?>">
				<div class="no"><?php echo $count--; ?></div>
				<div class="id"><?php echo $entry['id']; ?></div>
				<div class="date"><?php echo date( $date_format, $time ), '<br>', date( $time_format, $time ); ?></div>
                <div class="source">
                    <span class="icon <?php echo $convert_sources[$entry['57']][0] ?>"><?php echo $convert_sources[$entry['57']][1] ?></span>
                </div>
				<div class="name"><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></div>
				<div class="contact">
					<?php echo $entry['3']; ?><br>
					<?php echo "<a href='mailto:{$entry['11']}'>{$entry['11']}</a>"; ?>
				</div>
				<div class="address"><?php echo $entry['17.1'], '<br>', $entry['17.3'], '<br>', implode( ', ', array( $entry['17.4'], $entry['17.5'] ) ); ?></div>
				<div class="request">
					<?php
					echo '<span class="label">' . __( 'Type:', '7listings' ) . '</span> <span class="detail">' . $fields['leads_type_entry'][$entry['30']] . '</span><br>';
					$request_value = array( 'Solar PV' );
					if ( 'Yes' == $entry[56] )
						$request_value[] = 'Solar Hot Water';
					echo '<span class="label">' . __( 'Service:', '7listings' ) . '</span> <span class="detail">' . implode( ', ', $request_value ) . '</span><br>';
					echo '<span class="label">' . __( 'Assessment:', '7listings' ) . '</span> <span class="detail">' . $fields['assessment'][$entry['47']] . '</span>';
					?>
				</div>
				<div class="action">
					<?php if( !isset( $rejected_leads[$lead] ) ): ?>
					<a href="#modal-reject" role="button" data-toggle="modal" class="button small white reject-lead" data-lead_id="<?php echo $entry['id']; ?>"><?php _e( 'Reject', '7listings' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}
	?>
</div>

<?php
// Reject leads modal
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
