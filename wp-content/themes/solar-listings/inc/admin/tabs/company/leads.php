<h2><?php _e( 'Leads', '7listings' ); ?></h2>

<p class="checkbox-toggle">
	<label><?php _e( 'Purchase Leads', '7listings' ); ?></label>
	<?php SL_Form::checkbox_general( 'leads_enable', get_post_meta( get_the_ID(), 'leads_enable', true ) ); ?>
</p>
<div>
	<div class="row-fluid">
		<div class="span6">
			<p>
				<label><?php _e( 'Email', '7listings' ); ?></label>
				<span class="input-prepend">
					<span class="add-on"><i class="icon-envelope-alt"></i></span>
					<input type="text" name="leads_email" style="width: auto" value="<?php echo get_post_meta( get_the_ID(), 'leads_email', true ); ?>">
				</span>
			</p>
			<hr class="light">
			<p>
				<label><?php _e( 'Amount', '7listings' ); ?></label>
				<span>
					<input type="number" class="amount" name="leads" value="<?php echo get_post_meta( get_the_ID(), 'leads', true ); ?>">
					<?php
					$payment_method = solar_get_company_payment_method( get_the_ID(), false );

					if ( $payment_method !== 'upfront' )
					{
						$frequency = get_post_meta( get_the_ID(), 'lead_frequency', true );
						
						if ( ! $frequency )
							$frequency = 'month';

						Form::select( 'lead_frequency', array( 
							'day' 	=> 'Day',
							'week' 	=> 'Week',
							'month' => 'Month'
						), $frequency, array( 'class' => 'input-small' ) );
					}
					else
					{
						echo 'leads pack';
					}
					?>
				</span>
			</p>
			<p>
				<label><?php _e( 'Price', '7listings' ); ?></label>
				<span class="input-prepend input-append">
					<span class="add-on">$</span>
					<input type="number" class="amount" name="leads_price" value="<?php echo get_post_meta( get_the_ID(), 'leads_price', true ); ?>">
					<span class="add-on">/ lead</span>
				</span>
			</p>
			<hr class="light">
			<p class="lead-type">
				<label><?php _e( 'Type', '7listings' ); ?></label>
				<span class="vertical-options">
					<?php
					$leads_types = array_symmetry(['Hybrid', 'Hot Water', 'Off Grid', 'Commercial', 'Maintenance'], true);
					$leads_types['residential'] = 'Solar';
					$leads_type  = get_post_meta( get_the_ID(), 'leads_type', true );
					
					Form::checkboxList( 'leads_type', $leads_types, $leads_type );
					?>
				</span>
			</p>
			<p>
				<label><?php _e( 'Service Type', '7listings' ); ?></label>
				<span class="vertical-options">
					<?php
					$service_types = array_symmetry( ['Solar PV', 'Solar Hot Water', 'Solar A/C'], true );
					
					$service_type  = get_post_meta( get_the_ID(), 'service_type', true );

					Form::checkboxList( 'service_type', $service_types, $service_type );
					?>
				</span>
			</p>
			<p>
				<label><?php _e( 'Assessment', '7listings' ); ?></label>
				<span class="vertical-options">
					<?php
					$assessments = array_symmetry( ['Onsite', 'Phone / Email'], true );
					
					$assessment  = get_post_meta( get_the_ID(), 'assessment', true );
					
					Form::checkboxList( 'assessments', $assessments, $assessment);
					?>
				</span>
			</p>
			<p>
				<label><?php _e( 'Age', '7listings' ); ?></label>
				<span class="vertical-options">
					<?php
					
					$ages = array_symmetry(['Retrofit', 'Under Construction'], true);

					$age  = get_post_meta( get_the_ID(), 'age', true );

					Form::checkboxList('age', $ages, $age);
					?>
				</span>
			</p>
			<p>
				<label><?php _e( 'Postcodes', '7listings' ); ?></label>
				<textarea name="service_postcodes" rows="5" style="width:100%"><?php echo esc_textarea( get_post_meta( get_the_ID(), 'service_postcodes', true ) ); ?></textarea><br>
				<span class="input-hint"><?php _e( 'Enter postcodes separated by commas', '7listings' ); ?></span>
			</p>
		</div>
		<div class="span6">
			<div class="stat-info">
				<p>
					<label><?php _e( 'Sign-Up Date', '7listings' ); ?></label>
					<span class="result">
						<?php
						$owner = get_post_meta( get_the_ID(), 'user', true );
						if ( $owner )
						{
							$owner = get_userdata( $owner );
							echo date( 'm/d/Y H:i', strtotime( $owner->user_registered ) );
						}
						?>
					</span>
				</p>
				<p>
					<label><?php _e( 'Lead Date', '7listings' ); ?></label>
					<span class="result">
						<?php
						if ( $value = get_post_meta( get_the_ID(), 'leads_paid', true ) )
							$value = date( 'Y-m-d', $value );
						?>
						<input type="date" name="leads_paid" style="width: auto" value="<?php echo $value; ?>">
					</span>
				</p>
				<p>
					<label><?php _e( 'Last Payment', '7listings' ); ?></label>
					<span class="result">Paid Date, $Amount</span>
				</p>
				<p class="toggle-choices">
					<label><?php _e( 'Payment Type', '7listings' ); ?></label>
					<span class="result">
						<?php
						$payment_method = solar_get_company_payment_method( get_the_ID(), false );
						Form::select( 'leads_payment_type', solar_get_payment_methods(), $payment_method, array( 'class' => 'input-medium' ) ); 
						?>
					</span>
				</p>

				<p data-name="leads_payment_type" data-value="upfront">
					<label><?php _e( 'Activated', '7listings' ); ?></label>
					<span class="result">
						<?php Form::checkbox( 'leads_upfront_admin_active', true, get_post_meta( get_the_ID(), 'leads_upfront_admin_active', true ) ); ?>
					</span>
				</p>

				<p data-name="leads_payment_type" data-value="direct">
					<label><?php _e( 'Direct Debit Application saved', '7listings' ); ?></label>
					<span class="result">
						<?php SL_Form::checkbox_general( 'leads_direct_debit_saved', get_post_meta( get_the_ID(), 'leads_direct_debit_saved', true ) ); ?>
					</span>
				</p>
				
				<hr class="light">
				
				<p>
					<label><?php _e( 'Outstanding', '7listings' ); ?></label>
					<span class="result">#Invoice_ID, $Amount <a href="#email-link" class="button">Send Reminder</a> <a href="#email-link" class="button">Record Payment</a></span>
				</p>
				<hr class="light">
				<p>
					<label><?php _e( 'Manually Suspend Leads', '7listings' ); ?></label>
					<span class="result">
						<?php SL_Form::checkbox_general( 'leads_manually_suspend', get_post_meta( get_the_ID(), 'leads_manually_suspend', true ) ); ?>
					</span>
				</p>
			</div>
		</div>
	</div>
</div>
