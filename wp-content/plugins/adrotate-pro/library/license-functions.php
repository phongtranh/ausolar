<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2014 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      AJdG Solutions Licensing Library
 Version:	1.2
-------------------------------------------------------------*/

function adrotate_licensed_update() {
	add_filter('pre_set_site_transient_update_plugins', 'adrotate_update_check');
	add_filter('plugins_api', 'adrotate_get_updatedetails', 10, 3);
}

/* Check for new version */
function adrotate_update_check($checked_data) {
	global $ajdg_solutions_domain;
	
	if(empty($checked_data->checked)) {
		return $checked_data;	
	}

   	$license = get_option('adrotate_activate');
	if($license['status'] == 1) {
		$response = '';
		$license_database = substr($license['key'], 0, 3);
		$request_args = array(
			'slug' => 'adrotate', // Plugin
			'version' => $checked_data->checked[ADROTATE_FOLDER .'/adrotate.php'], // Plugin version
			'instance' => $license['instance'], // Instance ID
			'platform' => get_option('siteurl'), // Who's asking
			'database' => $license_database // Which database to use (101 || 102)
		);
		$raw_response = wp_remote_post($ajdg_solutions_domain.'api/updates/3/', adrotate_license_prepare_request('basic_check', adrotate_license_array_to_object($request_args)));
		
		if(!is_wp_error($raw_response) || wp_remote_retrieve_response_code($raw_response) === 200) {
			$response = unserialize($raw_response['body']);	
		}
		
		if(is_object($response) && !empty($response)) { // Feed the update data into WP updater
			$checked_data->response[ADROTATE_FOLDER .'/adrotate.php'] = $response;
		}
	}

	return $checked_data;
}

/* Get update information */
function adrotate_get_updatedetails($def, $action, $args) {
	global $ajdg_solutions_domain;
	
	if(!isset($args->slug) || $args->slug != 'adrotate') {
		return $def;	
	}

   	$license = get_option('adrotate_activate');
	$license_database = substr($license['key'], 0, 3);
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$args->version = $plugin_info->checked[ADROTATE_FOLDER .'/adrotate.php']; // Plugin version
	$args->instance = $license['instance']; // Instance ID
	$args->email = $license['email']; // License details
	$args->platform = get_option('siteurl'); // Who's asking
	$args->database = $license_database; // Which database to use (101 || 102)

	$request = wp_remote_post($ajdg_solutions_domain.'api/updates/3/', adrotate_license_prepare_request($action, $args));
	
	if(is_wp_error($request)) {
		$response = new WP_Error('plugins_api_failed', 'An Unexpected HTTP Error occurred during the API request. <a href="#" onclick="document.location.reload(); return false;">Try again</a>');
	} else {
		$response = unserialize($request['body']);
		if($response === false) {
			$response = new WP_Error('plugins_api_failed', 'An unknown error occurred');		
		}
	}
	
	return $response;
}

/* Set headers */
function adrotate_license_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
		),
		'user-agent' => 'AdRotate Pro/' . $args->version . '; ' . get_option('siteurl'),
		'sslverify' => false,
		'content-type' => 'application/x-www-form-urlencoded',
	);	
}

/* Send support ticket */
function adrotate_support_api_request() {
	if(wp_verify_nonce($_POST['adrotate_nonce_support'],'adrotate_nonce_support_request')) {
		$author = esc_attr($_POST['adrotate_updater_username']);
		$useremail = esc_attr($_POST['adrotate_updater_email']);
		$subject = strip_tags(stripslashes(trim($_POST['adrotate_updater_subject'], "\t\n ")));
		$text = strip_tags(stripslashes(trim($_POST['adrotate_updater_message'], "\t\n ")));
		if(adrotate_is_networked()) {
			$a = get_site_option('adrotate_activate');
			$networked = 'Yes';
		} else {
			$a = get_option('adrotate_activate');
			$networked = 'No';
		}

		if(strlen($text) < 1 OR strlen($subject) < 1 OR strlen($author) < 1 OR strlen($useremail) < 1) {
			adrotate_return('adrotate', 505);
		} else {
			$data = array(
			    'name' => $author,
			    'email' => $useremail,
			    'subject' => $subject,
			    'message' => $text,
			    'website' => get_bloginfo('wpurl'),
			    'wpversion' => get_bloginfo('version'),
			    'wpmultisite' => (is_multisite()) ? 'Yes' : 'No',
			    'pluginnetwork' => $networked,
			    'wplanguage' => get_bloginfo('language'),
			    'wpcharset' => get_bloginfo('charset'),
			    'pluginversion' => ADROTATE_DISPLAY,
			    'topicId' => 2,
			    'source' => 'API',
			    'ip' => '0.0.0.0',
			    'attachments' => array()
			);

			$request = wp_remote_post('https://ajdg.solutions/support/api/http.php/tickets.json', array(
				'timeout' => 15,
				'sslverify' => false,
				'user-agent' => 'AdRotate/'.ADROTATE_DISPLAY.' SupportAPI Client',
				'headers' => array('Expect:' => '', 'X-API-Key' => 'E084B50052E9CBA7963D55C1AC90032A'),
				'body' => json_encode($data),
				'cookies' => array()
			    )
			);

			if(is_wp_error($request) || wp_remote_retrieve_response_code($request) != 201) {
				$response = new WP_Error('plugins_api_failed', 'An Unexpected HTTP Error occurred during the Support API request.</p>');
			} else {
				adrotate_return('adrotate', 701, array('ticket' => $request['body']));
				exit;
			}
			adrotate_return('adrotate', 702, $response->errors);
			exit;
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

/* Concert array to object */
function adrotate_license_array_to_object($array = array()) {
    if (empty($array) || !is_array($array))
		return false;
		
	$data = new stdClass;
    foreach ($array as $akey => $aval)
            $data->{$akey} = $aval;
	return $data;
}
?>