<?php
add_action( 'company_settings_page_after', 'solar_leads_settings' );

/**
 * Add option for leads
 *
 * @return void
 */
function solar_leads_settings()
{
	$post_type = 'company';

	$pages = array(
		'leads' => __( 'Leads', '7listings' ),
	);
	foreach ( $pages as $k => $v )
	{
		echo '<p>';
		echo "<label>$v</label>";
		wp_dropdown_pages( array(
			'selected' => sl_setting( "{$post_type}_page_{$k}" ),
			'name'     => THEME_SETTINGS . "[{$post_type}_page_{$k}]",
		) );
	}
}

add_action( 'company_settings_page_after', 'solar_export_companies', 20 );
add_action( 'load-toplevel_page_7listings', 'solar_export_companies' );

/**
 * Export companies
 *
 * @return void
 */
function solar_export_companies()
{
	// Show export button
	if ( 'company_settings_page_after' == current_filter() )
	{
		echo '<h2>' . __( 'Export Companies', '7listings' ) . '</h2>';
		printf( '<a class="button" href="%s" target="_blank">%s</a>', wp_nonce_url( admin_url( 'admin.php?page=7listings&action=export' ), 'export-companies', 'nonce' ), __( 'Export', '7listings' ) );
		return;
	}

	if ( $_GET['action'] != 'export' || ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'export-companies' ) )
		return;

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=companies.csv' );
	$fh = @fopen( 'php://output', 'w' );

	@fputcsv( $fh, array(
		// Company basic info
		'Company ID', 'Company', 'Description',

		// Location tab
		'Level or unit and Building Name', 'Street Address', 'Suburb / Town', 'City', 'Post Code', 'State', 'Manual Marker Position', 'Latitude', 'Longitude',

		// Contact info tab
		'Website', 'Email', 'Phone Number', 'Facebook', 'Twitter', 'Google+', 'Pinterest', 'Linkedin', 'Instagram', 'RSS',
		'Open 24/7',
		'Monday Open', 'Monday From', 'Monday To',
		'Tuesday Open', 'Tuesday From', 'Tuesday To',
		'Wednesday Open', 'Wednesday From', 'Wednesday To',
		'Thursday Open', 'Thursday From', 'Thursday To',
		'Friday Open', 'Friday From', 'Friday To',
		'Saturday Open', 'Saturday From', 'Saturday To',
		'Sunday Open', 'Sunday From', 'Sunday To',
	) );

	$companies  = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => - 1,
	) );
	$fields     = array(
		// Location tab
		'address', 'address2', 'area', 'city', 'postcode', 'state', 'location_marker', 'latitude', 'longtitude',

		// Contact info tab
		'website', 'email', 'phone', 'facebook', 'twitter', 'googleplus', 'pinterest', 'linkedin', 'instagram', 'rss',
		'open_247',
		'business_hours_mon', 'business_hours_mon_from', 'business_hours_mon_to',
		'business_hours_tue', 'business_hours_tue_from', 'business_hours_tue_to',
		'business_hours_wed', 'business_hours_wed_from', 'business_hours_wed_to',
		'business_hours_thu', 'business_hours_thu_from', 'business_hours_thu_to',
		'business_hours_fri', 'business_hours_fri_from', 'business_hours_fri_to',
		'business_hours_sat', 'business_hours_sat_from', 'business_hours_sat_to',
		'business_hours_sun', 'business_hours_sun_from', 'business_hours_sun_to',
	);
	$checkboxes = array(
		'location_marker',
		'open_247',
		'business_hours_mon',
		'business_hours_tue',
		'business_hours_wed',
		'business_hours_thu',
		'business_hours_fri',
		'business_hours_sat',
		'business_hours_sun',
	);
	foreach ( $companies as $company )
	{
		$data = array(
			$company->ID, $company->post_title, $company->post_content
		);
		foreach ( $fields as $field )
		{
			$meta = get_post_meta( $company->ID, $field, true );
			if ( in_array( $field, $checkboxes ) )
				$meta = $meta ? 'Yes' : 'No';
			$data[] = $meta;
		}
		@fputcsv( $fh, $data );
	}

	@fclose( $fh );
	die;
}
