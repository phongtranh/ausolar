<h2><?php _e( 'Leads', '7listings' ); ?></h2>
<?php
$entry = GFFormsModel::get_lead( $_GET['leads_search'] );
if ( empty( $entry ) )
{
	echo 'No leads matched.';
	return;
}

// Todo:
$sources = solar_get_source_with_title();

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
	),
	'source'             => array(
		'I' => 'asq-website',
		'E' => 'energy-smart',
		'P' => 'asq-phone',
	)
);
$date_format = get_option( 'date_format' );
$time_format = get_option( 'time_format' );
$time_offset = sl_timezone_offset() * 3600;
?>
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
	$companies = Solar_Postcodes::match_companies( $entry, 4 );
	$time = strtotime( $entry['date_created'] ) + $time_offset;
	?>
	<div class="row">
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
			$request_value = array( __( 'Solar PV', '7listings' ) );
			if ( 'Yes' == $entry[56] )
				$request_value[] = __( 'Solar Hot Water', '7listings' );
			echo '<span class="label">' . __( 'Service:', '7listings' ) . '</span> <span class="detail">' . implode( ', ', $request_value ) . '</span><br>';
			echo '<span class="label">' . __( 'Assessment:', '7listings' ) . '</span> <span class="detail">' . $fields['assessment'][$entry['47']] . '</span>';
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

					echo "<a href='$link'><span class='member-$membership'></span>{$company->post_title} (" . ( $limit - solar_leads_count_total( $company, $key ) ) . "/$limit)</a><br>";
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
</div>
