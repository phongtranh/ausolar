<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

function adrotate_advertiser_front_end() {
	global $wpdb, $current_user;

	get_currentuserinfo();
	
	$status = $view = $ad_edit_id = $request = $request_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	if(isset($_GET['request'])) $request = esc_attr($_GET['request']);
	if(isset($_GET['id'])) $request_id = esc_attr($_GET['id']);
	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;
	
	if(isset($_GET['month']) AND isset($_GET['year'])) {
	$month = esc_attr($_GET['month']);
	$year = esc_attr($_GET['year']);
	} else {
	$month = date("m");
	$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	
	?>
	<div class="wrap">
		<h2><?php _e('Advertiser', 'adrotate'); ?></h2>
	
	<?php 
	if($status > 0) adrotate_status($status);
	
	$wpnonceaction = 'adrotate_email_advertiser_'.$request_id;
	if($view == "" OR $view == "manage") {
		
		$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $current_user->ID));
	
		if($ads) {
			$activebanners = $queuebanners = $disabledbanners = false;
			foreach($ads as $ad) {
				$banner = $wpdb->get_row("SELECT `id`, `title`, `type` FROM `".$wpdb->prefix."adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' OR `type` = 'error' OR `type` = 'a_error' OR `type` = 'expired' OR `type` = 'queue' OR `type` = 'reject') AND `id` = '".$ad->ad."';");
	
				// Skip if no ad
				if(!$banner) continue;
				
				$starttime = $stoptime = 0;
				$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	
				$type = $banner->type;
				if($type == 'active' AND $stoptime <= $in7days) $type = '7days';
				if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
				if($type == 'active' AND $stoptime <= $now) $type = 'expired'; 
	
				if($type == 'active' OR $type == '2days' OR $type == '7days' OR $type == 'expired') {
					$activebanners[$banner->id] = array(
						'id' => $banner->id,
						'title' => $banner->title,
						'type' => $type,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
	
				if($type == 'disabled') {
					$disabledbanners[$banner->id] = array(
						'id' => $banner->id,
						'title' => $banner->title,
						'type' => $type
					);
				}
	
				if($type == 'queue' OR $type == 'reject' OR $type == 'error' OR $type == 'a_error') {
					$queuebanners[$banner->id] = array(
						'id' => $banner->id,
						'title' => $banner->title,
						'type' => $type
					);
				}
			}
			
			$output = '';
			
			// Show active ads, if any
			if($activebanners) {
				$output .= adrotate_advertiser_active($activebanners, $today);
			}
	
/*
			// Show disabled ads, if any
			if($disabledbanners) {
				require('dashboard/advertiser/adrotate-main-disabled.php');
			}
	
			// Show queued ads, if any
			if($queuebanners) {
				require('dashboard/advertiser/adrotate-main-queue.php');
			}
	
			// Gather data for summary report
			$summary = adrotate_prepare_advertiser_report($current_user->ID, $activebanners);
			require('dashboard/advertiser/adrotate-main-summary.php');
*/
	
		} else {
			?>
			<table class="widefat" style="margin-top: .5em">
				<thead>
					<tr>
						<th><?php _e('Notice', 'adrotate'); ?></th>
					</tr>
				</thead>
				<tbody>
				    <tr>
						<td><?php _e('No ads for user.', 'adrotate'); ?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
	} else if($view == "addnew" OR $view == "edit") { 
	
		require('dashboard/advertiser/adrotate-edit.php');
	
	} else if($view == "report") { 
	
		require('dashboard/advertiser/adrotate-report.php');
	
	}
	?>
	</div>
<?php
	return $output;
}

function adrotate_advertiser_active($activebanners, $today) {
	global $adrotate_config;

	$output = '<h3>'.__("Your Active Ads", 'adrotate')."</h3>";
	$output .= '<p><em>'.__("These are active and currently in the pool of ads shown on the website.", "adrotate").'</em></p>';
	
	$output .= '<table class="widefat" style="margin-top: .5em">';
	$output .= '	<thead>';
	$output .= '		<tr>';
	$output .= '		<th width="5%"><center>'.__('ID', 'adrotate').'</center></th>';
	$output .= '		<th width="15%">'.__('Start / End', 'adrotate').'</th>';
	$output .= '		<th>'.__('Title', 'adrotate').'</th>';
	$output .= '		<th width="8%"><center>'.__('Impressions', 'adrotate').'</center></th>';
	$output .= '		<th width="8%"><center>'.__('Today', 'adrotate').'</center></th>';
	$output .= '		<th width="8%"><center>'.__('Clicks', 'adrotate').'</center></th>';
	$output .= '		<th width="8%"><center>'.__('Today', 'adrotate').'</center></th>';
	$output .= '		<th width="8%"><center>'.__('CTR', 'adrotate').'</center></th>';
	$output .= '	</tr>';
	$output .= '	</thead>';
		
	$output .= '	<tbody>';
	foreach($activebanners as $ad) {
		$stats = adrotate_stats($ad['id']);
		$stats_today = adrotate_stats($ad['id'], $today);
		$ctr = adrotate_ctr($stats['clicks'], $stats['impressions']);						

		$wpnonceaction = 'adrotate_email_advertiser_'.$ad['id'];
		$nonce = wp_create_nonce($wpnonceaction);

		$class = '';
		if($ad['type'] == '2days') $class = ' row_error'; 
		if($ad['type'] == '7days') $class = ' row_error';
		if($ad['type'] == 'expired') $class = ' row_urgent';

		$output .= '	    <tr id="banner-'.$ad['id'].' '.$ad['type'].'" class="'.$class.'">';
		$output .= '			<td><center>'.$ad['id'].'</center></td>';
		$output .= '			<td>'.date_i18n("F d, Y", $ad['firstactive']).'<br /><span style="color:'.adrotate_prepare_color($ad['lastactive']).';">'.date_i18n("F d, Y", $ad['lastactive']).'</span></td>';
		$output .= '			<td><strong>';
		if($adrotate_config['enable_editing'] == 'Y') {
			$output .= '<a class="row-title" href="#" title="'.__('Edit', 'adrotate').'">'. stripslashes(html_entity_decode($ad['title'])).'</a>';
		} else {
			$output .= stripslashes(html_entity_decode($ad['title']));
		}
		$output .= '</strong> - <a href="#" title="'.__('Stats', 'adrotate').'">Stats</a><br />Groups: Not implemented</td>';
		$output .= '			<td><center>'.$stats['impressions'].'</center></td>';
		$output .= '			<td><center>'.$stats_today['impressions'].'</center></td>';
		$output .= '			<td><center>'.$stats['clicks'].'</center></td>';
		$output .= '			<td><center>'.$stats_today['clicks'].'</center></td>';
		$output .= '			<td><center>'.$ctr.' %</center></td>';
		$output .= '		</tr>';
	}
	$output .= '	</tbody>';
	
	$output .= '</table>';
	$output .= '<p><center>';
	$output .= '	<span style="border: 1px solid #e6db55; height: 12px; width: 12px; background-color: #ffffe0">&nbsp;&nbsp;&nbsp;&nbsp;</span> '.__("Is almost expired.", "adrotate").'&nbsp;&nbsp;&nbsp;&nbsp;<span style="border: 1px solid #c00; height: 12px; width: 12px; background-color: #ffebe8">&nbsp;&nbsp;&nbsp;&nbsp;</span> '.__("Has expired.", "adrotate");
	$output .= '</center></p>';
	
	return $output;
}

?>