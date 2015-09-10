<?php
#include 'inc/control.php';
include 'inc/helpers/helpers.php';
include 'inc/helpers/messages.php';
include 'modules/locations/location.php';
include 'modules/locations/init.php';
include 'modules/bubble/bubble.php';
include 'modules/logs/log.php';
include 'modules/form/form.php';

include 'inc/log.php';
include 'inc/edit-company.php';
include 'inc/edit-post.php';
include 'inc/postcodes.php';
include 'inc/reject.php';
include 'inc/helpers/leads.php';
include 'inc/helpers/company.php';
include 'inc/popup.php';
include 'inc/notification.php';
include 'inc/reports/report.php';
include 'inc/xero/xero.php';
include 'inc/helpers/gravity.php';
include 'inc/redirect.php';
include 'inc/breadcrumbs.php';
include 'inc/email.php';
include 'inc/crm/crm.php';
include 'inc/crm/report.php';
include 'inc/rss.php';

// Add new badge widget
include 'inc/widgets/badge.php';

include 'inc/shortcodes/quote_form.php';
include 'inc/shortcodes/short_quote_form.php';

include 'inc/cron/class-next-match.php';
include 'inc/helpers/facebook.php';
include 'inc/helpers/google.php';
include 'inc/helpers/twitter.php';
include 'inc/helpers/bitly.php';
include 'inc/helpers/push.php';
include 'inc/filters.php';

if ( is_admin() )
{
	include 'inc/class-next-match-report.php';
	include 'inc/class-upfront-report.php';
	include 'inc/class-leads-map.php';
	include 'inc/class-company-search.php';

	include 'inc/settings.php';
	include 'inc/leads-settings.php';
	include 'inc/leads-report.php';
	include 'inc/leads-entries.php';

	// Add reject helper to execute GF on Dashboard
	include 'inc/helpers/rejects.php';

	// Add Wholesale leads report page
	include 'inc/wholesale-report.php';
	include 'inc/supplier-report.php';

	if ( defined( 'DOING_AJAX' ) )
	{
		include 'inc/ajax.php';
	}
	
	include 'inc/security/class-export-modal.php';

	if ( isset( $_GET['action'] ) && $_GET['action'] == 'export-companies' )
	{
		include 'inc/companies_csv.php';
	}
}
else
{
	include 'inc/shortcodes.php';
	include 'inc/account.php';
}
include 'inc/cron-suspension.php';

add_action( 'admin_init', 'solar_management' );


function solar_management()
{
	// include 'inc/management.php';
}

function register_badge_widget()
{
	register_widget( 'ASQ\Widget\Badge' );
}

add_action( 'widgets_init', 'register_badge_widget' );

add_action( 'wp_enqueue_scripts', 'dequeue_scripts', 999 );
function dequeue_scripts()
{
	if ( ! is_page( 'fba-solar-quotes' ) )
		return;

	wp_dequeue_script( 'sl' );

	wp_enqueue_script( 'sl-child', CHILD_URL . 'js/script.js', array( 'jquery' ), '', true );

	// Allow modules to add more params to global Sl variable
	$sl_params = apply_filters( 'sl_js_params', array(
		'lazyLoader' => THEME_IMG . 'ui/blank.png',
		'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
	) );

	if ( 1 == get_query_var( 'contact' ) )
		$sl_params['contact'] = 1;

	wp_localize_script( 'sl-child', 'Sl', $sl_params );
}

add_action( 'admin_enqueue_scripts', 'solar_admin_dequeue_scripts', 999 );
function solar_admin_dequeue_scripts()
{
	wp_dequeue_script( 'sl-location-autocomplete' );
}

// Wholesale Management & Report
add_action( 'after_setup_theme', 'solar_wholesale' );
function solar_wholesale()
{
	if ( is_admin() || strpos( $_SERVER['REQUEST_URI'], '/affiliates' ) !== false || ! empty ( $_POST ) )
		require __DIR__ . '/modules/wholesale/wholesale.php';
}

if ( strpos( $_SERVER['REQUEST_URI'], '/wholesale/' ) !== false )
{
	wp_redirect( home_url() . '/affiliates' );
	exit;
}

register_nav_menu( 'primary_company', 'Primary Menu for Company' );
register_nav_menu( 'primary_wholesale', 'Primary Menu for Wholesale' );
register_nav_menu( 'primary_mobile', 'Primary Menu for Mobile' );

// Gravity Forms Custom Addresses (Australia)
add_filter( 'gform_address_types', 'australian_address', 10, 2 );

function australian_address( $address_types, $form_id )
{
	$address_types['australia'] = array(
		'label'       => 'Australia', //labels the dropdown
		'country'     => 'Australia', //sets Australia as default country
		'zip_label'   => 'Post Code', //what it says
		'state_label' => 'State', //as above
		'states'      => array(
			'',
			'ACT',
			'NSW',
			'NT',
			'QLD',
			'SA',
			'TAS',
			'WA',
			'VIC'
		)
	);

	return $address_types;
}

// Gravity Forms - Custom spinner
add_filter( "gform_ajax_spinner_url", "spinner_url", 10, 2 );

function spinner_url( $image_src, $form )
{
	return "/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif";
}


// WP RSS Aggregator
// overwrite post thumbnail with source thumbnail

// add_filter( 'wprss_et_image_priority', 'wprss_et_force_source_default_thumbnail' );


add_action( 'comment_post', 'solar_add_common_answers', 20 );

/**
 * Rating field for comments
 *
 * @param int $comment_id
 *
 * @return void
 **/
function solar_add_common_answers( $comment_id )
{
	global $post;
	if ( 'company' != $post->post_type )
		return;

	$comment = get_comment( $comment_id );
	if ( $comment->comment_parent )
	{
		$fields = array(
			'rating_sales',
			'rating_service',
			'rating_installation',
			'rating_quality',
			'rating_timelyness',
			'rating_price',
			'size_system',
			'spend',
			'state',
			'suburb'
		);
		foreach ( $fields as $field )
		{
			delete_comment_meta( $comment_id, $field );
		}
		return;
	}

	$fields = array( 'size_system', 'spend', 'state', 'suburb' );
	foreach ( $fields as $field )
	{
		if ( ! empty( $_POST[$field] ) )
			update_comment_meta( $comment_id, $field, $_POST[$field] );
	}
}

add_action( 'admin_enqueue_scripts', 'asq_admin_enqueue_scripts' );
/**
 * Enqueue admin styles for admin
 *
 * @return void
 */
function asq_admin_enqueue_scripts()
{
	wp_enqueue_style( 'asq-admin', CHILD_URL . 'css/admin.css' );
	wp_enqueue_script( 'solar-location', sl_locate_url( 'js/location.min.js' ), array( 'jquery' ), '', true );
}

add_filter( 'company_special_pages', 'solar_add_leads_page' );

/**
 * Special pages for company
 *
 * @param array $pages List of special pages for company
 *
 * @return array
 * @since    4.12
 */
function solar_add_leads_page( $pages )
{
	$pages['leads'] = 'user-admin/leads';

	return $pages;
}

add_filter( 'template_include', 'solar_company_leads_print' );

/**
 * Use a custom template for company leads print page
 *
 * @param string $template
 *
 * @return string
 */
function solar_company_leads_print( $template )
{
	if ( is_page() && get_the_ID() == sl_setting( 'company_page_leads' ) && isset( $_GET['action'] ) && 'print' == $_GET['action'] )
		$template = locate_template( 'templates/company/user-admin/company-leads-print.php' );

	return $template;
}

add_filter( 'template_include', 'solar_admin_company_leads_print', 100 );

/**
 * Use a custom template for company leads print page
 *
 * @param string $template
 *
 * @return string
 */
function solar_admin_company_leads_print( $template )
{
	if ( isset( $_GET['action'] ) && 'admin-leads-print' == $_GET['action'] )
		$template = locate_template( 'inc/company-leads-print.php' );

	return $template;
}


add_filter( 'template_include', 'solar_company_lead_request_print', 100 );

/**
 * Use a custom template for print lead request page
 *
 * @param string $template
 *
 * @return string
 */
function solar_company_lead_request_print( $template )
{
	if ( is_page() && get_the_ID() == sl_setting( 'company_page_leads' ) && isset( $_GET['action'] ) && 'print-request' == $_GET['action'] )
		$template = locate_template( 'templates/company/user-admin/company-lead-request-print.php' );

	return $template;
}

add_filter( 'template_include', 'solar_wholesale_leads_print', 99 );

function solar_wholesale_leads_print( $template )
{
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'print_wholesale' )
		$template = locate_template( 'inc/wholesale-report-print.php' );

	return $template;
}

add_action( 'wp_enqueue_scripts', 'solar_enqueue_scripts', 200 );

/**
 * Enqueue scripts for leads page
 *
 * @return void
 */
function solar_enqueue_scripts()
{
	wp_dequeue_style( 'sl-homepage' );

	if ( is_page() && get_the_ID() == sl_setting( 'company_page_leads' ) )
	{
		wp_enqueue_script( 'solar-leads', sl_locate_url( 'js/leads.js' ), array( 'jquery' ), '', true );
		wp_localize_script( 'solar-leads', 'Solar', array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonceFill' => wp_create_nonce( 'fill' ),
			'nonceLog'  => wp_create_nonce( 'log' ),
			'homeUrl'   => HOME_URL,
		) );
	}

	if ( is_page() && get_the_ID() == sl_setting( 'company_page_account' ) )
	{
		wp_enqueue_script( 'solar-account', sl_locate_url( 'js/account.js' ), array( 'jquery' ), '', true );
	}

	// Signup page
	if ( is_page() && get_the_ID() == sl_setting( 'company_page_signup' ) )
	{
		wp_enqueue_script( 'solar-signup', sl_locate_url( 'js/signup.js' ), array( 'jquery' ), '', true );
	}

	// Send email on single company page
	if ( is_singular( 'company' ) )
	{
		wp_enqueue_script( 'solar-single-company', sl_locate_url( 'js/single.js' ), array( 'jquery' ), '', true );
		wp_localize_script( 'solar-single-company', 'SolarSingle', array(
			'nonce' => wp_create_nonce( 'send' ),
			'id'    => get_the_ID(),
		) );
	}

	if ( url_contains( ['/solar-installers', 'solar-quotes', 'sign-up', 'installer-join-now'] ) )
	{
		wp_enqueue_script( 'solar-location', sl_locate_url( 'js/location.min.js' ), array( 'jquery' ), '', true );
		wp_enqueue_script( 'solar-gmap-api', 'https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places', array(), '', false );
	}

	if ( 'company' == sl_is_listing_archive() )
	{
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_style( 'jquery-ui' );
	}
}

add_action( 'company_signup', 'solar_signup_save_fields', 10, 3 );

/**
 * Save company info when signup
 *
 * @param $user_data
 * @param $post_id
 * @param $data
 *
 * @return void
 */
function solar_signup_save_fields( $user_data, $post_id, $data )
{
	if ( isset( $data['trading_name'] ) )
		update_post_meta( $post_id, 'trading_name', $data['trading_name'] );
	if ( isset( $data['company_abn'] ) )
		update_post_meta( $post_id, 'company_abn', $data['company_abn'] );

	// Todo: Save address
}

/**
 * Review comments template
 *
 * @param       $comment
 * @param array $args
 * @param int   $depth
 *
 * @return void
 */
function solar_comment( $comment, $args, $depth )
{
	$GLOBALS['comment'] = $comment;

	$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
	$total = 0;
	foreach ( $names as $name )
	{
		$rating = get_comment_meta( $comment->comment_ID, $name, true );
		$total += (int) $rating;
	}
	$average = $total / 6;
	?>
	<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment_container">
		<span style="display:none" itemprop="itemReviewed"><?php the_title(); ?></span>

		<?php echo solar_avatar( $comment, 60 ); ?>

		<div class="comment-text">

			<div class="author">
				<strong itemprop="author"><?php comment_author(); ?></strong>
				<?php
				if ( $depth == 1 )
				{
					$suburb = get_comment_meta( $comment->comment_ID, 'suburb', true );
					$state  = get_comment_meta( $comment->comment_ID, 'state', true );

					if ( $suburb && $state )
					{
						printf(
							__( '<span class="comment-location">from <span class="suburb">%s</span>, <span class="state">%s</span></span>', '7listings' ),
							$suburb,
							$state
						);
					}
				}
				?>
			</div>

			<?php
			if ( $depth == 1 )
				sl_star_rating( $average, 'type=rating' );
			?>

			<p class="meta">
				<time itemprop="datePublished" datetime="<?php echo get_comment_date( 'c' ); ?>" class="date"><?php echo get_comment_date(); ?></time>
			</p>

			<div class="description">
				<?php
				if ( $comment->comment_approved == '0' )
					echo '<p><em>' . __( 'Your comment is awaiting approval', '7listings' ) . '</em></p>';
				?>
				<p itemprop="reviewBody">
					<?php echo strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' ); ?>
				</p>

				<a class="comment-reply-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Reply', '7listings' ); ?></a>
			</div>

			<div class="clear"></div>

		</div>
		<div class="clear"></div>
	</div>
	<?php
}

add_action( 'company_special_page_after_login', 'solar_process_stop_buying_leads' );

/**
 * Process stop buying leads
 * This happens in both account and leads page, thus need to be done without checking page
 *
 * @return void
 */
function solar_process_stop_buying_leads()
{
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
		return;

	$company = current( $company );

	// Email variables
	$from_name    = get_bloginfo( 'name' );
	$from_email   = 'no-reply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', home_url() );
	$to           = 'installer@australiansolarquotes.com.au';
	$company_name = $company->post_title;
	global $current_user;
	get_currentuserinfo();
	$user_name = $current_user->display_name;
	if ( $current_user->first_name && $current_user->last_name )
		$user_name = "{$current_user->first_name} {$current_user->last_name}";

	// Stop buying leads
	if ( ! empty( $_POST['submit_stop'] ) )
	{
		$reason = isset( $_POST['cancel_reason'] ) ? $_POST['cancel_reason'] : 'too_many_temp';
		update_post_meta( $company->ID, 'cancel_reason', $reason );

		$other_reason = isset( $_POST['other_reason'] ) ? $_POST['other_reason'] : '';
		if ( $other_reason )
			update_post_meta( $company->ID, 'cancel_other_reason', $other_reason );
		else
			delete_post_meta( $company->ID, 'cancel_other_reason' );

		if ( 'too_many_temp' == $reason )
		{
			// Save old value of leads and set leads = 0
			$leads = get_post_meta( $company->ID, 'leads', true );
			update_post_meta( $company->ID, 'leads', 0 );
			update_post_meta( $company->ID, 'leads_old', $leads );

			// Save number of days of suspension
			if ( isset( $_POST['suspend_days'] ) )
			{
				// Set end time of suspension, for faster check in cron
				$days     = intval( $_POST['suspend_days'] );
				$end_time = time() + 86400 * $days;
				update_post_meta( $company->ID, 'suspend_end', $end_time );
				update_post_meta( $company->ID, 'suspend_days', $days );

				// Clear buying leads date if days >= 7
				if ( $days >= 7 )
					delete_post_meta( $company->ID, 'leads_paid' ); // Paid time
			}
		}
		else
		{
			delete_post_meta( $company->ID, 'leads_enable' );
		}

		$reasons = array(
			'too_many_temp'    => __( 'I have too many leads and wish to suspend temporarily', '7listings' ),
			'too_many_ind'     => __( 'I have too many leads and wish to suspend indefinitely', '7listings' ),
			'another_provider' => __( 'I am using another solar quote provider', '7listings' ),
			'poor_quality'     => __( 'I am not happy with the quality of leads', '7listings' ),
			'poor_amount'      => __( 'I am not happy with the amount of leads', '7listings' ),
			'poor_service'     => __( 'I am not happy with the service provided by Australian Solar Quotes', '7listings' ),
			'other'            => __( 'Other Reason', '7listings' ),
		);

		$real_reason = ( 'other' == $reason && $other_reason ) ? $other_reason : $reasons[$reason];

		// Log and notification in email
		$during_time = '.';
		if ( 'too_many_temp' == $reason )
		{
			// Save number of days of suspension
			if ( isset( $_POST['suspend_days'] ) )
			{
				solar_log( array(
					'time'        => date( 'Y-m-d H:i:s' ),
					'type'        => __( 'Leads', 'sch' ),
					'action'      => __( 'Suspend', 'sch' ),
					'description' => sprintf( __( '<span class="label">Reason:</span> <span class="detail">%s</span><br><span class="label">Days:</span> <span class="detail">%s <span class="suspend-end">until: %s</span></span>', '7listings' ), $real_reason, $days, date( 'd/m/Y H:i', $end_time ) ),
					'object'      => $company->ID,
					'user'        => $current_user->ID,
				) );

				$during_time = sprintf( ' temporarily from %s to %s', date( 'd/m/Y', $end_time - ( 86400 * $days ) ), date( 'd/m/Y', $end_time ) );
			}
		}
		else
		{
			solar_log( array(
				'time'        => date( 'Y-m-d H:i:s' ),
				'type'        => __( 'Leads', 'sch' ),
				'action'      => __( 'Cancel', 'sch' ),
				'description' => sprintf( __( '<span class="label">Reason:</span> <span class="detail">%s</span>', '7listings' ), $real_reason ),
				'object'      => $company->ID,
				'user'        => $current_user->ID,
			) );
		}

		// Email
		$subject = sprintf( __( '%s stops buying leads', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just stopped buying leads%s <br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Reason:</b> %s', '7listings' );

		$body = sprintf( $body, $during_time, $user_name, $company_name, $real_reason );
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}
}

add_action( 'company_special_page_after_login', 'solar_process_edit_leads' );

/**
 * Process leads forms
 *
 * @return void
 */
function solar_process_edit_leads()
{
	if ( get_the_ID() != sl_setting( 'company_page_leads' ) )
		return;

	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
		return;

	$company = current( $company );

	// Email variables
	$from_name    = get_bloginfo( 'name' );
	$from_email   = 'no-reply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', home_url() );
	$to           = 'installer@australiansolarquotes.com.au';
	$company_name = $company->post_title;
	global $current_user;
	get_currentuserinfo();
	$user_name = $current_user->display_name;
	if ( $current_user->first_name && $current_user->last_name )
		$user_name = "{$current_user->first_name} {$current_user->last_name}";

	// List of service types
	$service_types = array(
		'solar_pv'        => __( 'Solar PV', '7listings' ),
		'solar_hot_water' => __( 'Solar Hot Water', '7listings' ),
		'solar_ac'        => __( 'Solar A/C', '7listings' ),
	);
	// List of assessments
	$assessments = array(
		'onsite'      => __( 'Onsite', '7listings' ),
		'phone_email' => __( 'Phone/Email', '7listings' ),
	);
	// List of ages
	$ages = array(
		'retrofit'           => __( 'Retrofit', '7listings' ),
		'under_construction' => __( 'Under Construction', '7listings' ),
	);
	// Payment types
	$payment_types = solar_get_payment_methods();

	// Buy leads
	if ( ! empty( $_POST['submit_buy'] ) )
	{
		// Lead amount and frequency
		$num = isset( $_POST['leads'] ) ? intval( $_POST['leads'] ) : 0;
		update_post_meta( $company->ID, 'leads', $num );
		$frequency = isset( $_POST['lead_frequency'] ) ? strip_tags( $_POST['lead_frequency'] ) : 'month';
		update_post_meta( $company->ID, 'lead_frequency', $frequency );

		update_post_meta( $company->ID, 'leads_enable', 1 );
		update_post_meta( $company->ID, 'leads_paid', time() ); // Paid time

		$payment_type = isset( $_POST['leads_payment_type'] ) ? $_POST['leads_payment_type'] : 'direct';
		update_post_meta( $company->ID, 'leads_payment_type', $payment_type );

		// Remove all cancel meta fields
		delete_post_meta( $company->ID, 'cancel_reason' );
		delete_post_meta( $company->ID, 'cancel_other_reason' );
		delete_post_meta( $company->ID, 'suspend_end' );
		delete_post_meta( $company->ID, 'leads_old' );

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Buy', 'sch' ),
			'description' => sprintf( __( '<span class="label">Leads:</span> <span class="detail">%s</span><br><span class="label">Payment Type:</span> <span class="detail">%s</span>', '7listings' ), $num, $payment_types[$payment_type] ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s starts buying leads', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just start buying leads.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Leads:</b> %s<br>
		<b>Payment Type:</b> %s', '7listings' );

		$body = sprintf( $body, $user_name, $company_name, $num, $payment_types[$payment_type] );
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit amount
	if ( ! empty( $_POST['submit_amount'] ) )
	{
		// Lead amount and frequency
		$num = isset( $_POST['leads'] ) ? intval( $_POST['leads'] ) : 0;
		update_post_meta( $company->ID, 'leads', $num );
		$frequency = isset( $_POST['lead_frequency'] ) ? strip_tags( $_POST['lead_frequency'] ) : 'month';
		update_post_meta( $company->ID, 'lead_frequency', $frequency );

		// If company suspended buying leads for < 7 days: keep its buying date
		// If > 7 days: reset buying date
		$cancel_reason = get_post_meta( $company->ID, 'cancel_reason', true );
		if ( 'too_many_temp' == $cancel_reason )
		{
			$days = intval( get_post_meta( $company->ID, 'suspend_days', true ) );

			// Set purchasing date to NOW
			if ( $days >= 7 )
				update_post_meta( $company->ID, 'leads_paid', time() ); // Paid time
		}

		// Remove all cancel meta fields
		delete_post_meta( $company->ID, 'cancel_reason' );
		delete_post_meta( $company->ID, 'cancel_other_reason' );
		delete_post_meta( $company->ID, 'suspend_end' );
		delete_post_meta( $company->ID, 'leads_old' );

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Leads:</span> <span class="detail">%s</span>', '7listings' ), $num ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed amount of leads', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed amount of leads.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Leads:</b> %s<br>
		<b>Lead Frequency:</b> %s', '7listings' );

		$body = sprintf( $body, $user_name, $company_name, $num, $frequency );
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit service radius
	if ( ! empty( $_POST['submit_radius'] ) )
	{
		$fields = array( 'service_radius', 'leads_service_radius', 'service_postcodes' );
		$old    = array();
		$new    = array();
		foreach ( $fields as $field )
		{
			$old[$field] = get_post_meta( $company->ID, $field, true );
			$new[$field] = empty( $_POST[$field] ) ? '' : $_POST[$field];
			update_post_meta( $company->ID, $field, $new[$field] );
		}

		// Log
		$description = array();
		if ( $old['service_radius'] != $new['service_radius'] )
			$description[] = sprintf( __( '<span class="label">Service type:</span> <span class="detail">%s</span>', 'sch' ), ucwords( $new['service_radius'] ) );
		if ( $old['leads_service_radius'] != $new['leads_service_radius'] )
			$description[] = sprintf( __( '<span class="label">Service radius:</span> <span class="detail">%s</span> km', 'sch' ), $new['leads_service_radius'] );
		if ( $old['service_postcodes'] != $new['service_postcodes'] )
			$description[] = sprintf( __( '<span class="label">Postcodes:</span> <span class="detail">%s</span>', 'sch' ), $new['service_postcodes'] );
		if ( ! empty( $description ) )
		{
			solar_log( array(
				'time'        => date( 'Y-m-d H:i:s' ),
				'type'        => __( 'Leads', 'sch' ),
				'action'      => __( 'Edit', 'sch' ),
				'description' => implode( '<br>', $description ),
				'object'      => $company->ID,
				'user'        => $current_user->ID,
			) );
		}

		// Email
		$subject = sprintf( __( '%s edited service radius', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just edited service radius.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Service type:</b> %s &rarr; %s<br>
		<b>Service radius:</b> %s km &rarr; %s km<br>
		<b>Postcodes:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			ucwords( $old['service_radius'] ), ucwords( $new['service_radius'] ),
			$old['leads_service_radius'], $new['leads_service_radius'],
			$old['service_postcodes'], $new['service_postcodes']
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit lead type
	if ( ! empty( $_POST['submit_type'] ) )
	{
		$old = get_post_meta( $company->ID, 'leads_type', true );
		$new = isset( $_POST['leads_type'] ) ? $_POST['leads_type'] : array();
		update_post_meta( $company->ID, 'leads_type', $new );

		$old = ucwords( implode( ', ', (array) $old ) );
		$new = ucwords( implode( ', ', (array) $new ) );

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Lead Type:</span> <span class="detail">%s</span>', '7listings' ), $new ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed lead type', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed lead type.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Lead Type:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			$old, $new
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit service type
	if ( ! empty( $_POST['submit_service_type'] ) )
	{
		$old = get_post_meta( $company->ID, 'service_type', true );
		$new = isset( $_POST['service_type'] ) ? $_POST['service_type'] : array();
		update_post_meta( $company->ID, 'service_type', $new );

		foreach ( (array) $old as $k => $v )
		{
			$old[$k] = $service_types[$v];
		}
		foreach ( (array) $new as $k => $v )
		{
			$new[$k] = $service_types[$v];
		}

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Service Type:</span> <span class="detail">%s</span>', '7listings' ), implode( ', ', $new ) ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed service type', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed service type.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Service Type:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			implode( ', ', $old ), implode( ', ', $new )
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit assessment
	if ( ! empty( $_POST['submit_assessment'] ) )
	{
		$old = get_post_meta( $company->ID, 'assessment', true );
		$new = isset( $_POST['assessment'] ) ? $_POST['assessment'] : array();
		update_post_meta( $company->ID, 'assessment', $new );

		foreach ( (array) $old as $k => $v )
		{
			$old[$k] = $assessments[$v];
		}
		foreach ( (array) $new as $k => $v )
		{
			$new[$k] = $assessments[$v];
		}

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Assessment:</span> <span class="detail">%s</span>', '7listings' ), implode( ', ', $new ) ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed assessment', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed assessment.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Assessment:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			implode( ', ', $old ), implode( ', ', $new )
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit age
	if ( ! empty( $_POST['submit_age'] ) )
	{
		$old = get_post_meta( $company->ID, 'age', true );
		$new = isset( $_POST['age'] ) ? $_POST['age'] : array();
		update_post_meta( $company->ID, 'age', $new );

		foreach ( (array) $old as $k => $v )
		{
			$old[$k] = $ages[$v];
		}
		foreach ( (array) $new as $k => $v )
		{
			$new[$k] = $ages[$v];
		}

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Age:</span> <span class="detail">%s</span>', '7listings' ), implode( ', ', $new ) ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed age', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed age.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Age:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			implode( ', ', $old ), implode( ', ', $new )
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}

	// Edit email
	if ( ! empty( $_POST['submit_email'] ) )
	{
		$old = get_post_meta( $company->ID, 'leads_email', true );
		$new = isset( $_POST['leads_email'] ) ? $_POST['leads_email'] : '';
		update_post_meta( $company->ID, 'leads_email', $new );

		// Log
		solar_log( array(
			'time'        => date( 'Y-m-d H:i:s' ),
			'type'        => __( 'Leads', 'sch' ),
			'action'      => __( 'Edit', 'sch' ),
			'description' => sprintf( __( '<span class="label">Leads recipient:</span> <span class="detail">%s</span>', '7listings' ), $new ),
			'object'      => $company->ID,
			'user'        => $current_user->ID,
		) );

		// Email
		$subject = sprintf( __( '%s changed leads recipient', '7listings' ), $company_name );
		$body    = __( 'Dear admin,<br><br>
		There is one company owner has just changed leads recipient.<br><br>
		<b>User:</b> %s<br>
		<b>Company:</b> %s<br>
		<b>Leads recipient:</b> %s &rarr; %s', '7listings' );

		$body = sprintf(
			$body, $user_name, $company_name,
			$old, $new
		);
		wp_mail( $to, $subject, $body, array( 'Content-type: text/html', "From: {$from_name} <{$from_email}>" ) );
	}
}

add_filter( 'sch_company_meta', 'solar_company_meta' );

/**
 * Add meta fields to be logged when edit company
 *
 * @param array $meta
 *
 * @return array
 */
function solar_company_meta( $meta )
{
	$meta = array_merge( $meta, array(
		'trading_name'         => __( 'Company Name', 'sch' ),
		'company_abn'          => __( 'ABN', 'sch' ),
		'service_radius'       => __( 'Service Area', 'sch' ),
		'leads_service_radius' => __( 'Service Radius', 'sch' ),
		'service_postcodes'    => __( 'Service Postcodes', 'sch' ),
	) );

	return $meta;
}

add_action( 'company_signup', 'solar_signup', 10, 3 );

/**
 * Add more meta (invoice recipient) for company when signup
 *
 * @param WP_User $user_data
 * @param int     $post_id
 * @param array   $data
 *
 * @return void
 */
function solar_signup( $user_data, $post_id, $data )
{
	$fields = array(
		'invoice_name',
		'invoice_position',
		'invoice_email',
		'invoice_phone',
		'invoice_direct_line',
		'paypal_email',
	);

	foreach ( $fields as $field )
	{
		if ( isset( $data[$field] ) )
			update_post_meta( $post_id, $field, $data[$field] );
	}

	if ( isset( $data['paypal_enable'] ) )
		update_post_meta( $post_id, 'paypal_enable', 1 );
}

add_filter( 'gform_form_tag_1', 'solar_css_class_form_quote' );

/**
 * Add CSS class for company owner, guess to hide private field on quote form
 *
 * @param string $tag
 *
 * @return string
 */
function solar_css_class_form_quote( $tag )
{
	$class = '';
	if ( ! is_user_logged_in() )
	{
		$class = 'hide-private';
	}
	else
	{
		$current_user = wp_get_current_user();
		if ( in_array( 'company_owner', $current_user->roles ) )
			$class = 'hide-private';
	}
	if ( $class )
		$tag = str_replace( '>', " class='$class'>", $tag );

	return $tag;
}

add_filter( 'sl_setting-social_buttons', 'solar_social_buttons' );

/**
 * Change option of social buttons on single company page
 * Display social buttons above post content
 *
 * @param array $value
 *
 * @return array
 */
function solar_social_buttons( $value )
{
	if ( ! is_singular( 'company' ) )
		return $value;

	$value['position'] = 'top';

	return $value;
}

function p( $object )
{
	echo '<pre>';
	print_r( $object );
	exit;
}

function v( $object )
{
	echo '<pre>';
	var_dump( $object );
	exit;
}

// Remove validation message from #27
add_filter( 'gform_validation_message_27', '__return_empty_string' );

add_action( 'template_redirect', 'solar_intranet_area' );

function solar_intranet_area()
{
	global $post;

	if ( ! empty ( $post ) && ( $post->ID === 17687 || $post->post_parent === 17687 ) )
	{
		$allowed_roles = ['administrator', 'author', 'editor', 'client_care_manager', 'account'];

		$redirect = true;

		foreach ( $allowed_roles as $role ) 
		{
			if ( current_user_can( $role ) )
				$redirect = false;
		}

		if ( $redirect )
		{
			wp_redirect( '/', 301 );
			exit( 0 );
		}
	}
}

// Priority = 1: runs first, so we can remove Disqus hook
add_filter( 'comments_template', 'asq_comments_template', 1 );

/**
 * Use Disqus for 'post' only
 *
 * @param string $template
 *
 * @return string
 */
function asq_comments_template( $template )
{
	if ( function_exists( 'dsq_comments_template' ) && 'post' != get_post_type() )
		remove_filter( 'comments_template', 'dsq_comments_template' );

	return $template;
}

// Remove Disqus error message
remove_action( 'pre_comment_on_post', 'dsq_pre_comment_on_post' );

function solar_custom_login_logo()
{ ?>
	<style type="text/css">
		body.login div#login h1 a {
			background: url('https://www.australiansolarquotes.com.au/wp-content/uploads/2014/09/Australian-Solar-Power-Quotes-Logo-2014.jpg') no-repeat;
			padding-bottom: 30px;
			font-size: 0;
			width: 300px;
			height: 120px;
			display: block;
		}

		.form-signin-heading {
			display: none;
		}

		#login h1 {
			margin: 20px 32px 0 32px !important;
		}
	</style>
	<?php
}

add_action( 'login_enqueue_scripts', 'solar_custom_login_logo' );

add_action( 'add_meta_boxes', 'remove_location_box', 1 );

function remove_location_box()
{
	remove_meta_box( 'locationdiv', 'post', 'side' );
	remove_meta_box( 'locationdiv', 'company', 'side' );
}

add_action( 'delete_post', 'prevent_delete_post' );

function prevent_delete_post()
{
	global $post_type;

	if ( isset( $post_type ) && $post_type === 'company' )
		return false;
}


add_filter( 'gform_validation_message_54', 'solar_custom_validation_message', 10, 1 );

function solar_custom_validation_message( $validation_message )
{
	$validation_message = '<div class="validation_error">We\'ve already got your details recorded. We will be in touch with you shortly.</div>';
	return $validation_message;
}

add_filter( 'wp_nav_menu_topnav_items', 'solar_loginout_menu_link' );

function solar_loginout_menu_link( $menu )
{
	$loginout = '<li class="login-url">' . wp_loginout( $_SERVER['REQUEST_URI'], false ) . '</li>';

	$menu .= $loginout;

	return $menu;
}

add_filter( 'the_content', 'solar_append_shortcode' );

function solar_append_shortcode( $content )
{
	if ( ! is_singular( 'post' ) )
		return $content;

	if ( ! str_contains( $content, '_footer_newsletter' ) )
		$content .= do_shortcode( '[do action="Post_footer_newsletter"/]' );

	return $content;
}

/**
 * Add 'place' query var since removing 'location' taxonomy support
 * @param  Filter Input $public_query_vars
 *
 * @return Array the query vars to add
 */
function solar_add_query_vars( $public_query_vars )
{
	$public_query_vars[] = 'place';

	return $public_query_vars;
}

add_filter( 'query_vars', 'solar_add_query_vars' );

function solar_get_payment_methods( $show_label = true )
{
	$methods = array(
		'direct'  => 'Direct Debit',
		'post'    => 'Post Pay',
		'upfront' => 'Upfront'
	);

	if ( ! $show_label )
		return array_keys( $methods );

	return $methods;
}

/**
 * Redirect all Black Listed IPs to Google
 */
add_action( 'init', function ()
{
	if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
	{
		$black_list = sl_setting( 'ip_blacklist' );

		if ( empty( $black_list ) )
			return;

		if ( str_contains( $black_list, ',' ) )
			$black_list = explode( ',', $black_list );

		if ( str_contains( $_SERVER['HTTP_X_FORWARDED_FOR'], $black_list ) )
		{
			header( 'location: https://www.google.com/#q=australiansolarquotes.com.au' );
			exit;
		}
	}
} );

add_filter( 'template_include', 'author_template', 100 );

/**
 * Author page
 */
function author_template( $template )
{
	if ( 'post' == sl_is_listing_archive() )
	{
		if ( url_contains( array( '/author/' ) ) )
			$template = locate_template( 'author.php' );
	}

	return $template;
}

function asq_send_purchase_another_lead_pack_email( $company_id, $percent = 75 )
{
	if ( get_post_meta( $company_id, 'leads_payment_type', true ) != 'upfront' )
		return;

	$company      = get_post( $company_id );
	$company_name = $company->post_title;
	$owner_id     = get_post_meta( $company_id, 'user', true );

	$owner_name = $company_name;

	if ( $owner_id )
	{
		$owner       = get_userdata( $owner_id );
		$owner_email = $owner->user_email;
		$owner_name  = ( ! empty( $owner->display_name ) ) ? $owner->display_name : $company_name;
	}

	$subject = "{$company_name} - Time to recharge!";

	if ( $percent == 75 )
	{
		// Prepare email content
		$message = "<p>Hi {$owner_name},</p>
			<p>We are just letting you know that your account balance is low and to keep the leads flowing, you'll need to recharge!<p>
			<p><a href=\"https://www.australiansolarquotes.com.au/my-account/leads/\">Click here</a> to recharge</p>
			<p>Have a great day</p>
		";
	}
	else
	{
		$subject = "{$company_name} - Your leads have stopped!";

		$message = "<p>Hi {$owner_name},</p>
			<p>Your leads have suspended because you've reached your limit. To keep the flow, you will need to recharge!<p>
			<p><a href=\"https://www.australiansolarquotes.com.au/my-account/leads/\">Click here</a> to recharge</p>
			<p>Have a great day</p>
		";
	}

	// Send email to owner and accounts@australiansolarquotes.com.au
	wp_mail( $owner_email, $subject, $message );
	wp_mail( 'tan@fitwp.com', $subject, $message );

	if ( $percent === 100 )
		wp_mail( 'accounts@australiansolarquotes.com.au', $subject, $message );
}

function asq_send_reached_notification( $company_id )
{
	if ( get_post_meta( $company_id, 'leads_payment_type', true ) == 'upfront' )
		return;

	$current_count  = solar_company_leads_logs( $company_id );

	if ( $current_count <= 2 )
		return;
	
	$limit 			= intval( get_post_meta( $company_id, 'leads', true ) );
	$leads_cap_notification = sl_setting( 'leads_cap_notification' );
	
	$notifications = [];
	
	foreach ( $leads_cap_notification as $notification )
	{
		if ( empty( $notification['percent'] ) || empty( $notification['title'] ) || empty( $notification['content'] ) )
			continue;

		$notifications[$notification['percent']] = $notification;
	}

	$numeric_percent 	= numeric_percent( $limit, array_keys( $notifications ) );

	if ( ! in_array( $current_count, $numeric_percent ) )
		return;
	
	$value_percent 	= array_swap( $numeric_percent );
	$current_count_percent 	= $value_percent[$current_count];

	$template 		= $notifications[$current_count_percent];

	if ( empty( $template['title'] ) || empty( $template['content'] ) )
		return;

	$company      	= get_post( $company_id );
	$company_name 	= $company->post_title;
	$owner_id     	= get_post_meta( $company_id, 'user', true );

	$owner_name = $company_name;

	if ( $owner_id )
	{
		$owner       = get_userdata( $owner_id );
		$owner_email = $owner->user_email;
		$owner_name  = ( ! empty( $owner->display_name ) ) ? $owner->display_name : $company_name;
	}

	$transform = [
		'{{owner name}}' 	=> $owner_name,
		'{{company name}}' 	=> $company_name
	];

	$subject 	= strtr( $template['title'], $transform );
	$message 	= strtr( $template['content'], $transform );
	
	// Send email to owner and accounts@australiansolarquotes.com.au
	wp_mail( $owner_email, $subject, $message );
	wp_mail( 'tan@fitwp.com', $subject, $message );
	wp_mail( 'accounts@australiansolarquotes.com.au', $subject, $message );
}

function get_company_callback()
{

	global $wpdb;
	if ( isset( $_POST['ids'] ) && isset( $_POST['page'] ) )
	{
		$ids    = trim( $_POST['ids'] );
		$page   = (int) $_POST['page'];
		$limit  = 30;
		$offset = $limit * ( $page - 1 );

		$q     = "
			SELECT * FROM asq_posts
			WHERE ID IN($ids)
			AND post_type 	= 'company'
			AND post_status NOT IN ( 'draft', 'trash' )
			LIMIT {$limit}
			OFFSET {$offset}
		";
		$posts = $wpdb->get_results( $q );
		$html  = '';
		foreach ( $posts as $post )
		{
			// Companies with ratings first
			$average = Sl_Company_Helper::get_average_rating( $post->ID );

			$html .= '<article class="company-listing">';
			$html .= '<div class="company-listing-logo">';
			//logo
			$logo = '';
			if ( has_post_thumbnail( $post->ID ) )
			{
				$logo = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
				$logo = $logo[0];
			}

			if ( ! empty( $logo ) && in_array( $membership, array( 'gold', 'silver' ) ) )
			{
				$html .= '<a href="' . get_permalink( $post->ID ) . '" title="">';
				$html .= '<img src="' . $logo . '" alt="' . $post->post_title . '">';
				$html .= '</a>';
			}

			$html .= '</div>';

			$html .= '<div class="company-listing-content">';

			$html .= '<div class="company-listing-content-left">';
			//title
			$html .= '<div class="company-title"><h3>';
			$html .= '<a class="title" href="' . get_permalink( $post->ID ) . '" title="' . $post->post_title . '">';
			$html .= $post->post_title;
			$html .= '</a>';
			$html .= '</h3></div>';

			//rating
			if ( $average )
			{
				$rate = sl_star_rating( $average, 'type=rating&echo=0' );
				$html .= '<div class="company-rating-phone">' . $rate . '<p>' . Sl_Company_Helper::get_no_reviews( $post->ID ) . ' reviews </p>' . '</div>';
				$html .= '<div class="company-rating rating">' . $rate . '<p>Based on ' . Sl_Company_Helper::get_no_reviews( $post->ID ) . ' reviews </p>' . '</div>';
			}
			else
			{
				$html .= '<div class="company-rating rating none">No Reviews</div>';
			}

			if ( current_user_can( 'administrator' ) )
			{
				$html .= '<span class="label label-warning">' . $membership . '</span>';
			}

			//company servicing
			$servicing = '';
			if ( isset( $_GET['s2'] ) && $_GET['s2'] !== '' )
			{
				$servicing .= $_GET['s2'] . ', ';
			}
			else
			{
				$area = get_post_meta( $post->ID, 'area', true );
				if ( $area !== '' )
					$servicing .= $area . ', ';
			}
			$state = get_post_meta( $post->ID, 'state', true );
			if ( ! empty( $state ) )
				$servicing .= $state . ', ';
			$postcode = get_post_meta( $post->ID, 'postcode', true );
			if ( ! empty( $postcode ) )
				$servicing .= $postcode;
			$html .= '<div class="company-servicing"><p>' . $servicing . '</p></div>';

			$html .= '<div class="company-types"><p>' . sl_get_type_of_company( $post->ID ) . '</p></div>';

			$html .= '</div>';

			$html .= '<div class="company-listing-content-right">';

			//phone
			$phone   = get_post_meta( $post->ID, 'phone', true );
			$pattern = '/(\\d{2})(\\d{4})(\\d{4})/';
			$phone   = preg_replace( $pattern, '$1 $2 $3', $phone );
			$html .= '<div class="company-phone"><span>' . $phone . '</span></div>';

			//Review button
			$html .= '<div class="company-add-review">';
			$html .= '<a class="button" href="' . get_permalink( $post->ID ) . '" >Reviews</a>';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '</div>';
			$html .= '</article>';
		}


		$data = array(
			'error_code' => 0,
			'posts'      => $html,
			'page'       => $page,
		);
	}
	else
	{
		$data = array(
			'error_code' => 1,
			'message'    => 'data not found',
		);
	}


	header( "Content-Type: application/json" );
	echo json_encode( $data );

	wp_die();
}

add_action( 'wp_ajax_nopriv_get_company', 'get_company_callback' );
add_action( 'wp_ajax_get_company', 'get_company_callback' );

function get_review_of_company_callback()
{

	if ( isset( $_POST['id'] ) && isset( $_POST['page'] ) )
	{
		$page     = $_POST['page'];
		$limit    = get_option( 'comments_per_page' );
		$offset   = $limit * ( $page - 1 );
		$post_id  = $_POST['id'];
		$comments = get_comments( array( 'post_id' => $post_id ) );
		$html     = wp_list_comments( array( 'callback' => 'solar_comment', 'per_page' => 10, 'page' => $page, 'echo' => false ), $comments );
		$data     = array(
			'error_code' => 0,
			'comments'   => $html,
			'page'       => $page,
		);
	}
	else
	{
		$data = array(
			'error_code' => 1,
			'message'    => 'data not found',
		);
	}

	header( "Content-Type: application/json" );
	echo json_encode( $data );

	wp_die();
}

add_action( 'wp_ajax_nopriv_get_review_of_company', 'get_review_of_company_callback' );
add_action( 'wp_ajax_get_review_of_company', 'get_review_of_company_callback' );

function solar_avatar( $id, $size, $default = '', $alt = false )
{
	if ( isset( $_COOKIE['retina'] ) )
		$size *= 2;

	return '<figure class="thumbnail">' . get_avatar( $id, $size, $default, $alt ) . '</figure>';
}

add_action( 'gform_entry_created', 'solar_send_feed', 11, 2 );
function solar_send_feed( $entry, $form )
{
	if ( $form['id'] == 1 )
	{
		// Process feed addon
		$gfa = GFMailChimp::get_instance();
		$gfa->maybe_process_feed( $entry, $form );
	}
}

add_action( 'gform_after_submission_63', 'gform_create_lead_entry', 10, 2 );

/**
 * When Form #63 has created, create a record on #1 also
 * @param  Gravity Form Entry $entry
 * @param  Gravity Form Form $form
 * @return void
 */
function gform_create_lead_entry( $entry, $form )
{
	unset( $entry['id'] );

	$entry['form_id'] = 1;

	$entry['17.1'] = $entry['91.1'];
	$entry['17.3'] = $entry['91.3'];
	$entry['17.4'] = $entry['91.4'];
	$entry['17.5'] = $entry['91.5'];

	$entry[30] = 'Home';

	unset( $entry['91.1'] );
	unset( $entry['91.3'] );
	unset( $entry['91.4'] );
	unset( $entry['91.5'] );

	$entry[91] = $entry[89];

	unset( $entry[89] );

	$id = \GFAPI::add_entry( $entry );

	// Get inserted lead
	$lead = \GFAPI::get_entry( $id );

	// Run match leads function
	$form = \GFAPI::get_form( 1 );

	do_action( 'gform_entry_created', $lead, $form );
}

function solar_gf_field_make_report_comment( $field_content, $field, $value, $zero, $form_id )
{
	if ( $form_id == 61 )
	{
		$field_content = str_replace( '[company_name]', get_the_title(), $field_content );
	}
	return $field_content;
}

add_filter( 'gform_field_content', 'solar_gf_field_make_report_comment', 10, 5 );


function form_submit_button( $button, $form )
{
	$footer_form = '<input type="hidden" value="" id="comment_id" name="comment_id">';
	$footer_form .= $button;
	$footer_form .= '<button class="button" data-dismiss="modal" aria-hidden="true">Close</button>';
	return $footer_form;
}

add_filter( 'gform_submit_button_61', 'form_submit_button', 10, 2 );

function set_post_content( $entry, $form )
{
	global $wpdb;
	$wpdb->insert( 'asq_rg_lead_meta',
		array(
			'lead_id'    => $entry['id'],
			'meta_key'   => 'comment_id',
			'meta_value' => $_POST['comment_id'],
			'form_id'    => $form['id']
		),
		array( '%d', '%s', '%s', '%d' )
	);

	//update comment
	// $commentarr = array();
	// $commentarr['comment_ID'] = $_POST['comment_id'];
	// $commentarr['comment_approved'] = 0;
	// wp_update_comment( $commentarr );

	//send mail
	//wp_mail( 'admin@ausraliansolarquotes.com.au', 'Report comment from user', 'Report comment from user' );
	//wp_mail( 'bang.nguyen47@gmail.com@gmail.com', 'Report comment from user', 'Report comment from user' );
}

add_action( 'gform_after_submission_61', 'set_post_content', 10, 2 );

function pus_notification( $ID, $post )
{
	$device_token = '439a9b9736d6156317ddd3990849179aac0fffca491a235bec69610ec3b98499';
	$message      = 'Test form ASQ';
	$push         = PushNotification::pushIos( $device_token, $message, $return_fields = array( 'post' => $post ) );
}

// add_action( 'publish_post', 'pus_notification', 10, 2 );


function solar_json_ld_ratings()
{
	global $wpdb, $post;

	if ( ! is_singular( 'company' ) )
		return;

	$data = $wpdb->get_row( "
		SELECT COUNT(meta_value) AS count, SUM(meta_value) AS total
		FROM {$wpdb->commentmeta} AS cm
		LEFT JOIN {$wpdb->comments} AS c ON cm.comment_id = c.comment_ID
		WHERE meta_key LIKE '%rating%'
		AND comment_post_ID = '{$post->ID}'
		AND comment_approved = '1'
	" );

	if ( empty( $data ) )
	{
		$count   = 0;
		$average = 0;
	}
	else
	{
		$count   = intval( $data->count );
		$average = $count ? (float) $data->total / $count : 0;
	}

	$total_reviews = ( $count > 6 ) ? $count / 6 : 0;

	$average = number_format_i18n( $average, 2 );

	$json_ld = [
		"@context"        => "http://schema.org/",
		"@type"           => "LocalBusiness",
		"name"            => get_the_title(),
		"aggregateRating" => [
			"@type"       => "AggregateRating",
			"ratingValue" => $average,
			"bestRating"  => "5",
			"worstRating" => "0",
			"ratingCount" => $total_reviews
		]
	];

	if ( ! empty( get_the_excerpt() ) )
		$json_ld['description'] = get_the_excerpt();

	$telephone = get_post_meta( $post->ID, 'phone', true );
	if ( ! empty( $phone ) )
		$json_ld['telephone'] = $telephone;

	$info = array( 'address', 'area', 'state', 'postcode', 'tripadvisor' );

	$website = get_post_meta( $post->ID, 'website', true );
	if ( ! empty( $website ) )
		$json_ld['url'] = $website;

	$address  = get_post_meta( $post->ID, 'address', true );
	$address2 = get_post_meta( $post->ID, 'address2', true );
	$address  = implode( ', ', array_filter( compact( 'address', 'address2' ) ) );
	$area     = get_post_meta( $post->ID, 'area', true );
	$state    = get_post_meta( $post->ID, 'state', true );
	$postcode = get_post_meta( $post->ID, 'postcode', true );

	if ( ! empty( $area ) && ! empty( $state ) && ! empty( $postcode ) && ! empty( $address ) )
	{
		$state = asq_states_format( $state );

		$json_ld['address'] = [
			"@type"           => "PostalAddress",
			"addressLocality" => $area,
			"addressRegion"   => $state,
			"postalCode"      => $postcode,
			"streetAddress"   => $address
		];
	}

	$json_ld = json_encode( $json_ld );

	echo '<script type="application/ld+json">' . $json_ld . '</script>';
}

add_action( 'wp_head', 'solar_json_ld_ratings' );

// Remove schema.org tag for body only for singular company
add_action( 'after_setup_theme', function ()
{
	if ( url_contains( '/solar-installers' ) )
		remove_action( 'sl_body', 'sl_schema_body_tag' );
} );

if ( isset( $_POST['sticky_postcode'] ) && intval( $_POST['sticky_postcode'] ) > 0 )
{
	$postcode = intval( $_POST['sticky_postcode'] );

	if ( $postcode < 0 || $postcode > 9999 )
		return;

	// Insert to Sticky Postcode Form
	$check = GFAPI::add_entry([
		'form_id' => 57,
		2 => $postcode
	]);

	// Redirect to /solar-quotes
	if ( $check )
	{
		wp_redirect( '/solar-quotes?postcode=' . $postcode , 301 );
		exit;
	}
}