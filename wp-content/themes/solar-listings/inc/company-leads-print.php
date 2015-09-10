<?php
if ( !is_user_logged_in() )
	die( __( 'You have to login to print lead report', '7listings' ) );
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php _e( 'Company Leads Report', '7listings' ); ?></title>
		<?php wp_head(); ?>
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
			<?php
			if ( ! current_user_can( 'administrator') )
				die( __( 'You have not permission to access this page', '7listings' ) );

			$company_id = $_GET['company_id'];
			$company = get_post( $company_id );

			$membership = get_user_meta( get_post_meta( $company_id, 'user', true ), 'membership', true );
			if ( !$membership )
				$membership = 'none';

			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			$time_offset = sl_timezone_offset() * 3600;
			$now = current_time( 'timestamp', true );

			$_GET['year'] = $_GET['report_year'];
			$_GET['month'] = $_GET['report_month'];

			$year = isset( $_GET['year'] ) ? $_GET['year'] : date( 'Y', $now );
			$month = isset( $_GET['month'] ) ? $_GET['month'] : date( 'n', $now );

			$key = $month . '-' . $year;
			if ( $month < 10 )
				$key = '0' . $key;
			$leads_count = get_post_meta( $company_id, 'leads_count', true );
			if ( empty( $leads_count ) )
				$leads_count = array();
			if ( empty( $leads_count[$key] ) )
				$leads_count[$key] = '';
			$values = array_filter( explode( ',', $leads_count[$key] . ',' ) );
			$rejected_leads = solar_get_rejected_leads( $company );
			$rejected = array_keys( $rejected_leads );
			$approved = array_diff( $values, $rejected );
			?>

			<header id="branding" class="site-header" role="banner">
				<img id="print-logo" src="http://i.imgur.com/ud3reLk.png">

				<div class="header-text">
					<h1 id="site-title"><?php bloginfo( 'name' ); ?></h1>

					<p>web: www.australainsolarquotes.com.au</p>
					<p>email: accounts@australiansolarquotes.com.au</p>
					<p>phone: 07 3171 2290</p>
					<p>abn: 97 149 751 888</p>
				</div>
			</header>

			<div class="heading">
				<p>
					<?php _e( 'Company:', '7listings' ); ?>
					<?php echo $company->post_title; ?>
				</p>
				<p>
					<?php _e( 'Month:', '7listings' ); ?>
					<?php echo date( 'F', strtotime( "01-$month-2000" ) ) . ' ' . $year; ?>
				</p>
				<p>
					<?php _e( 'Approved Leads:', '7listings' ); ?>
					<?php echo count( $approved ); ?>
				</p>
			</div>

			<?php
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
				)
			);
			$leads = $leads_count[$key];
			$leads = array_filter( array_unique( explode( ',', $leads . ',' ) ) );
			$leads = array_reverse( $leads );
			?>

			<?php if ( empty( $leads ) ) : ?>

				You do not have leads this month yet.<br />
				Maybe consider increasing your service area to be matched with more leads.

			<?php else : ?>
			
				<table class="data-grid table table-bordered table-striped" id="leads">
					<thead class="header">
						<tr>
						<td class="no"><?php _e( '#', '7listings' ); ?></td>
						<td class="id"><?php _e( 'ID', '7listings' ); ?></td>
						<td class="date"><?php _e( 'Date', '7listings' ); ?></td>
						<td class="name"><?php _e( 'Name', '7listings' ); ?></td>
						<td class="contact"><?php _e( 'Contact', '7listings' ); ?></td>
						<td class="address"><?php _e( 'Address', '7listings' ); ?></td>
						<td class="request"><?php _e( 'Request', '7listings' ); ?></td>
						<td class="status"><?php _e( 'Status', '7listings' ); ?></td>
						</tr>
					</thead>
					<?php
					$count = count( $leads );
					foreach ( $leads as $lead )
					{
						$entry = GFFormsModel::get_lead( $lead );
						$time  = strtotime( $entry['date_created'] ) + $time_offset;
						$class = 'row';
						?>
						<tr>
							<td class="no"><?php echo $count--; ?></td>
							<td class="id"><?php echo $entry['id']; ?></td>
							<td class="date"><?php echo date( $date_format, $time ); ?></td>
							<td class="name"><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></td>
							<td class="contact">
								<?php echo $entry['3']; ?><br>
								<?php echo $entry['11']; ?>
							</td>
							<td class="address"><?php echo $entry['17.1'], '<br>', $entry['17.3'], '<br>', implode( ', ', array( $entry['17.4'], $entry['17.5'] ) ); ?></td>
							<td class="request">
								<?php
								echo '<span class="label">' . __( 'Type:', '7listings' ) . '</span> <span class="detail">' . $fields['leads_type_entry'][$entry['30']] . '</span><br>';
								$request_value = array( 'Solar PV' );
								if ( 'Yes' == $entry[56] )
									$request_value[] = 'Solar Hot Water';
								echo '<span class="label">' . __( 'Service:', '7listings' ) . '</span> <span class="detail">' . implode( ', ', $request_value ) . '</span><br>';
								echo '<span class="label">' . __( 'Assessment:', '7listings' ) . '</span> <span class="detail">' . $fields['assessment'][$entry['47']] . '</span>';
								?>
							</td>
							<td class="status">
								<?php echo isset( $rejected_leads[$lead] ) ? __( 'Rejected', '7listings' ) : __( 'Accepted' ); ?>
							</td>
						</tr>
					<?php
					}
					?>
				</div>

			<?php endif; ?>
			<?php wp_footer(); ?>
		</div>
		<script>window.onload = window.print;</script>
	</body>
</html>