<?php

$year    = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month   = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );

$month   = intval( $month );
$year    = intval( $year );

$report  = Solar_Report::general( $year, $month );
$sources = solar_get_sources();
foreach( $sources as $k => $v )
{
	$sources[$k] = str_title( $v );
}
?>
<script type="text/javascript">
var state_pie_chart = <?php echo json_encode( $report['state_pie'] ); ?>;

jQuery( function ( $ )
{
	$( '#select_company' ).select2( {
		placeholder: 'Select a company',
		width      : 'resolve'
	} ).on( "select2:select", function ( e )
	{
		var companyId = $('#select_company').val();

		if ( typeof companyId != 'undefined' )
			location.href = "/wp-admin/admin.php?page=leads&company_id=" + companyId + "&year=" + <?php echo $year ?> +"&month=" + <?php echo $month ?> +"&action=view_company_leads";
	} );
} );
</script>

<div class="wrap">
	<h2><?php _e( 'Leads Report', '7listings' ); ?></h2>
	<form method="get">
		<input type="hidden" name="page" value="leads-report">
		<div class="tablenav">
			<div class="alignleft actions">

				<?php Form::select( 'year', array_symmetry( range( 2012, intval( date( 'Y' ) ) ) ), $year ); ?>

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
					foreach( $sources as $k => $v )
					{
						printf( '<option value="%s"%s>%s</option>', $k, selected( in_array( $k, $get_sources ), 1, false ), str_title( $v ) );
					}
					?>
				</select>

				<button class="button" type="submit">
					<img id="ajax-load" src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" alt="loading">
					<?php _e( 'Go', '7listings' ); ?>
				</button>

				<a href="?action=export-companies&amp;year=<?php echo $year ?>&amp;month=<?php echo $month ?>" class="button">Export</a>
			</div>

			<div class="alignright search">
				<?php $companies = get_active_companies(); ?>
				<select id="select_company">
					<option value="0">Please select</option>
					<?php foreach( $companies as $company ): ?>
					<option value="<?php echo $company->post_id ?>"><?php echo get_the_title( $company->post_id ) ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</form>

	<hr>

	<section class="section">
		<label class="label"><h3>Income</h3></label>
		<span class="total big">$ <?php echo number_format( $report['total_approved'] * 30, 0 ); ?></span>
	</section>

	<section class="section">
		<label class="label">Leads</label>
		<span class="total big"><?php echo $report['total_leads'] . '+' . count( $report['all_previous_month_leads'] ) . ' next matches' ?> </span>
	</section>

	<section class="section">
		<label class="label">Potential Matches</label>
		<span class="total big"><?php echo $report['total_leads'] * 4 ?></span>
	</section>

	<section class="section">
		<label class="label">Actual Matches</label>
		<span class="total big">
            <?php echo $report['total_approved'] + array_sum( $report['total_rejected_reasons'] ); ?>
        </span>
	</section>

	<section class="section">
		<label class="label">Total Rejection</label>
		<span class="total big"><?php echo array_sum( $report['total_rejected_reasons'] ); ?></span>
	</section>

	<section class="section">
		<label class="label">Total Approved</label>
		<span class="total big"><?php echo $report['total_approved']; ?></span>
	</section>

	<br><br>

	<section class="section icons">
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
        <?php foreach( solar_get_sources() as $key => $source ): ?>
            <div class="span1 <?php echo $source ?>">
                $ <?php echo number_format( $report['approved_leads_sources'][$source] * 30, 0 ); ?>
            </div>
        <?php endforeach; ?>
	</section>

	<section class="section">
		<label class="label">Leads</label>
        <?php foreach( solar_get_sources() as $key => $source ): ?>
            <div class="span1 <?php echo $source ?>"><?php echo count( $report['sources_count'][$source] ); ?></div>
        <?php endforeach; ?>
	</section>

	<?php
    if ( ! empty ( $report['approved_leads_sources'] ) ):
    $bar_percent = array();
	foreach ( $report['approved_leads_sources'] as $key => $value )
	{
		$bar_percent[$key] = ( array_sum( $report['approved_leads_sources'] ) > 0 )
			? floor( $value / array_sum( $report['approved_leads_sources'] ) * 10000 ) / 100
			: 0;

        if ( $bar_percent[$key] < 0.3 ) $bar_percent[$key] = 0;
	}
	?>
	<section class="section overall total">
		<label class="label"></label>
		<div class="chart-container">
			<div class="bar-chart sources">
                <?php 
                foreach( solar_get_sources() as $key => $source ): 
                ?>
                    <div title="# <?php echo $source ?> Leads - Total income: $<?php echo number_format( $report['approved_leads_sources'][$source] * 30, 0 ) ?>"
                         style="width:<?php echo $bar_percent[$source] ?>%"
                         class="part <?php echo $source ?>"></div>
                <?php endforeach; ?>
			</div>
		</div>
	</section>
    <?php endif; ?>
	<br><br>

	<section class="section">
		<label class="label"><h3><?php _e( 'Rejections', '7listings' ); ?></h3></label>
		<span class="total big"><?php echo array_sum( $report['total_rejected_reasons'] ) ?></span>
	</section>

	<section class="section rejections-breakdown">
		<label class="label"></label>
        <?php foreach ( solar_get_rejection_reasons() as $reason => $title ): ?>
            <div class="span2">
                <span class="<?php echo $reason ?>"></span>
                <?php echo $report['total_rejected_reasons'][$reason] ?>
            </div>
        <?php endforeach; ?>
	</section>

	<?php
	$bar_percent = array();
	foreach ( $report['total_rejected_reasons'] as $reason => $count )
	{
		$bar_percent[$reason] = ( array_sum( $report['total_rejected_reasons'] ) > 0 ) 
							? number_format( $count / array_sum( $report['total_rejected_reasons'] ) * 100, 2 )
							: 0;
	}
	?>

	<section class="section overall rejections">
		<label class="label"></label>
		<div class="chart-container">
			<div class="bar-chart reasons">
                <?php foreach ( solar_get_rejection_reasons() as $reason => $title ): ?>
                    <div title="# <?php echo $title ?>: <?php echo $report['total_rejected_reasons'][$reason] ?>"
                         style="width:<?php echo $bar_percent[$reason] ?>%"
                         class="part bar-<?php echo $reason ?>"></div>
                <?php endforeach; ?>
			</div>
		</div>
	</section>

	<br><br><br>

	<div class="row-fluid">

		<div class="span6">
			<h3><?php _e( 'States', '7listings' ); ?></h3>
			<div id="chart-states"></div>
		</div>

		<div class="span6">
			<h3><?php _e( 'States Sources', '7listings' ); ?></h3>
			<?php
			$i = 0;
            if ( ! empty( $report['state_bar'] ) ) :
				foreach ( $report['state_bar'] as $state => $data ):
	                if ( $data['count'] > 0 ):
						$i++;
						$bar_percent = array();

						foreach ( $data['sources'] as $source => $count )
						{
							$bar_percent[$source] = ( $data['count'] > 0 )
												? floor( $count / $data['count'] * 10000 ) / 100:
												0;
		                    if ( $bar_percent[$source] < 0.3 ) 
		                    	$bar_percent[$source] = 0;
							
							if ( $i === 1 ) 
								$max = $data['count'];
						}
					?>

					<section class="section single-state">
						<label class="label"><?php echo $state ?></label>
						<div class="chart-container sources">
							<div class="bar-chart sources" style="width: <?php echo $data['count'] / $max * 100 ?>%">
		                        <?php foreach ( solar_get_sources() as $key => $source ): ?>
		                            <div title="<?php echo $source ?>: <?php echo $data['sources'][$source] ?>, $<?php echo number_format( $data['sources'][$source] * 30, 0 )?>"
		                                 style="width: <?php echo $bar_percent[$source] ?>%"
		                                 class="part <?php echo $source ?>">
		                            </div>
		                        <?php endforeach; ?>
							<span class="bar-total">$ <?php echo number_format( $data['count'] * 30, 0 ); ?></span>
						</div>
					</section>
					<?php
		            endif;
	            endforeach;
            endif; ?>
		</div>
	</div>

	<br><br>

	<div class="row-fluid">

		<div class="span6">
			<h3><?php _e( 'Companies', '7listings' ); ?></h3>

			<?php
			$max = 0;
			$i = 0;
			foreach ( $report['companies_leads_sources'] as $company_id => $data ) :
				$i++;
				if ( $data['count'] > 0 ) :

					$bar_percent = array();
					foreach ( $data['sources'] as $source => $count )
					{
						$bar_percent[$source] = ( $data['count'] > 0 )
							? floor( $count / $data['count'] * 10000 ) / 100
							: 0;

                        if ( $bar_percent[$source] < 0.3 ) 
                        	$bar_percent[$source] = 0;
					}

					$limit = get_post_meta( $company_id, 'leads', true );
					$total = $limit - $data['count_distinct'];

					if ( $i === 1 ) 
						$max = $data['count'];
					?>
					<section class="section single-company">
						<label class="label">
							<a href="/wp-admin/admin.php?page=leads&amp;company_id=<?php echo $company_id ?>&amp;year=<?php echo $year ?>&amp;month=<?php echo $month ?>&amp;action=view_company_leads">
								<?php echo $data['name'] ?> ( <?php echo $total ?> / <?php echo $limit ?> )
							</a>
						</label>
                        <!-- Todo: Set setting_asq and setting_energysmart -->
						<div class="chart-container">
							<div class="bar-chart sources" style="width: <?php echo $data['count'] / $max * 100 ?>%">
                                <?php foreach ( solar_get_sources() as $key => $source ): ?>
                                    <div title="<?php echo $source ?>: <?php echo $data['sources'][$source] ?>, $<?php echo number_format( $data['sources'][$source] * 30, 0 ) ?>"
                                         class="part <?php echo $source ?>"
                                         style="width:<?php echo $bar_percent[$source] ?>%">
                                    </div>
                                <?php endforeach; ?>
							</div>
							<span class="bar-total">
                                $<?php echo number_format( $data['count'] * 30 ) ?>
                            </span>
						</div>
					</section>
					<?php
				endif;
			endforeach;
			?>
		</div>

		<div class="span6">
		   <h3><?php _e( 'Rejections', '7listings' ); ?></h3>

			<?php
				$i = 0;
                $max = 0;
				foreach ( $report['companies_rejected_sources'] as $company_id => $data ) :
				$i++;
				$bar_percent = array();
				foreach ( $data['sources'] as $source => $count )
                {
					$bar_percent[$source] = ( $data['count'] )
										? floor( $count / $data['count'] * 10000 ) / 100
										: 0;

                    if ( $bar_percent[$source] < 0.3 ) 
                    	$bar_percent[$source] = 0;
					
					if ( $i === 1 ) 
						$max = $data['count'];
				}
			?>
			<section class="section single-company">
				<label class="label">
					<a href="/wp-admin/admin.php?page=leads&amp;company_id=<?php echo $company_id ?>&amp;year=<?php echo $year ?>&amp;month=<?php echo $month ?>&amp;action=view_company_leads">
						<?php echo $data['name'] ?>
					</a> | <?php echo $data['percent'] ?> %
				</label>
				<div class="chart-container">
                    <!-- Todo: Set setting_asq and setting_energysmart -->
					<div class="bar-chart sources" style="width: <?php echo $data['count'] / $max * 100 ?>%">
                        <?php foreach ( solar_get_sources() as $key => $source ): ?>
                            <div title="<?php echo $source ?>: <?php echo $$data['sources'][$source] ?>, $<?php echo number_format( $data['sources'][$source] * 30, 0) ?>"
                                 class="part <?php echo $source ?>"
                                 style="width:<?php echo $bar_percent[$source] ?>%">
                            </div>
                        <?php endforeach; ?>
                    </div>
					<span class="bar-total">
                        $<?php echo number_format( $data['count'] * 30 ) ?>
                    </span>
				</div>
			</section>
			<?php endforeach; ?>
		</div>
	</div>
</div>
