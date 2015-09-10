<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_export_stats

 Purpose:   Export CSV data of given month
 Receive:   -- None --
 Return:    -- None --
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_export_stats() {
	global $wpdb;

	if(wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_ads') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_groups') 
	OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'],'adrotate_report_global')) {
		$id = $type = $month = $year = '';
		$id	 				= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_id'], "\t\n "), ENT_QUOTES));
		$type	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_type'], "\t\n "), ENT_QUOTES));
		$month	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_month'], "\t\n "), ENT_QUOTES));
		$year	 			= strip_tags(htmlspecialchars(trim($_POST['adrotate_export_year'], "\t\n "), ENT_QUOTES));
	
		$csv_emails = trim($_POST['adrotate_export_addresses']);
		if(strlen($csv_emails) > 0) {
			$csv_emails = explode(',', trim($csv_emails));
			foreach($csv_emails as $csv_email) {
				$csv_email = strip_tags(htmlspecialchars(trim($csv_email), ENT_QUOTES));
				if(strlen($csv_email) > 0) {
					if(preg_match("/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i", $csv_email) ) {
						$clean_advertiser_email[] = $csv_email;
					}
				}
			}
			$emails = array_unique(array_slice($clean_advertiser_email, 0, 3));
		} else {
			$emails = array();
		}
		
		$emailcount = count($emails);
	
		if($month == 0) {
			$from = mktime(0,0,0,1,1,$year);
			$until = mktime(0,0,0,12,31,$year);
		} else {
			$from = mktime(0,0,0,$month,1,$year);
			$until = mktime(0,0,0,$month+1,0,$year);
		}
		$now = time();
		$from_name = date_i18n("M-d-Y", $from);
		$until_name = date_i18n("M-d-Y", $until);
	
		$generated = array("Generated on ".date_i18n("M d Y, H:i"));
	
		if($type == "single" OR $type == "group" OR $type == "global") {
			if($type == "single") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE (`thetime` >= '".$from."' AND `thetime` <= '".$until."') AND `ad` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = %d;", $id));
		
				$filename = "Single-ad ID".$id." - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Report for ad '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
		
			if($type == "group") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE (`thetime` >= '".$from."' AND `thetime` <= '".$until."') AND  `group` = %d GROUP BY `thetime` ASC;", $id), ARRAY_A);
				$title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM `".$wpdb->prefix."adrotate_groups` WHERE `id` = %d;", $id));
		
				$filename = "Ad Group ID".$id." - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Report for group '".$title."'");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
		
			if($type == "global") {
				$ads = $wpdb->get_results($wpdb->prepare("SELECT `thetime`, SUM(`clicks`) as `clicks`, SUM(`impressions`) as `impressions` FROM `".$wpdb->prefix."adrotate_stats` WHERE `thetime` >= %d AND `thetime` <= %d GROUP BY `thetime` ASC;", $from, $until), ARRAY_A);
		
				$filename = "Global report - ".$from_name." to ".$until_name." - exported ".$now.".csv";
				$topic = array("Global report");
				$period = array("Period - From: ".$from_name." Until: ".$until_name);
				$keys = array("Day", "Clicks", "Impressions");
			}
	
			$x=0;
			foreach($ads as $ad) {
				// Prevent gaps in display
				if($ad['impressions'] == 0) $ad['impressions'] = 0;
				if($ad['clicks'] == 0) $ad['clicks'] = 0;
		
				// Build array
				$adstats[$x]['day']	= date_i18n("M d Y", $ad['thetime']);
				$adstats[$x]['clicks'] = $ad['clicks'];
				$adstats[$x]['impressions'] = $ad['impressions'];
				$x++;
			}
		}
	
		if($type == "advertiser") {
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $id));

			$x=0;
			foreach($ads as $ad) {
				$title = $wpdb->get_var("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = '".$ad->ad."';");
				$startshow = $endshow = 0;
				$startshow = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->ad."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$endshow = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->ad."' AND  `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				$username = $wpdb->get_var($wpdb->prepare("SELECT `display_name` FROM `$wpdb->users` WHERE `ID` = %d ORDER BY `user_nicename` ASC;", $id));
				$stat = adrotate_stats($ad->ad);
				
				// Prevent gaps in display
				if($stat['impressions'] == 0 AND $stat['clicks'] == 0) {
					$ctr = "0";
				} else {
					$ctr = round((100/$stat['impressions']) * $stat['clicks'],2);
				}
	
				// Build array
				$adstats[$x]['title']					= $title;			
				$adstats[$x]['id']						= $ad->ad;			
				$adstats[$x]['startshow']				= date_i18n("M d Y", $startshow);
				$adstats[$x]['endshow']					= date_i18n("M d Y", $endshow);
				$adstats[$x]['clicks']					= $stat['clicks'];
				$adstats[$x]['impressions']				= $stat['impressions'];
				$adstats[$x]['ctr']						= $ctr;
				$x++;
			}
			
			$filename = "Advertiser - ".$username." - export.csv";
			$topic = array("Advertiser report for ".$username);
			$period = array("Period - Not Applicable");
			$keys = array("Title", "Ad ID", "First visibility", "Last visibility", "Clicks", "Impressions", "CTR (%)");
		}
		
		if($adstats) {
			if(!file_exists(WP_CONTENT_DIR . '/reports/')) mkdir(WP_CONTENT_DIR . '/reports/', 0755);
			$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');
			
			if($fp) {
				fputcsv($fp, $topic);
				fputcsv($fp, $period);
				fputcsv($fp, $generated);
				fputcsv($fp, $keys);
				foreach($adstats as $stat) {
					fputcsv($fp, $stat);
				}
				
				fclose($fp);

				if($emailcount > 0) {
					$attachments = array(WP_CONTENT_DIR . '/reports/'.$filename);
					$siteurl 	= get_option('siteurl');
					$email 		= get_option('admin_email');
		
				    $headers = "MIME-Version: 1.0\r\n" .
		    					"From: AdRotate Plugin <".$email.">\r\n" . 
		    					"Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";

					$subject = __('[AdRotate] CSV Report!', 'adrotate');

					$message = 	"<p>".__('Hello', 'adrotate').",</p>";
					$message .= "<p>".__('Attached in this email you will find the exported CSV file you generated on ', 'adrotate')." $siteurl.</p>";
					$message .= "<p>".__('Have a nice day!', 'adrotate')."<br />";
					$message .= __('Your AdRotate Notifier', 'adrotate')."<br />";
					$message .= "https://ajdg.solutions/products/adrotate-for-wordpress/</p>";

					wp_mail($emails, $subject, $message, $headers, $attachments);

					if($type == "single") adrotate_return('adrotate-ads', 212, array('view' => 'report', 'ad' => $id));
					if($type == "group") adrotate_return('adrotate-groups', 212, array('view' => 'report', 'group' => $id));
					if($type == "global") adrotate_return('adrotate-global-report', 212);
					if($type == "advertiser") adrotate_return('adrotate-advertiser', 303, array('view' => 'report', 'ad' => $id));
					exit;
				}
				if($type == "single") adrotate_return('adrotate-ads', 215, array('view' => 'report', 'ad' => $id, 'file' => $filename));
				if($type == "group") adrotate_return('adrotate-groups', 215, array('view' => 'report', 'group' => $id, 'file' => $filename));
				if($type == "global") adrotate_return('adrotate-global-report', 215, array('file' => $filename));
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 215, array('view' => 'report', 'ad' => $id, 'file' => $filename));
				exit;
			} else {
				if($type == "single") adrotate_return('adrotate-ads', 507, array('view' => 'report', 'ad' => $id));
				if($type == "group") adrotate_return('adrotate-groups', 507, array('view' => 'report', 'group' => $id));
				if($type == "global") adrotate_return('adrotate-global-report', 507);
				if($type == "advertiser") adrotate_return('adrotate-advertiser', 507, array('view' => 'report', 'ad' => $id));
			}
		} else {
			if($type == "single") adrotate_return('adrotate-ads', 503, array('view' => 'report', 'ad' => $id));
			if($type == "group") adrotate_return('adrotate-groups', 503, array('view' => 'report', 'group' => $id));
			if($type == "global") adrotate_return('adrotate-global-report', 503);
			if($type == "advertiser") adrotate_return('adrotate-advertiser', 503);
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_export_ads

 Purpose:   Export adverts in various formats
 Receive:   $ids, $format
 Return:    -- None --
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_export_ads($ids, $format) {
	global $wpdb;

	$all_ads = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."adrotate` ORDER BY `id` ASC;", ARRAY_A);

	$ads = array();
	foreach($all_ads as $single) {
		if(in_array($single['id'], $ids)) {
			$starttime = $stoptime = 0;
			$starttime = $wpdb->get_var("SELECT `starttime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$single['id']."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
			$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$single['id']."' AND  `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");

			if(!is_array($single['cities'])) $single['cities'] = array();
			if(!is_array($single['countries'])) $single['countries'] = array();
			
			$ads[$single['id']] = array(
				'title' => $single['title'],
				'bannercode' => stripslashes($single['bannercode']),
				'imagetype' => $single['imagetype'],
				'image' => $single['image'],
				'link' => $single['link'],
				'tracker' => $single['tracker'],
				'responsive' => $single['responsive'],
				'weight' => $single['weight'],
				'budget' => $single['budget'],
				'crate' => $single['crate'],
				'irate' => $single['irate'],
				'cities' => implode(',', unserialize($single['cities'])),
				'countries' => implode(',', unserialize($single['countries'])),
				'start' => $starttime,
				'end' => $stoptime,
			);
		}
	}

 	if($ads) {
	 	if($format == 'xml') {
			$filename = "AdRotate_export_".date_i18n("mdYHi")."_".uniqid().".xml";
			$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');

			$xml = new SimpleXMLElement('<adverts></adverts>');
			foreach($ads as $ad) {
				$node = $xml->addChild('advert');
				$node->addChild('title', $ad['title']);
				$node->addChild('bannercode', $ad['bannercode']);
				$node->addChild('imagetype', $ad['imagetype']);
				$node->addChild('image', $ad['image']);
				$node->addChild('link', $ad['link']);
				$node->addChild('tracker', $ad['tracker']);
				$node->addChild('responsive', $ad['responsive']);
				$node->addChild('weight', $ad['weight']);
				$node->addChild('budget', $ad['budget']);
				$node->addChild('crate', $ad['crate']);
				$node->addChild('irate', $ad['irate']);
				$node->addChild('cities', $ad['cities']);
				$node->addChild('countries', $ad['countries']);
				$node->addChild('start', $ad['start']);
				$node->addChild('end', $ad['end']);
			}

			file_put_contents(WP_CONTENT_DIR . '/reports/'.$filename, $xml->saveXML());
	 	}

	 	if($format == 'csv') {
			$filename = "AdRotate_export_".date_i18n("mdYHi")."_".uniqid().".csv";
			$fp = fopen(WP_CONTENT_DIR . '/reports/'.$filename, 'w');

			$keys = array("title", "bannercode", "imagetype", "image", "link", "tracker", "responsive", "weight", "budget", "crate", "irate", "cities", "countries", "start", "end");
	
			if($fp) {
				fputcsv($fp, $keys, ',', '"');
				foreach($ads as $ad) {
					fputcsv($fp, $ad, ',', '"');
				}
				fclose($fp);
			}
		}
		unset($all_ads, $ads);

		adrotate_return('adrotate-ads', 215, array('file' => $filename));
		exit;
	} else {
		adrotate_return('adrotate-ads', 509);
	}
}
?>