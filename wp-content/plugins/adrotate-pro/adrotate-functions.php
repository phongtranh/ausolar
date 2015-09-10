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
 Name:      adrotate_shortcode

 Purpose:   Prepare function requests for calls on shortcodes
 Receive:   $atts, $content
 Return:    Mixed string
 Since:		0.7
-------------------------------------------------------------*/
function adrotate_shortcode($atts, $content = null) {
	global $adrotate_config;

	$banner_id = $group_ids = $fallback = $weight = $site = 0;
	if(!empty($atts['banner'])) $banner_id = trim($atts['banner'], "\r\t ");
	if(!empty($atts['group'])) $group_ids = trim($atts['group'], "\r\t ");
	if(!empty($atts['fallback'])) $fallback	= trim($atts['fallback'], "\r\t "); // Optional for groups (override)
	if(!empty($atts['weight']))	$weight	= trim($atts['weight'], "\r\t "); // Optional for groups (override)
	if(!empty($atts['site'])) $site = trim($atts['site'], "\r\t "); // Optional for site (override)

	$output = '';

	if($adrotate_config['w3caching'] == "Y") $output .= '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

	if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
		if($adrotate_config['supercache'] == "Y") $output .= '<!--mfunc echo adrotate_ad('.$banner_id.', true, 0, 0, '.$site.') -->';
		$output .= adrotate_ad($banner_id, true, 0, 0, $site);
		if($adrotate_config['supercache'] == "Y") $output .= '<!--/mfunc-->';
	}

	if($banner_id == 0 AND $group_ids > 0) { // Show group 
		if($adrotate_config['supercache'] == "Y") $output .= '<!--mfunc echo adrotate_group('.$group_ids.', '.$fallback.', '.$weight.', '.$site.') -->';
		$output .= adrotate_group($group_ids, $fallback, $weight, $site);
		if($adrotate_config['supercache'] == "Y") $output .= '<!--/mfunc-->';
	}

	if($adrotate_config['w3caching'] == "Y") $output .= '<!-- /mfunc -->';

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_can_edit

 Purpose:   Return a array of adverts to use on advertiser dashboards
 Receive:   $user_id
 Return:    array
 Since:		3.11
-------------------------------------------------------------*/
function adrotate_can_edit() {
	global $adrotate_config;
	
	if($adrotate_config['enable_editing'] == 'Y') {
		return true;
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_is_networked

 Purpose:   Determine if AdRotate is network activated
 Receive:   -None-
 Return:    Boolean
 Since:		3.9.8
-------------------------------------------------------------*/
function adrotate_is_networked() {
	if(!function_exists( 'is_plugin_active_for_network')) require_once(ABSPATH.'/wp-admin/includes/plugin.php');
	 
	if(is_plugin_active_for_network(ADROTATE_FOLDER.'/adrotate.php')) {
		return true;
	}		
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_is_human

 Purpose:   Check if visitor is a bot
 Receive:   -None-
 Return:    Boolean
 Since:		3.11.10
-------------------------------------------------------------*/
function adrotate_is_human() {
	global $adrotate_crawlers;

	if(is_array($adrotate_crawlers)) {
		$crawlers = $adrotate_crawlers;
	} else {
		$crawlers = array();
	}

	if(isset($_SERVER['HTTP_USER_AGENT'])) {
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$useragent = trim($useragent, ' \t\r\n\0\x0B');
	} else {
		$useragent = '';
	}

	if(strlen($useragent) > 0) {
		$nocrawler = true;
		foreach($crawlers as $crawler) {
			if(preg_match("/$crawler/i", $useragent)) $nocrawler = false;
		}
	} else {
		$nocrawler = false;
	}
	
	// Returns true if no bot.
	return $nocrawler;
}

/*-------------------------------------------------------------
 Name:      adrotate_count_impression

 Purpose:   Count Impressions where needed
 Receive:   $ad, $group
 Return:    -None-
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_count_impression($ad, $group = 0, $blog_id = 0, $impression_timer = 0) { 
	global $wpdb, $adrotate_config, $adrotate_debug;

	if(($adrotate_config['enable_loggedin_impressions'] == 'Y' AND is_user_logged_in()) OR !is_user_logged_in()) {
		$now = adrotate_now();
		$today = adrotate_date_start('day');
		$remote_ip 	= adrotate_get_remote_ip();

		if($blog_id > 0 AND adrotate_is_networked()) {
			$current_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
		}

		if($adrotate_debug['timers'] == true) {
			$impression_timer = $now;
		} else {
			$impression_timer = $now - $impression_timer;
		}

		$saved_timer = $wpdb->get_var($wpdb->prepare("SELECT `timer` FROM `".$wpdb->prefix."adrotate_tracker` WHERE `ipaddress` = '%s' AND `stat` = 'i' AND `bannerid` = %d ORDER BY `timer` DESC LIMIT 1;", $remote_ip, $ad));
		if($saved_timer < $impression_timer AND adrotate_is_human()) {
			$stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `".$wpdb->prefix."adrotate_stats` WHERE `ad` = %d AND `group` = %d AND `thetime` = $today;", $ad, $group));
			if($stats > 0) {
				$wpdb->query("UPDATE `".$wpdb->prefix."adrotate_stats` SET `impressions` = `impressions` + 1 WHERE `id` = $stats;");
			} else {
				$wpdb->insert($wpdb->prefix.'adrotate_stats', array('ad' => $ad, 'group' => $group, 'thetime' => $today, 'clicks' => 0, 'impressions' => 1));
			}

			$adrotate_geo = adrotate_get_cookie('geo');
			$country = (isset($adrotate_geo['country'])) ? $adrotate_geo['country']: '';
			$city = (isset($adrotate_geo['city'])) ? $adrotate_geo['city'] : '';

			$wpdb->insert($wpdb->prefix."adrotate_tracker", array('ipaddress' => $remote_ip, 'timer' => $now, 'bannerid' => $ad, 'stat' => 'i', 'useragent' => '', 'country' => $country, 'city' => $city));
		}

		if($blog_id > 0 AND adrotate_is_networked()) {
			switch_to_blog($current_blog);
		}
	}
} 

/*-------------------------------------------------------------
 Name:      adrotate_impression_callback

 Purpose:   Register a impression for dynamic groups
 Receive:   $_POST
 Return:    -None-
 Since:		3.11.4
-------------------------------------------------------------*/
function adrotate_impression_callback() {
	define('DONOTCACHEPAGE', true);
	define('DONOTCACHEDB', true);
	define('DONOTCACHCEOBJECT', true);

	global $adrotate_debug;

	$meta = $_POST['track'];
	if($adrotate_debug['track'] != true) {
		$meta = base64_decode($meta);
	}
		
	$meta = esc_attr($meta);
	list($ad, $group, $blog_id, $impression_timer) = explode(",", $meta, 4);
	adrotate_count_impression($ad, $group, $blog_id, $impression_timer);

	wp_die();
}

/*-------------------------------------------------------------
 Name:      adrotate_click_callback

 Purpose:   Register clicks for clicktracking
 Receive:   $_POST
 Return:    -None-
 Since:		3.11.4
-------------------------------------------------------------*/
function adrotate_click_callback() {
	define('DONOTCACHEPAGE', true);
	define('DONOTCACHEDB', true);
	define('DONOTCACHCEOBJECT', true);

	global $wpdb, $adrotate_config, $adrotate_debug;

	$meta = $_POST['track'];

	if($adrotate_debug['track'] != true) {
		$meta = base64_decode($meta);
	}
	
	$meta = esc_attr($meta);
	list($ad, $group, $blog_id, $impression_timer) = explode(",", $meta, 4);

	if(is_numeric($ad) AND is_numeric($group) AND is_numeric($blog_id)) {

		if($blog_id > 0 AND adrotate_is_networked()) {
			$current_blog = $wpdb->blogid;
			switch_to_blog($blog_id);
		}
	
		if(($adrotate_config['enable_loggedin_clicks'] == 'Y' AND is_user_logged_in()) OR !is_user_logged_in()) {
			$remote_ip = adrotate_get_remote_ip();
	
			if(adrotate_is_human() AND $remote_ip != "unknown" AND !empty($remote_ip)) {
				$now = adrotate_now();
				$today = adrotate_date_start('day');

				if($adrotate_debug['timers'] == true) {
					$click_timer = $now;
				} else {
					$click_timer = $now - $adrotate_config['click_timer'];
				}
	
				$saved_timer = $wpdb->get_var($wpdb->prepare("SELECT `timer` FROM `".$wpdb->prefix."adrotate_tracker` WHERE `ipaddress` = '%s' AND `stat` = 'c' AND `bannerid` = %d ORDER BY `timer` DESC LIMIT 1;", $remote_ip, $ad));
				if($saved_timer < $click_timer) {
					$stats = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `".$wpdb->prefix."adrotate_stats` WHERE `ad` = %d AND `group` = %d AND `thetime` = $today;", $ad, $group));
					if($stats > 0) {
						$wpdb->query("UPDATE `".$wpdb->prefix."adrotate_stats` SET `clicks` = `clicks` + 1 WHERE `id` = $stats;");
					} else {
						$wpdb->insert($wpdb->prefix.'adrotate_stats', array('ad' => $ad, 'group' => $group, 'thetime' => $today, 'clicks' => 1, 'impressions' => 1));
					}
					
					$adrotate_geo = adrotate_get_cookie('geo');
					$country = (isset($adrotate_geo['country'])) ? $adrotate_geo['country']: '';
					$city = (isset($adrotate_geo['city'])) ? $adrotate_geo['city'] : '';

					$wpdb->insert($wpdb->prefix.'adrotate_tracker', array('ipaddress' => $remote_ip, 'timer' => $now, 'bannerid' => $ad, 'stat' => 'c', 'useragent' => $useragent, 'country' => $country, 'city' => $city));
				}

				// Advertising budget
				$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `budget` = `budget` - `crate` WHERE `id` = $ad AND `crate` > 0;");
			}
		}

		if($blog_id > 0 AND adrotate_is_networked()) {
			switch_to_blog($current_blog);
		}

		unset($remote_ip, $track, $meta, $ad, $group, $remote, $banner);
	}

	wp_die();
}

/*-------------------------------------------------------------
 Name:      adrotate_filter_schedule

 Purpose:   Weed out ads that are over the limit of their schedule
 Receive:   $selected, $banner
 Return:    $selected
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_schedule($selected, $banner) { 
	global $wpdb, $adrotate_config, $adrotate_debug;

	$now = adrotate_now();

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_schedule()] Filtering banner</strong><pre>";
		print_r($banner->id); 
		echo "</pre></p>"; 
	}
	
	// Get schedules for advert
	$schedules = $wpdb->get_results("SELECT `".$wpdb->prefix."adrotate_schedule`.`id`, `starttime`, `stoptime`, `maxclicks`, `maximpressions`, `spread`, `dayimpressions` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` AND `ad` = ".$banner->id." ORDER BY `starttime` ASC;");

	$current = array();
	foreach($schedules as $schedule) {	
		if($schedule->starttime > $now OR $schedule->stoptime < $now) {
			$current[] = 0;
		} else {
			$current[] = 1;
			if($adrotate_config['enable_stats'] == 'Y') {
				$stat = adrotate_stats($banner->id, $schedule->starttime, $schedule->stoptime, 1);
	
				if($adrotate_debug['general'] == true) {
					echo "<p><strong>[DEBUG][adrotate_filter_schedule] Ad ".$banner->id." - Schedule (id: ".$schedule->id.")</strong><pre>";
					echo "<br />Start: ".$schedule->starttime." (".date("F j, Y, g:i a", $schedule->starttime).")";
					echo "<br />End: ".$schedule->stoptime." (".date("F j, Y, g:i a", $schedule->stoptime).")";
					echo "<br />Clicks this period: ".$stat['clicks'];
					echo "<br />Impressions this period: ".$stat['impressions'];
					echo "<br />Impressions this day: ".$stat['day'];
					echo "<br />Impression Spread: ".$schedule->spread." (Max ".$schedule->dayimpressions." per day)";
					echo "<br />Current: ";
					print_r($current);
					echo "</pre></p>";
				}
	
				if($stat['clicks'] >= $schedule->maxclicks AND $schedule->maxclicks > 0 AND $banner->tracker == "Y") {
					unset($selected[$banner->id]);
				}

				if($schedule->spread == 'Y' && $stat['day'] >= $schedule->dayimpressions) {
					unset($selected[$banner->id]);
				} else {
					if($stat['impressions'] >= $schedule->maximpressions AND $schedule->maximpressions > 0) {
						unset($selected[$banner->id]);
					}
				}
			}
		}
	}
	
	// Remove advert from array if all schedules are false (0)
	if(!in_array(1, $current)) {
		unset($selected[$banner->id]);
	}
	unset($current, $schedules);
	
	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_budget

 Purpose:   Weed out ads that are over the limit of their schedule
 Receive:   $selected, $banner
 Return:    $selected
 Since:		3.6.11
-------------------------------------------------------------*/
function adrotate_filter_budget($selected, $banner) { 
	global $wpdb, $adrotate_debug;

	$now = adrotate_now();
	if($banner->budget == null) $banner->budget = '0';
	if($banner->crate == null) $banner->crate = '0';
	if($banner->irate == null) $banner->irate = '0';

	if($adrotate_debug['general'] == true) {
		echo "<p><strong>[DEBUG][adrotate_filter_budget] Ad ".$banner->id."</strong><pre>";
		echo "Click Budget: ".$banner->budget;
		echo "<br />Cost per click: ".$banner->crate;
		echo "<br />Cost per impression: ".$banner->irate;
		echo "</pre></p>";
	}

	// Impressions
	if(($banner->budget <= 0 AND $banner->crate > 0) OR ($banner->budget <= 0 AND $banner->irate > 0)) {
		unset($selected[$banner->id]);
		return $selected;
	} 
	if($banner->budget > 0) {
		$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `budget` = `budget` - `irate` WHERE `id` = ".$banner->id.";");
	}

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_filter_location

 Purpose:   Determine the users location, the ads geo settings and filter out ads
 Receive:  	$selected, $banner
 Return:    $selected|array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_filter_location($selected, $banner) { 
	global $adrotate_debug;

	// Grab geo data from session or from cookie data
	if(isset($_SESSION['adrotate-geo'])) {
		$geo = $_SESSION['adrotate-geo'];
		$geo_source = 'Session data';
	} else {
		$geo = adrotate_get_cookie('geo');
		$geo_source = 'Cookie';
	}

	if(is_array($geo)) {
		$cities = unserialize(stripslashes($banner->cities));
		$countries = unserialize(stripslashes($banner->countries));
		if(!is_array($cities)) $cities = array();
		if(!is_array($countries)) $countries = array();
		
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			echo "Cookie or _SESSION: ".$geo_source;
			echo "<br />Geo Provider: ".$geo['provider']." (Code: ".$geo['status'].")";
			echo "<br />Visitor City and State: ".$geo['city']." (DMA: ".$geo['dma']."), " .$geo['state']." (ISO: ".$geo['statecode'].")";
			echo "<br />Advert Cities/States (".count($cities)."): ";
			print_r($cities);
			echo "<br />Visitor Country: ".$geo['country']." (".$geo['countrycode'].")";
			echo "<br />Advert Countries (".count($countries)."): ";
			print_r($countries);
			echo "</pre></p>";
		}
	
		if($geo['status'] == 200) {
			if(count($cities) > 0 AND count(array_intersect($cities, array($geo['city'], $geo['dma'], $geo['state'], $geo['statecode']))) == 0) {
				unset($selected[$banner->id]);
				return $selected;
			}
			if(count($countries) > 0 AND !in_array($geo['countrycode'], $countries)) {
				unset($selected[$banner->id]);
				return $selected;
			}
		}
	} else {
		if($adrotate_debug['general'] == true OR $adrotate_debug['geo'] == true) {
			echo "<p><strong>[DEBUG][adrotate_filter_location] Ad (id: ".$banner->id.")</strong><pre>";
			print_r($geo);
			echo "</pre></p>";
		}
	}

	return $selected;
} 

/*-------------------------------------------------------------
 Name:      adrotate_geolocation

 Purpose:   Find the location of the visitor
 Receive:   -None-
 Return:    $array
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_geolocation() {
	global $wpdb, $ajdg_solutions_domain;

	if((!adrotate_has_cookie('geo') AND adrotate_is_human())) {
		$adrotate_config = get_option('adrotate_config');

		$remote_ip = adrotate_get_remote_ip();
		$geo_result = array('status' => 403, 'provider' => 'Forbidden', 'city' => '', 'dma' => '', 'country' => '', 'countrycode' => '', 'state' => '', 'statecode' => '');

		if($adrotate_config['enable_geo'] == 2) { // GeoBytes
			$adrotate_config['enable_geo'] == 5; // AdRotate Geo
		}
	
		if($adrotate_config['enable_geo'] == 5) { // AdRotate Geo
			if(adrotate_is_networked()) {
				$adrotate_activate = get_site_option('adrotate_activate');
			} else {
				$adrotate_activate = get_option('adrotate_activate');
			}

			$args = array('headers' => array('User-Agent' => 'AdRotate Pro;' . get_option('siteurl')), 'sslverify' => false);
			$auth = base64_encode($adrotate_activate["instance"].':'.$adrotate_activate["key"]);
			$raw_response = wp_remote_get($ajdg_solutions_domain.'api/geo/1/?auth='.$auth.'&ip='.$remote_ip, $args);
		    
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
				$geo_result['provider'] = 'AdRotate Geo';
	
			    if($geo_result['status'] == 200 AND $response['code'] == 200) {
					$geo_result['city'] = (isset($response['city'])) ? strtolower($response['city']) : '';
					$geo_result['dma'] = (isset($response['dma'])) ? strtolower($response['dma']) : '';
					$geo_result['country'] = (isset($response['country'])) ? strtolower($response['country']) : '';
					$geo_result['countrycode'] = (isset($response['countrycode'])) ? $response['countrycode'] : '';
					$geo_result['state'] = (isset($response['state'])) ? strtolower($response['state']) : '';
					$geo_result['statecode'] = (isset($response['statecode'])) ? strtolower($response['statecode']) : '';
				} else { 			
					$geo_result['error'] = $response['code'].' '.$response['error'];
				}
				update_option('adrotate_geo_requests', $response['queries_remaining']);
			} else {
				$adrotate_config['enable_geo'] == 1;
			}
		}

		if($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) { // MaxMind
			if($adrotate_config['enable_geo'] == 3) {
				$service_type = 'country';
			}
			if($adrotate_config['enable_geo'] == 4) {
				$service_type = 'city';
			}
	
			$args = array('headers' => array('user-agent' => 'AdRotate Pro;', 'Authorization' => 'Basic '.base64_encode($adrotate_config["geo_email"].':'.$adrotate_config["geo_pass"])));
			$raw_response = wp_remote_get('https://geoip.maxmind.com/geoip/v2.1/'.$service_type.'/'.$remote_ip, $args);
		    
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
				$geo_result['provider'] = 'MaxMind '.$service_type;
	
			    if($geo_result['status'] == 200) {
					$geo_result['city'] = (isset($response['city']['names']['en'])) ? strtolower($response['city']['names']['en']) : '';
					$geo_result['dma'] = (isset($response['location']['metro_code'])) ? strtolower($response['location']['metro_code']) : '';
					$geo_result['country'] = (isset($response['country']['names']['en'])) ? strtolower($response['country']['names']['en']) : '';
					$geo_result['countrycode'] = (isset($response['country']['iso_code'])) ? $response['country']['iso_code'] : '';
					$geo_result['state'] = (isset($response['subdivisions'][0]['names']['en'])) ? strtolower($response['subdivisions'][0]['names']['en']) : '';
					$geo_result['statecode'] = (isset($response['subdivisions'][0]['iso_code'])) ? strtolower($response['subdivisions'][0]['iso_code']) : '';
				} else { 			
					$geo_result['error'] = $response['code'].' '.$response['error'];
					if($response['code'] == 'OUT_OF_QUERIES') $response['maxmind']['queries_remaining'] = 0;
				}
				update_option('adrotate_geo_requests', $response['maxmind']['queries_remaining']);
			} else {
				$adrotate_config['enable_geo'] == 1;
			}
		}

		if($adrotate_config['enable_geo'] == 1) { // Telize
			$args = array('headers' => array('user-agent' => 'AdRotate Pro;'));
			$raw_response = wp_remote_get('http://www.telize.com/geoip/'.$remote_ip, $args);
		    if(!is_wp_error($raw_response)) {	
			    $response = json_decode($raw_response['body'], true);
				$geo_result['status'] = $raw_response['response']['code'];
				$geo_result['provider'] = 'Telize';
				$geo_result['city'] = (isset($response['city'])) ? strtolower($response['city']) : '';
				$geo_result['dma'] = (isset($response['dma_code']) AND $response['dma_code'] > 0) ? strtolower($response['dma_code']) : '';
				$geo_result['country'] = (isset($response['country'])) ? strtolower($response['country']) : '';
				$geo_result['countrycode'] = (isset($response['country_code'])) ? $response['country_code'] : '';
				$geo_result['state'] = '';
				$geo_result['statecode'] = '';
			}
		} 
	    unset($raw_response, $response);

		setcookie('adrotate-geo', serialize($geo_result), time() + $adrotate_config['geo_cookie_life'], COOKIEPATH, COOKIE_DOMAIN);
		if(!isset($_SESSION['adrotate-geo'])) $_SESSION['adrotate-geo'] = $geo_result;
	}	
}

/*-------------------------------------------------------------
 Name:      adrotate_has_cookie

 Purpose:   Check if a certain AdRotate Cookie exists
 Receive:   $get
 Return:    Boolean
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_has_cookie($get) {
	if($get == 'geo') {
		if(!empty($_COOKIE['adrotate-geo'])) return true;
	}
	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_cookie

 Purpose:   Get a certain AdRotate Cookie
 Receive:   $get
 Return:    $data
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_get_cookie($get) {

	$data = false;
	if($get == 'geo') {
		if(!empty($_COOKIE['adrotate-geo'])) $data = $_COOKIE['adrotate-geo'];
	}
	return maybe_unserialize(stripslashes($data));
}
	
/*-------------------------------------------------------------
 Name:      adrotate_object_to_array

 Purpose:   Convert an object to a array
 Receive:   $data
 Return:    $data|$result
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_object_to_array($data) {
	if(is_array($data)) {
		return $data;
	}

	if(is_object($data)) {
		$result = array();
		foreach($data as $key => $value) {
			$result[$key] = adrotate_object_to_array($value);
		}
		return $result;
	}
	return $data;
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Filter out duplicate records in multidimensional arrays
 Receive:   $array
 Return:    $array|$return
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_array_unique($array) {
	if(count($array) > 0) {
		if(is_array($array[0])) {
			$return = array();
			// multidimensional
			foreach($array as $row) {
				if(!in_array($row, $return)) {
					$return[] = $row;
				}
			}
			return $return;
		} else {
			// not multidimensional
			return array_unique($array);
		}
	} else {
		return $array;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_array_unique

 Purpose:   Generate a random string
 Receive:   $length
 Return:    $result
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_rand($length = 8) {
	$available_chars = "abcdefghijklmnopqrstuvwxyz";	

	$result = '';
	for($i = 0; $i < $length; $i++) {
		$result .= $available_chars[mt_rand(0, 25)];
	}

	return $result;
}

/*-------------------------------------------------------------
 Name:      adrotate_pick_weight

 Purpose:   Sort out and pick a random ad based on weight
 Receive:   $selected
 Return:    $ads[$key]
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_pick_weight($selected) { 
    $ads = array_keys($selected); 
    foreach($selected as $banner) {
		$weight[] = $banner->weight;
		unset($banner);
	}
     
    $sum_of_weight = array_sum($weight)-1; 
    $rnd = mt_rand(0,$sum_of_weight); 

    foreach($ads as $key => $var){ 
        if($rnd<$weight[$key]){ 
            return $ads[$key]; 
        } 
        $rnd  -= $weight[$key]; 
    }
    unset($ads, $weight, $sum_of_weight, $rnd);
} 

/*-------------------------------------------------------------
 Name:      adrotate_shuffle

 Purpose:   Randomize and slice an array but keep keys intact
 Receive:   $array
 Return:    $shuffle
 Since:		3.8.8.3
-------------------------------------------------------------*/
function adrotate_shuffle($array, $amount = 20) { 
	if(!is_array($array)) return $array; 
	$keys = array_keys($array); 
	shuffle($keys);
	
	$shuffle = array(); 
	foreach($keys as $key) {
		$shuffle[$key] = $array[$key];
	}
	return $shuffle; 
}

/*-------------------------------------------------------------
 Name:      adrotate_select_categories

 Purpose:   Create scrolling menu of all categories.
 Receive:   $savedcats, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_categories($savedcats, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedcats)) $savedcats = explode(',', $savedcats);
	$categories = get_categories(array('child_of' => $parent, 'parent' => $parent,  'orderby' => 'id', 'order' => 'asc', 'hide_empty' => 0));

	if(!empty($categories)) {
		$output = '';
		if($parent == 0) {
			$output = '<table width="100%">';
			if(count($categories) > 5) {
				$output .= '<thead><tr><td scope="col" class="manage-column check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			}
			$output .= '<tbody>';
		}
		foreach($categories as $category) {
			if($category->parent > 0) {
				if($category->parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_categories[]" value="'.$category->cat_ID.'"';
			if(in_array($category->cat_ID, $savedcats)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$indent.$category->name.' ('.$category->category_count.')</td>';
			$output .= '</tr>';
			$output .= adrotate_select_categories($savedcats, $count, $category->parent, $category->cat_ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_select_pages

 Purpose:   Create scrolling menu of all pages.
 Receive:   $savedpages, $count, $child_of, $parent
 Return:    $output
 Since:		3.8.4
-------------------------------------------------------------*/
function adrotate_select_pages($savedpages, $count = 2, $child_of = 0, $parent = 0) {
	if(!is_array($savedpages)) $savedpages = explode(',', $savedpages);
	$pages = get_pages(array('child_of' => $parent, 'parent' => $parent, 'sort_column' => 'ID', 'sort_order' => 'asc'));

	if(!empty($pages)) {
		$output = '';
		if($parent == 0) {
			$output = '<table width="100%">';
			if(count($pages) > 5) {
				$output .= '<thead><tr><td scope="col" class="manage-column check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr></thead>';
			}
			$output .= '<tbody>';
		}
		foreach($pages as $page) {
			if($page->post_parent > 0) {
				if($page->post_parent != $child_of) {
					$count = $count + 1;
				}
				$indent = '&nbsp;'.str_repeat('-', $count * 2).'&nbsp;';
			} else {
				$indent = '';
			}
			$output .= '<tr>';
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_pages[]" value="'.$page->ID.'"';
			if(in_array($page->ID, $savedpages)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$indent.$page->post_title.'</td>';
			$output .= '</tr>';
			$output .= adrotate_select_pages($savedpages, $count, $page->post_parent, $page->ID);
			$child_of = $parent;
		}
		if($parent == 0) {
			$output .= '</tbody></table>';
		}
		return $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_countries

 Purpose:   List of countries
 Receive:   -None-
 Return:    array
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_countries() {
	return array('US' => "United States", 'CA' => "Canada", 'GB' => "United Kingdom", 'AU' => "Australia", 'AT' => "Austria", 'BE' => "Belgium", 'BR' => "Brazil", 'FR' => "France", 'DE' => "Germany", 'IT' => "Italy", 
	'NL' => "the Netherlands", 'NZ' => "New Zealand", 'PL' => "Poland", 'JP' => "Japan", 'SA' => "Saudi Arabia", 'ES' => "Spain", 'IE' => "Ireland", 'CH' => "Switzerland", 
	
	'DIVIDER' => "--- The rest is listed alphabetically ---", 'AF' => "Afghanistan", 'AL' => "Albania", 'DZ' => "Algeria", 'AD' => "Andorra", 'AO' => "Angola", 'AG' => "Antigua and Barbuda", 'AR' => "Argentina", 
	'AM' => "Armenia",  'AZ' => "Azerbaijan", 'BS' => "Bahamas", 'BH' => "Bahrain", 'BD' => "Bangladesh", 'BB' => "Barbados", 'BY' => "Belarus", 'BZ' => "Belize", 
	'BJ' => "Benin", 'BT' => "Bhutan", 'BO' => "Bolivia", 'BA' => "Bosnia and Herzegovina", 'BW' => "Botswana", 'BN' => "Brunei", 'BG' => "Bulgaria", 'BF' => "Burkina Faso", 'BI' => "Burundi", 'KH' => "Cambodia", 
	'CM' => "Cameroon", 'CV' => "Cape Verde", 'CF' => "Central African Republic", 'TD' => "Chad", 'CL' => "Chile", 'CN' => "China", 'CO' => "Colombia", 'KM' => "Comoros", 'CG' => "Congo (Brazzaville)", 
	'CD' => "Congo", 'CR' => "Costa Rica", 'CI' => "Cote d'Ivoire", 'HR' => "Croatia", 'CU' => "Cuba", 'CY' => "Cyprus", 'CZ' => "Czech Republic", 'DK' => "Denmark", 'DJ' => "Djibouti", 'DM' => "Dominica", 
	'DO' => "Dominican Republic", 'TL' => "East Timor (Timor Timur)", 'EC' => "Ecuador", 'EG' => "Egypt", 'SV' => "El Salvador", 'GQ' => "Equatorial Guinea", 'ER' => "Eritrea", 'EE' => "Estonia", 'ET' => "Ethiopia", 
	'FJ' => "Fiji", 'FI' => "Finland", 'GA' => "Gabon", 'GM' => "Gambia, The", 'GE' => "Georgia", 'GH' => "Ghana", 'GR' => "Greece", 'GD' => "Grenada", 'GT' => "Guatemala", 
	'GN' => "Guinea", 'GW' => "Guinea-Bissau", 'GY' => "Guyana", 'HT' => "Haiti", 'HN' => "Honduras", 'HU' => "Hungary", 'IS' => "Iceland", 'IN' => "India", 'ID' => "Indonesia", 'IR' => "Iran", 'IQ' => "Iraq", 
	'IS' => "Israel", 'JM' => "Jamaica", 'JO' => "Jordan", 'KZ' => "Kazakhstan", 'KE' => "Kenya", 'KI' => "Kiribati", 'KP' => "Korea, North", 'KR' => "Korea, South", 
	'KW' => "Kuwait", 'KG' => "Kyrgyzstan", 'LA' => "Laos", 'LV' => "Latvia", 'LB' => "Lebanon", 'LS' => "Lesotho", 'LR' => "Liberia", 'LY' => "Libya", 'LI' => "Liechtenstein", 'LT' => "Lithuania", 
	'LU' => "Luxembourg", 'MK' => "Macedonia", 'MG' => "Madagascar", 'MW' => "Malawi", 'MY' => "Malaysia", 'MV' => "Maldives", 'ML' => "Mali", 'MT' => "Malta", 'MH' => "Marshall Islands", 'MR' => "Mauritania", 
	'MU' => "Mauritius", 'MX' => "Mexico", 'FM' => "Micronesia", 'MD' => "Moldova", 'MC' => "Monaco", 'MN' => "Mongolia", 'MA' => "Morocco", 'MZ' => "Mozambique", 'MM' => "Myanmar", 'NA' => "Namibia", 'NR' => "Nauru", 
	'NP' => "Nepal",  'NI' => "Nicaragua", 'NE' => "Niger", 'NG' => "Nigeria", 'NO' => "Norway", 'OM' => "Oman", 'PK' => "Pakistan", 'PW' => "Palau", 'PA' => "Panama", 
	'PG' => "Papua New Guinea", 'PY' => "Paraguay", 'PE' => "Peru", 'PH' => "Philippines", 'PT' => "Portugal", 'QA' => "Qatar", 'RO' => "Romania", 'RU' => "Russia", 'RW' => "Rwanda", 
	'KN' => "Saint Kitts and Nevis", 'LC' => "Saint Lucia", 'VC' => "Saint Vincent", 'WS' => "Samoa", 'SM' => "San Marino", 'ST' => "Sao Tome and Principe", 'SN' => "Senegal", 
	'RS' => "Serbia and Montenegro", 'SC' => "Seychelles", 'SL' => "Sierra Leone", 'SG' => "Singapore", 'SK' => "Slovakia", 'SI' => "Slovenia", 'SB' => "Solomon Islands", 'SO' => "Somalia", 'ZA' => "South Africa", 
	 'LK' => "Sri Lanka", 'SD' => "Sudan", 'SR' => "Suriname", 'SZ' => "Swaziland", 'SE' => "Sweden", 'SY' => "Syria", 'TW' => "Taiwan", 'TJ' => "Tajikistan", 'TZ' => "Tanzania", 
	'TH' => "Thailand", 'TG' => "Togo", 'TO' => "Tonga", 'TT' => "Trinidad and Tobago", 'TN' => "Tunisia", 'TR' => "Turkey", 'TM' => "Turkmenistan", 'TV' => "Tuvalu", 'UG' => "Uganda", 'UA' => "Ukraine", 
	'AE' => "United Arab Emirates",  'UY' => "Uruguay", 'UZ' => "Uzbekistan", 'VU' => "Vanuatu", 'VA' => "Vatican City", 'VE' => "Venezuela", 'VN' => "Vietnam", 
	'YE' => "Yemen", 'ZM' => "Zambia", 'ZW' => "Zimbabwe");
}

/*-------------------------------------------------------------
 Name:      adrotate_select_countries

 Purpose:   Create scrolling menu of all countries.
 Receive:   $savedcountries
 Return:    $output
 Since:		3.8.5.1
-------------------------------------------------------------*/
function adrotate_select_countries($savedcountries) {
	if(!is_array($savedcountries)) $savedcountries = array();
	$countries = adrotate_countries();

	$output = '<table width="100%">';
	$output .= '<thead><tr><td scope="col" class="manage-column check-column" style="padding: 0px;"><input type="checkbox" /></td><td style="padding: 0px;">Select All</td></tr>';
	$output .= '<tr><td colspan="2" style="padding: 0px;"><em>--- Popular countries ---</em></td></tr></thead><tbody>';
	foreach($countries as $k => $v) {
		$output .= '<tr>';
		if($k != "DIVIDER") {
			$output .= '<td class="check-column" style="padding: 0px;"><input type="checkbox" name="adrotate_geo_countries[]" value="'.$k.'"';
			if(in_array($k, $savedcountries)) {
				$output .= ' checked';
			}
			$output .= '></td><td style="padding: 0px;">'.$v.'</td>';
		} else {
			$output .= '<td colspan="2" style="padding: 0px;"><em>'.$v.'</em></td>';
		}
		$output .= '</tr>';
	}
	$output .= '</tbody></table>';
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_evaluate_ads

 Purpose:   Initiate evaluations for errors and determine the ad status
 Receive:   $return
 Return:    opt|int
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_prepare_evaluate_ads($return = true) {
	global $wpdb;
	
	// Fetch ads
	$ads = $wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."adrotate` WHERE `type` != 'disabled' AND `type` != 'empty' AND `type` != 'a_empty' AND `type` != 'queue' AND `type` != 'reject' AND `type` NOT LIKE 's_%' ORDER BY `id` ASC;");

	// Determine error states
	$error = $expired = $expiressoon = $normal = $unknown = 0;
	foreach($ads as $ad) {
		$result = adrotate_evaluate_ad($ad->id);
		if($result == 'error') {
			$error++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'error' WHERE `id` = '".$ad->id."';");
		} 

		if($result == 'expired') {
			$expired++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'expired' WHERE `id` = '".$ad->id."';");
		} 
		
		if($result == '2days') {
			$expiressoon++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = '2days' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == '7days') {
			$normal++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = '7days' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == 'active') {
			$normal++;
			$wpdb->query("UPDATE `".$wpdb->prefix."adrotate` SET `type` = 'active' WHERE `id` = '".$ad->id."';");
		}
		
		if($result == 'unknown') {
			$unknown++;
		}
		unset($ad);
	}

	$count = $expired + $expiressoon + $error + $unknown;
	$result = array('error' => $error, 'expired' => $expired, 'expiressoon' => $expiressoon, 'normal' => $normal, 'total' => $count, 'unknown' => $unknown);
	update_option('adrotate_advert_status', $result);
	unset($ads, $result);
	if($return) adrotate_return('adrotate-settings', 405);
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ads

 Purpose:   Initiate automated evaluations for errors and determine the ad status
 Receive:   -None-
 Return:    -None-
 Since:		3.8.7.1
-------------------------------------------------------------*/
function adrotate_evaluate_ads() {
	adrotate_prepare_evaluate_ads(false);
}

/*-------------------------------------------------------------
 Name:      adrotate_evaluate_ad

 Purpose:   Evaluates ads for errors
 Receive:   $ad_id
 Return:    boolean
 Since:		3.6.5
-------------------------------------------------------------*/
function adrotate_evaluate_ad($ad_id) {
	global $wpdb, $adrotate_config;
	
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;

	// Fetch ad
	$ad = $wpdb->get_row($wpdb->prepare("SELECT `id`, `bannercode`, `tracker`, `link`, `imagetype`, `image`, `budget`,`crate`, `irate`, `responsive` FROM `".$wpdb->prefix."adrotate` WHERE `id` = %d;", $ad_id));
	$advertiser = $wpdb->get_var("SELECT `user` FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `group` = 0 AND `user` > 0 AND `schedule` = 0;");
	$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `".$wpdb->prefix."adrotate_schedule`, `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `schedule` = `".$wpdb->prefix."adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	$schedules = $wpdb->get_var("SELECT COUNT(`schedule`) FROM `".$wpdb->prefix."adrotate_linkmeta` WHERE `ad` = '".$ad->id."' AND `group` = 0 AND `user` = 0;");

	$bannercode = stripslashes(htmlspecialchars_decode($ad->bannercode, ENT_QUOTES));
	// Determine error states
	if(
		strlen($bannercode) < 1 // AdCode empty
		OR ($ad->tracker == 'N' AND $advertiser > 0) // Didn't enable click-tracking, DID set a advertiser
		OR (!preg_match_all('/<a[^>](.*?)>/i', $bannercode, $things) AND $ad->tracker == 'Y') // Clicktracking active but no valid link present
		OR ($ad->tracker == 'N' AND $ad->crate > 0)	// Clicktracking in-active but set a Click rate
		OR (preg_match("/%image%/i", $bannercode) AND $ad->image == '' AND $ad->imagetype == '') // Did use %image% but didn't select an image
		OR (!preg_match("/%image%/i", $bannercode) AND $ad->image != '' AND $ad->imagetype != '') // Didn't use %image% but selected an image
		OR (!preg_match("/%image%/i", $bannercode) AND $ad->responsive == 'Y') // Didn't use %image% but enabled Responsive
		OR (strlen($ad->image) > 0 AND !preg_match("/full/", $ad->image) AND $ad->responsive == 'Y') // Filename not correct for Responsive
		OR (($ad->image == '' AND $ad->imagetype != '') OR ($ad->image != '' AND $ad->imagetype == '')) // Image and Imagetype mismatch
		OR $schedules == 0 // No Schedules for this ad
	) {
		return 'error';
	} else if(
		$stoptime <= $now // Past the enddate
		OR (($ad->crate > 0 OR $ad->irate > 0) AND $ad->budget <= 0) // Ad ran out of money
	){
		return 'expired';
	} else if(
		$stoptime <= $in2days AND $stoptime >= $now	// Expires in 2 days
	){
		return '2days';
	} else if(
		$stoptime <= $in7days AND $stoptime >= $now	// Expires in 7 days
	){
		return '7days';
	} else {
		return 'active';
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_prepare_color

 Purpose:   Check if ads are expired and set a color for its end date
 Receive:   $banner_id
 Return:    $result
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_prepare_color($enddate) {
	$now = adrotate_now();
	$in2days = $now + 172800;
	$in7days = $now + 604800;
	
	if($enddate <= $now) {
		return '#CC2900'; // red
	} else if($enddate <= $in2days AND $enddate >= $now) {
		return '#F90'; // orange
	} else if($enddate <= $in7days AND $enddate >= $now) {
		return '#E6B800'; // yellow
	} else {
		return '#009900'; // green
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_is_in_groups

 Purpose:   Build list of groups the ad is in (overview)
 Receive:   $id
 Return:    $output
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_ad_is_in_groups($id) {
	global $wpdb;

	$output = '';
	$groups	= $wpdb->get_results("
		SELECT 
			`".$wpdb->prefix."adrotate_groups`.`name` 
		FROM 
			`".$wpdb->prefix."adrotate_groups`, 
			`".$wpdb->prefix."adrotate_linkmeta` 
		WHERE 
			`".$wpdb->prefix."adrotate_linkmeta`.`ad` = '".$id."'
			AND `".$wpdb->prefix."adrotate_linkmeta`.`group` = `".$wpdb->prefix."adrotate_groups`.`id`
			AND `".$wpdb->prefix."adrotate_linkmeta`.`user` = 0
		;");
	if($groups) {
		foreach($groups as $group) {
			$output .= $group->name.", ";
		}
	}
	$output = rtrim($output, ", ");
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_hash

 Purpose:   Generate the adverts clicktracking hash
 Receive:   $ad, $group, $remote, $blog_id
 Return:    $result
 Since:		3.9.12
-------------------------------------------------------------*/
function adrotate_hash($ad, $group = 0, $blog_id = 0) {
	global $adrotate_debug, $adrotate_config;
	
	if($adrotate_debug['timers'] == true) {
		$timer = 0;
	} else {
		$timer = $adrotate_config['impression_timer'];
	}
		
	if($adrotate_debug['track'] == true) {
		return "$ad,$group,$blog_id,$timer";
	} else {
		return base64_encode("$ad,$group,$blog_id,$timer");
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_get_remote_ip

 Purpose:   Get the remote IP from the visitor
 Receive:   -None-
 Return:    $buffer[0]
 Since:		3.6.2
-------------------------------------------------------------*/
function adrotate_get_remote_ip(){
	if(empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$remote_ip = $_SERVER["REMOTE_ADDR"];
	} else {
		$remote_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	$buffer = explode(',', $remote_ip, 2);

	return $buffer[0];
}

/*-------------------------------------------------------------
 Name:      adrotate_home_path

 Purpose:   Get the home/base path for a site
 Receive:   -None-
 Return:    $home_path
 Since:		3.8.8
-------------------------------------------------------------*/
function adrotate_home_path() {
	$home = get_option( 'home' );
	$siteurl = get_option( 'siteurl' );
	if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {
		$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
		$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
		$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
		$home_path = trailingslashit( $home_path );
	} else {
		$home_path = ABSPATH;
	}

	return $home_path;
}

/*-------------------------------------------------------------
 Name:      adrotate_sanitize_file_name

 Purpose:   Clean up file names of files that are being uploaded.
 Receive:   $filename
 Return:    $filename
 Since:		3.11.3
-------------------------------------------------------------*/
function adrotate_sanitize_file_name($filename) {
    $filename_raw = $filename;
    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
    $filename = str_replace($special_chars, '', $filename);
    $filename = preg_replace('/[\s-]+/', '-', $filename);
    $filename = strtolower(trim($filename, '.-_'));
    return $filename;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_sorted_roles

 Purpose:   Returns all roles and capabilities, sorted by user level. Lowest to highest.
 Receive:   -none-
 Return:    $sorted
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_get_sorted_roles() {	
	global $wp_roles;

	$editable_roles = apply_filters('editable_roles', $wp_roles->roles);
	$sorted = array();
	
	foreach($editable_roles as $role => $details) {
		$sorted[$details['name']] = get_role($role);
	}

	$sorted = array_reverse($sorted);

	return $sorted;
}

/*-------------------------------------------------------------
 Name:      adrotate_set_capability

 Purpose:   Grant or revoke capabilities to a role and all higher roles
 Receive:   $lowest_role, $capability
 Return:    -None-
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_set_capability($lowest_role, $capability){
	$check_order = adrotate_get_sorted_roles();
	$add_capability = false;
	
	foreach($check_order as $role) {
		if($lowest_role == $role->name) $add_capability = true;
		if(empty($role)) continue;
		$add_capability ? $role->add_cap($capability) : $role->remove_cap($capability) ;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_remove_capability

 Purpose:   Remove the $capability from the all roles
 Receive:   $capability
 Return:    -None-
 Since:		3.2
-------------------------------------------------------------*/
function adrotate_remove_capability($capability){
	$check_order = adrotate_get_sorted_roles();

	foreach($check_order as $role) {
		$role = get_role($role->name);
		$role->remove_cap($capability);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications

 Purpose:   Determine how to contact who
 Receive:   -None-
 Return:    -None-
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_notifications() {
	adrotate_push_notifications();
	adrotate_mail_notifications();
}

/*-------------------------------------------------------------
 Name:      adrotate_push_notifications

 Purpose:   Email the manager that his ads need help
 Receive:   $action, $adid
 Return:    -None-
 Since:		3.9.9
-------------------------------------------------------------*/
function adrotate_push_notifications($action = false, $adid = false) {
	global $wpdb, $adrotate_config, $advert_status;

	$notifications = get_option("adrotate_notifications");

	if($notifications['notification_push'] == 'Y') {
		$geo_lookups = get_option('adrotate_geo_requests');

		if(isset($_POST['adrotate_notification_test_submit'])) {
			$notification_test = true;
		} else { 
			$notification_test = false;
		}

		$title = $message = '';
	
		if($notification_test) {
			$title = "AdRotate Test";
			$message = "This is a test notification.\nHave a nice day!";
			$advert_status['total'] = 0;
		}
	
		if($advert_status['total'] > 0 AND $notifications['notification_push_status'] == 'Y') {
			$title = "AdRotate Alert";
			if($advert_status['error'] > 0) $message .= $advert_status['error']." ".__('ad(s) with errors!', 'adrotate');
			if($advert_status['expired'] > 0) $message .= "\n".$advert_status['expired']." ".__('ad(s) expired!', 'adrotate');
			if($advert_status['expiressoon'] > 0) $message .= "\n".$advert_status['expiressoon']." ".__('ad(s) will expire in less than 2 days.', 'adrotate');
			if($advert_status['unknown'] > 0) $message .= "\n".$advert_status['unknown']." ".__('ad(s) have an unknown status.', 'adrotate');
		}
	
		if($adrotate_config['enable_geo'] > 2 AND $geo_lookups < 1000 AND $notifications['notification_push_geo'] == 'Y') { // Send notifications about Geo Targeting
			$title = "AdRotate Geo Targeting";
			if($geo_lookups > 0) $message = "Your website has less than 1000 lookups left for Geo Targeting. If you run out of lookups, Geo Targeting will stop working.";
			if($geo_lookups < 1) $message = "Your website has no lookups for Geo Targeting. Geo Targeting is currently not working.";
		}

		// User (Advertiser) invoked actions
		if(($action == 'approved' AND $notifications['notification_push_approved'] == 'Y') OR ($action == 'rejected' AND $notifications['notification_push_rejected'] == 'Y')) {
			$name = $wpdb->get_var("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = ".$adid.";");
			$title = "AdRotate Advert";
			$message = "A moderator has just ".$action." advert\n".$name." (ID: ".$adid.")";
		}
		
		if($action == 'queued' AND $notifications['notification_push_queue'] == 'Y') {
			$name = $wpdb->get_var("SELECT `title` FROM `".$wpdb->prefix."adrotate` WHERE `id` = ".$adid.";");
			$queued = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."adrotate` WHERE `type` = 'queue' OR `type` = 'reject';");

			$title = "AdRotate Moderation Queue";
			$message = "An advertiser has just queued their advert.\nName '".$name."' (ID: ".$adid.")\nAwaiting moderation: ".$queued." adverts.";
		}
		
		$args = array('token' => $notifications['notification_push_api'], 'user' => $notifications['notification_push_user'], 'title' => $title, 'message' => $message, 'url' => get_option('siteurl'), 'url_title' => get_option('blogname'), 'priority' => 0);
	
		wp_remote_post('https://api.pushover.net/1/messages.json?' . http_build_query($args), array('timeout' => 15));
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_notifications

 Purpose:   Email the manager that his ads need help
 Receive:   -None-
 Return:    -None-
 Since:		3.0
-------------------------------------------------------------*/
function adrotate_mail_notifications() {
	global $adrotate_config, $adrotate_advert_status;

	$notifications = get_option("adrotate_notifications");

	if($notifications['notification_email'] == 'Y' AND $notifications['notification_email_switch'] == 'Y') {
		if(isset($_POST['adrotate_notification_test_submit'])) {
			$notification_test = true;
		} else { 
			$notification_test = false;
		}
	
		$emails = $notifications['notification_email_publisher'];
		$x = count($emails);
		if($x == 0) $emails = array(get_option('admin_email'));
		
		$blogname 		= get_option('blogname');
		$dashboardurl	= get_option('siteurl')."/wp-admin/admin.php?page=adrotate-ads";
		$pluginurl		= "https://ajdg.solutions/products/adrotate-for-wordpress/";
	
		if($notification_test) { // Send test message
			$subject = __('[AdRotate Alert] Test message!', 'adrotate');
			
			$message = "<p>".__('Hello', 'adrotate').",</p>";
			$message .= "<p>".__('This notification is sent to you from your website', 'adrotate')." '$blogname'.<br />";
			$message .= "<p><strong>".__('This is a test notification!', 'adrotate')."</strong></p>";

			$message .= "<p>".__('Have a nice day!', 'adrotate')."</p>";
			$message .= "<p>".__('Your AdRotate Notifier', 'adrotate')."<br />";
			$message .= "$pluginurl</p>";
	
			for($i=0;$i<$x;$i++) {
			    $headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: AdRotate Plugin <".$emails[$i].">" . "\r\n";
				wp_mail($emails[$i], $subject, $message, $headers);
			}
		}

		if($advert_status['total'] > 0) { // Notifier for advert status
			$subject = __('[AdRotate Alert] Your ads need your help!', 'adrotate');
			
			$message = "<p>".__('Hello', 'adrotate').",</p>";
			$message .= "<p>".__('This notification is sent to you from your website', 'adrotate')." '$blogname'.<br />";
			$message .= "<p><strong>".__('Current issues:', 'adrotate')."</strong><br />";
			if($advert_status['error'] > 0) $message .= $advert_status['error']." ".__('ad(s) have configuration errors. This needs your immediate attention!', 'adrotate')."<br />";
			if($advert_status['expired'] > 0) $message .= $advert_status['expired']." ".__('ad(s) expired. This needs your immediate attention!', 'adrotate')."<br />";
			if($advert_status['expiressoon'] > 0) $message .= $advert_status['expiressoon']." ".__('ad(s) will expire in less than 2 days.', 'adrotate')."<br />";
			$message .= __('A total of', 'adrotate')." ".$advert_status['total']." ".__('ad(s) are in need of your care!', 'adrotate')."</p>";

			$message .= "<p>".__('Access your dashboard here:', 'adrotate')." $dashboardurl<br />";	
			$message .= __('Have a nice day!', 'adrotate')."</p>";
			$message .= "<p>".__('Your AdRotate Notifier', 'adrotate')."<br />";
			$message .= "$pluginurl</p>";
	
			for($i=0;$i<$x;$i++) {
			    $headers = "Content-Type: text/html; charset=UTF-8\r\nFrom: AdRotate Plugin <".$emails[$i].">" . "\r\n";
				wp_mail($emails[$i], $subject, $message, $headers);
			}
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_mail_message

 Purpose:   Email the publisher that an advertiser wants something
 Receive:   -None-
 Return:    -None-
 Since:		3.1
-------------------------------------------------------------*/
function adrotate_mail_message() {
	if(wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_advertiser') OR wp_verify_nonce($_POST['adrotate_nonce'], 'adrotate_email_moderator')) {
		$notifications 	= get_option("adrotate_notifications");
		$id 			= $_POST['adrotate_id'];
		$request 		= $_POST['adrotate_request'];
		$author 		= $_POST['adrotate_username'];
		$useremail 		= $_POST['adrotate_email'];
		$text	 		= strip_tags(stripslashes(trim($_POST['adrotate_message'], "\t\n ")));
	
		if(strlen($text) < 1) $text = "";
		
		$emails = $notifications['notification_email_advertiser'];
		$x = count($emails);
		if($x == 0) $emails = array(get_option('admin_email'));
		
		$siteurl 		= get_option('siteurl');
		$adurl			= $siteurl."/wp-admin/admin.php?page=adrotate-ads&view=edit&ad=".$id;
		$pluginurl		= "https://ajdg.solutions/products/adrotate-for-wordpress/";
	
		$now 		= adrotate_now();
		
		if($request == "renew") $subject = __('[AdRotate] An advertiser has put in a request for renewal!', 'adrotate');
		if($request == "remove") $subject = __('[AdRotate] An advertiser wants his ad removed.', 'adrotate');
		if($request == "other") $subject = __('[AdRotate] An advertiser wrote a comment on his ad!', 'adrotate');
		if($request == "issue") $subject = __('[AdRotate] An advertiser has a problem!', 'adrotate');
		
		$message = "<p>Hello,</p>";
	
		if($request == "renew") $message .= "<p>$author ".__('requests ad', 'adrotate')." <strong>$id</strong> ".__('renewed!', 'adrotate')."</p>";
		if($request == "remove") $message .= "<p>$author ".__('requests ad', 'adrotate')." <strong>$id</strong> ".__('removed.', 'adrotate')."</p>";
		if($request == "other") $message .= "<p>$author ".__('has something to say about ad', 'adrotate')." <strong>$id</strong>.</p>";
		if($request == "issue") $message .= "<p>$author ".__('has a problem with AdRotate.', 'adrotate')."</p>";
		
		$message .= "<p>".__('Attached message:', 'adrotate')." $text</p>";
		
		$message .= "<p>".__('You can reply to this message to contact', 'adrotate')." $author.<br />";
		if($request != "issue") $message .= __('Review the ad here:', 'adrotate')." $adurl";
		$message .= "</p>";
		
		$message .= "<p>".__('Have a nice day!', 'adrotate')."<br />";
		$message .= __('Your AdRotate Notifier', 'adrotate')."<br />";
		$message .= "$pluginurl</p>";

		for($i=0;$i<$x;$i++) {
		    $headers 	= "Content-Type: text/html; charset=UTF-8" . "\r\n" .
		      			  "From: $author <$useremail>" . "\r\n";
		
			wp_mail($emails[$i], $subject, $message, $headers);
		}
	
		adrotate_return('adrotate-advertiser', 300);
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_scripts

 Purpose:   Load file uploaded popup
 Receive:   -None-
 Return:	-None-
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_scripts() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('raphael', plugins_url('/library/raphael-min.js', __FILE__), array('jquery'));
	wp_enqueue_script('elycharts', plugins_url('/library/elycharts.min.js', __FILE__), array('jquery', 'raphael'));
	wp_enqueue_script('textatcursor', plugins_url('/library/textatcursor.js', __FILE__));

	// WP Pointers
	$seen_it = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	$do_add_script = false;
	if(!in_array('adrotate_pro', $seen_it)) {
		$do_add_script = true;
		add_action('admin_print_footer_scripts', 'adrotate_welcome_pointer');
	}

	if($do_add_script) {
		wp_enqueue_script('wp-pointer');
		wp_enqueue_style('wp-pointer');
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_styles

 Purpose:   Load file uploaded popup
 Receive:   -None-
 Return:	-None-
 Since:		3.6
-------------------------------------------------------------*/
function adrotate_dashboard_styles() {
	wp_enqueue_style( 'adrotate-admin-stylesheet', plugins_url( 'library/dashboard.css', __FILE__ ) );
}

/*-------------------------------------------------------------
 Name:      adrotate_folder_contents

 Purpose:   List folder contents of /wp-content/banners and /wp-content/uploads
 Receive:   $current
 Return:	$output
 Since:		0.4
-------------------------------------------------------------*/
function adrotate_folder_contents($current) {
	global $wpdb, $adrotate_config;

	$output = '';
	$siteurl = get_option('siteurl');

	// Read Banner folder
	$files = array();
	$i = 0;
	if($handle = opendir(adrotate_home_path().'/'.$adrotate_config['banner_folder'])) {
	    while (false !== ($file = readdir($handle))) {
	        if ($file != "." AND $file != ".." AND $file != "index.php") {
	            $files[] = $file;
	        	$i++;
	        }
	    }
	    closedir($handle);

	    if($i > 0) {
			sort($files);
			foreach($files as $file) {
				$fileinfo = pathinfo($file);
		
				if((strtolower($fileinfo['extension']) == "jpg" OR strtolower($fileinfo['extension']) == "gif" OR strtolower($fileinfo['extension']) == "png" 
				OR strtolower($fileinfo['extension']) == "jpeg" OR strtolower($fileinfo['extension']) == "swf" OR strtolower($fileinfo['extension']) == "flv")) {
				    $output .= "<option value='".$file."'";
				    if(($current == $siteurl.'/wp-content/banners/'.$file) OR ($current == $siteurl."/%folder%".$file)) { $output .= "selected"; }
				    $output .= ">".$file."</option>";
				}
			}
		} else {
	    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('No files found', 'adrotate')."</option>";
		}
	} else {
    	$output .= "<option disabled>&nbsp;&nbsp;&nbsp;".__('Folder not found or not accessible', 'adrotate')."</option>";
	}
	
	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_unlink

 Purpose:   Delete a file from the banners folder
 Receive:   $file
 Return:    boolean
 Since:		3.10
-------------------------------------------------------------*/
function adrotate_unlink($file) {
	global $adrotate_config;

	$path = adrotate_home_path().'/'.$adrotate_config['banner_folder'].$file;
	if(file_exists($path) AND unlink($path)) {
		return true;
	} else {
		return false;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_return

 Purpose:   Internal redirects
 Receive:   $page, $status
 Return:    -none-
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_return($page, $status, $args = null) {

	if(strlen($page) > 0 AND ($status > 0 AND $status < 1000)) {
		$defaults = array(
			'status' => $status
		);
		$arguments = wp_parse_args($args, $defaults);
		$redirect = 'admin.php?page=' . $page . '&'.http_build_query($arguments);
	} else {
		$redirect = 'admin.php?page=adrotate';
	}

	wp_redirect($redirect);
}

/*-------------------------------------------------------------
 Name:      adrotate_status

 Purpose:   Internal redirects
 Receive:   $status
 Return:    -none-
 Since:		3.8.5
-------------------------------------------------------------*/
function adrotate_status($status, $args = null) {

	$defaults = array(
		'ad' => '',
		'group' => '',
		'file' => ''
	);
	$arguments = wp_parse_args($args, $defaults);

	switch($status) {
		// Management messages
		case '200' :
			echo '<div id="message" class="updated"><p>'. __('Ad saved', 'adrotate') .'</p></div>';
		break;

		case '201' :
			echo '<div id="message" class="updated"><p>'. __('Group saved', 'adrotate') .'</p></div>';
		break;

		case '202' :
			echo '<div id="message" class="updated"><p>'. __('Banner image saved', 'adrotate') .'</p></div>';
		break;

		case '203' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deleted', 'adrotate') .'</p></div>';
		break;

		case '204' :
			echo '<div id="message" class="updated"><p>'. __('Group deleted', 'adrotate') .'</p></div>';
		break;

		case '206' :
			echo '<div id="message" class="updated"><p>'. __('Banner image deleted', 'adrotate') .'</p></div>';
		break;

		case '207' :
			echo '<div id="message" class="updated"><p>'. __('Something went wrong deleting the file', 'adrotate') .'</p></div>';
		break;

		case '208' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) statistics reset', 'adrotate') .'</p></div>';
		break;

		case '209' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) renewed', 'adrotate') .'</p></div>';
		break;

		case '210' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) deactivated', 'adrotate') .'</p></div>';
		break;

		case '211' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) activated', 'adrotate') .'</p></div>';
		break;

		case '212' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate') .'</p></div>';
		break;

		case '213' :
			echo '<div id="message" class="updated"><p>'. __('Group including it\'s Ads deleted', 'adrotate') .'</p></div>';
		break;

		case '214' :
			echo '<div id="message" class="updated"><p>'. __('Weight changed', 'adrotate') .'</p></div>';
		break;

		case '215' :
			echo '<div id="message" class="updated"><p>'. __('Export created', 'adrotate') .'. <a href="' . WP_CONTENT_URL . '/reports/'.$arguments['file'].'">Download</a>.</p></div>';
		break;

		case '216' :
			echo '<div id="message" class="updated"><p>'. __('Ads imported', 'adrotate') .'</div>';
		break;

		case '217' :
			echo '<div id="message" class="updated"><p>'. __('Schedule saved', 'adrotate') .'</div>';
		break;

		case '218' :
			echo '<div id="message" class="updated"><p>'. __('Schedule(s) deleted', 'adrotate') .'</div>';
		break;

		// Advertiser messages
		case '300' :
			echo '<div id="message" class="updated"><p>'. __('Your message has been sent. Someone will be in touch shortly.', 'adrotate') .'</p></div>';
		break;

		case '301' :
			echo '<div id="message" class="updated"><p>'. __('Advert submitted for review', 'adrotate') .'</p></div>';
		break;

		case '302' :
			echo '<div id="message" class="updated"><p>'. __('Advert updated and awaiting review', 'adrotate') .'</p></div>';
		break;

		case '303' :
			echo '<div id="message" class="updated"><p>'. __('Email(s) with reports successfully sent', 'adrotate') .'</p></div>';
		break;

		case '304' :
			echo '<div id="message" class="updated"><p>'. __('Ad approved', 'adrotate') .'</p></div>';
		break;

		case '305' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) rejected', 'adrotate') .'</p></div>';
		break;

		case '306' :
			echo '<div id="message" class="updated"><p>'. __('Ad(s) queued', 'adrotate') .'</p></div>';
		break;

		// Settings
		case '400' :
			echo '<div id="message" class="updated"><p>'. __('Settings saved', 'adrotate') .'</p></div>';
		break;

		case '401' :
			echo '<div id="message" class="updated"><p>'. __('AdRotate client role added', 'adrotate') .'</p></div>';
		break;

		case '402' :
			echo '<div id="message" class="updated"><p>'. __('AdRotate client role removed', 'adrotate') .'</p></div>';
		break;

		case '403' :
			echo '<div id="message" class="updated"><p>'. __('Database optimized', 'adrotate') .'</p></div>';
		break;

		case '404' :
			echo '<div id="message" class="updated"><p>'. __('Database repaired', 'adrotate') .'</p></div>';
		break;

		case '405' :
			echo '<div id="message" class="updated"><p>'. __('Ads evaluated and statuses have been corrected where required', 'adrotate') .'</p></div>';
		break;

		case '406' :
			echo '<div id="message" class="updated"><p>'. __('Empty database records removed', 'adrotate') .'</p></div>';
		break;

		case '407' :
			echo '<div id="message" class="updated"><p>'. __('Test notification sent', 'adrotate') .'</p></div>';
		break;

		case '408' :
			echo '<div id="message" class="updated"><p>'. __('Test mailing sent', 'adrotate') .'</p></div>';
		break;

		// (all) Error messages
		case '500' :
			echo '<div id="message" class="error"><p>'. __('Action prohibited', 'adrotate') .'</p></div>';
		break;

		case '501' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Review the colored ad.', 'adrotate') .'</p></div>';
		break;

		case '502' :
			echo '<div id="message" class="error"><p>'. __('The ad was saved but has an issue which might prevent it from working properly. Please contact staff.', 'adrotate') .'</p></div>';
		break;

		case '503' :
			echo '<div id="message" class="error"><p>'. __('No data found in selected time period', 'adrotate') .'</p></div>';
		break;

		case '504' :
			echo '<div id="message" class="error"><p>'. __('Database can only be optimized or cleaned once every hour', 'adrotate') .'</p></div>';
		break;

		case '505' :
			echo '<div id="message" class="error"><p>'. __('Form can not be (partially) empty!', 'adrotate') .'</p></div>';
		break;

		case '506' :
			echo '<div id="message" class="updated"><p>'. __('No file uploaded.', 'adrotate') .'</p></div>';
		break;

		case '507' :
			echo '<div id="message" class="updated"><p>'. __('The file could not be read.', 'adrotate') .'</p></div>';
		break;

		case '508' :
			echo '<div id="message" class="updated"><p>'. __('Wrong file type.', 'adrotate') .'</p></div>';
		break;

		case '509' :
			echo '<div id="message" class="updated"><p>'. __('No ads found.', 'adrotate') .'</p></div>';
		break;

		case '510' :
			echo '<div id="message" class="updated"><p>'. __('Wrong file type. No file uploaded.', 'adrotate') .'</p></div>';
		break;

		case '511' :
			echo '<div id="message" class="updated"><p>'. __('File is too large.', 'adrotate') .'</p></div>';
		break;


		// Licensing
		case '600' :
			echo '<div id="message" class="error"><p>'. __('Invalid request', 'adrotate') .'</p></div>';
		break;

		case '601' :
			echo '<div id="message" class="error"><p>'. __('No license key or email provided', 'adrotate') .'</p></div>';
		break;

		case '602' :
			echo '<div id="message" class="error"><p>'. __('No valid response from license server. Contact support.', 'adrotate') .'<br />Response code: '.$arguments['error'].'</p></div>';
		break;

		case '603' :
			echo '<div id="message" class="error"><p>'. __('The email provided is invalid. If you think this is not true please contact support.', 'adrotate') .'</p></div>';
		break;

		case '604' :
			echo '<div id="message" class="error"><p>'. __('Invalid license key. If you think this is not true please contact support.', 'adrotate') .'</p></div>';
		break;

		case '605' :
			echo '<div id="message" class="error"><p>'. __('The purchase matching this product is not complete. Contact support.', 'adrotate') .'</p></div>';
		break;

		case '606' :
			echo '<div id="message" class="error"><p>'. __('No remaining activations for this license. If you think this is not true please contact support.', 'adrotate') .'</p></div>';
		break;

		case '607' :
			echo '<div id="message" class="error"><p>'. __('Could not (de)activate key. Contact support.', 'adrotate') .'</p></div>';
		break;

		case '608' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your license is now active', 'adrotate') .'</p></div>';
		break;

		case '609' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your license is now de-activated', 'adrotate') .'</p></div>';
		break;

		case '610' :
			echo '<div id="message" class="updated"><p>'. __('Thank you. Your licenses have been reset', 'adrotate') .'</p></div>';
		break;

		case '611' :
			echo '<div id="message" class="updated"><p>'. __('This license can not be activated for networks. Please purchase a Developer or Network license.', 'adrotate') .'</p></div>';
		break;

		// Support
		case '701' :
			echo '<div id="message" class="updated"><p><strong>Support request sent.</strong><br />View and respond to your tickets on the <a href="https://ajdg.solutions/support/view.php" target="_blank">AJdG Solutions Support page</a> using your submitted email address and ticket number <strong>'.$arguments['ticket'].'</strong>.<br />Due to spam concerns and auto-replies you will *not* receive a confirmation email from the ticket system!</p></div>';
		break;
		
		case '702' :
			echo '<div id="message" class="updated"><p>'. __('Support request could not be sent! Please try again - If the issue persists write to adrotate-support@ajdg.net!', 'adrotate') .'</p></div>';
		break;
		
		default :
			echo '<div id="message" class="updated"><p>'. __('Unexpected error', 'adrotate') .'</p></div>';			
		break;
	}
	
	unset($arguments, $args);
}
?>