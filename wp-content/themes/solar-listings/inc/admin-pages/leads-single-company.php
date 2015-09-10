<?php
if ( empty( $_GET['company_id'] ) )
{
	_e( '<p>Invalid request</p>', '7listings' );
	return;
}
$company_id = $_GET['company_id'];

// Todo:
$sources = solar_get_source_with_title();

$membership = get_user_meta( get_post_meta( $company_id, 'user', true ), 'membership', true );
if ( !$membership )
	$membership = 'none';

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
$now = time() + $time_offset;

$key = date( 'm', $now ) . '-' . date( 'Y', $now );
$leads_count = get_post_meta( $company_id, 'leads_count', true );
if ( empty( $leads_count ) )
	$leads_count = array();
if ( empty( $leads_count[$key] ) )
	$leads_count[$key] = '';
$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
$limit = intval( get_post_meta( $company_id, 'leads', true ) );

$title = "<span class='member-$membership'></span>" . get_the_title( $company_id ) . ' (' . ( $limit - solar_leads_count_total( $company_id, $key ) ) . "/$limit)";

echo "<h2>$title</h2>";
printf( __( '<p>Buying since: %s</p>', '7listings' ), date( 'd/m/Y H:i', get_post_meta( $company_id, 'leads_paid', true ) ) );

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
?>

<form action="<?php menu_page_url( 'leads' ); ?>" method="get">
	<input type="hidden" name="page" value="leads">
	<input type="hidden" name="action" value="view_company_leads">
	<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
	<div class="tablenav">
		<?php
		$year = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
		$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'n', $now );
		?>
		<div class="alignleft actions">
			<select name="year">
				<?php
				$max = intval( date( 'Y' ) );
				for ( $i = 2012; $i <= $max; $i++ )
				{
					printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $year, false ), $i );
				}
				?>
			</select>
			<select name="month">
				<?php
				for ( $i = 1; $i <= 12; $i++ )
				{
					printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $month, false ), date( 'M', strtotime( "01-$i-2000" ) ) );
				}
				?>
				<option value="all" <?php selected( 'all', $month ); ?>><?php _e( '- All', '7listings' ); ?></option>
			</select>
			<input type="submit" class="button" value="<?php _e( 'Go', '7listings' ); ?>">
		</div>
		<p class="search-box">
			<input type="search" name="leads_search" value="<?php echo $_GET['leads_search'] ?>">
			<input type="submit" name="" class="button" value="Search #ID">
		</p>
	</div>
</form>

<div class="data-grid" id="leads">
	<div class="header">
		<div class="no"><?php _e( '#', '7listings' ); ?></div>
		<div class="id"><?php _e( 'ID', '7listings' ); ?></div>
		<div class="date"><?php _e( 'Date', '7listings' ); ?></div>
		<div class="source"><?php _e( 'Source', '7listings' ); ?></div>
		<div class="name"><?php _e( 'Name', '7listings' ); ?></div>
		<div class="contact"><?php _e( 'Contact', '7listings' ); ?></div>
		<div class="address"><?php _e( 'Address', '7listings' ); ?></div>
		<div class="request"><?php _e( 'Request', '7listings' ); ?></div>
	</div>
	<?php
	$company = get_post( $company_id );
	$rejected_leads = solar_get_rejected_leads( $company );
	$count = 1;

	$by_month = true;
	if ( 'all' == $month )
		$by_month = false;
	if ( $month < 10 )
		$month = '0' . $month;
	$key = "$month-$year";

	$found = false;
	foreach ( $leads_count as $time => $leads )
	{
		if ( in_array( $_GET['leads_search'], $leads ) )
		{
			$found = true;
			break;
		}
	}
	$lead = $_GET['leads_search'];
	$entry = GFFormsModel::get_lead( $lead );
	$time = strtotime( $entry['date_created'] ) + $time_offset;

	$class = 'row';
	if ( isset( $rejected_leads[$lead] ) )
		$class .= " rejected {$rejected_leads[$lead]}";
	?>
	<div class="<?php echo $class; ?>">
		<div class="no"><?php echo $count++; ?></div>
		<div class="id"><?php echo $entry['id']; ?></div>
		<div class="date"><?php echo date( $date_format, $time ), '<br>', date( $time_format, $time ); ?></div>
        <div class="source">
            <span class="icon <?php echo $sources[$entry['57']][0] ?>"><?php echo $sources[$entry['57']][1] ?></span>
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
	</div>
</div>
