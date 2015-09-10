<?php

class Solar_Popup
{
	/**
	 * @var int GF form ID
	 */
	public $form_id;

	/**
	 * Constructor
	 */
	function __construct( $form_id )
	{
		$this->form_id = $form_id;

		add_action( 'template_redirect', array( $this, 'run' ) );

		add_action( "gform_after_submission_{$form_id}", array( $this, 'user_sign' ), 10, 2 );

		add_shortcode( 'form_tc_agree', array( $this, 'shortcode' ) );
	}

	/**
	 * Add hooks in the frontend
	 *
	 * @return void
	 */
	function run()
	{
		if ( !$this->is_displayed() )
			return;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	/**
	 * Check if popup is displayed for current user
	 *
	 * @return bool
	 */
	function is_displayed()
	{
		if ( !sl_setting( 'terms_cond_popup' ) )
			return false;

		if ( ! is_user_logged_in() )
			return false;

		// For company owners only
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => get_current_user_id(),
		) );
		if ( empty( $company ) )
			return false;
		$company = current( $company );

		// User 'solar_agree' = ID (time update) of popup
		// If solar_agree >= time update: user agreed
		// If not: show popup
		// And if now - update > grace period: suspend user
		$update = intval( sl_setting( 'terms_cond_update' ) );
		$user_agree = intval( get_user_meta( get_current_user_id(), 'solar_agree', true ) );
		if ( $user_agree >= $update )
			return false;

		// User don't agree for a long time: suspend buying leads
		$now = time();
		$grace_period = sl_setting( 'terms_cond_grace_period' );
		if ( $now - $update > $grace_period * 86400 )
		{
			delete_post_meta( $company->ID, 'leads_enable' );

			// Log
			solar_log( array(
				'time'        => date( 'Y-m-d H:i:s' ),
				'type'        => __( 'Leads', '7listings' ),
				'action'      => __( 'Suspend', '7listings' ),
				'description' => sprintf( __( '<span class="detail">Not agree to new Terms &amp; Conditions</span>', '7listings' ) ),
				'object'      => $company->ID,
				'user'        => get_current_user_id(),
			) );
		}

		return true;
	}

	/**
	 * Enqueue JS file of GravityForms Signature plugin in the <header> to make JS works
	 *
	 * @return void
	 */
	function enqueue()
	{
		wp_enqueue_script( 'solar_signature', plugins_url( 'super_signature/ss.js', 'gravityformssignature/anything' ), array( 'jquery' ) );
	}

	/**
	 * Javascript on footer to display the popup
	 *
	 * @return void
	 */
	function wp_footer()
	{
		global $current_user;

		get_currentuserinfo();

		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $current_user->ID,
		) );

		if ( empty( $company ) )
			return;

		$company = current( $company );
		$name = $current_user->display_name;
		if ( $current_user->user_firstname && $current_user->user_lastname )
			$name = "{$current_user->user_firstname} {$current_user->user_lastname}";
		$replacement = array(
			'%USERNAME%'         => $current_user->user_login,
			'%NAME%'             => $name,
			'%CURRENT_COMPANY%'  => $company->post_title,
			'%TERMS_CONDITIONS%' => sl_setting( 'solar_term_cond' ),
		);
		$message = do_shortcode( wpautop( sl_setting( 'terms_cond_popup_message' ) ) );
		$message = strtr( $message, $replacement );
		?>
		<div id="overlay"></div>
		<div class="modal" id="popup">
			<div class="modal-header">
				<h3><?php _e( 'Terms &amp; Conditions', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<?php echo do_shortcode( $message ); ?>
			</div>
			<div class="modal-footer">
				<span class="note">Scroll down to agree</span>
			</div>
		</div>
	<?php
	}

	/**
	 * Update user status after signing form
	 *
	 * @param array $entry
	 * @param array $form
	 *
	 * @return void
	 */
	function user_sign( $entry, $form )
	{
		update_user_meta( get_current_user_id(), 'solar_agree', sl_setting( 'terms_cond_update' ) );

		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => get_current_user_id(),
		) );

		if ( !empty( $company ) )
		{
			$company = current( $company );

			/**
			 * @since 30/7/2014: Not enable leads by default
			 */
			// update_post_meta( $company->ID, 'leads_enable', 1 );

			// Log
			solar_log( array(
				'time'        => date( 'Y-m-d H:i:s' ),
				'type'        => __( 'Terms &amp; Conditions', '7listings' ),
				'action'      => __( 'Sign', '7listings' ),
				'description' => sprintf( __( '<span class="detail">New Terms &amp; Conditions</span>', '7listings' ) ),
				'object'      => $company->ID,
				'user'        => get_current_user_id(),
			) );
		}
	}

	/**
	 * Add shortcode for gravity form
	 *
	 * @return string
	 */
	function shortcode()
	{
		return do_shortcode( '[gravityform id="' . $this->form_id . '" name="Term & Conditions" title="false" description="false" ajax="true" field_values="leads_company=%CURRENT_COMPANY%&terms_cond=%TERMS_CONDITIONS%"]' );
	}
}

new Solar_Popup( 41 );
