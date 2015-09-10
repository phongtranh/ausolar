<?php
get_header();

get_template_part( 'templates/parts/featured-title' );
?>

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


    <div id="main-wrapper" class="container">

        <?php
        the_post();
        $sidebar_layout = sl_sidebar_layout();
        $content_class = 'entry-content';
        $content_class .= 'none' == $sidebar_layout ? ' full' : ( 'right' == $sidebar_layout ? ' left' : ' right' );

        if ( ! is_user_logged_in() )
        {
            get_template_part( 'templates/company/user-admin/form-login' );
            return;
        }

        $wholesale = get_posts( array(
            'post_type'      => 'wholesale',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'meta_key'       => 'user',
            'meta_value'     => get_current_user_id(),
        ) );

        if ( empty( $wholesale ) )
        {
            get_template_part( 'templates/wholesale/no-company' );
            return;
        }

        $wholesale = current( $wholesale );

        $source = get_post_meta( $wholesale->ID, 'wholesale_code', true );

        if ( empty ( $source ) )
        {
            get_template_part( 'templates/wholesale/review' );
            return;
        }

        $_GET['sources'] = array( $source );
        $_GET['year']   = isset( $_GET['report_year'] ) ? $_GET['report_year'] : date( 'Y' );
        $_GET['month']  = isset( $_GET['report_month'] ) ? $_GET['report_month'] : date( 'm' );

        $paged   = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) - 1 : 0;

        if ( $month < 8 && $year <= 2014 )
        {
            get_template_part( 'templates/wholesale/denied' );
            return;
        }

        $sources = solar_get_source_with_title();

        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );
        $time_offset = sl_timezone_offset() * 3600;
        $now = time() + $time_offset;

        $year   = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
        $month  = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm', $now );

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

        $sites = array();
        $month = intval( $month );
        ?>

        <script type="text/javascript">
	        var state_pie_chart = <?php echo json_encode( $report['states']['state_pie_chart'] ); ?>;
        </script>

        <article id="content" <?php post_class( $content_class ); ?>>

            <?php peace_action( 'entry_top' ); ?>

            <a class="pull-right" href="<?php echo wp_logout_url( get_permalink() ); ?>" title="Logout">Logout</a>

            <h1>Wholesale Report <small>(<?php echo count( $leads ) ?>)</small></h1>

            <form id="filter" method="get">
                <div class="table-nav">
                    <div class="alignleft actions">
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
                            <option value="all" <?php selected( 'all', $month ); ?>><?php _e( '- All', '7listings' ); ?></option>
                        </select>

                        <button class="button" type="submit" id="btn_submit">
                            <?php _e( 'Go', '7listings' ); ?>
                        </button>

	                    <a href="<?php echo add_query_arg( array( 'csv_export' => 1 ) ); ?>" class="button"
	                       role="button">Export</a>

                        <?php $print_sources = base64_encode( serialize( $_GET['sources'] ) ); ?>
                        <button class="button" type="button" onclick="window.open('<?php bloginfo("home") ?>?action=print_wholesale&amp;report_year=<?php echo $year ?>&amp;report_month=<?php echo $month ?>&amp;sources=<?php echo $print_sources; ?>', 'Print Wholesale Leads', 'width=800,height=800')">
                            <?php _e( 'Print Report', '7listings' ); ?>
                        </button>
                    </div>
                </div>
            </form>

            <br><br><br><br>
            <ul class="subsubsub unstyled">
                <li class="total">Total Matches <span class="update-count"><?php echo count( $report['rejected_leads' ] ) + count( $report['approved_leads'] ) ?></span></li>
                <li class="total">Total Rejection <span class="update-count"><?php echo count( $report['rejected_leads'] ); ?></span></li>
                <li class="total">Total Approved <span class="update-count"><?php echo count( $report['approved_leads'] ); ?></span></li>
            </ul>
            <br><br>

	        <div class="row-fluid">
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

	        <div class="row-fluid">
		        <div class="span12">
			        <h3><?php _e( 'States', '7listings' ); ?></h3>
			        <div id="chart-states"></div>
		        </div>
	        </div>

            <div id="leads" class="data-grid">
                <div class="header">
                    <div class="id"><?php _e( 'ID', '7listings' ); ?></div>
                    <div class="date"><?php _e( 'Date', '7listings' ); ?></div>
                    <div class="name"><?php _e( 'Name', '7listings' ); ?></div>
                    <div class="state"><?php _e( 'State', '7listings' ); ?></div>
                    <div class="count matches"><?php _e( 'Matches', '7listings' ); ?></div>
                    <div class="count rejections"><?php _e( 'Rejections', '7listings' ); ?></div>
                    <div class="count approved"><?php _e( 'Approved', '7listings' ); ?></div>
                    <?php if ( $source === 'U' ) : ?>
                    <div class="site"><?php _e( 'Site', '7listings'); ?></div>
                    <?php endif; ?>
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

                            <?php if ( $source === 'U' ) : 

                                $site = solar_get_lead_site( $entry['id'] );
                                $sites[$site][] = $entry['id'];
                            ?>
                            <div class="site"><?php echo $site; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php
                    endif;
                endforeach; ?>
            </div>
    
            <?php if ( ! empty( $sites ) ): ?>
            <h3>Sites</h3>
            <ul class="subsubsub unstyled">
                <?php foreach ( $sites as $site_name => $entries ) : 
                if ( empty( $site_name ) ) 
                    $site_name = 'General';
                ?>
                <li><?php echo $site_name . ' : ' . count( $entries ) ?></li>
                <?php endforeach; ?>    
            </ul>
            <?php endif; ?>

            <?php
            wp_link_pages( array(
                'before' => '<p class="pages">' . __( 'Pages:', '7listings' ),
                'after'  => '</p>',
            ) );
            ?>

            <?php edit_post_link( __( 'Edit Page', '7listings' ), '<span class="edit-link button small">', '</span>' ); ?>

            <?php peace_action( 'entry_bottom' ); ?>

            <?php
            if ( sl_setting( 'comments_page' ) )
            {
                if ( comments_open() || ( get_post_meta( get_the_ID(), 'show_old_comments', true ) && get_comments_number() ) )
                    comments_template();
            }
            ?>

        </article>

        <?php if ( 'none' != $sidebar_layout ) : ?>
            <aside id="sidebar" class="<?php echo $sidebar_layout?>">
                <?php get_sidebar(); ?>
            </aside>
        <?php endif; ?>

    </div>

<?php get_footer(); ?>