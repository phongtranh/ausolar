<h2><?php _e( 'Leads', '7listings' ); ?></h2>

<form action="<?php menu_page_url( 'leads' ); ?>" method="get">
	<div class="tablenav">
		<?php

		$sources = solar_get_source_with_title();

		$form_id = 1;
		$page_size = 20;
		$paged = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) - 1 : 0;

		$fields = array(
			'leads_type'         => array(
				'residential' => 'Residential',
				'commercial'  => 'Commercial',
			),
			'leads_type_entry'   => array(
				''     => 'Residential',
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
				''                                               					 => 'No preference',
				'any'                                               				 => 'No preference',
			),
			'assessment_company' => array(
				'onsite'      => __( 'Onsite', '7listings' ),
				'phone_email' => __( 'Phone/Email', '7listings' ),
			)
		);

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

		//$all_entries = GFFormsModel::get_leads( $form_id, 0, 'DESC', '', 0, 999999, null, null, false, $start_date, $end_date );

		$offset = $paged * $page_size;
		$paging = array( 'offset' => $offset, 'page_size' => $page_size );

		$search_criteria = array(
			'start_date'    => $start_date,
			'end_date'      => $end_date
		);

		if ( ! empty ( $_GET['leads_search'] ) )
		{
			$search_criteria["field_filters"][] = array( 'value' => trim( $_GET['leads_search'] ) );
			$search_criteria["field_filters"][] = array( "key" => "id", 'value' => trim( $_GET['leads_search'] ) );
		}

		if ( ! empty( $_GET['source'] ) )
			$search_criteria["field_filters"][] = array( "key" => "57", 'value' => trim( $_GET['source'] ) );

		$company_count = isset( $_GET['company_count'] ) ? $_GET['company_count'] : array();

		if ( ! empty( $company_count ) )
			$search_criteria['field_filters'][] = [
													'key' => '88', 
													'operator' => 'in', 
													'value' => $company_count
												];

		$search_criteria["field_filters"]["mode"] = "any";

		$order = array(
			'key'   => 'date_created',
			'value' => 'DESC'
		);

		$total = 0;

		$entries = GFAPI::get_entries( 1, $search_criteria, $order, $paging, $total );
		$page_size = ( $page_size > $total ) ? $total : $page_size;

		$display_total = 0;

		if ( $total > 0 )
			$display_total = ceil( $total / $page_size );
		?>
		<div class="alignleft actions">
			<input type="hidden" name="page" value="leads"> 

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
			
			<?php
				Form::select( 'company_count[]', array_symmetry(['Company Count', 0, 1, 2, 3, 4]), $company_count, ['multiple' => 'multiple', 'class' => 'select'] );
			?>

			<input type="submit" class="button" value="<?php _e( 'Go', '7listings' ); ?>">
		</div>
		<p class="search-box">
			<select name="source">
				<?php 
				$selected_source = isset( $_GET['source'] ) ? $_GET['source'] : '';
				foreach ( $sources as $key => $code ) : 
				if ( empty( $key ) )
					$code[1] = 'All';
				?>
				<option value="<?php echo $key ?>" <?php selected( $key, $selected_source ) ?>><?php echo $code[1] ?></option>
				<?php endforeach; ?>			
			</select>

			<input type="search" name="leads_search" value="<?php echo isset( $_GET['leads_search'] ) ? $_GET['leads_search'] : ''; ?>">
			<input type="submit" name="" class="button" value="Search">
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
				'total'     => $display_total,
				'current'   => $paged + 1,
			) );
			echo $pagination;
			?>
		</div>
	</div>
</form>

<br><br><br>
<?php if ( ! current_user_can( 'administrator' ) && ( empty( $_GET['leads_search'] ) || is_numeric( $_GET['leads_search'] ) ) ) : ?>
	<h3>Please enter search term to continue</h3>
<?php exit; endif; ?>

<div class="data-grid" id="leads">
	<div class="header">
		<div class="id"><?php _e( 'ID', '7listings' ); ?></div>
		<div class="date"><?php _e( 'Date', '7listings' ); ?></div>
		<div class="source"><?php _e( 'Source', '7listings' ); ?></div>
		<div class="name"><?php _e( 'Name', '7listings' ); ?></div>
		<div class="contact"><?php _e( 'Contact', '7listings' ); ?></div>
		<div class="address"><?php _e( 'Address', '7listings' ); ?></div>
		<div class="request"><?php _e( 'Request', '7listings' ); ?></div>
		<div class="company"><?php _e( 'Company', '7listings' ); ?></div>
		<div class="recipients"><?php _e( 'Leads Recipients', '7listings' ); ?></div>
	</div>
	<?php
	if ( $total > 0 ):
	foreach ( $entries as $entry ):
		$companies = Solar_Postcodes::match_companies( $entry, 4 );
		$time = strtotime( $entry['date_created'] ) + $time_offset;

		$rejected_companies = solar_get_rejected_companies( $entry['id'] );
		?>
		<div class="row">
			<div class="id">
				<?php
				$url = admin_url( 'admin.php?page=gf_entries&view=entry&id=1&lid=' . $entry['id'] );
				echo "<a href='$url'>{$entry['id']}</a>";
				?>
			</div>
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
				$request_value = array( __( 'Solar PV', '7listings' ) );
				if ( 'Yes' == $entry[56] )
					$request_value[] = __( 'Solar Hot Water', '7listings' );
				echo '<span class="label">' . __( 'Service:', '7listings' ) . '</span> <span class="detail">' . implode( ', ', $request_value ) . '</span><br>';
				echo '<span class="label">' . __( 'Assessment:', '7listings' ) . '</span> <span class="detail">' . $fields['assessment'][$entry['47']] . '</span><br>';		
				echo '<span class="label">' . __( 'Age:', '7listings' ) . '</span> <span class="detail">' .$entry['18'] . '</span>';
				?>
			</div>
			<div class="company">
				<?php
				if ( empty( $companies ) )
				{
					_e( 'No companies', '7listings' );
				}
				else
				{
					$key = date( 'm', $time ) . '-' . date( 'Y', $time );
					foreach ( $companies as $company )
					{
						$leads_count = get_post_meta( $company->ID, 'leads_count', true );
						if ( empty( $leads_count ) )
							$leads_count = array();
						if ( empty( $leads_count[$key] ) )
							$leads_count[$key] = '';
						$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
						$limit = intval( get_post_meta( $company->ID, 'leads', true ) );

						// Link to view company leads
						$link = remove_query_arg( 'paged' );
						$link = add_query_arg( array(
							'company_id' => $company->ID,
							'action'     => 'view_company_leads',
						) );

						// Membership
						$membership = get_user_meta( get_post_meta( $company->ID, 'user', true ), 'membership', true );
						if ( !$membership )
							$membership = 'none';

						$class = '';
						if ( isset( $rejected_companies[$company->post_title] ) )
							$class .= "rejected {$rejected_companies[$company->post_title]}";

						echo "<a href='$link' class='$class'><span class='member-$membership'></span>{$company->post_title} (" . ( $limit - solar_leads_count_total( $company->ID, $key ) ) . "/$limit)</a><br>";
					}
				}
				?>
			</div>
			<div class="recipients">
				<?php
				if ( empty( $companies ) )
				{
					_e( 'No companies', '7listings' );
				}
				else
				{
					foreach ( $companies as $company )
					{
						echo get_post_meta( $company->ID, 'leads_email', true ) . '<br>';
					}
				}
				?>
			</div>
		</div>
	<?php
	endforeach;
	else:
		echo '<h2 style="textalign:center;">Cannot find leads with your criteria</h2>';
	endif;
	?>
</div>
<div class="tablenav">
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
		<?php echo $pagination; ?>
	</div>
</div>