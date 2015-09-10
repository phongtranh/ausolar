<?php

if ( !empty( $_GET['leads_search'] ) )
{
	include CHILD_DIR . 'inc/admin-pages/leads-single.php';
	die;
}

$year    = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month   = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );
$month   = intval( $month );
$year    = intval( $year );
$paged   = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) - 1 : 0;

// Todo:
$sources = solar_get_source_with_title();

$select_sources = solar_get_sources();
foreach( $select_sources as $k => $v )
{
	$select_sources[$k] = mb_convert_case( $v, MB_CASE_TITLE, "UTF-8" );
}

$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
$now            = time() + $time_offset;

$year   = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
$month  = isset( $_GET['month'] ) ? $_GET['month'] : date( 'n', $now );
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
$start_date     = date( 'Y-m-d H:i:s', strtotime( $start_date ) - $time_offset );
$end_date       = date( 'Y-m-d H:i:s', strtotime( $end_date ) - $time_offset );

$all_entries    = GFFormsModel::get_leads( 1, 0, 'DESC', '', 0, 999999, null, null, false, $start_date, $end_date );

// Split the page into several part and get current part information
$page_size 	= 20;
$report     = Solar_Report::wholesale();

$raw    = $report['raw'];

$chunks = array_chunk( $raw, $page_size, true );
$leads  = $chunks[$paged];
$total  = count( $raw );
$offset 	    = $paged * $page_size;
$display_total  = ceil( $total / $page_size );
?>

<script type="text/javascript">
	jQuery( document ).ready( function( $ )
	{
		$( '#ajax-load' ).hide();

		$( '#btn_export' ).click( function( e )
		{
			$( '#csv_export' ).val( '1' );
		} );

		$( '#btn_submit' ).click( function( e )
		{
			$( '#csv_export' ).val( '0' );
		} );
	} );
</script>

<h2> Wholesale Report </h2>

<form id="filter" method="get">
	<input type="hidden" name="page" value="wholesale-report">
	<input type="hidden" id="csv_export" name="csv_export" value="0">

	<div class="table-nav">
		<div class="alignleft actions">
			<select name="year">
				<?php
				$max = intval( date( 'Y' ) );
				for ( $i = 2012; $i <= $max; $i ++ )
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

			<select name="sources[]" id="sources" multiple="multiple">
				<?php
				$get_sources = isset( $_GET['sources'] ) ? $_GET['sources'] : array();
				foreach( $select_sources as $k => $v )
				{
					printf( '<option value="%s"%s>%s</option>', $k, selected( in_array( $k, $get_sources ), 1, false ), $v );
				}
				?>
			</select>

			<button class="button" type="submit" id="btn_submit">
				<img id="ajax-load" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" alt="loading">
				<?php _e( 'Go', '7listings' ); ?>
			</button>
			
			<?php $print_sources = base64_encode( serialize( $_GET['sources'] ) ); ?>
			<button class="button" type="button" onclick="window.open('<?php bloginfo("home") ?>?action=print_wholesale&amp;report_year=<?php echo $year ?>&amp;report_month=<?php echo $month ?>&amp;sources=<?php echo $print_sources; ?>', 'Print Wholesale Leads', 'width=800,height=800')">
				<?php _e( 'Print Report', '7listings' ); ?>
			</button>
			
			<button class="button" type="submit" id="btn_export">
				<?php _e( 'Export', '7listings' ); ?>
			</button>
		</div>

		<p class="search-box">
			<input type="search" name="leads_search" value="">
			<input type="submit" name="" class="button" value="Search #ID">
		</p>

		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php
				printf(
					__( 'Displaying %d - %d of %d', '7listings' ),
					$offset + 1, $offset + $page_size,
					$total
				);
				?>
			</span>
			<?php
			$pagination = paginate_links( array(
				'base'      => remove_query_arg( 'paged', add_query_arg( '%_%', '' ) ),
				'format'    => 'paged=%#%',
				'prev_text' => __( '&laquo;', '7listings' ),
				'next_text' => __( '&raquo;', '7listings' ),
				'total'     => count($chunks),
				'current'   => $paged + 1,
			) );
			echo $pagination;
			?>
		</div>

	</div>
</form>

<br>
<br>
<br>
<br>
<br>
<br>
<br>

<ul class="subsubsub">
	<li class="total">Total Matches <span class="update-count"><?php echo count( $report['rejected_leads' ] ) + count( $report['approved_leads'] ) ?></span></li>
	<li class="total">Total Rejection <span class="update-count"><?php echo count( $report['rejected_leads'] ); ?></span></li>
	<li class="total">Total Approved <span class="update-count"><?php echo count( $report['approved_leads'] ); ?></span></li>
</ul>

<br><br><br><br>

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

	endforeach;
    ?>
</div>