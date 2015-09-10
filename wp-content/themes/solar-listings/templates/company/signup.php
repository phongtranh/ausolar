<?php
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
		echo '<div class="alert">' . __( '<p>You do not own a Company, please list your Company now!.</p><p>Not registered with us yet? It only takes 30 seconds!</p>', '7listings' ) . '</div>';
	}
	else
	{
		printf( '<div class="alert alert-error">' . __( 'You already own a company, consider <a href="%s">edit company details here</a>.', '7listings' ) . '</div>', get_permalink( sl_setting( 'company_page_edit' ) ) );

		return;
	}
}

$name 			= ( isset( $_GET['full_name'] ) ) 	? $_GET['full_name'] : '';
$email 			= ( isset( $_GET['email'] ) )	? $_GET['email'] : '';
$phone 			= ( isset( $_GET['phone'] ) )	? $_GET['phone'] : '';
$business_name 	= ( isset( $_GET['business_name'] ) ) ? $_GET['business_name'] : '';

$first_name = '';
$last_name 	= '';
$user_name 	= '';

if ( ! empty( $name ) )
{
	$names 		= explode( ' ', $name );

	$first_name = $names[0];

	$last_name 	= str_replace( $first_name, '', $name );

	$user_name 	= str_snake( $name );
}
?>

<form action="" method="post" enctype="multipart/form-data" class="company-form signup" id="signup-form">

	<div id="ajax-message"></div>

	<?php if ( ! is_user_logged_in() ) : ?>

		<h2><?php _e( 'User Details', '7listings' ); ?></h2>
		<div class="user-details">
			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Username', '7listings' ); ?> <span class="required">*</span></label>
					<input type="text" name="username" value="<?php echo $user_name ?>" required class="span12">
				</div>
				<div class="span6">
					<label><?php _e( 'Password', '7listings' ); ?> <span class="required">*</span></label>
					<input type="password" name="password" required class="span12">
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Name', '7listings' ); ?> <span class="required">*</span></label>
					<input required type="text" name="first_name" value="<?php echo $first_name ?>" class="span12">
					<span class="help-block"><?php _e( 'First', '7listings' ); ?></span>
				</div>
				<div class="span6">
					<label>&nbsp;</label> <input required type="text" value="<?php echo $last_name ?>" name="last_name" class="span12">
					<span class="help-block"><?php _e( 'Last', '7listings' ); ?></span>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Phone', '7listings' ); ?> <span class="required">*</span></label>
					<input required type="tel" name="direct_line" required value="<?php echo $phone ?>" class="span12">
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<label><?php _e( 'Email', '7listings' ); ?> <span class="required">*</span></label>
					<input type="email" name="user_email" value="<?php echo $email ?>" required class="span12">
					<span class="help-block"><?php _e( 'Enter Email', '7listings' ); ?></span>
				</div>
				<div class="span6">
					<label>&nbsp;</label> <input type="email" name="user_email_confirm" value="<?php echo $email ?>" required class="span12">
					<span class="help-block"><?php _e( 'Confirm Email', '7listings' ); ?></span>
				</div>
			</div>
		</div>
	
		<input type="hidden" name="membership_time" value="year">
		<?php
		if ( sl_setting( 'company_membership_display' ) )
		{
			echo '<h2>' . __( 'Membership Type', '7listings' ) . '</h2>';
			echo '<div class="membership-type">';

			$types = array(
				'gold'   => __( 'Gold', '7listings' ),
				'silver' => __( 'Silver', '7listings' ),
				'bronze' => __( 'Bronze', '7listings' ),
			);

			foreach ( $types as $k => $v )
			{
				if ( ! sl_setting( "company_membership_$k" ) )
					continue;

				$price_month = sl_setting( "company_membership_price_$k" );
				$price_year  = sl_setting( "company_membership_price_year_$k" );

				if ( ( '' === $price_month || false === $price_month ) && ( '' === $price_year || false === $price_year ) )
					continue;

				if ( ! empty( $price_month ) )
					$price_month = sprintf( __( '$%s/month', '7listings' ), $price_month );
				elseif ( '' !== $price_month && false !== $price_month )
					$price_month = __( 'FREE', '7listings' );

				if ( ! empty( $price_year ) )
					$price_year = sprintf( __( '$%s/year', '7listings' ), $price_year );
				elseif ( '' !== $price_year && false !== $price_year )
					$price_year = __( 'FREE', '7listings' );

				if ( '' !== $price_month && false !== $price_month )
				{
					printf(
						'<label class="radio inline %1$s"><input type="radio" name="membership" value="%1$s,month"> %2$s</label>',
						$k,
						sprintf( __( '%s (%s)', '7listings' ), $v, $price_month )
					);
				}

				if ( '' !== $price_year && false !== $price_year )
				{
					$membership_checked = '';
					if ( isset( $_GET['membership'] ) )
					{
						$membership = trim( $_GET['membership'] );

						if ( in_array( $membership, array( 'gold', 'silver', 'bronze' ) ) && $membership === $k )
							$membership_checked = 'checked';
					}
					printf(
						'<label class="radio inline %1$s"><input type="radio" name="membership" value="%1$s,year" %2$s> %3$s</label>',
						$k,
						$membership_checked,
						sprintf( __( '%s (%s)', '7listings' ), $v, $price_year )
					);
				}
			}

			echo '</div>';
		}
		?>

	<?php endif; ?>

	<h2><?php _e( 'Company Details', '7listings' ); ?></h2>
	
	<?php
		$business_name_text = '';
		$business_name_exists = get_page_by_title( $business_name, OBJECT, 'company' );
		if ( $business_name_exists )
			$business_name_text = $business_name;
	?>

	<script type="text/javascript">
		jQuery( function( $ )
		{
			// The Ajax URL which application should point to
		    var endpoint = ( typeof ajaxurl != 'undefined' ) ? ajaxurl : Sl.ajaxUrl;

		    // Cache object. This will save after each ajax look up.
		    var cache = {};
			
			var NoResultsLabel 		= "Company not listed";
			var hasResultSelected 	= '';
			var shoudCreateNew 		= false;

			$( '.alert-info' ).hide();
			$('#main-submit-btn').hide();
			$( "#autocomplete-trading" ).autocomplete({
				minLength: 2,
		        source: function(request, response) {

		        	var term = request.term;

		            if ( term in cache ) {
		                response( cache[ term ] );
		                return;
		            }
		            console.log(endpoint);
		            $.getJSON( endpoint + '?action=autocomplete_companies&term=' + term, function( r, status, xhr )
		            {

		            	results = [NoResultsLabel];

		            	if ( r.success )
		            	{
		            		cache[ term ] = r.data;
			                results = r.data;
			                results.push( NoResultsLabel );
		            	}
		                response( results );
		            } );
		        },
		        select: function (event, ui) {
		            if ( ui.item.label === NoResultsLabel ) 
		            {
		            	$( '.company-hidden-default, #main-submit-btn' ).show();
		            	$('#main-submit-btn').val('Create New Listing');
		            	$( '.alert-info, .confirm-my-company' ).hide();
		            	event.preventDefault();
		            }
		            else
		            {
		            	//$('#company-post-title').val(ui.item.label);
		            	$( '.company-hidden-default, #main-submit-btn' ).hide();
		            	$('#main-submit-btn').val('Sign Up');
		            	$( '.confirm-my-company, .alert-info' ).show();
		            }

		            hasResultSelected = ui.item.label;
		            
		            $('#autocomplete-trading').trigger('change');
		        },
		        focus: function (event, ui) {
		            if (ui.item.label === NoResultsLabel) {
		                event.preventDefault();
		            }
		        }
	    	});

			$( '#this_is_my_company, #autocomplete-trading' ).change( function()
			{
				var thisIsMyCompany = $( '#this_is_my_company');
				var findMyCompany 	= $('#autocomplete-trading');

				if ( thisIsMyCompany.is(':checked') && hasResultSelected != NoResultsLabel )
				{
					$( '#main-submit-btn' ).show();
					$( '.alert-info' ).hide();
					$('#company-post-title').val(findMyCompany.val());
				}
			} );

	    	$( '#signup-form' ).submit( function( e )
	    	{
	    		e.preventDefault();

	    		if ( $('#this_is_my_company').is(':checked') && $('#company-post-title').val() == '' )
	    			return false;
	    	} );
		} );
	</script>

	<div class="row-fluid">
		<div class="span6">
			<input type="text" placeholder="Find My Company" id="autocomplete-trading" class="span8">
		</div>

		<div class="span6">
			<label class="confirm-my-company">
				<input id="this_is_my_company" type="checkbox" name="this_is_my_company"> This is my company
			</label>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span12">
			<div class="alert alert-info hidden">
				<p>Tick the box to confirm that you are the owner of this company.</p>
				<p>If your company did not display, please enter your company or trading name to create your listing.</p>
			</div>
		</div>
	</div>
	
	<div class="hidden company-hidden-default">
		<div class="row-fluid">
			<div class="span12">
				<label><?php _e( 'Company Name', '7listings' ); ?> <span class="required">*</span></label>
				<input type="text" name="post_title" id="company-post-title" class="span12">
			</div>
		</div>
		<div class="row-fluid">
			<div class="span9">
				<label class="description-label"><?php _e( 'Description', '7listings' ); ?>
					<span class="required">*</span></label>
				<textarea name="post_content" class="span12 description-input"></textarea>
			</div>
			<div class="span3"></div>
		</div><!--.row-fluid-->
		
		<div class="row-fluid" style="margin-top: 20px">
			<div class="span6 address-inputs">

				<label><?php _e( 'Address', '7listings' ); ?></label>
				<input type="text" name="address" value="" class="span12">
				<span class="help-block"><?php _e( 'Street Address', '7listings' ); ?></span>

				<input type="text" name="address2" value="" class="span12 no-highlight">
				<span class="help-block"><?php _e( 'Level or unit and Building Name', '7listings' ); ?></span>
				
				<div class="row-fluid">
					<div class="span6">
						<input type="number" min="0" max="9999" name="postcode" id="auto_fill_postcode" value="" class="span12">
						<span class="help-block"><?php _e( 'Zip Postal Code', '7listings' ); ?></span>
					</div>
					<div class="span6">
					<input type="text" name="state" id="auto_fill_state" class="span12 no-highlight" readonly>
						<span class="help-block"><?php _e( 'State / Province / Region', '7listings' ); ?></span>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<select name="area" id="auto_fill_suburb" class="span12 no-highlight"></select>
						<span class="help-block"><?php _e( 'Suburb / Town', '7listings' ); ?></span>
					</div>
					<div class="span6">
						<input type="text" name="city" id="auto_fill_city" value="" class="span12 no-highlight" readonly>
						<span class="help-block"><?php _e( 'City', '7listings' ); ?></span>
					</div>
				</div>

			</div><!-- .span6 -->

			<div class="span5 offset1 contact-inputs">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Website', '7listings' ); ?></label>
					<div class="controls">
						<input type="text" name="website" class="span12 no-highlight">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Company Email', '7listings' ); ?></label>
					<div class="controls">
						<input type="email" name="email" class="span8 no-highlight">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Phone', '7listings' ); ?></label>
					<div class="controls">
						<input type="tel" name="phone" value="<?= $phone ?>" class="span8 no-highlight">
					</div>
				</div>
			</div><!-- .span6 -->
		</div><!-- .row-fluid -->

		<div class="row-fluid">
			<div class="span6">
				<label><?php _e( 'Company Name', '7listings' ); ?> <span class="required">*</span></label>
				<input type="text" name="trading_name" class="span12">
			</div>
			<div class="span5 offset1">
				<label><?php _e( 'ABN', '7listings' ); ?> <span class="required">*</span></label>
				<input type="number" name="company_abn">
			</div>
		</div>

		<h2><?php _e( 'Invoice Recipient', '7listings' ); ?></h2>

		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Name', '7listings' ); ?></label>
					<div class="controls">
						<input type="text" name="invoice_name">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Position', '7listings' ); ?></label>

					<div class="controls">
						<input type="text" name="invoice_position">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Email', '7listings' ); ?></label>

					<div class="controls">
						<input type="email" name="invoice_email">
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label"><?php _e( 'Mobile', '7listings' ); ?></label>

					<div class="controls">
						<input type="text" name="invoice_phone" pattern="04[0-9]{8}" placeholder="04xxxxxxxx" title="<?php esc_attr_e( 'Must start with 04 and have 10 characters', '7listings' ); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Direct Line', '7listings' ); ?></label>
					<div class="controls">
						<input type="text" name="invoice_direct_line">
					</div>
				</div>
				<hr class="light">
				<div class="control-group checkbox-toggle" style="float:none">
					<label class="control-label"><?php _e( 'Does your business have Paypal account', '7listings' ); ?></label>
					<div class="controls">
						<?php SL_Form::checkbox_general( 'paypal_enable', 0 ); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php _e( 'Paypal Email', '7listings' ); ?></label>
					<div class="controls">
						<input type="email" name="paypal_email">
					</div>
				</div>
			</div>
		</div>

		<?php do_action( 'company_signup_form_after' ); ?>
	</div><!--.company-hidden-default-->

	<div class="submit">
		<input id="main-submit-btn" type="submit" name="submit" class="button booking large hidden" value="<?php _e( 'Sign Up', '7listings' ); ?>">
	</div>
</form>
