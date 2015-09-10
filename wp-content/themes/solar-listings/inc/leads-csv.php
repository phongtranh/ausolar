<?php

$get_sources = isset( $_GET['sources'] ) ? join( ',', $_GET['sources'] ) : 'All';

// Check if it views from front page
if( ! current_user_can( 'edit_theme_options' ) )
{
	$wholesale = get_posts( array(
		'post_type'      => 'wholesale',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	$wholesale = current( $wholesale );

	$source = get_post_meta( $wholesale->ID, 'wholesale_code', true );

	if ( empty( $source ) ) exit;

	$_GET['sources'] = array( $source );
	$_GET['year']   = $_GET['report_year'];
	$_GET['month']  = $_GET['report_month'];

	if ( $_GET['month'] < 8 && $_GET['year'] <= 2014 ) exit;
}

$year  = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );
$month = intval( $month );
$year  = intval( $year );

ob_clean();

$file_name = "Export_{$get_sources}_{$year}_{$month}.csv";

header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( 'Content-Description: File Transfer' );
header( "Content-type: text/csv" );
header( "Content-Disposition: attachment; filename={$file_name}" );
header( "Expires: 0" );
header( "Pragma: public" );

$fh = @fopen( 'php://output', 'w' );

// Todo: Update this, set array values to one place
$sources = solar_get_source_with_title();

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_report_timezone_offset() * 3600;
$now         = time() + $time_offset;

$year  = isset ( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
$month = isset ( $_GET['month'] ) ? $_GET['month'] : date( 'n', $now );
if ( 'all' == $month )
{
	$start_date = "$year-01-01 00:00:00";
	$end_date   = "$year-12-31 23:59:59";
}
else
{
	$days       = cal_days_in_month( CAL_GREGORIAN, $month, $year );
	$start_date = "$year-$month-01 00:00:00";
	$end_date   = "$year-$month-$days 23:59:59";
}

$start_date = date( 'Y-m-d H:i:s', strtotime( $start_date ) - $time_offset );
$end_date   = date( 'Y-m-d H:i:s', strtotime( $end_date ) - $time_offset );

$all_entries = GFFormsModel::get_leads( 1, 0, 'DESC', '', 0, 999999, null, null, false, $start_date, $end_date );

$report = Solar_Report::wholesale();
$raw    = $report['raw'];

@fputcsv( $fh, array(
	'Lead ID', 'Date', 'Source',
	'Name', 'State', 'Matches',
	'Rejections', 'Approved'
) );

foreach ( $all_entries as $entry )
{
	if ( array_key_exists( $entry['id'], $raw ) )
	{
		$time    = strtotime( $entry['date_created'] ) + $time_offset;
		$date    = date( $date_format, $time );
		$name    = $entry['1.3'] . ' ' . $entry['1.6'];
		$matches = $raw[$entry['id']]['approved'] + $raw[$entry['id']]['rejected'];

		@fputcsv( $fh, array(
			$entry['id'], $date,
			$sources[$entry['57']][1],
			$name, $entry['17.4'], $matches,
			$raw[$entry['id']]['rejected'],
			$raw[$entry['id']]['approved']
		) );
	}
}

@fclose( $fh );
