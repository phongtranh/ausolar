<?php

add_action( 'company_edit_tab_admin_after', 'solar_admin' );

/**
 * Show leads form
 *
 * @return void
 */
function solar_admin()
{
	$membership_paid_override = get_post_meta( get_the_ID(), 'membership_paid_override', true );
	$user_id 				  = get_post_meta( get_the_ID(), 'user', true );
	$membership_type 		  = get_user_meta( $user_id, 'membership', true )
	?>
	<p>
		<label><?php _e( 'Company Name', '7listings' ); ?></label>
		<input type="text" name="trading_name" size="6" value="<?php echo get_post_meta( get_the_ID(), 'trading_name', true ); ?>">
	</p>
	<p>
		<label><?php _e( 'ABN', '7listings' ); ?></label>
		<input type="text" name="company_abn" size="6" value="<?php echo get_post_meta( get_the_ID(), 'company_abn', true ); ?>">
	</p>
	<p>
		<label><?php _e( 'What best describes your company', '7listings' ); ?></label>
		<select name="range">
			<?php
			$selected = get_post_meta( get_the_ID(), 'range', true );
			if ( ! $selected )
				$selected = 'local';
			SL_Form::options( $selected, array(
				'nationwide' => __( 'Nationwide', '7listings' ),
				'statewide'  => __( 'Statewide', '7listings' ),
				'local'      => __( 'Local', '7listings' ),
			) );
			?>
		</select>
	</p>

	<?php if ( ! empty( $membership_type ) ) : ?>
	<div class="sl-settings">
		<div class="sl-label">
			<label><?php _e( 'Override payment status?', '7listings' ); ?></label>
		</div>
		<div class="sl-input">
			<select name="membership_paid_override">
			<?php
				$selected = ( ! empty( $membership_paid_override ) ) ? $membership_paid_override : 'no_override';
				Sl_Form::options( $selected, array(
					'no_override' 	=> 'No Override',
					'not_paid'		=> 'Not Paid',
					'paid' 			=> 'Paid'
				) );
			?>
			</select>
		</div>
	</div>
	<?php endif;

	if ( isset( $user_id ) && intval( $user_id ) > 0 ) :
		$switch_to_user_url = wp_nonce_url( add_query_arg( array(
			'action'  => 'switch_to_user',
			'user_id' => $user_id
		), wp_login_url() ), "switch_to_user_{$user_id}" );
	?>
	<a href="<?php echo $switch_to_user_url ?>" class="button">Switch to user</a>
	<?php
	endif;
}

add_action( 'company_edit_tab_reports_after', 'solar_reports' );

/**
 * Show reports table
 * @return void
 */
function solar_reports()
{
	?>
	<h2><?php _e( 'Leads Report', '7listings' ); ?></h2>
	<table width="100%">
		<tr>
			<th scope="col"><?php _e( 'Invoice ID', '7listings' ); ?></th>
			<th scope="col"><?php _e( 'Month', '7listings' ); ?></th>
			<th scope="col"><?php _e( '#Leads', '7listings' ); ?></th>
			<th scope="col"><?php _e( 'Total', '7listings' ); ?></th>
			<th scope="col"><?php _e( 'Paid On', '7listings' ); ?></th>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
<?php
}

add_action( 'company_edit_tab', 'solar_add_tab_lead' );

/**
 * Add tab 'Leads'
 *
 * @return void
 */
function solar_add_tab_lead()
{
	echo '<li>' . __( 'Leads', '7listings' ) . '</li>';
}

add_action( 'company_edit_tab_content', 'solar_tab_leads' );

/**
 * Show leads form
 *
 * @return void
 */
function solar_tab_leads()
{
	echo '<div>';
	include locate_template( 'inc/admin/tabs/company/leads.php' );
	echo '</div>';
}


add_action( 'company_edit_tab', 'company_marketing_tab' );
add_action( 'company_edit_tab_content', 'company_marketing_tab_content' );

function company_marketing_tab()
{
	$membership_paid_override = get_post_meta( get_the_ID(), 'membership_paid_override', true );
	if ( $membership_paid_override == 'not_paid' )
		return;

	echo '<li>' . __( 'Marketing', '7listings' ) . '</li>';
}

function company_marketing_tab_content()
{
	$membership_paid_override = get_post_meta( get_the_ID(), 'membership_paid_override', true );
	if ( $membership_paid_override == 'not_paid' )
		return;

	echo '<div>';
	include locate_template( 'inc/admin/tabs/company/marketing.php' );
	echo '</div>';
}

add_action( 'company_save_post', 'solar_save_company' );

/**
 * Save more for company
 *
 * @param int $post_id
 *
 * @return void
 */
function solar_save_company( $post_id )
{
	$marketing = array();

	if ( isset( $_POST['news_posts'] ) && is_array( $_POST['news_posts'] ) )
	{
		foreach ( $_POST['news_posts']['date'] as $index => $date )
		{
			foreach ( $_POST['news_posts']['url'] as $key => $url )
				if ( $key === $index && ! empty( $url ) )
					$marketing['news_posts'][$key] = compact( 'date', 'url' );
		}
	}

	if ( isset( $_POST['social_shares'] ) && is_array( $_POST['social_shares'] ) )
	{
		foreach ( $_POST['social_shares']['date'] as $index => $date )
		{
			foreach ( $_POST['social_shares']['url'] as $key => $url )
				if ( $key === $index && ! empty( $url ) )
					$marketing['social_shares'][$key] = compact( 'date', 'url' );
		}
	}

	$marketing = serialize( $marketing );
	update_post_meta( $post_id, 'marketing', $marketing );

	if ( ! empty( $_POST['membership_paid_override'] ) && ! empty( $_POST['membership_type'] ) )
		update_post_meta( $post_id, 'membership_paid_override', $_POST['membership_paid_override'] );
	else
		update_post_meta( $post_id, 'membership_paid_override', 'no_override' );

	// Service Area is always enabled
	// And always use postcodes
	update_post_meta( $post_id, 'service_area', 1 );
	update_post_meta( $post_id, 'service_radius', 'postcodes' );

	// Check leads enable for backend only
	if ( is_admin() )
	{
		if ( isset( $_POST['leads_enable'] ) )
			update_post_meta( $post_id, 'leads_enable', 1 );
		else
			delete_post_meta( $post_id, 'leads_enable' );
	}

	// Text and Select fields
	$fields = array(
		'leads_email', 'leads', 'leads_type', 'leads_price', 'leads_payment_type', 'trading_name', 'company_abn', 'range',
		'service_type', 'assessment', 'age',
		'invoice_position', 'invoice_direct_line', 'also_known_as', 'lead_frequency', 'company_service_location'
	);

	// Set default amount to 30 if payment type = upfront. Don't need to set frequency because default is month
	if ( isset( $_POST['leads_payment_type'] ) && $_POST['leads_payment_type'] === 'upfront' && empty( $_POST['leads'] ) )
		$_POST['leads'] = 30;

	if ( ! empty( $_POST['leads_upfront_admin_active'] ) && $_POST['leads_payment_type'] === 'upfront' )
		update_post_meta( $post_id, 'leads_upfront_admin_active', $_POST['leads_upfront_admin_active'] );
	else
		update_post_meta( $post_id, 'leads_upfront_admin_active', false );

	foreach ( $fields as $field )
	{
		if ( isset( $_POST[$field] ) )
			update_post_meta( $post_id, $field, $_POST[$field] );
	}

	if ( current_user_can( 'manage_options' ) )
	{
		// Saved direct debit app?
		if ( isset( $_POST['leads_direct_debit_saved'] ) )
			update_post_meta( $post_id, 'leads_direct_debit_saved', 1 );
		else
			delete_post_meta( $post_id, 'leads_direct_debit_saved' );

		// Manually suspend leads
		if ( isset( $_POST['leads_manually_suspend'] ) )
		{
			update_post_meta( $post_id, 'leads_manually_suspend', 1 );
			update_post_meta( $post_id, 'leads_manually_suspend_start', current_time( 'timestamp' ) );
		}
		else
		{
			delete_post_meta( $post_id, 'leads_manually_suspend' );
			$start = get_post_meta( $post_id, 'leads_manually_suspend_start', true );
			delete_post_meta( $post_id, 'leads_manually_suspend_start' );

			// If long time suspension (>= 7 days), set date buying leads to current time
			if ( $start + 7 * 86400 < time() )
				update_post_meta( $post_id, 'leads_paid', current_time( 'timestamp' ) );
		}
	}

	// Lead buying time: save in admin only
	if ( isset( $_POST['leads_paid'] ) && is_admin() )
	{
		$paid = strtotime( $_POST['leads_paid'] );

		// Date buying lead cannot start from tomorrow and go on
		if ( $paid < time() )
			update_post_meta( $post_id, 'leads_paid', $paid );
	}

	// Update owner data
	$user_data = array();
	$user_data['ID'] = get_post_meta( $post_id, 'user', true );
	if ( isset( $_POST['owner_email'] ) )
		$user_data['user_email'] = $_POST['owner_email'];
	if ( isset( $_POST['owner_mobile'] ) )
		$user_data['mobile'] = $_POST['owner_mobile'];
	if ( isset( $_POST['owner_direct_line'] ) )
		$user_data['direct_line'] = $_POST['owner_direct_line'];
	wp_update_user( $user_data );
}

add_action( 'admin_enqueue_scripts', 'solar_edit_company_enqueue' );

/**
 * Enqueue script for company edit page
 *
 * @return void
 */
function solar_edit_company_enqueue()
{
	$screen = get_current_screen();
	if ( 'post' == $screen->base || 'company' == $screen->post_type )
	{
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_script( 'sl-choose-image' );
		wp_enqueue_script( 'solar-company-edit', sl_locate_url( 'js/admin/company-edit.js' ), array( 'jquery-ui-datepicker' ), '', true );
	}
}

add_action( 'company_edit_single_membership_main', 'solar_edit_single_membership_main' );

/**
 * Display web stats in company setting
 *
 * @return void
 */

 function solar_edit_single_membership_main()
 {
	 $post_type = 'company';
?>
	 <tr>
		 <th><?php _e( 'Show Web stats', '7listings' ); ?></th>
		 <td><?php Sl_Form::checkbox( "{$post_type}_show_web_stats_none" ); ?></td>
		 <td><?php Sl_Form::checkbox( "{$post_type}_show_web_stats_bronze" ); ?></td>
		 <td><?php Sl_Form::checkbox( "{$post_type}_show_web_stats_silver" ); ?></td>
		 <td><?php Sl_Form::checkbox( "{$post_type}_show_web_stats_gold" ); ?></td>
	 </tr>
<?php
 }

add_filter( 'sl_settings_sanitize', 'solar_sanitize_filter', 10, 3 );

/**
 * Sanitize options
 *
 * @param array  $options_new
 * @param array  $options
 * @param string $page
 *
 * @return array
 */

function solar_sanitize_filter( $options_new, $options, $page = '' )
{
	$type = 'company';
	if ( $page == 'page_company' )
	{
		$checkboxes  = array(
			"{$type}_show_web_stats",
		);
		$memberships = array( 'none', 'bronze', 'silver', 'gold' );
		foreach ( $checkboxes as $cb ) {
			foreach ( $memberships as $membership ) {
				if ( empty( $options["{$cb}_{$membership}"] ) ) {
					unset( $options_new["{$cb}_{$membership}"] );
				}
			}
		}
	}
	return $options_new;
}

add_action( 'sl_featured_title_title', 'solar_search_company', 100 );

/**
 * Show search company field on the featured header
 *
 * @return void
 */

function solar_search_company( $param )
{
	if( get_post_type() == 'company' && is_archive() )
	{
		$s1 = isset( $_GET['s1'] ) ? $_GET['s1'] : '';
		$s2 = isset( $_GET['s2'] ) ? $_GET['s2'] : '';
?>
		<div class="company-search-box">
			<div class="search-company">
				<h1>Finding solar companies has never been easier</h1>
				<form method="get" action="/solar-installers/" class="widget_sl-company-search" method="get">
					<input type="text" value="<?= stripslashes( $s1 ); ?>" name="s1" placeholder="Enter company or service">
					<input type="text" value="<?= stripslashes( $s2 ); ?>" name="s2" id="search-location" placeholder="Enter location">
					<button title="Search" class="button search" type="submit"></button>
				</form>
			</div>
			<div class="popular-company">
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_03.png"></a>
					<p>Brands</p>
				</div>
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_05.png"></a>
					<p>Installers</p>
				</div>
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_07.png"></a>
					<p>Technologies</p>
				</div>
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_09.png"></a>
					<p>Maintenances</p>
				</div>
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_11.png"></a>
					<p>Battery Storage</p>
				</div>
				<div class="company-filter-box">
					<a href="#"><img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/company-images/Australian-Solar-installers_13.png"></a>
					<p>Gird connect</p>
				</div>
			</div>
		</div>
<?php
	}
	else
	{
		return $param;
	}
}

add_filter( 'company_edit_archive_main_sorting_items', 'solar_edit_archive_main_sorting_items' );

/**
 * Adding item Top Rating for sorting
 *
 * @param array $items
 *
 * @return array
 */

function solar_edit_archive_main_sorting_items( $items )
{
	$items['rating'] = __( 'Top Rating', '7listings' );
	return $items;
}

add_action( 'add_meta_boxes', function( $post_type, $post )
{

	add_meta_box( 'move_comments', __( 'Move Reviews', '7listings' ), function()
	{
		?>
		Move all reviews of current company to: <br>
		<input id="move-comment-to" type="number" placeholder="Enter company ID"><div class="spinner"></div>
		<div id="move-comment-to-target">Target: <span></span>. Is that correct?</div>
		<input type="hidden" id="move-comment-nonce" value="<?php echo wp_create_nonce( 'move_comment_nonce' ); ?>">
		<button type="button" id="move-comment-button" data-start="<?php echo $_GET['post'] ?>" class="button">I understand the risk</button>
		<?php
	}, 'company' );

}, 10, 2 );

add_action( 'wp_ajax_post_info', 'asq_ajax_post_info' );

function asq_ajax_post_info()
{
	ob_clean();

	$post_id = intval( $_GET['post_id'] );

	if ( empty( $post_id ) )
		wp_send_json_error();

	$post = get_post( $post_id );

	if ( $post->post_type !== 'company' )
		wp_send_json_error();

	wp_send_json_success( $post );
}

add_action( 'wp_ajax_move_comment', 'asq_ajax_move_comment' );

function asq_ajax_move_comment()
{
	global $wpdb;

	ob_clean();

	check_ajax_referer( 'move_comment_nonce', 'nonce' );

	$start 	= intval( $_POST['start'] );
	$target = intval( $_POST['target'] );

	if ( ! $start || ! $target || $start === $target )
		wp_send_json_error();

	$comment_ids 	= $wpdb->get_col( "SELECT comment_ID FROM asq_comments WHERE comment_post_ID = $start" );

	if ( ! empty( $comment_ids ) )
		$comment_ids  	= implode( ',', $comment_ids );

	$affected_rows = $wpdb->update( 'asq_comments', array( 'comment_post_ID' => $target ), array( 'comment_post_ID' => $start ) );

	if ( $affected_rows )
	{
		// Always remember to log important data like this
		$description = "Move reviews from $start to $target. Rows affected: $affected_rows. Review IDs: $comment_ids";

		// Just log the description. This function is smart enough to fill other data.
		\ASQ\Log::make( compact( 'description' ) );

		wp_send_json_success( compact( 'affected_rows' ) );
	}

	die;
}