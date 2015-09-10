<?php

ignore_user_abort(true);

if ( !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') )
	die();

/**
 * Tell WordPress we are doing the CRON task.
 *
 * @var bool
 */
define('DOING_CRON', true);

if ( !defined('ABSPATH') ) {
	/** Set up WordPress environment */
	require_once( dirname( __FILE__ ) . '/wp-load.php' );
}

//check access
//block access from browser, only allow local (command php)
$ip = $_SERVER['SERVER_NAME'];
if($ip !== NULL)
	die('');

function weekly_companies_email()
{
    $companies = get_posts( array(
        'post_type'      => 'company',
        'post_status'    => 'publish',
        'posts_per_page' => - 1,
        'meta_query'     => array(
            'relation' => 'AND',
            // Company must enable buying leads
            array(
                'key'   => 'leads_enable',
                'value' => 1,
            ),

            // Company hasn't stop buying leads
            array(
                'key'     => 'cancel_reason',
                'value'   => 1,
                'compare' => 'NOT EXISTS',
            ),

            // Company is not manually suspended
            array(
                'key'     => 'leads_manually_suspend',
                'value'   => 1,
                'compare' => 'NOT EXISTS',
            ),
        ),
    ) );

    $companies_leads = array();

    foreach ( $companies as $company )
    {
        $companies_leads[$company->ID] = solar_company_leads_logs( $company->ID, null, 'week' );
    }

    $users  = get_users( array( 'role' => 'company_owner' ) );

    $owners = array();
    
    foreach ( $users as $owner )
    {
        $owners[$owner->ID] = (array) $owner->data;
    }

    $count_sendmail_success = 1; //count number email send success
    $count_sendmail_fail = 1; //count number email send fail
    foreach ( $companies as $company )
    {

        $count  = $companies_leads[$company->ID];

        $company_name = $company->post_title;
        $leads = _n( 'lead', 'leads', $count );
       
        $company_owner_id       = get_post_meta( $company->ID, 'user', true );
        if ( ! isset( $company_owner_id ) || empty( $company_owner_id ) )
            continue;

        $company_owner_id = intval( $company_owner_id );

        // Skip all companies without membership
        $membership = get_user_meta( $company_owner_id, 'membership', true );
        if ( empty( $membership ) )
            continue;

        $company_owner_name     = '';

        if ( isset( $owners[$company_owner_id]['display_name'] ) )
            $company_owner_name = str_title( $owners[$company_owner_id]['display_name'] );

        $company_owner_email    = $owners[$company_owner_id]['user_email'];

        $to = $company_owner_email;

        $amount = intval( get_post_meta( $company->ID, 'leads', true ) );
        
        if ( ! $amount )
            $amount = 0;

        $lead_frequency = get_post_meta( $company->ID, 'lead_frequency', true );

        if ( empty( $lead_frequency ) )
            $lead_frequency = 'month';

        $subject = "{$company_name}'s weekly activity with Australian Solar Quotes";

        $message = "<p>Hi {$company_owner_name}</p>";

        if ( $count > 0 )
        {
            $reached_message = '';

            if ( $lead_frequency == 'month' )
            {
                $total_lead_this_month = count( solar_get_company_leads( $company->ID ) );

                if ( $total_lead_this_month >= $amount )
                {
                    $reached_message = "<p>Solar power seems to popular in your area so to connect with more qualified buyers, simply increase your lead cap from {$amount} a {$lead_frequency}.</p>";
                }
            }

            if ( $lead_frequency == 'week' )
            {
                if ( $count >= $amount )
                {
                   $reached_message = "<p>Solar power seems to popular in your area so to connect with more qualified buyers, simply increase your lead cap from {$amount} a {$lead_frequency}.</p>";
                }
            }

            $message .= "
            <p>We're just letting you know that your account is active and you have received {$count} {$leads} this week.</p>
            {$reached_message}
            <p>To edit your lead settings, please log in to the installer portal by clicking on the below link:</p>
            <p><a href=\"https://www.australiansolarquotes.com.au/my-account\">My Account</a></p>
            ";
        }
        else
        {
            $m = "
            <p>We're just letting you know that although you have not received any leads this week, your account is active.</p>
            <p>There is a number of things that you can proactivelly do to score higher with us and get more leads. find out more here:</p>
            <p><a href=\"https://www.australiansolarquotes.com.au/my-account/support/company-score/\">How to score higher and get more leads</a></p>
            <p>You have opted to receive {$amount} {$leads} a {$lead_frequency}.</p>
            <p>To edit your lead settings, please log in to the installer portal by clicking on the below link:</p>
            <p><a href=\"https://www.australiansolarquotes.com.au/my-account\">My Account</a></p>
            ";

            $postcodes = get_post_meta( $company->ID, 'service_postcodes', true );

            $c = 0;

            if ( empty( $postcodes ) )
            {
                $c++;

                $m = "
                    <p>We're just letting you know that you have not received any leads this week because you have not let us know what areas you service.</p>

                    <p><a href=\"https://www.australiansolarquotes.com.au/my-account/leads/\">Edit service area</a></p>

                    <p>Once this has been updated, we will try hard to connect you with {$amount} {$leads} a {$lead_frequency}.</p>
                ";
            }

            if ( 'direct' == get_post_meta( $company->ID, 'leads_payment_type', true ) && ! get_post_meta( $company->ID, 'leads_direct_debit_saved', true ) )
            {
                $c++;

                $m = "
                    <p>We're just letting you know that you have not received any leads this week because we have not yet received your Ezi Debit application.</p>

                    <p><a href=\"https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/files/DDR_37533-Australian%20Solar%20Quotes.pdf\">Download Ezi Debit application</a></p>

                    <p>Once you have completed the application, please scan and email to <a href=\"mailto:accounts@australiansolarquotes.com.au\">accounts@australiansolarquotes.com.au</a></p>
                ";
            }

            if ( $c === 2 )
            {
                $m = "
                    <p>We're just letting you know that you have not received any leads this week for a couple of reasons.</p>

                    <p>1. You have not let us know what areas that you service.</p>

                    <p><a href=\"https://www.australiansolarquotes.com.au/my-account/leads/\">Edit service area</a></p>

                    <p>2. We have not yet received you Ezi Debit application.</p>

                    <p><a href=\"https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/files/DDR_37533-Australian%20Solar%20Quotes.pdf\">Download Ezi Debit application</a></p>

                    <p>Once you have completed the application, please scan and email to <a href=\"mailto:accounts@australiansolarquotes.com.au\">accounts@australiansolarquotes.com.au</a></p>

                    <p>As soon as these quick tasks are complete, we will try hard to connect you with {$amount} {$leads} a {$lead_frequency}.<p>
                ";
            }

            $message .= $m;
        }

        $message .= "<p>Have a great week.</p>
                    <p>Australian Solar Quotes</p>";
        
        
        if(is_email($to))
        {
            echo $to .PHP_EOL;
            //$to = 'accounts@australiansolarquotes.com.au';
            //$headers = array();
            //$headers[] = 'Cc: accounts@australiansolarquotes.com.au, tan@fitwp.com, installer@australiansolarquotes.com.au';
            wp_mail( $to, $subject, $message );
            wp_mail( 'installer@australiansolarquotes.com.au', $subject, $message );
            wp_mail( 'tannt.com@gmail.com', $subject, $message );
            $count_sendmail_success ++;
        } else {
            echo 'fail' .PHP_EOL;
            $count_sendmail_fail ++;
        }
        
    }
    return array('success' => $count_sendmail_success, 'fail' => $count_sendmail_fail);
}

//benchmark
$start = time();
$count_sendmail = weekly_companies_email();
$end = time();
echo 'Start: ' .date('H:i:s', $start) .PHP_EOL;
echo 'End: ' .date('H:i:s', $end) .PHP_EOL;
$tatal = $end - $start;
echo 'Total time: ' .$tatal .PHP_EOL;

//log
$path = '/var/www/vhosts/australiansolarquotes.com.au/public_html/crontab_log.txt';
//$path = '/home/bangnd/www/asq/crontab_log.txt';
$old_content = file_get_contents($path, true);

$content = $old_content .PHP_EOL
            .'------------------------------------' .PHP_EOL
            .'Start: ' .date('d-m-Y H:i:s', $start) .PHP_EOL
            .'End: ' .date('d-m-Y H:i:s', $end) .PHP_EOL
            .'Total time: ' .$tatal .PHP_EOL
            .'Total send email success: ' .$count_sendmail['success'] .PHP_EOL
            .'Total send email fail: ' .$count_sendmail['fail'] .PHP_EOL;

$file = fopen($path,"w");
fwrite($file, $content);
fclose($file);
die;
