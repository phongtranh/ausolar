<?php

$_GET['year'] 		= $_GET['report_year'];
$_GET['month'] 		= $_GET['report_month'];
$_GET['sources'] 	= unserialize( base64_decode( $_GET['sources'] ) );

if ( ! is_user_logged_in() )
{
    get_template_part( 'templates/company/user-admin/form-login' );
    return;
}
else
{
    if( ! current_user_can( 'edit_theme_options' ) )
    {
        $wholesale = get_posts( array(
            'post_type'      => 'wholesale',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_key'       => 'user',
            'meta_value'     => get_current_user_id(),
        ) );

        if ( empty( $wholesale ) )
        {
            get_template_part( 'templates/company/user-admin/no-company' );
            return;
        }

        $wholesale = current( $wholesale );

        $source = get_post_meta( $wholesale->ID, 'wholesale_code', true );

        if ( empty ( $source ) )
        {
            get_template_part( 'templates/wholesale/review' );
            return;
        }

	    if ( $_GET['month'] < 8 && $_GET['year'] <= 2014 )
	    {
		    get_template_part( 'templates/wholesale/denied' );
		    return;
	    }
    }
}

$year    = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month   = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );
$month   = intval( $month );
$year    = intval( $year );
$paged   = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) - 1 : 0;

// Todo:
$sources = solar_get_source_with_title();

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
$now = time() + $time_offset;

$year = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'n', $now );

if ( 'all' == $month )
{
	$start_date = "$year-01-01 00:00:00";
	$end_date = "$year-12-31 23:59:59";
}
else
{
	$days = cal_days_in_month( CAL_GREGORIAN, $month, $year );
	$start_date = "$year-$month-01 00:00:00";
	$end_date = "$year-$month-$days 23:59:59";
}

$start_date = date( 'Y-m-d H:i:s', strtotime( $start_date ) - $time_offset );
$end_date = date( 'Y-m-d H:i:s', strtotime( $end_date ) - $time_offset );

$all_entries = GFFormsModel::get_leads( 1, 0, 'DESC', '', 0, 999999, null, null, false, $start_date, $end_date );

// Split the page into several part and get current part information
$report = Solar_Report::wholesale();
$leads = $report['raw'];

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php _e( 'Company Leads Report', '7listings' ); ?></title>
		<?php wp_head(); ?>
		<link rel="stylesheet" type="text/css" href="<?php echo get_stylesheet_directory_uri(); ?>/css/admin.css" />
		<style>
			.wrapper { width: 980px; margin: 30px auto; }

			#print-logo { width: 120px; height: 120px; margin: 0; float: left; }
			.header-text{ margin-left: 140px; font-size: 12px; line-height: 1; font-style: italic; }
			#site-title { font-style: initial; line-height: 1; margin: 0 0 20px 0; }
			.header-text p { margin: 0 0 5px 0; }

			.heading { clear: both; font-weight: bold; margin: 60px 0 20px; }
			
			#leads { font-size: 13px; }
			#leads div > div { float: none !important; vertical-align: top; }
			#leads .no { width: 30px; }
			#leads.data-grid .id { text-align: left; width: 50px; }
			#leads.data-grid .date { width: 100px; }
			#leads .name { width: 110px; min-width: 0; }
			#leads .contact { width: 180px; }
			#leads .address { width: 130px; margin: 0 !important; }
			#leads .request { width: 240px; margin: 0 !important; }
			#leads .status { width: 70px; }
		</style>
	</head>

	<body <?php body_class(); ?>>
		<div class="wrapper">
			<h1>Wholesale Report <small>(<?php echo count( $leads ) ?>)</small></h1>
			
			<ul class="subsubsub">
				<li class="total">Total Matches <span class="update-count"><?php echo count( $report['rejected_leads' ] ) + count( $report['approved_leads'] ) ?></span></li>
				<li class="total">Total Rejection <span class="update-count"><?php echo count( $report['rejected_leads'] ); ?></span></li>
				<li class="total">Total Approved <span class="update-count"><?php echo count( $report['approved_leads'] ); ?></span></li>
			</ul>

			<div id="leads" class="data-grid">
				<div class="header">
					<div class="id"><?php _e( 'ID', '7listings' ); ?></div>
					<div class="date"><?php _e( 'Date', '7listings' ); ?></div>
					<div class="source"><?php _e( 'Source', '7listings' ); ?></div>
					<div class="name"><?php _e( 'Name', '7listings' ); ?></div>
					<div class="state"><?php _e( 'State', '7listings' ); ?></div>
					<div class="count matches"><?php _e( 'Matches', '7listings' ); ?></div>
					<div class="count rejections"><?php _e( 'Rejections', '7listings' ); ?></div>
					<div class="count approved"><?php _e( 'Approved', '7listings' ); ?></div>
				</div>

				<?php foreach ( $all_entries as $entry ): 
					$time = strtotime( $entry['date_created'] ) + $time_offset;
					
					if ( array_key_exists( $entry['id'], $leads ) ):
					?>
					<div class="row">
						<div class="id"><?php echo $entry['id'] ?></div>
						<div class="date"><?php echo date( $date_format, $time ) ?></div>
						<div class="source">
							<span class="icon <?php echo $sources[$entry['57']][0] ?>"><?php echo $sources[$entry['57']][1] ?></span>
						</div>
						<div class="name"><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></div>
						<div class="state"><?php echo $entry['17.4']; ?></div>
						<div class="count matches"><?php echo $leads[$entry['id']]['approved'] + $leads[$entry['id']]['rejected'] ?></div>
						<div class="count rejection"><?php echo $leads[$entry['id']]['rejected'] ?></div>
						<div class="count approved"><?php echo $leads[$entry['id']]['approved'] ?></div>
					</div>
					<?php 
					endif;
				endforeach; ?>
			</div>
		</div>
		<script>window.onload = window.print;</script>
	</body>
</html>