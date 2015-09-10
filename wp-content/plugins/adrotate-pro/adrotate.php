<?php
/*
Plugin Name: AdRotate Professional
Plugin URI: https://ajdg.solutions/products/adrotate-for-wordpress/
Author: Arnan de Gans of AJdG Solutions
Author URI: http://ajdg.solutions/
Description: Used on thousands of websites! AdRotate Pro is the popular choice for monetizing your website with adverts while keeping things simple.
Version: 3.12.5
License: Limited License (See the readme.html in your account on https://ajdg.solutions/)
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2015 AJdG Solutions (Arnan de Gans). All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*--- AdRotate values ---------------------------------------*/
define("ADROTATE_DISPLAY", '3.12.5 Professional');
define("ADROTATE_VERSION", 378);
define("ADROTATE_DB_VERSION", 50);
define("ADROTATE_FOLDER", 'adrotate-pro');
/*-----------------------------------------------------------*/

/*--- Load Files --------------------------------------------*/
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-setup.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-manage-publisher.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-manage-advertiser.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-functions.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-advertiser-functions.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-statistics.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-import.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-export.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-output.php');
require_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/adrotate-widget.php');
/*-----------------------------------------------------------*/

/*--- Check and Load config ---------------------------------*/
load_plugin_textdomain('adrotate', false, basename( dirname( __FILE__ ) ) . '/language' );
$adrotate_config = get_option('adrotate_config');
$adrotate_crawlers = get_option('adrotate_crawlers');
$adrotate_roles = get_option('adrotate_roles');
$adrotate_version = get_option("adrotate_version");
$adrotate_db_version = get_option("adrotate_db_version");
$adrotate_debug = get_option("adrotate_debug");
$adrotate_advert_status	= get_option("adrotate_advert_status");
$ajdg_solutions_domain = 'https://ajdg.solutions/';
/*-----------------------------------------------------------*/

/*--- Core --------------------------------------------------*/
register_activation_hook(__FILE__, 'adrotate_activate');
register_deactivation_hook(__FILE__, 'adrotate_deactivate');
register_uninstall_hook(__FILE__, 'adrotate_uninstall');
add_action('adrotate_notification', 'adrotate_notifications');
add_action('adrotate_clean_trackerdata', 'adrotate_clean_trackerdata');
add_action('adrotate_evaluate_ads', 'adrotate_evaluate_ads');
add_action('widgets_init', create_function('', 'return register_widget("adrotate_widgets");'));
/*-----------------------------------------------------------*/

/*--- Front end ---------------------------------------------*/
if(!is_admin()) {
	if($adrotate_config['adminbar'] == 'Y') {
		add_action('admin_bar_menu', 'adrotate_adminmenu', 100);
	}
	if(get_option('adrotate_geo_required') > 0) {
		add_action('init', 'adrotate_geolocation');
	}
	add_shortcode('adrotate', 'adrotate_shortcode');
	add_shortcode('adrotate_advertiser_dashboard', 'adrotate_front_end');
	add_action('wp_enqueue_scripts', 'adrotate_custom_scripts');
	add_action('wp_head', 'adrotate_custom_css');
	add_filter('the_content', 'adrotate_inject_pages');
	add_filter('the_content', 'adrotate_inject_posts');
}

// AJAX Callbacks
if($adrotate_config['enable_stats'] == 'Y'){
	add_action('wp_ajax_adrotate_impression', 'adrotate_impression_callback');
	add_action('wp_ajax_nopriv_adrotate_impression', 'adrotate_impression_callback');
	add_action('wp_ajax_adrotate_click', 'adrotate_click_callback');
	add_action('wp_ajax_nopriv_adrotate_click', 'adrotate_click_callback');
}
/*-----------------------------------------------------------*/

if(is_admin()) {
	/*--- Back end ----------------------------------------------*/
	adrotate_check_config();
	add_action('admin_menu', 'adrotate_dashboard');
	add_action("admin_enqueue_scripts", 'adrotate_dashboard_scripts');
	add_action("admin_print_styles", 'adrotate_dashboard_styles');
	add_action('admin_notices', 'adrotate_notifications_dashboard');
	if(adrotate_is_networked()) {
		add_action('network_admin_menu', 'adrotate_network_dashboard');
	}
	/*--- Update API --------------------------------------------*/
	include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/library/license-functions.php');
	include_once(WP_CONTENT_DIR.'/plugins/'.ADROTATE_FOLDER.'/library/license-api.php');
	add_action('admin_init', 'adrotate_licensed_update');

	if(isset($_POST['adrotate_license_support_submit'])) add_action('init', 'adrotate_support_api_request');
	if(isset($_POST['adrotate_license_activate'])) add_action('init', 'adrotate_license_activate');
	if(isset($_POST['adrotate_license_deactivate'])) add_action('init', 'adrotate_license_deactivate');
	if(isset($_POST['adrotate_license_reset'])) add_action('init', 'adrotate_license_reset');
	/*--- Internal redirects ------------------------------------*/
	if(isset($_POST['adrotate_ad_submit'])) add_action('init', 'adrotate_insert_input');
	if(isset($_POST['adrotate_group_submit'])) add_action('init', 'adrotate_insert_group');
	if(isset($_POST['adrotate_schedule_submit'])) add_action('init', 'adrotate_insert_schedule');
	if(isset($_POST['adrotate_media_submit'])) add_action('init', 'adrotate_insert_media');
	if(isset($_POST['adrotate_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_disabled_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_error_action_submit'])) add_action('init', 'adrotate_request_action');
	if(isset($_POST['adrotate_notification_test_submit'])) add_action('init', 'adrotate_notifications');
	if(isset($_POST['adrotate_options_submit'])) add_action('init', 'adrotate_options_submit');
	if(isset($_POST['adrotate_request_submit'])) add_action('init', 'adrotate_mail_message');
	if(isset($_POST['adrotate_role_add_submit'])) add_action('init', 'adrotate_prepare_roles');
	if(isset($_POST['adrotate_role_remove_submit'])) add_action('init', 'adrotate_prepare_roles');
	if(isset($_POST['adrotate_db_optimize_submit'])) add_action('init', 'adrotate_optimize_database');
	if(isset($_POST['adrotate_db_cleanup_submit'])) add_action('init', 'adrotate_cleanup_database');
	if(isset($_POST['adrotate_evaluate_submit'])) add_action('init', 'adrotate_prepare_evaluate_ads');
	if(isset($_POST['adrotate_import'])) add_action('init', 'adrotate_import_ads');
	if(isset($_POST['adrotate_export_submit'])) add_action('init', 'adrotate_export_stats');
	/*--- Advertiser redirects ----------------------------------*/
	if(isset($_POST['adrotate_advertiser_ad_submit'])) add_action('init', 'adrotate_advertiser_insert_input');
	/*-----------------------------------------------------------*/
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard

 Purpose:   Add pages to admin menus
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_dashboard() {
	global $adrotate_config;

	$adrotate_page = $adrotate_adverts = $adrotate_groups = $adrotate_schedules = $adrotate_media = $adrotate_queue = $adrotate_settings =  '';
	$adrotate_page = add_menu_page('AdRotate Pro', 'AdRotate Pro', 'adrotate_ad_manage', 'adrotate', 'adrotate_info', plugins_url('/images/icon.png', __FILE__), '25.8');
	$adrotate_page = add_submenu_page('adrotate', 'AdRotate Pro > '.__('General Info', 'adrotate'), __('General Info', 'adrotate'), 'adrotate_ad_manage', 'adrotate', 'adrotate_info');
	$adrotate_adverts = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Manage Ads', 'adrotate'), __('Manage Ads', 'adrotate'), 'adrotate_ad_manage', 'adrotate-ads', 'adrotate_manage');
	$adrotate_groups = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Manage Groups', 'adrotate'), __('Manage Groups', 'adrotate'), 'adrotate_group_manage', 'adrotate-groups', 'adrotate_manage_group');
	$adrotate_schedules = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Manage Schedules', 'adrotate'), __('Manage Schedules', 'adrotate'), 'adrotate_schedule_manage', 'adrotate-schedules', 'adrotate_manage_schedules');
	$adrotate_media = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Manage Media', 'adrotate'), __('Manage Media', 'adrotate'), 'adrotate_ad_manage', 'adrotate-media', 'adrotate_manage_media');
	if($adrotate_config['enable_advertisers'] == 'Y' AND $adrotate_config['enable_editing'] == 'Y') {
		$adrotate_queue = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Moderate', 'adrotate'), __('Moderate Adverts', 'adrotate'), 'adrotate_moderate', 'adrotate-moderate', 'adrotate_moderate');
	}
	$adrotate_settings = add_submenu_page('adrotate', 'AdRotate Pro > '.__('Settings', 'adrotate'), __('Settings', 'adrotate'), 'manage_options', 'adrotate-settings', 'adrotate_options');
	
	if($adrotate_config['enable_advertisers'] == 'Y') {
		add_menu_page(__('Advertiser', 'adrotate'), __('Advertiser', 'adrotate'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser', plugins_url('/images/icon.png', __FILE__), '25.9');
		add_submenu_page('adrotate-advertiser', 'AdRotate Pro > '.__('Advertiser', 'adrotate'), __('Advertiser', 'adrotate'), 'adrotate_advertiser', 'adrotate-advertiser', 'adrotate_advertiser');
	}
	
	// Add help tabs
	add_action('load-'.$adrotate_page, 'adrotate_help_info');
	add_action('load-'.$adrotate_adverts, 'adrotate_help_info');
	add_action('load-'.$adrotate_groups, 'adrotate_help_info');
	add_action('load-'.$adrotate_schedules, 'adrotate_help_info');
	add_action('load-'.$adrotate_media, 'adrotate_help_info');
	add_action('load-'.$adrotate_queue, 'adrotate_help_info');
	add_action('load-'.$adrotate_settings, 'adrotate_help_info');
}

/*-------------------------------------------------------------
 Name:      adrotate_adminmenu

 Purpose:   Add things to the admin bar
 Receive:   -None-
 Return:    -None-
 Since:		3.8
-------------------------------------------------------------*/
function adrotate_adminmenu() {
    global $wp_admin_bar, $adrotate_config;

	if(!is_super_admin() OR !is_admin_bar_showing())
		return;

    $wp_admin_bar->add_node(array( 'id' => 'adrotate', 'title' => __('AdRotate', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-ads-new','parent' => 'adrotate', 'title' => __('Add new Advert', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-ads&view=addnew')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-ads','parent' => 'adrotate', 'title' => __('Manage Adverts', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-ads')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-groups','parent' => 'adrotate', 'title' => __('Manage Groups', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-groups')));
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-schedules','parent' => 'adrotate', 'title' => __('Manage Schedules', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-schedules')));
	if($adrotate_config['enable_advertisers'] == 'Y' AND $adrotate_config['enable_editing'] == 'Y') {
   		$wp_admin_bar->add_node(array( 'id' => 'adrotate-moderate','parent' => 'adrotate', 'title' => __('Moderate Adverts', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-moderate')));
	}
    $wp_admin_bar->add_node(array( 'id' => 'adrotate-report','parent' => 'adrotate', 'title' => __('Full Report', 'adrotate'), 'href' => admin_url('/admin.php?page=adrotate-ads&view=fullreport')));
}

/*-------------------------------------------------------------
 Name:      adrotate_network_dashboard

 Purpose:   Add pages to admin menus if AdRotate is network activated
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_network_dashboard() {
	add_menu_page('AdRotate', 'AdRotate', 'manage_network', 'adrotate', 'adrotate_network_license');
	add_submenu_page('adrotate', 'AdRotate > '.__('License', 'adrotate'), 'AdRotate '.__('License', 'adrotate'), 'manage_network', 'adrotate', 'adrotate_network_license');
}

/*-------------------------------------------------------------
 Name:      adrotate_info

 Purpose:   Admin general info page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_info() {
	global $wpdb, $current_user, $adrotate_advert_status;

	if(adrotate_is_networked()) {
		$a = get_site_option('adrotate_activate');
	} else {
		$a = get_option('adrotate_activate');
	}
	
	$status = $ticketid = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['ticket'])) $ticketid = esc_attr($_GET['ticket']);

	$user = get_userdata($current_user->ID); 
	if(strlen($user->first_name) < 1) $firstname = $user->user_login;
		else $firstname = $user->first_name;
	if(strlen($user->last_name) < 1) $lastname = ''; 
		else $lastname = ' '.$user->last_name;
	?>

	<div class="wrap">
		<h2><?php _e('AdRotate Info', 'adrotate'); ?></h2>

		<br class="clear" />

		<?php include("dashboard/adrotate-info.php"); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage

 Purpose:   Admin management page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage() {
	global $wpdb, $current_user, $userdata, $blog_id, $adrotate_config, $adrotate_debug;

	$status = $file = $view = $ad_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
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
		<h2><?php _e('Ad Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status, array('file' => $file)); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_linkmeta';")) { ?>

			<?php
			$allbanners = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `weight`, `budget`, `crate`, `irate` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'active' OR `type` = 'error' OR `type` = 'expired' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' ORDER BY `sortorder` ASC, `id` ASC;");
			
			$activebanners = $errorbanners = $disabledbanners = false;
			foreach($allbanners as $singlebanner) {
				$advertiser = '';
				$starttime = $stoptime = 0;
				$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND  `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				if($adrotate_config['enable_advertisers'] == 'Y') {
					$advertiser = $wpdb->get_var("SELECT `user_login` FROM `{$wpdb->prefix}adrotate_linkmeta`, `$wpdb->users` WHERE `$wpdb->users`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`user` AND `ad` = '".$singlebanner->id."' AND `group` = '0' AND `schedule` = '0' LIMIT 1;");
				}

				$type = $singlebanner->type;
				if($type == 'active' AND $stoptime <= $now) $type = 'expired'; 
				if($type == 'active' AND $stoptime <= $in2days) $type = '2days';
				if($type == 'active' AND $stoptime <= $in7days) $type = '7days';
				if(($singlebanner->crate > 0 OR $singlebanner->irate > 0) AND $singlebanner->budget < 1) $type = 'expired';

				if($type == 'active' OR $type == '7days') {
					$activebanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($type == 'error' OR $type == 'expired' OR $type == '2days') {
					$errorbanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($type == 'disabled') {
					$disabledbanners[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'advertiser' => $advertiser,
						'type' => $type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
			}
			?>
			
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=import');?>"><?php _e('Import', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-ads&view=fullreport');?>"><?php _e('Full Report', 'adrotate'); ?></a>
				</div>
			</div>

	    	<?php 
	    	if ($view == "" OR $view == "manage") {
				// Show list of errorous ads if any			
				if ($errorbanners) {
					include("dashboard/publisher/adrotate-ads-main-error.php");
				}
		
				include("dashboard/publisher/adrotate-ads-main.php");
	
				// Show disabled ads, if any
				if ($disabledbanners) {
					include("dashboard/publisher/adrotate-ads-main-disabled.php");
				}
		   	} else if($view == "addnew" OR $view == "edit") { 
				include("dashboard/publisher/adrotate-ads-edit.php");
			} else if($view == "report") {
				include("dashboard/publisher/adrotate-ads-report.php");
			} else if($view == "import") {			
				include("dashboard/publisher/adrotate-ads-import.php");
			} else if($view == "fullreport") {
				$adrotate_stats = adrotate_prepare_fullreport();
				
				if($adrotate_stats['tracker'] > 0 AND $adrotate_stats['clicks'] > 0) {
					$clicks = round($adrotate_stats['clicks'] / $adrotate_stats['tracker'], 2);
				} else { 
					$clicks = 0; 
				}

				$ctr = adrotate_ctr($adrotate_stats['clicks'], $adrotate_stats['impressions']);						

				include("dashboard/publisher/adrotate-fullreport.php");
			}
		} else {
			echo adrotate_error('db_error');
		}
		?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_group

 Purpose:   Manage groups
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_group() {
	global $wpdb, $adrotate_config, $adrotate_debug;

	$status = $view = $group_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['group'])) $group_edit_id = esc_attr($_GET['group']);

	if(isset($_GET['month']) AND isset($_GET['year'])) {
		$month = esc_attr($_GET['month']);
		$year = esc_attr($_GET['year']);
	} else {
		$month = date("m");
		$year = date("Y");
	}
	$monthstart = mktime(0, 0, 0, $month, 1, $year);
	$monthend = mktime(0, 0, 0, $month+1, 0, $year);	

	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	?>
	<div class="wrap">
		<h2><?php _e('Group Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_linkmeta';")) { ?>
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a>
					<?php if($group_edit_id) { ?>
					| <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-groups&view=report&group='.$group_edit_id);?>"><?php _e('Report', 'adrotate'); ?></a>
					<?php } ?>
				</div>
			</div>

	    	<?php if ($view == "" OR $view == "manage") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-main.php");
				?>

		   	<?php } else if($view == "addnew" OR $view == "edit") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-edit.php");
				?>

		   	<?php } else if($view == "report") { ?>

				<?php
				include("dashboard/publisher/adrotate-groups-report.php");
				?>

		   	<?php } ?>
		<?php } else { ?>
			<?php echo adrotate_error('db_error'); ?>
		<?php }	?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_schedules

 Purpose:   Manage schedules for ads
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_schedules() {
	global $wpdb, $adrotate_config, $adrotate_debug;

	$status = $view = $schedule_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['schedule'])) $schedule_edit_id = esc_attr($_GET['schedule']);

	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;
	?>
	<div class="wrap">
		<h2><?php _e('Schedule Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php 
		if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_linkmeta';")) {
		?>
			<div class="tablenav">
				<div class="alignleft actions">
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a> | 
					<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-schedules&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a>
				</div>
			</div>

	    	<?php if ($view == "" OR $view == "manage") { ?>

				<?php
				include("dashboard/publisher/adrotate-schedules-main.php");
				?>

		   	<?php } else if($view == "addnew" OR $view == "edit") { ?>
		   	
				<?php
				include("dashboard/publisher/adrotate-schedules-edit.php");
				?>

		   	<?php } ?>

		<?php } else { ?>
			<?php echo adrotate_error('db_error'); ?>
		<?php }	?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_manage_images

 Purpose:   Manage banner images for ads
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_manage_media() {
	global $wpdb, $adrotate_config;

	$status = $file = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['file'])) $file = esc_attr($_GET['file']);

	if(strlen($file) > 0 AND wp_verify_nonce($_REQUEST['_wpnonce'], 'adrotate_delete_media_'.$file)) {
		if(adrotate_unlink($file)) {
			$status = 206;
		} else {
			$status = 207;
		}
	}
	?>

	<div class="wrap">
		<h2><?php _e('Media Management', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<p><?php _e('Upload images to the AdRotate Pro banners folder from here. This is especially useful if you use responsive adverts with multiple images.', 'adrotate'); ?></p>

		<?php
		include("dashboard/publisher/adrotate-media-main.php");
		?>

		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_moderate

 Purpose:   Moderation queue
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_moderate() {
	global $wpdb, $current_user, $userdata, $adrotate_config, $adrotate_debug;

	$status = $view = $ad_edit_id = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['view'])) $view = esc_attr($_GET['view']);
	if(isset($_GET['ad'])) $ad_edit_id = esc_attr($_GET['ad']);
	$now 			= adrotate_now();
	$today 			= adrotate_date_start('day');
	$in2days 		= $now + 172800;
	$in7days 		= $now + 604800;
	$in84days 		= $now + 7257600;
	?>
	<div class="wrap">
		<h2><?php _e('Moderation queue', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>

		<?php if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_groups';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_schedule';") AND $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}adrotate_linkmeta';")) { ?>

			<?php
			$allbanners = $wpdb->get_results("SELECT `id`, `title`, `type`, `tracker`, `weight` FROM `{$wpdb->prefix}adrotate` WHERE `type` = 'queue' OR `type` = 'reject' ORDER BY `id` ASC;");
			
			$queued = $rejected = false;
			foreach($allbanners as $singlebanner) {
				
				$starttime = $stoptime = 0;
				$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
				$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$singlebanner->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
				
				if($singlebanner->type == 'queue') {
					$queued[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'type' => $singlebanner->type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
				
				if($singlebanner->type == 'reject') {
					$rejected[$singlebanner->id] = array(
						'id' => $singlebanner->id,
						'title' => $singlebanner->title,
						'type' => $singlebanner->type,
						'tracker' => $singlebanner->tracker,
						'weight' => $singlebanner->weight,
						'firstactive' => $starttime,
						'lastactive' => $stoptime
					);
				}
			}
			?>

	    	<?php
	    	if ($view == "" OR $view == "manage") {
				// Show list of queued ads			
				include("dashboard/publisher/adrotate-moderation-queue.php");
	
				// Show rejected ads, if any
				if($rejected) {
					include("dashboard/publisher/adrotate-moderation-rejected.php");
				}
			} else if($view == "message") {
				$wpnonceaction = 'adrotate_moderate_'.$request_id;
				if(wp_verify_nonce($_REQUEST['_wpnonce'], $wpnonceaction)) {
					include("dashboard/publisher/adrotate-moderation-message.php");
				} else {
					adrotate_nonce_error();
					exit;
				}
			}
		} else {
			echo adrotate_error('db_error');
		}
		?>
		<br class="clear" />

		<?php adrotate_credits(); ?>

		<br class="clear" />
	</div>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_advertiser

 Purpose:   Advertiser page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_advertiser() {
	global $wpdb, $current_user, $adrotate_config, $adrotate_debug;
		
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

		<?php if($status > 0) adrotate_status($status); ?>

		<div class="tablenav">
			<div class="alignleft actions">
				<a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=manage');?>"><?php _e('Manage', 'adrotate'); ?></a>
				<?php if($adrotate_config['enable_editing'] == 'Y') { ?>
				 | <a class="row-title" href="<?php echo admin_url('/admin.php?page=adrotate-advertiser&view=addnew');?>"><?php _e('Add New', 'adrotate'); ?></a> 
				<?php  } ?>
			</div>
		</div>

		<?php 
		$wpnonceaction = 'adrotate_email_advertiser_'.$request_id;
		if($view == "" OR $view == "manage") {
			
			$ads = $wpdb->get_results($wpdb->prepare("SELECT `ad` FROM `{$wpdb->prefix}adrotate_linkmeta` WHERE `group` = 0 AND `user` = %d ORDER BY `ad` ASC;", $current_user->ID));

			if($ads) {
				$activebanners = $queuebanners = $disabledbanners = false;
				foreach($ads as $ad) {
					$banner = $wpdb->get_row("SELECT `id`, `title`, `type` FROM `{$wpdb->prefix}adrotate` WHERE (`type` = 'active' OR `type` = '2days' OR `type` = '7days' OR `type` = 'disabled' OR `type` = 'error' OR `type` = 'a_error' OR `type` = 'expired' OR `type` = 'queue' OR `type` = 'reject') AND `id` = '".$ad->ad."';");

					// Skip if no ad
					if(!$banner) continue;
					
					$starttime = $stoptime = 0;
					$starttime = $wpdb->get_var("SELECT `starttime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `starttime` ASC LIMIT 1;");
					$stoptime = $wpdb->get_var("SELECT `stoptime` FROM `{$wpdb->prefix}adrotate_schedule`, `{$wpdb->prefix}adrotate_linkmeta` WHERE `ad` = '".$banner->id."' AND `schedule` = `{$wpdb->prefix}adrotate_schedule`.`id` ORDER BY `stoptime` DESC LIMIT 1;");
	
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
				
				// Show active ads, if any
				if($activebanners) {
					include("dashboard/advertiser/adrotate-main.php");
				}

				// Show disabled ads, if any
				if($disabledbanners) {
					include("dashboard/advertiser/adrotate-main-disabled.php");
				}

				// Show queued ads, if any
				if($queuebanners) {
					include("dashboard/advertiser/adrotate-main-queue.php");
				}

				// Gather data for summary report
				$summary = adrotate_prepare_advertiser_report($current_user->ID, $activebanners);
				include("dashboard/advertiser/adrotate-main-summary.php");

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

			include("dashboard/advertiser/adrotate-edit.php");

		} else if($view == "report") { 

			include("dashboard/advertiser/adrotate-report.php");

		} else if($view == "message") {

			if(wp_verify_nonce($_REQUEST['_wpnonce'], $wpnonceaction)) {
				include("dashboard/advertiser/adrotate-message.php");
			} else {
				adrotate_nonce_error();
				exit;
			}

		}
		?>
		<br class="clear" />

		<?php adrotate_user_notice(); ?>

		<br class="clear" />
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_options

 Purpose:   Admin options page
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_options() {
	global $wpdb, $wp_roles;

	$adrotate_config 			= get_option('adrotate_config');
	$adrotate_crawlers 			= get_option('adrotate_crawlers');
	$adrotate_roles				= get_option('adrotate_roles');
	$adrotate_debug				= get_option('adrotate_debug');
	$adrotate_version			= get_option('adrotate_version');
	$adrotate_db_version		= get_option('adrotate_db_version');
	$adrotate_hide_license		= get_option('adrotate_hide_license');
	$adrotate_advert_status		= get_option("adrotate_advert_status");
	$adrotate_notifications		= get_option("adrotate_notifications");
	$adrotate_is_networked		= adrotate_is_networked();
	$adrotate_geo_requests		= get_option("adrotate_geo_requests");
	$adrotate_geo 				= adrotate_get_cookie('geo');

	if($adrotate_is_networked) {
		$adrotate_activate = get_site_option('adrotate_activate');
	} else {
		$adrotate_activate = get_option('adrotate_activate');
	}

	$crawlers = $notification_mails = $advertiser_mails = '';
	if(is_array($adrotate_crawlers)) {
		$crawlers = implode(', ', $adrotate_crawlers);
	}
	if(is_array($adrotate_notifications['notification_email_publisher'])) {
		$notification_mails	= implode(', ', $adrotate_notifications['notification_email_publisher']);
	}
	if(is_array($adrotate_notifications['notification_email_advertiser'])) {
		$advertiser_mails = implode(', ', $adrotate_notifications['notification_email_advertiser']);
	}

	$status = $error = $corrected = $converted = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	if(isset($_GET['error'])) $error = esc_attr($_GET['error']);

	$converted = base64_decode($converted);
	$adevaluate = wp_next_scheduled('adrotate_evaluate_ads');
	$adschedule = wp_next_scheduled('adrotate_notification');
	$adtracker = wp_next_scheduled('adrotate_clean_trackerdata');
?>
	<div class="wrap">
	  	<h2><?php _e('AdRotate Settings', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status, array('error' => $error)); ?>
		
	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings">

			<?php wp_nonce_field('adrotate_email_test','adrotate_nonce'); ?>
			<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
			<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>

			<h3><?php _e('AdRotate License', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Activate your AdRotate License here to receive automated updates and enable support via the fast and personal ticket system.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('License Type', 'adrotate'); ?></th>
					<td>
						<?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'] : __('Not activated - Not eligible for support and updates.', 'adrotate'); ?>
					</td>
				</tr>
				<?php if($adrotate_hide_license == 0 AND !$adrotate_is_networked) { ?>
				<tr>
					<th valign="top"><?php _e('License Key', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Email', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used in your purchase of AdRotate Pro.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Hide License Details', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_license_hide" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('If you have installed AdRotate Pro for a client or in a multisite setup and want to hide the License Key, Email and Mass-deactivation button (Duo, Multi and Developer License) from them.', 'adrotate'); ?></span>
					</td>
				</tr>
				<?php } ?>
				<?php if(!$adrotate_is_networked) { ?>
				<tr>
					<th valign="top">&nbsp;</th>
					<td>
						<?php if($adrotate_activate['status'] == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate', 'adrotate'); ?>" class="button-secondary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate', 'adrotate'); ?>" class="button-secondary" />
							<?php if($adrotate_activate['type'] != 'Single' AND $adrotate_hide_license == 0) { ?>
							&nbsp;<input type="submit" id="post-role-submit" name="adrotate_license_reset" value="<?php _e('De-activate all active keys on all sites', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to de-activate your license on ALL sites currently using your AdRotate License. This can not be reversed!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" />
							<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</table>

			<h3><?php _e('Access Rights', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Who has access to what? All but the "advertiser page" are usually for admins and moderators.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Advertiser page', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_advertiser"><select name="adrotate_advertiser">
							<?php wp_dropdown_roles($adrotate_config['advertiser']); ?>
						</select> <?php _e('Role to allow users/advertisers to see their advertisement page.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Full report page', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_global_report"><select name="adrotate_global_report">
							<?php wp_dropdown_roles($adrotate_config['global_report']); ?>
						</select> <?php _e('Role to review the full report.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit adverts', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_ad_manage"><select name="adrotate_ad_manage">
							<?php wp_dropdown_roles($adrotate_config['ad_manage']); ?>
						</select> <?php _e('Role to see and add/edit ads.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete/Reset adverts', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_ad_delete"><select name="adrotate_ad_delete">
							<?php wp_dropdown_roles($adrotate_config['ad_delete']); ?>
						</select> <?php _e('Role to delete ads and reset stats.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit groups', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_group_manage"><select name="adrotate_group_manage">
							<?php wp_dropdown_roles($adrotate_config['group_manage']); ?>
						</select> <?php _e('Role to see and add/edit groups.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete groups', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_group_delete"><select name="adrotate_group_delete">
							<?php wp_dropdown_roles($adrotate_config['group_delete']); ?>
						</select> <?php _e('Role to delete groups.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Manage/Add/Edit schedules', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_schedule_manage"><select name="adrotate_schedule_manage">
							<?php wp_dropdown_roles($adrotate_config['schedule_manage']); ?>
						</select> <?php _e('Role to see and add/edit schedules.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Delete schedules', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_schedule_delete"><select name="adrotate_schedule_delete">
							<?php wp_dropdown_roles($adrotate_config['schedule_delete']); ?>
						</select> <?php _e('Role to delete schedules.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Moderate new adverts', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_moderate"><select name="adrotate_moderate">
							<?php wp_dropdown_roles($adrotate_config['moderate']); ?>
						</select> <?php _e('Role to approve ads submitted by advertisers.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Approve/Reject adverts in Moderation Queue', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_moderate_approve"><select name="adrotate_moderate_approve">
							<?php wp_dropdown_roles($adrotate_config['moderate_approve']); ?>
						</select> <?php _e('Role to approve or reject ads submitted by advertisers.', 'adrotate'); ?></label>
					</td>
				</tr>

				<?php if($adrotate_debug['userroles'] == true) { ?>
				<tr>
					<td colspan="2">
						<?php 
						echo "<p><strong>[DEBUG] AdRotate Advertiser role enabled? (0 = no, 1 = yes)</strong><pre>"; 
						print_r($adrotate_roles); 
						echo "</pre></p>"; 
						echo "<p><strong>[DEBUG] Current User Capabilities</strong><pre>"; 
						print_r($wp_roles); 
						echo "</pre></p>"; 
						?>
					</td>
				</tr>
				<?php } ?>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Geo Targeting', 'adrotate'); ?></h3>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Which Geo Service', 'adrotate'); ?></th>
					<td>
						<select name="adrotate_enable_geo">
							<option value="0" <?php if($adrotate_config['enable_geo'] == 0) { echo 'selected'; } ?>><?php _e('Disabled', 'adrotate'); ?></option>
							<option value="5" <?php if($adrotate_config['enable_geo'] == 5) { echo 'selected'; } ?>>AdRotate Geo</option>
							<option value="4" <?php if($adrotate_config['enable_geo'] == 4) { echo 'selected'; } ?>>MaxMind City (Recommended)</option>
							<option value="3" <?php if($adrotate_config['enable_geo'] == 3) { echo 'selected'; } ?>>MaxMind Country</option>
							<option value="5" <?php if($adrotate_config['enable_geo'] == 2) { echo 'selected'; } ?>>GeoBytes IpLocator (Deprecated)</option>
							<option value="1" <?php if($adrotate_config['enable_geo'] == 1) { echo 'selected'; } ?>>Telize</option>
						</select><br />
						<span class="description">
							<strong>MaxMind</strong> - <a href="https://www.maxmind.com/en/geoip2-precision-services?rId=ajdgnet" target="_blank">GeoIP2 Precision</a> - <?php _e('The most complete and accurate geo targeting you can get for only $20 USD per 50000 lookups.', 'adrotate'); ?> <a href="https://www.maxmind.com/en/geoip2-precision-city?rId=ajdgnet" target="_blank"><?php _e('Buy now', 'adrotate'); ?>.</a><br />
							<em><strong>Supports:</strong> Countries, States, State ISO codes, Cities and DMA codes.</em><br /><br />
							
							<strong>GeoBytes (Deprecated)</strong> - GeoBytes broke their stuff and is no longer supported by AdRotate.<br />If you select the option from the dropdown above it will use AdRotate Geo instead.<br /><br />
							
							<strong>AdRotate Geo</strong> - <?php _e('50000 free lookups every day, uses GeoLite2 databases from MaxMind!', 'adrotate'); ?><br />
							<em><strong>Supports:</strong> Countries, Cities, DMA codes, States and State ISO codes.</em><br /><br />
							
							<strong>Telize</strong> - <?php _e('Free service, uses GeoLite2 databases from MaxMind!', 'adrotate'); ?><br />
							<em><strong>Supports:</strong> Countries, Cities and DMA codes.</em>
						</span>
					</td>
				</tr>
				<?php if($adrotate_config['enable_geo'] > 1) { ?>
				<tr>
					<th valign="top"><?php _e('Remaining Requests', 'adrotate'); ?></th>
					<td><?php echo $adrotate_geo_requests; ?> <span class="description"><?php _e('This number is provided by the geo service and not checked for accuracy.', 'adrotate'); ?></span></td>
				</tr>
				<?php } ?>
				<tr>
					<th valign="top"><?php _e('Username/Email', 'adrotate'); ?></th>
					<td><label for="adrotate_geo_email"><input name="adrotate_geo_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_email']; ?>" autocomplete="off" /> <?php _e('Only for premium/paid geo services.', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Password/License Key', 'adrotate'); ?></th>
					<td><label for="adrotate_geo_pass"><input name="adrotate_geo_pass" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['geo_pass']; ?>" autocomplete="off" /> <?php _e('Only for premium/paid geo services.', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Geo Cookie Lifespan', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_geo_cookie_life"><select name="adrotate_geo_cookie_life">
							<option value="24" <?php if($adrotate_config['geo_cookie_life'] == 86400) { echo 'selected'; } ?>>24 (<?php _e('Default', 'adrotate'); ?>)</option>
							<option value="36" <?php if($adrotate_config['geo_cookie_life'] == 129600) { echo 'selected'; } ?>>36</option>
							<option value="48" <?php if($adrotate_config['geo_cookie_life'] == 172800) { echo 'selected'; } ?>>48</option>
							<option value="72" <?php if($adrotate_config['geo_cookie_life'] == 259200) { echo 'selected'; } ?>>72</option>
							<option value="120" <?php if($adrotate_config['geo_cookie_life'] == 432000) { echo 'selected'; } ?>>120</option>
							<option value="168" <?php if($adrotate_config['geo_cookie_life'] == 604800) { echo 'selected'; } ?>>168</option>
						</select> <?php _e('Hours.', 'adrotate'); ?></label><br />
						<span class="description"><?php _e('Geo Data is stored in a cookie to reduce lookups. How long should this cookie last? A longer period is less accurate for mobile users but may reduce the usage of your lookups drastically.', 'adrotate'); ?></span>

					</td>
				</tr>
				<?php if($adrotate_debug['geo'] == true) { ?>
				<tr>
					<td colspan="2">
						<?php
						echo "<p><strong>Geo Targeting Data in YOUR cookie</strong><br />";
						echo "<strong>CAUTION! When you change Geo Services the cookie needs to refresh. You may have to save the settings twice for that to happen.</strong><pre>";
						print_r($adrotate_geo); 
						echo "</pre></p>"; 
						?>
					</td>
				</tr>
				<?php } ?>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Advertisers', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Enable advertisers so they can review and manage their own ads.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Enable Advertisers', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_enable_advertisers"><input type="checkbox" name="adrotate_enable_advertisers" <?php if($adrotate_config['enable_advertisers'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Allow adverts to be coupled to users (Advertisers).', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Edit/update adverts', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_enable_editing"><input type="checkbox" name="adrotate_enable_editing" <?php if($adrotate_config['enable_editing'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to add new or edit their adverts.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Geo Targeting', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_geo_advertisers" <?php if($adrotate_config['enable_geo_advertisers'] == 1) { ?>checked="checked" <?php } ?> /> <?php _e('Allow advertisers to specify where their ads will show. Geo Targeting has to be enabled, too.', 'adrotate'); ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Advertiser role', 'adrotate'); ?></th>
					<td>
						<?php if($adrotate_roles == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_role_add_submit" value="<?php _e('Create Role', 'adrotate'); ?>" class="button-secondary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_role_remove_submit" value="<?php _e('Remove Role', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to remove the AdRotate Clients role.', 'adrotate'); ?>\n\n<?php _e('This may lead to users not being able to access their ads statistics!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" />
						<?php } ?><br />
						<span class="description"><?php _e('This role has no capabilities unless you assign them using the above options. Obviously you should use this with care.', 'adrotate'); ?><br />
						<?php _e('This type of user is NOT required to use AdRotate or any of it\'s features. It merely helps you to seperate advertisers from regular subscribers without giving them too much access to your dashboard.', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>

			<?php
			if($adrotate_debug['dashboard'] == true) {
				echo "<p><strong>[DEBUG] Globalized Config</strong><pre>"; 
				print_r($adrotate_config); 
				echo "</pre></p>"; 
			}
			?>

			<h3><?php _e('Banner Folder', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Set a location where your banner images will be stored.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Location', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_banner_folder"><?php echo site_url(); ?>/<input name="adrotate_banner_folder" type="text" class="search-input" size="30" value="<?php echo $adrotate_config['banner_folder']; ?>" autocomplete="off" /> <?php _e('(Default: wp-content/banners/).', 'adrotate'); ?><br />
						<span class="description"><?php _e('To try and trick ad blockers you could set the folder to something crazy like:', 'adrotate'); ?> "/wp-content/<?php echo adrotate_rand(12); ?>/".<br />
						<?php _e("This folder will not be automatically created if it doesn't exist. AdRotate will show errors when the folder is missing.", 'adrotate'); ?></span>
					</td>
				</tr>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Notifications', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Set up who gets notifications if ads need your attention.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Delivery method', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_notification_push" <?php if($adrotate_notifications['notification_push'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Push notifications to your smartphone.', 'adrotate'); ?><br />
						<input type="checkbox" name="adrotate_notification_email" <?php if($adrotate_notifications['notification_email'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Email message.', 'adrotate'); ?><br />
						<span class="description"><?php _e('Push notifications are delivered through Pushover, a notification service for Android and iOS', 'adrotate'); ?><br /><?php _e('The Pushover App is a one time purchase for either Android and/or iOS. More information can be found on the pushover website;', 'adrotate'); ?> <a href="http://www.pushover.net" target="_blank">pushover.net</a>.</span>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top"><?php _e('Test', 'adrotate'); ?></th>
					<td>
						<input type="submit" name="adrotate_notification_test_submit" class="button-secondary" value="<?php _e('Test', 'adrotate'); ?>" /> 
						<span class="description"><?php _e('This sends a test notification. Before you test, save the options first!', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>
			
			<h3><?php _e('Push Notifications', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Receive information about what is happening with your AdRotate setup on your smartphone via Pushover.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Publishers', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_notification_push_geo" <?php if($adrotate_notifications['notification_push_geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('When you are running out of Geo Targeting Lookups.', 'adrotate'); ?><br /><br />
						<input type="checkbox" name="adrotate_notification_push_status" <?php if($adrotate_notifications['notification_push_status'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Daily digest of any advert status other than normal.', 'adrotate'); ?><br />
						<input type="checkbox" name="adrotate_notification_push_queue" <?php if($adrotate_notifications['notification_push_queue'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Any advertiser saving an advert in your moderation queue.', 'adrotate'); ?><br />
						<input type="checkbox" name="adrotate_notification_push_approved" <?php if($adrotate_notifications['notification_push_approved'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('A moderator approved an advert from the moderation queue.', 'adrotate'); ?><br />
						<input type="checkbox" name="adrotate_notification_push_rejected" <?php if($adrotate_notifications['notification_push_rejected'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('A moderator rejected an advert from the moderation queue.', 'adrotate'); ?><br /><span class="description"><?php _e('If you have a lot of activity with many advertisers adding/changing adverts you may get a lot of messages!', 'adrotate'); ?></span>

					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('User Key', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_notification_push_user"><input name="adrotate_notification_push_user" type="text" class="search-input" size="50" value="<?php  echo $adrotate_notifications['notification_push_user']; ?>" autocomplete="off" /> <?php _e('Get your user token', 'adrotate'); ?> <a href="https://pushover.net" target="_blank"><?php _e('here', 'adrotate'); ?></a>.</label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Api Token', 'adrotate'); ?></th>
					<td>
						<label for="adrotate_notification_push_api"><input name="adrotate_notification_push_api" type="text" class="search-input" size="50" value="<?php  echo $adrotate_notifications['notification_push_api']; ?>" autocomplete="off" /> <?php _e('Create your', 'adrotate'); ?> <a href="https://pushover.net/apps/build" target="_blank"><?php _e('App', 'adrotate'); ?></a> <?php _e('and get your API token', 'adrotate'); ?> <a href="https://pushover.net/apps" target="_blank"><?php _e('here', 'adrotate'); ?></a>.</label>
					</td>
				</tr>
<!--
				<tr>
					<th valign="top"><?php _e('Advertisers', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_notification_push_advertisers" <?php if($adrotate_notifications['notification_push_advertisers'] == 'Y') { ?>checked="checked" <?php } ?> /> Allow advertisers to set up push notifications<br />
						<span class="description"><?php _e('Advertisers need their own account at Pushover and need to buy their own copy of the Pushover app for their smartphone. Your token is not shared with them.', 'adrotate'); ?></span>
					</td>
				</tr>
-->
			</table>

			<h3><?php _e('Email Notifications', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Set up who gets notification emails.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Publishers', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_notification_email_publisher" cols="50" rows="2"><?php echo $notification_mails; ?></textarea><br />
						<span class="description"><?php _e('A comma separated list of email addresses. Maximum of 5 addresses. Keep this list to a minimum!', 'adrotate'); ?><br />
						<?php _e('Messages are sent once every 24 hours when needed. If this field is empty no email notifications will be send.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Advertisers', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_notification_email_advertiser" cols="50" rows="2"><?php echo $advertiser_mails; ?></textarea><br />
						<span class="description"><?php _e('Who gets email from advertisers. Maximum of 2 addresses. Comma seperated. This field may not be empty!', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>
			
		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Statistics', 'adrotate'); ?></h3></td>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Enable stats', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_stats" <?php if($adrotate_config['enable_stats'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track clicks and impressions.', 'adrotate'); ?><br /><span class="description"><?php _e('Disabling this also disables click and impression limits on schedules and disables timeframes.', 'adrotate'); ?></span><br />
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Logged in impressions', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_loggedin_impressions" <?php if($adrotate_config['enable_loggedin_impressions'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track impressions from logged in users (Recommended).', 'adrotate'); ?><br /><span class="description"><?php _e('Has no effect when click and impression tracking is disabled.', 'adrotate'); ?></span><br />
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Logged in clicks', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_enable_loggedin_clicks" <?php if($adrotate_config['enable_loggedin_clicks'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track clicks from logged in users.', 'adrotate'); ?><br /><span class="description"><?php _e('Has no effect when click and impression tracking is disabled.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Impressions timer', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_impression_timer" type="text" class="search-input" size="5" value="<?php echo $adrotate_config['impression_timer']; ?>" autocomplete="off" /> <?php _e('Seconds.', 'adrotate'); ?><br />
						<span class="description"><?php _e('Default: 60.', 'adrotate'); ?><br /><?php _e('This number may not be empty, be lower than 10 or exceed 3600 (1 hour).', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Clicks timer', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_click_timer" type="text" class="search-input" size="5" value="<?php echo $adrotate_config['click_timer']; ?>" autocomplete="off" /> <?php _e('Seconds.', 'adrotate'); ?><br />
						<span class="description"><?php _e('Default: 86400.', 'adrotate'); ?><br /><?php _e('This number may not be empty, be lower than 60 or exceed 86400 (24 hours).', 'adrotate'); ?></span>
					</td>
				</tr>
			</table>
	
			<h3><?php _e('Bot filter', 'adrotate'); ?></h3></td>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('User-Agent Filter', 'adrotate'); ?></th>
					<td>
						<textarea name="adrotate_crawlers" cols="90" rows="15"><?php echo $crawlers; ?></textarea><br />
						<span class="description"><?php _e('A comma separated list of keywords. Filter out bots/crawlers/user-agents. To prevent impressions and clicks counted on them.', 'adrotate'); ?><br />
						<?php _e('Keep in mind that this might give false positives. The word \'google\' also matches \'googlebot\', but not vice-versa. So be careful!', 'adrotate'); ?>. <?php _e('Keep your list up-to-date', 'adrotate'); ?> <a href="http://www.robotstxt.org/db.html" target="_blank">robotstxt.org/db.html</a>.<br />
						<?php _e('Use only words with alphanumeric characters, [ - _ ] are allowed too. All other characters are stripped out.', 'adrotate'); ?><br />
						<?php _e('Additionally to the list specified here, empty User-Agents are blocked as well.', 'adrotate'); ?> (<?php _e('Learn more about', 'adrotate'); ?> <a href="http://en.wikipedia.org/wiki/User_agent" title="User Agents" target="_blank"><?php _e('user-agents', 'adrotate'); ?></a>.)</span>
					</td>
				</tr>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Ad Blocker detection', 'adrotate'); ?></h3>
			<span class="description"><?php _e('Detect ad blockers and show a message to those users. Make sure jQuery Ad Blocker Detection is enabled under Javascript', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('Show nag for', 'adrotate'); ?></th>
					<td><select name="adrotate_adblock_timer">
						<option value="1" <?php if($adrotate_config['adblock_timer'] == 1) { echo 'selected'; } ?>>1</option>
						<option value="2" <?php if($adrotate_config['adblock_timer'] == 2) { echo 'selected'; } ?>>2</option>
						<option value="3" <?php if($adrotate_config['adblock_timer'] == 3) { echo 'selected'; } ?>>3</option>
						<option value="4" <?php if($adrotate_config['adblock_timer'] == 4) { echo 'selected'; } ?>>4</option>
						<option value="5" <?php if($adrotate_config['adblock_timer'] == 5) { echo 'selected'; } ?>>5</option>
						<option value="6" <?php if($adrotate_config['adblock_timer'] == 6) { echo 'selected'; } ?>>6</option>
						<option value="7" <?php if($adrotate_config['adblock_timer'] == 7) { echo 'selected'; } ?>>7</option>
						<option value="8" <?php if($adrotate_config['adblock_timer'] == 8) { echo 'selected'; } ?>>8</option>
						<option value="9" <?php if($adrotate_config['adblock_timer'] == 9) { echo 'selected'; } ?>>9</option>
						<option value="10" <?php if($adrotate_config['adblock_timer'] == 10) { echo 'selected'; } ?>>10</option>
						<option value="15" <?php if($adrotate_config['adblock_timer'] == 15) { echo 'selected'; } ?>>15</option>
						<option value="20" <?php if($adrotate_config['adblock_timer'] == 20) { echo 'selected'; } ?>>20</option>
					</select> <?php _e('Seconds.', 'adrotate'); ?><br /><span class="description"><?php _e('More seconds means you hinder your visitors more, which may drive them away. Use with caution!', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Message to show', 'adrotate'); ?></th>
					<td><input name="adrotate_adblock_message" type="text" class="search-input" size="50" value="<?php echo $adrotate_config['adblock_message']; ?>" autocomplete="off" /><br />
					<span class="description"><?php _e('Default: "Ad blocker detected! Please wait %time% seconds or disable your ad blocker!"', 'adrotate'); ?><br />
					<?php _e('No HTML/Javascript allowed. %time% will be replaced with a countdown in seconds.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Logged in users', 'adrotate'); ?></th>
					<td><input type="checkbox" name="adrotate_adblock_loggedin" <?php if($adrotate_config['adblock_loggedin'] == 'Y') { ?>checked="checked" <?php } ?> /> <span class="description"><?php _e('Show the message to logged in users?', 'adrotate'); ?></span></td>
				</tr>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Miscellaneous', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Widget alignment', 'adrotate'); ?></th>
					<td><label for="adrotate_widgetalign"><input type="checkbox" name="adrotate_widgetalign" <?php if($adrotate_config['widgetalign'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Check this box if your widgets do not align in your themes sidebar. (Does not always help!)', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Widget padding', 'adrotate'); ?></th>
					<td><label for="adrotate_widgetpadding"><input type="checkbox" name="adrotate_widgetpadding" <?php if($adrotate_config['widgetpadding'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Enable this to remove the padding (blank space) around ads in widgets. (Does not always work!)', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Admin Bar', 'adrotate'); ?></th>
					<td><label for="adrotate_adminbar"><input type="checkbox" name="adrotate_adminbar" <?php if($adrotate_config['adminbar'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Enable the AdRotate Quickmenu in the Admin Bar', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Dashboard Notifications', 'adrotate'); ?></th>
					<td><label for="adrotate_dashboard_notifications"><input type="checkbox" name="adrotate_dashboard_notifications" <?php if($adrotate_config['dashboard_notifications'] == 'N') { ?>checked="checked" <?php } ?> /> <?php _e('Disable Dashboard Notifications.', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Hide Schedules', 'adrotate'); ?></th>
					<td><label for="adrotate_hide_schedules"><input type="checkbox" name="adrotate_hide_schedules" <?php if($adrotate_config['hide_schedules'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('When editing adverts; Hide schedules that are not in use by that advert.', 'adrotate'); ?></label></td>
				</tr>
				<?php if($adrotate_config['w3caching'] == "Y" AND !defined('W3TC_DYNAMIC_SECURITY')) { ?>
				<tr>
					<th valign="top"><?php _e('NOTICE:', 'adrotate'); ?></th>
					<td><span style="color:#f00;"><?php _e('You have enabled W3 Total Caching support but not defined the security hash. You need to add the following line to your wp-config.php near the bottom or below line 52 (which defines another hash.) Using the "late init" function needs to be enabled in W3 Total Cache as well too.', 'adrotate'); ?></span><br /><pre>define('W3TC_DYNAMIC_SECURITY', '<?php echo md5(rand(0,999)); ?>');</pre></td>
				</tr>
				<?php } ?>
				<tr>
					<th valign="top"><?php _e('W3 Total Caching', 'adrotate'); ?></th>
					<td><label for="adrotate_w3caching"><input type="checkbox" name="adrotate_w3caching" <?php if($adrotate_config['w3caching'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Check this box if you use W3 Total Caching on your site.', 'adrotate'); ?></label></td>
				</tr>

				<?php if($adrotate_config['supercache'] == "Y") { ?>
				<tr>
					<th valign="top"><?php _e('NOTICE:', 'adrotate'); ?></th>
					<td><span style="color:#f00;"><?php _e('You have enabled WP Super Cache support. If you have version 1.4 or newer, this function will not work. WP Super Cache has discontinued support for dynamic content.', 'adrotate'); ?></span></td>
				</tr>
				<?php } ?>
				<tr>
					<th valign="top"><?php _e('WP Super Cache', 'adrotate'); ?></th>
					<td><label for="adrotate_supercache"><input type="checkbox" name="adrotate_supercache" <?php if($adrotate_config['supercache'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Check this box if you use WP Super Cache on your site.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top">&nbsp;</th>
					<td><span class="description"><?php _e('It may take a while for the ad to start rotating. The caching plugin needs to refresh the cache. This can take up to a week if not done manually.', 'adrotate'); ?> <?php _e('Caching support only works for [shortcodes] and the AdRotate Widget. If you use a PHP Snippet you need to wrap your PHP in the exclusion code yourself.', 'adrotate'); ?></span></td>
				</tr>
			</table>

			<h3><?php _e('Javascript', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Load jQuery', 'adrotate'); ?></th>
					<td><label for="adrotate_jquery"><input type="checkbox" name="adrotate_jquery" <?php if($adrotate_config['jquery'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('jQuery is required for all Javascript features below. Enable this if your theme does not load jQuery already.', 'adrotate'); ?></label></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Load Ad Blocker Detection', 'adrotate'); ?></th>
					<td><label for="adrotate_adblock"><input type="checkbox" name="adrotate_adblock" <?php if($adrotate_config['adblock'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Try to detect Ad Blockers and show your visitors a nag message.', 'adrotate'); ?></label>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Load in footer?', 'adrotate'); ?></th>
					<td><label for="adrotate_jsfooter"><input type="checkbox" name="adrotate_jsfooter" <?php if($adrotate_config['jsfooter'] == 'Y') { ?>checked="checked" <?php } ?> /><?php _e('Enable if you want to load the above libraries in the footer. Your theme needs to call wp_footer() for this to work.', 'adrotate'); ?></label></td>
				</tr>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Maintenance', 'adrotate'); ?></h3>
			<span class="description"><?php _e('NOTE: The below functions are intented to be used to OPTIMIZE your database. They only apply to your ads/groups and stats. Not to other settings or other parts of WordPress! Always always make a backup! These functions are to be used when you feel or notice your database is slow, unresponsive and sluggish.', 'adrotate'); ?></span>
			<table class="form-table">			
				<tr>
					<th valign="top"><?php _e('Optimize Database', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_db_optimize_submit" value="<?php _e('Optimize Database', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to optimize the AdRotate database.', 'adrotate'); ?>\n\n<?php _e('Did you make a backup of your database?', 'adrotate'); ?>\n\n<?php _e('This may take a moment and may cause your website to respond slow temporarily!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<span class="description"><?php _e('Cleans up overhead data in the AdRotate tables.', 'adrotate'); ?><br />
						<?php _e('Overhead data is accumulated garbage resulting from many changes you\'ve made. This can vary from nothing to hundreds of KiB of data.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Clean-up Database', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_db_cleanup_submit" value="<?php _e('Clean-up Database', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to clean up your database. This may delete expired schedules and older statistics.', 'adrotate'); ?>\n\n<?php _e('Are you sure you want to continue?', 'adrotate'); ?>\n\n<?php _e('This might take a while and may slow down your site during this action!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<label for="adrotate_db_cleanup_schedules"><input type="checkbox" name="adrotate_db_cleanup_schedules" value="1" /> <?php _e('Delete old (expired) schedules (Optional).', 'adrotate'); ?></label><br />
						<label for="adrotate_db_cleanup_statistics"><input type="checkbox" name="adrotate_db_cleanup_statistics" value="1" /> <?php _e('Delete stats older than 356 days (Optional).', 'adrotate'); ?></label><br />
						<span class="description"><?php _e('AdRotate creates empty records when you start making ads, groups or schedules. In rare occasions these records are faulty.', 'adrotate'); ?><br /><?php _e('If you made an ad, group or schedule that does not save when you make it use this button to delete those empty records.', 'adrotate'); ?><br /><?php _e('Additionally you can clean up old schedules and/or statistics. This will improve the speed of your site.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Re-evaluate Ads', 'adrotate'); ?></th>
					<td>
						<input type="submit" id="post-role-submit" name="adrotate_evaluate_submit" value="<?php _e('Re-evaluate all ads', 'adrotate'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to check all ads for errors.', 'adrotate'); ?>\n\n<?php _e('This might take a while and may slow down your site during this action!', 'adrotate'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')" /><br />
						<span class="description"><?php _e('This will apply all evaluation rules to all ads to see if any error slipped in. Normally you should not need this feature.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<td colspan="2"><span class="description"><?php _e('DISCLAIMER: If for any reason your data is lost, damaged or otherwise becomes unusable in any way or by any means in whichever way I will not take responsibility. You should always have a backup of your database. These functions do NOT destroy data. If data is lost, damaged or unusable, your database likely was beyond repair already. Claiming it worked before clicking these buttons is not a valid point in any case.', 'adrotate'); ?></span></td>
				</tr>
			</table>

		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>

			<h3><?php _e('Troubleshooting', 'adrotate'); ?></h3>
			<table class="form-table">			
				<tr>
					<td><?php _e('Current version:', 'adrotate'); ?> <?php echo $adrotate_version['current']; ?></td>
					<td><?php _e('Previous version:', 'adrotate'); ?> <?php echo $adrotate_version['previous']; ?></td>
				</tr>
				<tr>
					<td><?php _e('Current database version:', 'adrotate'); ?> <?php echo $adrotate_db_version['current']; ?></td>
					<td><?php _e('Previous database version:', 'adrotate'); ?> <?php echo $adrotate_db_version['previous']; ?></td>
				</tr>
				<tr>
					<td><?php _e('Ad evaluation next run:', 'adrotate'); ?></td>
					<td><?php if(!$adevaluate) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adevaluate); ?></td>
				</tr>
				<tr>
					<td><?php _e('Ad Notifications next run:', 'adrotate'); ?></td>
					<td><?php if(!$adschedule) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adschedule); ?></td>
				</tr>
				<tr>
					<td><?php _e('Clean Trackerdata next run:', 'adrotate'); ?></td>
					<td><?php if(!$adtracker) _e('Not scheduled!', 'adrotate'); else echo date_i18n(get_option('date_format')." H:i", $adtracker); ?></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Current status of adverts', 'adrotate'); ?></th>
					<td><?php _e('Normal', 'adrotate'); ?>: <?php echo $adrotate_advert_status['normal']; ?>, <?php _e('Error', 'adrotate'); ?>: <?php echo $adrotate_advert_status['error']; ?>, <?php _e('Expired', 'adrotate'); ?>: <?php echo $adrotate_advert_status['expired']; ?>, <?php _e('Expires Soon', 'adrotate'); ?>: <?php echo $adrotate_advert_status['expiressoon']; ?>, <?php _e('Unknown Status', 'adrotate'); ?>: <?php echo $adrotate_advert_status['unknown']; ?>.</td>
				</tr>
				<tr>
					<td colspan="2"><span class="description"><?php _e('NOTE: The below options are not meant for normal use and are only there for developers to review saved settings or how ads are selected. These can be used as a measure of troubleshooting upon request but for normal use they SHOULD BE LEFT UNCHECKED!!', 'adrotate'); ?></span></td>
				</tr>
				<tr>
					<th valign="top"><?php _e('Developer Debug', 'adrotate'); ?></th>
					<td>
						<input type="checkbox" name="adrotate_debug" <?php if($adrotate_debug['general'] == true) { ?>checked="checked" <?php } ?> /> General - <span class="description"><?php _e('Troubleshoot ads and how (if) they are selected, has front-end output.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_dashboard" <?php if($adrotate_debug['dashboard'] == true) { ?>checked="checked" <?php } ?> /> Dashboard - <span class="description"><?php _e('Show all settings, dashboard routines and related values.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_userroles" <?php if($adrotate_debug['userroles'] == true) { ?>checked="checked" <?php } ?> /> User Roles - <span class="description"><?php _e('Show array of all userroles and capabilities.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_userstats" <?php if($adrotate_debug['userstats'] == true) { ?>checked="checked" <?php } ?> /> Userstats - <span class="description"><?php _e('Review saved advertisers! Visible to advertisers.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_stats" <?php if($adrotate_debug['stats'] == true) { ?>checked="checked" <?php } ?> /> Stats - <span class="description"><?php _e('Review Full Report, per ad/group stats. Visible only to publishers.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_geo" <?php if($adrotate_debug['geo'] == true) { ?>checked="checked" <?php } ?> /> Geo Targeting - <span class="description"><?php _e('Output retrieved Geo data or errors related to the retrieving of Geo Services.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_timers" <?php if($adrotate_debug['timers'] == true) { ?>checked="checked" <?php } ?> /> Clicktracking - <span class="description"><?php _e('Disable timers for clicks and impressions and enable a alert window for clicktracking.', 'adrotate'); ?></span><br />
						<input type="checkbox" name="adrotate_debug_track" <?php if($adrotate_debug['track'] == true) { ?>checked="checked" <?php } ?> /> Tracking Encryption - <span class="description"><?php _e('Temporarily disable encryption on the redirect url.', 'adrotate'); ?></span><br />
					</td>
				</tr>
	    	</table>
	    	
		    <p class="submit">
		      	<input type="submit" name="adrotate_options_submit" class="button-primary" value="<?php _e('Update Options', 'adrotate'); ?>" />
		    </p>
		</form>
	</div>
<?php 
}

/*-------------------------------------------------------------
 Name:      adrotate_network_license

 Purpose:   Network activated license dashboard
 Receive:   -none-
 Return:    -none-
-------------------------------------------------------------*/
function adrotate_network_license() {
	global $wpdb, $adrotate_advert_status;

	$status = '';
	if(isset($_GET['status'])) $status = esc_attr($_GET['status']);
	$adrotate_activate = get_site_option('adrotate_activate');
	?>

	<div class="wrap">
	  	<h2><?php _e('AdRotate Network License', 'adrotate'); ?></h2>

		<?php if($status > 0) adrotate_status($status); ?>
		
	  	<form name="settings" id="post" method="post" action="admin.php?page=adrotate-network-settings">
			<input type="hidden" name="adrotate_license_network" value="1" />

			<?php wp_nonce_field('adrotate_license','adrotate_nonce_license'); ?>

			<span class="description"><?php _e('Activate your AdRotate License here to receive automated updates and enable support via the fast and personal ticket system.', 'adrotate'); ?><br />
			<?php _e('For network activated setups like this you need a Network or Developer License.', 'adrotate'); ?></span>
			<table class="form-table">
				<tr>
					<th valign="top"><?php _e('License Type', 'adrotate'); ?></th>
					<td>
						<?php echo ($adrotate_activate['type'] != '') ? $adrotate_activate['type'] : __('Not activated - Not eligible for support and updates.', 'adrotate'); ?>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Key', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_key" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['key']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('You can find the license key in your order email.', 'adrotate'); ?></span>
					</td>
				</tr>
				<tr>
					<th valign="top"><?php _e('License Email', 'adrotate'); ?></th>
					<td>
						<input name="adrotate_license_email" type="text" class="search-input" size="50" value="<?php echo $adrotate_activate['email']; ?>" autocomplete="off" <?php echo ($adrotate_activate['status'] == 1) ? 'disabled' : ''; ?> /> <span class="description"><?php _e('The email address you used in your purchase of AdRotate Pro.', 'adrotate'); ?></span>
					</td>
				</tr>

				<tr>
					<th valign="top">&nbsp;</th>
					<td>
						<?php if($adrotate_activate['status'] == 0) { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_activate" value="<?php _e('Activate', 'adrotate'); ?>" class="button-primary" />
						<?php } else { ?>
						<input type="submit" id="post-role-submit" name="adrotate_license_deactivate" value="<?php _e('De-activate', 'adrotate'); ?>" class="button-secondary" />
						<?php } ?>
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php
}
?>