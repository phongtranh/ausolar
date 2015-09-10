<?php

$year  = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y' );
$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'm' );

$companies = get_posts( array(
    'post_type'      => 'company',
    'posts_per_page' => - 1,
    'post_status'	 => 'any'
) );

if ( $month < 10 && strlen( $month ) == 1 )
	$month = '0' . $month;

$payment_types = solar_get_payment_methods();
ob_clean();

$file_name = "Companies Monthly Report {$year}_{$month}.csv";

header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
header( 'Content-Description: File Transfer' );
header( "Content-type: text/csv" );
header( "Content-Disposition: attachment; filename={$file_name}" );
header( "Expires: 0" );
header( "Pragma: public" );

$fh = @fopen( 'php://output', 'w' );

@fputcsv( $fh, array( 'Company Title', 'Customer ID', 'Payment Method', 'DD Received', 'Num of Leads', 'Total Rejected', 'Approved', 'Price per Lead' ) );

foreach ( $companies as $company ) :

	$company_leads 	= solar_get_company_leads( $company->ID, "$month-$year" );
	
	if ( empty( $company_leads ) || count( $company_leads ) === 0 )
		continue;

	$number_of_leads = count( $company_leads );
	
	$all_rejected 			= array_keys( solar_get_rejected_leads( $company ) );
	$rejected_this_month 	= count( array_intersect( $all_rejected, $company_leads ) );

	$title				= $company->post_title;
	$customer_id 		= get_post_meta( $company->ID, 'accounting_number', true );

	$payment_type_raw 	= get_post_meta( $company->ID, 'leads_payment_type', true );
	$payment_type 		= $payment_types[$payment_type_raw];

	$direct_debit_received 		= 'No';
	if ( get_post_meta( $company->ID, 'leads_direct_debit_saved', true ) == 1 && $payment_type_raw == 'direct' )
		$direct_debit_received 	= 'Yes';
	
	$approved			= $number_of_leads - $rejected_this_month;

	$leads_price 		= 30;
	if ( intval( get_post_meta( $company->ID, 'leads_price', true ) ) > 0 )
		$leads_price 	= intval( get_post_meta( $company->ID, 'leads_price', true ) );

	@fputcsv( $fh, compact( 'title', 'customer_id', 'payment_type', 'direct_debit_received', 'number_of_leads', 'rejected_this_month', 'approved', 'leads_price' ) );

endforeach;

@fclose( $fh );

exit;