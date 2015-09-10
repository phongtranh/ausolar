<?php
if ( !is_user_logged_in() )
	die( __( 'You have to login to print lead request', '7listings' ) );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php _e( 'Company Lead Request', '7listings' ); ?></title>
	<?php wp_head(); ?>
	<style>
		.wrapper { width: 980px; margin: 30px auto; }

		#print-logo { width: 120px; height: 120px; margin: 0; float: left; }
		.header-text{ margin-left: 140px; font-size: 12px; line-height: 1; font-style: italic; }
		#site-title { font-style: initial; line-height: 1; margin: 0 0 20px 0; }
		.header-text p { margin: 0 0 5px 0; }
		.heading { clear: both; font-weight: bold; margin: 60px 0 20px; }
		.content-print,
		.content-print table,
		.content-print table h2,
		.content-print table tr,
		.content-print table td
		{
			font-family: Helvetica, Arial, sans-serif;
		}
		.content-print table { border: 0; width: 100%; border-spacing: 0; }
		.content-print h2 { color: #333; }
		.content-print table td
		{
			padding: 6px;
			border-top-width: 1px;
			border-top-color: #ECECEC;
			border-top-style: solid;
			border-collapse: collapse;
			background-color: #F7F7F7;
		}
		.table1 td
		{
			color: #333;
		}
		.table1 h3 { color: #96cd3b; }
		.table2 td
		{
			color: #333;
		}
		.table2 tr:last-child td:first-child { width: 40% }
		.table2 tr:last-child td:nth-child(2n) { width: 45% }
		.table2 tr:last-child td:last-child { width: 15% }
		.table3 tr:nth-child( even ) td:first-child,
		.table4 tr:nth-child( even ) td:first-child
		{
			width: 33%;
		}
		.table3 tr:nth-child( even ) td:last-child,
		.table4 tr:nth-child( even ) td:last-child
		{
			width: 67%;
		}
		.table5 tr:nth-child( odd ) td:first-child { width: 33%; }
		.table5 tr:nth-child( odd ) td:last-child  { width: 67%; }
		.notice-footer { color: #666; font-size: .75em; }
	</style>
</head>
<body <?php body_class(); ?>>
<div class="wrapper">
	<?php
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
	{
		get_template_part( 'templates/company/user-admin/no-company' );
		return;
	}
	$company = current( $company );
	$company_id = $company->ID;

	$now = current_time( 'timestamp', true );

	$year = date( 'Y', $now );
	$month = date( 'n', $now );
	$date_format = get_option( 'date_format' );
	$time_offset = sl_timezone_offset() * 3600;
	$lead = $_GET['lead_id'];
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
	</div>

	<?php if ( empty( $lead ) ) : ?>

		<?php _e( 'You need to select a lead to print.', '7listings' ); ?><br />

	<?php else : ?>
		<?php
		$entry = GFFormsModel::get_lead( $lead );
		$time = strtotime( $entry['date_created'] ) + $time_offset;
		?>
		<div class="content-print">
			<table class="table1">
				<tbody>
				<tr>
					<td>
						<h3><?php _e( 'Solar PV System Quote Request', '7listings' ); ?></h3>
					</td>
					<td align="right">
						<span style="text-align: right;"><?php echo date( $date_format, $time ) ?>ID: S-<strong><?php echo $lead; ?></strong></span>
					</td>
				</tr>
				</tbody>
			</table>
			&nbsp;
			<table class="table2">
				<tbody>
				<tr>
					<td><?php _e( 'Size', '7listings' ); ?></td>
					<td><?php _e( 'Timeframe', '7listings' ); ?></td>
					<td align="right"><?php _e( 'Area', '7listings' ); ?></td>
				</tr>
				<tr>
					<td>
						<h2><?php echo $entry['29']; ?></h2>
					</td>
					<td>
						<h2><?php echo $entry['51']; ?></h2>
					</td>
					<td>
						<h2><?php echo $entry['17.5']; ?></h2>
					</td>
				</tr>
				</tbody>
			</table>
			&nbsp;
			<h3>Contact</h3>
			<table class="table3">
				<tbody>
				<tr>
					<td><?php _e( 'Name', '7listings' ); ?></td>
					<td><strong><?php echo $entry['1.3'] . ' ' . $entry['1.6']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Email', '7listings' ); ?></td>
					<td><strong><?php echo $entry['11']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Mobile', '7listings' ); ?></td>
					<td><strong><?php echo $entry['3']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Phone', '7listings' ); ?></td>
					<td><strong><?php echo $entry['33']; ?></strong></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><?php _e( 'Contact Time', '7listings' ); ?></td>
					<td><strong><?php echo $entry['40']; ?></strong></td>
				</tr>
				</tbody>
			</table>
			&nbsp;
			<h3><?php _e( 'Quote Details', '7listings' ); ?></h3>
			<table class="table4">
				<tbody>
				<tr>
					<td><?php _e( 'System Size', '7listings' ); ?></td>
					<td><strong><?php echo $entry['29']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Timeframe', '7listings' ); ?></td>
					<td><strong><?php echo $entry['51']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Quote Detail', '7listings' ); ?></td>
					<td><strong><?php echo $entry['47']; ?></strong></td>
				</tr>
				</tbody>
			</table>
			&nbsp;
			<h3><?php _e( 'Property Information', '7listings' ); ?></h3>
			<table class="table5">
				<tbody>
				<tr>
					<td valign="top"<?php _e( 'Address', '7listings' ); ?></td>
					<td>
						<strong>
							<?php echo sprintf(' %s, %s, %s, %s' ,$entry['17.1'], $entry['17.3'], $entry['17.4'], $entry['17.5'] ); ?>
						</strong>
					</td>
				</tr>
				<tr>
					<td><?php _e( 'Type', '7listings' ); ?></td>
					<td><strong><?php echo $entry['30']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Ownership', '7listings' ); ?></td>
					<td><strong><?php echo $entry['14']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Installation Permission', '7listings' ); ?></td>
					<td><strong><?php echo $entry['45']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Building Type', '7listings' ); ?></td>
					<td><strong><?php echo $entry['23']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Building Age', '7listings' ); ?></td>
					<td><strong><?php echo $entry['18']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Roof Height', '7listings' ); ?></td>
					<td><strong><?php echo $entry['44']; ?></strong></td>
				</tr>
				<tr>
					<td><?php _e( 'Roof', '7listings' ); ?></td>
					<td><strong><?php echo $entry['43']; ?></strong>, with a <strong><?php echo $entry['42']; ?></strong> <?php _e( 'pitch.', '7listings' ); ?></td>
				</tr>
				<tr>
					<td><?php _e( 'Power Provider', '7listings' ); ?></td>
					<td><strong><?php echo $entry['53']; ?></strong></td>
				</tr>
				</tbody>
			</table>
			<h4><?php _e( 'Additional Info', '7listings' ); ?></h4>
			<p><?php echo $entry['50']; ?></p>
			<?php echo $entry['25']; ?>

			&nbsp;
			<p class="notice-footer"><?php _e( '*If this is a duplicate lead, has incorrect contact details or you are unable to contact the prospect within 7 days starting from the date the lead was delivered, please notify us by completing our lead', '7listings' ); ?> <a href="http://www.australiansolarquotes.com.au/my-account/"><?php _e( 'Rejection Form', '7listings' ); ?></a>.</p>

		</div>

	<?php endif; ?>
	<?php wp_footer(); ?>
</div>
<script>window.onload = window.print;</script>
</body>
</html>
