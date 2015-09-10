<script type="text/javascript">
	jQuery( function( $ )
	{
		$( '#lead_frequency' ).change( function()
		{
			var freq = $(this).val();

			if ( 'month' === freq ) {
				$('#modal-message').text('Awesome! You\'ve selected a monthly cap. Make sure to keep it above 20!');
				$('input[name=leads]').attr('min', 20);
			}
			if ( 'week' === freq ) {
				$('#modal-message').text('Great Stuff! You\'ve selected a weekly cap. Make sure to keep it above 5!');
				$('input[name=leads]').attr('min', 5);
			}
			if ( 'day' === freq ) {
				$('input[name=leads]').attr('min', 1);
			}
		} );	
	} );
</script>
<?php

global $messages;

if ( is_user_logged_in() )
{
	$company = get_posts( array(
		'post_type'      => 'company',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_key'       => 'user',
		'meta_value'     => get_current_user_id(),
	) );

	if ( empty( $company ) )
	{
		get_template_part( 'templates/company/user-admin/no-company' );
		return;
	}
	$company = current( $company );
}
else
{
	get_template_part( 'templates/company/user-admin/form-login' );
	return;
}

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

$available_frequencies = [];
foreach ( ['day', 'week', 'month'] as $period )
{
	if ( sl_setting( "lead_frequency_{$period}" ) )
	{
		$available_frequencies[$period] = str_title( $period );
	}
}

$frequency = get_post_meta( $company->ID, 'lead_frequency', true );
if ( ! $frequency )
	$frequency 	= 'month';

$frequency_min 	= [ 'month' => 20, 'week' => 5, 'day' => 1 ];
$leads_min 		= $frequency_min[$frequency];

if ( ! empty( $_GET['message'] ) ):
?>
<div id="main-message" class="alert alert-<?php echo $messages['leads'][$_GET['message']]['priority'] ?>">
	<?php echo $messages['leads'][$_GET['message']]['message'] ?>
</div>
<?php endif ; ?>

<div id="company-admin">

<?php
// If company is manually suspended
// Show only leads, no settings. Allow to reject
if ( get_post_meta( $company->ID, 'leads_manually_suspend', true ) ) : ?>

	<?php include 'company-leads.php'; ?>

<?php
// If company doesn't buying leads
// Show leads and popup to start buying leads
elseif ( ! get_post_meta( $company->ID, 'leads_enable', true ) ) : ?>

	<?php include 'company-leads.php'; ?>

	<h2><?php _e( 'Settings', '7listings' ); ?></h2>

	<div id="account-settings">
		<div class="admin-block">
			<p><?php _e( 'You are not purchasing leads.', '7listings' ); ?></p>
		</div>
	</div>

	<div id="account-actions">
		<a href="#modal-buy-leads" role="button" data-toggle="modal" class="button green-light large full"><?php _e( 'Start Buying Leads', '7listings' ); ?></a>
	</div>

	<?php // Buy leads modal ?>
	<form action="" method="post" id="modal-buy-leads" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3><?php _e( 'Start buying Leads', '7listings' ); ?></h3>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<div class="alert alert-warning" id="modal-message"></div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Amount', '7listings' ); ?></label>

					<div class="controls">
						<?php
						$value = get_post_meta( $company->ID, 'leads', true );
						if ( ! $value )
							$value = '';
						?>
						<input type="number" min="<?= $leads_min ?>" required name="leads" class="input-mini" value="<?php echo $value; ?>">
						/
						<?php Form::select( 'lead_frequency', $available_frequencies, $frequency, ['class' => 'input-small', 'id' => 'lead_frequency'] ); ?>
					</div>
				</div>
				<?php if ( sl_setting( 'solar_term_post' ) ) : ?>
				<div class="control-group toggle-choices">
					<label class="control-label"><?php _e( 'Payment Type' ); ?></label>
					<div class="controls toggle-choices">
						<?php Form::select( 'leads_payment_type', [
							'direct' 	=> 'Direct Debit',
							'post'		=> 'Post Pay',
							'upfront' 	=> 'Upfront'
						] ); 
						?>
					</div>
				</div>
				<div data-name="leads_payment_type" data-value="direct">
					<hr class="light">
					<?php echo sl_setting( 'solar_term_direct' ); ?>
				</div>
				<div data-name="leads_payment_type" data-value="post">
					<hr class="light">
					<?php echo sl_setting( 'solar_term_post' ); ?>
				</div>
				<?php endif; ?>
			</div>
			<label><input type="checkbox" id="agree-term" name="agree" /> I agree to <a href="<?php bloginfo('url'); ?>/my-account/tc-asl-a/">Australian Solar Quotes terms and conditions</a></label>
		</div>
		<div class="modal-footer">
			<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
			<input type="submit" name="submit_buy" class="button primary hidden" id="submit-buying-leads" value="<?php _e( 'Start Buying Leads', '7listings' ); ?>">
		</div>
	</form>

<?php
// Otherwise, show full data
else : ?>

	<h2><?php _e( 'Settings', '7listings' ); ?></h2>

	<div class="buy-since">
		<?php printf( __( 'Purchasing since: %s', '7listings' ), date( 'd F, Y', intval( get_post_meta( $company->ID, 'leads_paid', true ) ) ) ); ?>
	</div>

	<div id="account-settings">
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Amount', '7listings' ); ?></span>
			<?php
			if ( 'too_many_temp' == get_post_meta( $company->ID, 'cancel_reason', true ) )
			{
				$end = get_post_meta( $company->ID, 'suspend_end', true );
				printf( __( 'Leads suspended till %s', '7listings' ), date( 'd/m/Y H:i', $end ) );
			}
			elseif ( get_post_meta( $company->ID, 'leads_payment_type', true ) === 'upfront' )
			{
				echo "You have purchased a 30 pack";

				$hide_modal = true;
			}
			else
			{
				printf( __( '%d leads/%s', '7listings' ), get_post_meta( $company->ID, 'leads', true ), $frequency );
			}

			if ( ! isset( $hide_modal ) ) :
			?>
			<a href="#modal-edit-amount" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
			<?php endif; ?>
		</div>
        <div class="admin-block">
			<span class="admin_label"><?php _e( 'Leads Type', '7listings' ); ?></span>
	        <?php
			$leads_type = get_post_meta( $company->ID, 'leads_type', true );
			if ( empty( $leads_type ) )
				$leads_type = [];

	        echo str_title( implode( ', ', $leads_type ) );
	        ?>
	        <a href="#modal-lead-type" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Service Type', '7listings' ); ?></span>
			<?php
			$service_type = get_post_meta( $company->ID, 'service_type', true );
			if ( empty( $service_type ) )
				$service_type = [];
			foreach ( $service_type as $k => $v )
			{
				$service_type[$k] = $service_types[$v];
			}
			echo str_title( implode( ', ', $service_type ) );
			?>
			<a href="#modal-service-type" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Assessment', '7listings' ); ?></span>
			<?php
			$assessment = get_post_meta( $company->ID, 'assessment', true );
			if ( empty( $assessment ) )
				$assessment = array();
			foreach ( $assessment as $k => $v )
			{
				$assessment[$k] = $assessments[$v];
			}
			echo str_title( implode( ', ', $assessment ) );
			?>
			<a href="#modal-assessment" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Age', '7listings' ); ?></span>
			<?php
			$age = get_post_meta( $company->ID, 'age', true );
			if ( empty( $age ) )
				$age = array();
			foreach ( $age as $k => $v )
			{
				$age[$k] = $ages[$v];
			}
			echo str_title( implode( ', ', $age ) );
			?>
			<a href="#modal-age" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
		
		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Service Area', '7listings' ); ?></span>
			<a href="#modal-edit-area" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
			<div class="toggle caret">
				<a href="#">Show/Hide</a>
				<div class="content">
				<?php if ( 'radius' == get_post_meta( $company->ID, 'service_radius', true ) ) : ?>
					<?php printf( __( '%dkm from office', '7listings' ), get_post_meta( $company->ID, 'leads_service_radius', true ) ); ?>
				<?php else : ?>
					<?php echo get_post_meta( $company->ID, 'service_postcodes', true ); ?>
				<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="admin-block">
			<span class="admin_label"><?php _e( 'Leads Recipient', '7listings' ); ?></span>
			<?php echo get_post_meta( $company->ID, 'leads_email', true ); ?>
			<a href="#modal-email" role="button" data-toggle="modal" class="button small edit"><?php _e( 'Edit', '7listings' ); ?></a>
		</div>
	</div>

	<div id="account-actions">
		<a href="#modal-stop" role="button" data-toggle="modal" class="button white small cancel"><?php _e( 'Stop Buying Leads', '7listings' ); ?></a>

		<?php // Stop buying leads modal ?>
		<form action="" method="post" id="modal-stop" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Suspend Leads', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Reason', '7listings' ); ?></label>

					<div class="controls toggle-choices">
						<select name="cancel_reason" class="full-width">
							<?php
							SL_Form::options( get_post_meta( $company->ID, 'cancel_reason', true ), array(
								'too_many_temp'    => __( 'I have too many leads and wish to suspend temporarily', '7listings' ),
								'too_many_ind'     => __( 'I have too many leads and wish to suspend indefinitely', '7listings' ),
								'another_provider' => __( 'I am using another solar quote provider', '7listings' ),
								'poor_quality'     => __( 'I am not happy with the quality of leads', '7listings' ),
								'poor_amount'      => __( 'I am not happy with the amount of leads', '7listings' ),
								'poor_service'     => __( 'I am not happy with the service provided by Australian Solar Quotes', '7listings' ),
								'other'            => __( 'Other Reason', '7listings' ),
							) );
							?>
						</select>
					</div>
				</div>

				<br>

				<div class="control-group" data-name="cancel_reason" data-value="too_many_temp">
					<?php _e( 'Suspend leads for', '7listings' ); ?>

					<span class="input-append">
                        <input type="number" name="suspend_days" class="input-mini" min="1">
						<span class="add-on"><?php _e( 'days', '7listings' ); ?></span>
					</span>
				</div>

				<div class="control-group" data-name="cancel_reason" data-value="other">
					<label class="control-label"><strong><?php _e( 'Please describe your reason for deactivation.', '7listings' ); ?></strong></label>
					<div class="controls">
						<textarea name="other_reason" class="full-width" rows="5"></textarea>
					</div>
				</div>

				<?php _e( '
					<h4>Terms & Conditions</h4>
					<div class="company-terms">
						I hereby wish to deactivate the leads for the reason described, and the time frame selected in this form. I am aware that if my account is suspended for a period of 7 days or longer, I will be placed in a cue and may not start receiving leads immediately.
						<br><br>
						I am aware that an invoice may be raised within 48 hours for the leads that I have received this month if I have selected any of the following reasons for suspension:
						<ul>
						  <li>I have too many leads and wish to suspend indefinitely</li>
						  <li>I am using another solar quote provider</li>
						  <li>I am not happy with the quality of leads</li>
						  <li>I am not happy with the amount of leads</li>
						  <li>I am not happy with the service provided by Australian Solar Quotes</li>
						  <li>Other Reason</li>
						</ul>
					</div>
					<hr class="light">
					By clicking "<strong>Stop buying leads</strong>" you consent to the agreement above between Australian Solar Quotes and your business.
					<hr class="light">
					<strong>Note:</strong> This will not close or remove your company account/listing.
				', '7listings' ); ?>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_stop" class="button primary" value="<?php _e( 'Stop buying leads', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit amount modal ?>
		<form action="" method="post" id="modal-edit-amount" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit lead amount and frequency', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="alert alert-warning" id="modal-message"></div>

					<div class="control-group">
						<label class="control-label"><?php _e( 'Leads', '7listings' ); ?></label>

						<div class="controls">
							<?php
							$value = intval( get_post_meta( $company->ID, 'leads', true ) );
							if ( ! $value )
								$value = '';
							?>
							<input type="number" min="<?= $leads_min ?>" required name="leads" class="input-mini" value="<?php echo $value; ?>">
							/
							<?php Form::select( 'lead_frequency', $available_frequencies, $frequency, ['class' => 'input-small', 'id' => 'lead_frequency'] ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_amount" class="button primary" value="<?php _e( 'Update amount', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit email ?>
		<form action="" method="post" id="modal-email" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit leads recipient', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group">
						<label class="control-label"><?php _e( 'Emails', '7listings' ); ?></label>

						<div class="controls">
							<input type="text" required name="leads_email" value="<?php echo get_post_meta( $company->ID, 'leads_email', true ); ?>"><br>
							<span class="hint"><?php _e( 'Multiple emails are separated by commas.', '7listings' ); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_email" class="button primary" value="<?php _e( 'Update email', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit lead type modal ?>
		<form action="" method="post" id="modal-lead-type" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit lead type', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group">

                    	<p><?php _e( 'Select what type of leads you want to receive.', '7listings' ); ?></p>

						<label class="control-label"><?php _e( '', '7listings' ); ?></label>
						<div class="controls">
							<?php
							$leads_types = array_symmetry(['Hybrid', 'Hot Water', 'Off Grid', 'Commercial', 'Maintenance'], true);
							$leads_types['residential'] = 'Solar';
							$leads_type  = get_post_meta( $company->ID, 'leads_type', true );
							
							Form::checkboxList( 'leads_type', $leads_types, $leads_type );
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_type" class="button primary" value="<?php _e( 'Update lead type', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit service type modal ?>
		<form action="" method="post" id="modal-service-type" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit service type', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group">

						<p><?php _e( 'Select for what type of service you want receive leads.', '7listings' ); ?></p>

						<label class="control-label"><?php _e( '', '7listings' ); ?></label>
						<div class="controls">
						<?php
							$service_types = array_symmetry( ['Solar PV', 'Solar Hot Water', 'Solar A/C'], true );
							$service_type  = get_post_meta( $company->ID, 'service_type', true );
							Form::checkboxList( 'service_type', $service_types, $service_type );
						?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_service_type" class="button primary" value="<?php _e( 'Update service type', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit assessment modal ?>
		<form action="" method="post" id="modal-assessment" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit assessment', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group">

						<p><?php _e( 'Select what type of assessment you offer to provide quotes.', '7listings' ); ?></p>

						<label class="control-label">&nbsp;</label>
						<div class="controls">
						<?php
							$assessments = array_symmetry( ['Onsite', 'Phone / Email'], true );
							
							$assessment  = get_post_meta( $company->ID, 'assessment', true );
							
							Form::checkboxList( 'assessments', $assessments, $assessment);
						?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_assessment" class="button primary" value="<?php _e( 'Update assessment', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit age modal ?>
		<form action="" method="post" id="modal-age" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit age', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group">

						<p><?php _e( 'Select what age of the property you offer service.', '7listings' ); ?></p>

						<label class="control-label">&nbsp;</label>
						<div class="controls">
							<?php
								$ages = array_symmetry(['Retrofit', 'Under Construction'], true);
								$age  = get_post_meta( $company->ID, 'age', true );
								Form::checkboxList('age', $ages, $age);
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_age" class="button primary" value="<?php _e( 'Update age', '7listings' ); ?>">
			</div>
		</form>

		<?php // Edit area modal ?>
		<form action="" method="post" id="modal-edit-area" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3><?php _e( 'Edit my service area', '7listings' ); ?></h3>
			</div>
			<div class="modal-body">
				<div class="form-horizontal">
					<div class="control-group toggle-choices">
						<label class="control-label"><?php _e( 'Service Area', '7listings' ); ?></label>

						<div class="controls">
							<select name="service_radius">
								<?php
								SL_Form::options( get_post_meta( $company->ID, 'service_radius', true ), array(
//									'radius'    => __( 'Radius', '7listings' ),
									'postcodes' => __( 'Postcodes', '7listings' ),
								) );
								?>
							</select>
						</div>
					</div>
					<div class="control-group" data-name="service_radius" data-value="postcodes">
						<label class="control-label"><?php _e( 'Postcodes', '7listings' ); ?></label>

						<div class="controls">
							<textarea name="service_postcodes" class="input-xlarge service-postcodes" rows="3"><?php echo esc_textarea( get_post_meta( $company->ID, 'service_postcodes', true ) ); ?></textarea>

							<p class="input-hint"><?php _e( 'Enter postcodes separated by commas', '7listings' ); ?></p>
							<p><a href="http://www.freemaptools.com/find-australian-postcodes-inside-radius.htm" target="_blank" class="button"><?php _e( 'Get Postcodes', '7listings' ); ?></a></p>
							
							<p class="input-hint">
								The postcodes that are generated from the external website may not be accurate and should be reviewed before saving
							</p>
						</div>
					</div>
					<div class="control-group" data-name="service_radius" data-value="radius">
						<label class="control-label"><?php _e( 'From Office', '7listings' ); ?></label>

						<div class="controls">
							<span class="input-append">
						        <input type="number" min="1" name="leads_service_radius" class="input-mini" value="<?php echo get_post_meta( $company->ID, 'leads_service_radius', true ); ?>">
						        <span class="add-on">km</span>
						    </span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button" data-dismiss="modal" aria-hidden="true"><?php _e( 'Cancel', '7listings' ); ?></button>
				<input type="submit" name="submit_radius" class="button primary" value="<?php _e( 'Update service area', '7listings' ); ?>">
			</div>
		</form>
	</div>

	<?php include 'company-leads.php'; ?>

<?php endif; ?>

</div><!-- #company-admin -->

<input type="hidden" id="company-id" value="<?php echo $company->ID; ?>">
