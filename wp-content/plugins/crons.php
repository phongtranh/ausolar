<?php

/*
Plugin Name: GF Simple Autoresponder
Plugin URI: http://frontandsocial.com
Description: Creates a simple message time delay for Gravity Forms notifications.
Version: 1.2
Author: Front & Social
Author URI: http://frontandsocial.com
*/

add_action('gform_after_submission', 'prep_cron_events', 10, 2);
add_action( 'set_gf_cron_events', 'do_cron_events', 10, 1 );

function prep_cron_events($entry, $form) {
//    $uid = uniqid();
    $events = get_option('gf_cron_pendings');
//    $events = array();
    
//    $uid = (array_key_exists($uid, $cron_pendings)) ? uniqid() : '';

    // Get all notification with autoresponder delay enabled and set cron events
    $form_meta = RGFormsModel::get_form_meta($entry['form_id']);
    foreach($form_meta['notifications'] as $key => $fields) {
        if($fields['gf_autoresponder_cb'] == 1) {
            $uid = uniqid();
            $events[$uid] = array('form_id' => $entry['form_id'], 'lid' => $entry['id'], 'nid' => $key);
            $time = GFcrons::translate_to_time($fields);
            wp_schedule_single_event( $time, 'set_gf_cron_events', array($uid) );
//            $events[$uid][] = $entry_meta;            
        }
    }
    
    update_option('gf_cron_pendings', $events);  
//    update_option('gf_cron_test', $entry_meta);  
//    wp_schedule_single_event( time() + 3600, 'set_gf_cron_events', array($uid) );
}

/**
 * On the scheduled action hook, run the function. Resend notifications.
 */
function do_cron_events($uid) {
    $pendings = get_option('gf_cron_pendings');
    
    
    if(array_key_exists($uid, $pendings)) {
        $form = RGFormsModel::get_form_meta($pendings[$uid]['form_id']);
        $lead = RGFormsModel::get_lead($pendings[$uid]['lid']);
        GFCommon::send_notification($form['notifications'][$pendings[$uid]['nid']], $form, $lead);
        unset($pendings[$uid]);
        update_option('gf_cron_pendings', $pendings);
    }
}

if(!class_exists('GFcrons')) {
    class GFcrons {
        private $autoresponder_id = 'gf_autoresponder_cb';
        private $autoresponder_option = 'gf_disable_notification';
        
        public function __construct() {           
            add_action('gform_notification_ui_settings', array(&$this, 'gf_ui_addon'), 10, 3);
            add_action('gform_pre_notification_save', array(&$this, 'gf_notification_save'), 10, 2);
            
            // Disable notification if gf_autoresponder_cb is checked
            add_action('init', array(&$this, 'disable_default_notification'));
            
            // When a form is submitted we prepare a cron single event for gf_autoresponder_cb enabled 
//            add_action('gform_after_submission', array(&$this, 'prep_cron_events'), 10, 2);
            
            // Set single cron events for sending notification at a later time
//            add_action('set_gf_cron_events', array(&$this, 'do_cron_events'), 10, 1);            
            
//            add_action('gf_autoresponder_events', array(&$this, 'send_notice'));
//            add_action('gform_after_submission', array(&$this, 'add_event_autoresponder'));
//            add_filter('cron_schedules', array(&$this, 'cron_sec'));
        }
        
        public function activate() {
            if( get_option('gf_disable_notification')  == false ) {
                update_option('gf_disable_notification', array());
            }
            
            if( get_option('gf_cron_pendings') == false ) {
                update_option('gf_cron_pendings', array());
            }
        }
        
        public function deactivate() {
            ;
        }
        
        public function gf_ui_addon($ui_settings, $confirmation, $form) {
            
            $autoresponder_id_val = rgar($confirmation, $this->autoresponder_id);            
            $checked = empty($autoresponder_id_val) ? "" : "checked='checked'";
            $onclick = 'onclick="jQuery(\'.notification_to_container\').hide(); jQuery(\'#gf_cron_settings\').show(\'slow\')"';
            
            $ui_settings['gf_cron_autoresponser'] = '
                <tr>
                    <th><label for="'.$this->autoresponder_id.'">Autoresponder</label></th>
                    <td><input type="checkbox" value="1" name="'.$this->autoresponder_id.'" '.$checked.' '.$onclick.'> Set Time Delay</td>
                </tr>
                <tr id="gf_cron_settings" class="notification_to_container">
                    <th><label>Settings</label></th>
                    <td>Send notification in: <br />
                        <select name="cron_months" id="cron_months">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(12, rgar($confirmation, 'cron_months'), 'months').'
                        </select> Month(s) <br />
                        <select name="cron_weeks" id="cron_weeks">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(4, rgar($confirmation, 'cron_weeks'), 'weeks').'
                        </select> Week(s) <br />
                        <select name="cron_days" id="cron_days">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(7, rgar($confirmation, 'cron_days'), 'days').'
                        </select> Day(s) <br />
                        <select name="cron_hours" id="cron_hours">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(24, rgar($confirmation, 'cron_hours'), 'hours').'
                        </select> Hour(s) <br />   
                        <select name="cron_minutes" id="cron_minutes">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(60, rgar($confirmation, 'cron_minutes'), 'minutes').'
                        </select> minute(s) <br />
                        <select name="cron_seconds" id="cron_seconds">
                            <option value=""> --- </option>
                            '.$this->loop_counter_option(60, rgar($confirmation, 'cron_seconds'), 'seconds').'
                        </select> second(s) <br />
                    </td>
                </tr>    
                ';
            
//            '.  var_dump($confirmation).'
//            '.  var_dump($form).'

            return $ui_settings;
        }
        
        public function gf_notification_save($notification, $form) {
            $notification[$this->autoresponder_id] = rgpost($this->autoresponder_id);
            $notification['cron_months'] = rgpost('cron_months');
            $notification['cron_weeks'] = rgpost('cron_weeks');
            $notification['cron_days'] = rgpost('cron_days');
            $notification['cron_hours'] = rgpost('cron_hours');
            $notification['cron_minutes'] = rgpost('cron_minutes');
            $notification['cron_seconds'] = rgpost('cron_seconds');
            
            // Enable/Disbale notification email if autoresponder is checked/unchecked
            $this->set_form($notification, $form);
            
            // Set Autoresponder
//            $this->set_autoresponder($notification, $form);
            
            return $notification;
        }
        
        public function set_form($notification, $form) {  
            $options = get_option('gf_disable_notification');            
            
            if($notification['gf_autoresponder_cb'] == 1) {    
                if(!in_array($form['id'], $options))
                    $options[] = $form['id'];                                  
            } else {
                $options = array_diff($options, array($form['id']));
            }
            
            update_option($this->autoresponder_option, $options);
        }
        
        public function disable_default_notification() {
            $id_forms = get_option('gf_disable_notification');
            
            if(!empty($id_forms)) {
                foreach($id_forms as $id) {
                    add_filter('gform_disable_notification_' . $id, array(&$this, 'disable_notification'), 10, 4);
                }
            }
        }
        
        public function disable_notification($is_disabled, $notification, $form, $entry) {
            return true;
        }
        
        public function prep_cron_events($entry, $form) {
            $uid = uniqid();
            $events = array();
            
            // Get all notification with autoresponder delay enabled
            $entry_meta = RGFormsModel::get_form_meta($entry['form_id']);
            foreach($entry_meta['notifications'] as $id => $fields) {
                if($fields['gf_autoresponder_cb'] == 1 || isset($fields['gf_autoresponder_cb'])) {
                    $events[$uid][] = $fields;
                }
            }
            update_option('gf_cron_test', $uid);
//            wp_schedule_single_event(time() + 3600, array(&$this, 'set_gf_cron_events'), array($uid));
//            wp_schedule_event( time(), 'hourly', 'set_gf_cron_events', $events);
        }
        
        // Activate cron events and send notification
        public function do_cron_events($entry) {
            
        }
        
        public function add_event_autoresponder($entry, $form) {
            update_option('gf_cron_test', array($entry, $form));
            // Get all notification with autoresponder delay enabled 
            
            $id = uniqid();
            
        }

        public function set_autoresponder($notification, $form) {
            wp_schedule_single_event(time() + 30, 'gf_autoresponder_events');            
        }
        
        
        public function cron_sec($schedules) {
            $schedules['seconds'] = array(
                'interval' => 30,
                'display' => __('30 Seconds')
            );
            
            return $schedules;
        }
        
        public static function translate_to_time($data_arr = array(), $string = '') {
            $strtime = "+";
            
            if(!empty($data_arr)) {
                if(!empty($data_arr['cron_months']) && array_key_exists('cron_months', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_months'];
                if(!empty($data_arr['cron_weeks']) && array_key_exists('cron_weeks', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_weeks'];
                if(!empty($data_arr['cron_days']) && array_key_exists('cron_days', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_days'];
                if(!empty($data_arr['cron_hours']) && array_key_exists('cron_hours', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_hours'];
                if(!empty($data_arr['cron_minutes']) && array_key_exists('cron_minutes', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_minutes'];
                if(!empty($data_arr['cron_seconds']) && array_key_exists('cron_seconds', $data_arr))
                    $strtime .= ' ' . $data_arr['cron_seconds'];  
                
                return strtotime($strtime);
            }
            
            if(!empty($string))
                return strtotime($string);
        }

        private function loop_counter_option($num, $selected = '', $string = '') { 
            for($i=1, $i >= $num; $num--;) {
                if($i .' '. $string == $selected)
                    $option .= '<option value="'.$i.' '.$string.'" selected>'.$i.'</option>';
                else
                    $option .= '<option value="'.$i.' '.$string.'">'.$i.'</option>';
                $i++;
            }
            
            return $option;
        }
    }  
    
    register_activation_hook(__FILE__, array('GFcrons', 'activate'));
    register_deactivation_hook(__FILE__, array('GFcrons', 'deactivate'));
    
    $gf_cron_addon = new GFcrons();
}


?>
