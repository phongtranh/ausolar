<style type="text/css">
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

<?php

$_GET['year']   = isset( $_GET['report_year'] ) ? $_GET['report_year'] : date('Y');
$_GET['month']  = isset( $_GET['report_month'] ) ? $_GET['report_month'] : date('m');

$all_leads = get_filtered_leads( 'array' );
$all_leads = substr_count( $all_leads, ',' ) + 1;

$_GET['sources'] = ( isset( $_GET['source'] ) ) ? array( $_GET['source'] ) : array( 'E' );

$year    = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month   = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );
$month   = intval( $month );
$year    = intval( $year );
$paged   = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) - 1 : 0;

$sources = solar_get_sources();
unset( $sources['I'], $sources['C'], $sources['P'] );

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

$total_leads = count( $leads );
$actual_matches = count( $report['rejected_leads' ] ) + count( $report['approved_leads'] );
?>

<script type="text/javascript">
var state_pie_chart = <?php echo json_encode( $report['states']['state_pie_chart'] ); ?>;
</script>

<h2>Supplier Report <small>(<?php echo count( $leads ) ?>)</small></h1>

<form id="filter" method="get">
    <div class="table-nav">
        <div class="alignleft actions">
            <input type="hidden" name="page" value="supplier-report">
            <select name="source">
                <?php 
                $selected_source = isset( $_GET['source'] ) ? $_GET['source'] : '';
                foreach ( $sources as $key => $code ) : ?>
                <option value="<?php echo $key ?>" <?php selected( $key, $selected_source ) ?>><?php echo str_title( $code ); ?></option>
                <?php endforeach; ?>            
            </select>

            <select name="report_year">
                <?php
                $max = intval( date( 'Y' ) );
                for ( $i = 2014; $i <= $max; $i ++ )
                {
                    printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $year, false ), $i );
                }
                ?>
            </select>

            <select name="report_month">
                <?php
                for ( $i = 1; $i <= 12; $i++ )
                {
                    printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $month, false ), date( 'M', strtotime( "01-$i-2000" ) ) );
                }
                ?>
                <option value="all" <?php selected( 'all', $month ); ?>>
                    <?php _e( '- All', '7listings' ); ?>
                </option>
            </select>

            <button class="button" type="submit" id="btn_submit">
                <?php _e( 'Go', '7listings' ); ?>
            </button>
        </div>
    </div>
</form>

<br><br><br><br>

<h3>General</h3>
<table>
    <tr>
        <td style="width: 160px;">Leads</td>
        <td style="width: 60px"><?php echo $total_leads ?></td>
        <td style="width: 160px">vs All Sources</td>
        <td style="width: 60px"><?php echo number_format( $total_leads / $all_leads * 100, 2 ) ?>%</td>
    </tr>

    <tr>
        <td>Potential Matches</td>
        <td colspan="3"><?php echo $total_leads * 4 ?></td>
    </tr>

    <tr>
        <td>Actual Matches</td>
        <td><?php echo $actual_matches ?></td>
        <td>vs Potential Matches</td>
        <td><?php echo number_format( $actual_matches / $total_leads / 4 * 100, 2 ) ?> %</td>
    </tr>

    <tr>
        <td>Total Rejection</td>
        <td><?php echo count( $report['rejected_leads'] ); ?></td>
        <td>vs Potential Matches</td>
        <td><?php echo number_format( count( $report['rejected_leads'] ) / $total_leads / 4 * 100, 2 ) ?> %</td>
    </tr>

    <tr>
        <td>Total Approved</td>
        <td><?php echo count( $report['approved_leads'] ); ?></td>
        <td>vs Potential Matches</td>
        <td><?php echo number_format( count( $report['approved_leads'] ) / $total_leads / 4 * 100, 2 ) ?> %</td>
    </tr>
</table>

<div class="row-fluid">
    <div class="span12">
        <h3><?php _e( 'States', '7listings' ); ?></h3>
        <div id="chart-states"></div>
    </div>
</div>

<div class="row-fluid">
    <h3>Rejections</h3>

    <div class="span12">
        <?php
        $bar_percent = array();
        foreach ( $report['reasons'] as $key => $value )
        {
	        $bar_percent[$key] = ( array_sum( $report['reasons'] ) > 0 )
		        ? number_format( $value / array_sum( $report['reasons'] ) * 100, 2 )
		        : 0;
        }
        $bar_percent['other'] = 0;
        $bar_percent['other'] = 100 - array_sum( $bar_percent );
        ?>

        <section class="section overall rejections">
	        <label class="label"></label>
	        <div class="chart-container">
		        <div class="bar-chart reasons">
			        <?php foreach ( solar_get_rejection_reasons() as $reason => $title ): ?>
				        <div title="# <?php echo $title ?>: <?php echo $report['reasons'][$reason] ?>"
				             style="width:<?php echo $bar_percent[$reason] ?>%"
				             class="part bar-<?php echo $reason ?>"></div>
			        <?php endforeach; ?>
		        </div>
	        </div>
        </section>
    </div>
</div>

<h3>Leads</h3>
<div id="leads" class="data-grid">
    <div class="header">
        <div class="id"><?php _e( 'ID', '7listings' ); ?></div>
        <div class="date"><?php _e( 'Date', '7listings' ); ?></div>
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
                <div class="name"><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></div>
                <div class="state"><?php echo $entry['17.4']; ?></div>
                <div class="count matches"><?php echo $leads[$entry['id']]['approved'] +
                        $leads[$entry['id']]['rejected'] ?></div>
                <div class="count rejection"><?php echo $leads[$entry['id']]['rejected'] ?></div>
                <div class="count approved"><?php echo $leads[$entry['id']]['approved'] ?></div>
            </div>
        <?php
        endif;
    endforeach; ?>
</div>
