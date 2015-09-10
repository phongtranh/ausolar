<?php

if ( ! is_user_logged_in() )
{
    get_template_part( 'templates/company/user-admin/form-login' );
    return;
}

//if ( ! current_user_can( 'wholesale_owner' ) )
//{
//    get_template_part( 'templates/wholesale/denied' );
//    return;
//}

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

//require_once get_stylesheet_directory() . '/inc/reports/report.php';

$_GET['sources'] = array( $source );
$_GET['year']   = $_GET['_y'];
$_GET['month']  = $_GET['_m'];

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
<h1>Wholesale Report <small>(<?php echo count( $leads ) ?>)</small></h1>

<form id="filter" method="get">
    <div class="table-nav">
        <div class="alignleft actions">
            <select name="_y">
                <?php
                $max = intval( date( 'Y' ) );
                for ( $i = 2012; $i <= $max; $i ++ )
                {
                    printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $year, false ), $i );
                }
                ?>
            </select>

            <select name="_m">
                <?php
                for ( $i = 1; $i <= 12; $i++ )
                {
                    printf( '<option value="%s"%s>%s</option>', $i, selected( $i, $month, false ), date( 'M', strtotime( "01-$i-2000" ) ) );
                }
                ?>
                <option value="all" <?php selected( 'all', $month ); ?>><?php _e( '- All', '7listings' ); ?></option>
            </select>

            <button class="button" type="submit" id="btn_submit">
                <?php _e( 'Go', '7listings' ); ?>
            </button>
        </div>
    </div>
</form>

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
                print_r( $entry );
                ?>
            <?php
            endif;
        endforeach; ?>
    </div>