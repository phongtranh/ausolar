<?php

namespace ASQ\Shortcode;

/**
 * Class Quote_Form
 *
 * The Solar Split Form Shortcode Class
 *
 * @package ASQ\Shortcode
 */
class Quote_Form
{
	// Two-way data binding. The posted data from quote form and the data to be bind to the from
	public $data;

	// The form message. Store as string
	public $message;

	public function __construct()
	{
		// Use 'wp' hook to prepare data before page rendering
		add_action( 'wp', array( $this, 'process_post' ) );

		add_shortcode( 'power_quote_form', array( $this, 'shortcode' ) );
	}

	/**
	 * Process Post data and prepare data before page rendering.
	 *
	 * When loading page. If the form is loaded without post data, then bind default
	 * values to the form, otherwise, process the form with posted data.
	 *
	 * @param $data The data to be passed to the form
	 * @param $message The form message
	 *
	 * @return void
	 */
	public function process_post()
	{
		// Bind default values to form
		$data = array(
			23 => 'House',
			18 => 'Under 20 years',
			44 => 'Two Storey or more',
			43 => 'Tiled',
			42 => 'Flat',
			14 => 'yes',
			29 => '4',
			51 => 'Within the next month',
			48 => 'Local',
			53 => 'Ergon Energy',
			40 => 'Any_time_is_fine',
			'17_1'  => '',
			'17_4'  => '',
			'17_5'  => '',
			'17_3'  => '',
			'1_3'   => '',
			'1_6'   => '',
			3     => '',
			30    => 'Home',
			47      => 'I have no preference',
			11      => ''
		);

		// Also, supports auto populate the form via query string
		// same functionality as Gravity Form
		if ( isset( $_GET['postcode'] ) ){
			$data['17_5'] = trim( $_GET['postcode'] );
		}
		if ( isset( $_GET['full_name'] ) )
		{
			$name       = explode( ' ', trim( $_GET['full_name'] ) );
			$first_name = $name[0];
			$last_name  = '';
			if ( isset( $name[1] ) )
				$last_name = ltrim( $_GET['full_name'], $first_name );

			$data['1_3'] = $first_name;
			$data['1_6'] = $last_name;
		}

		if ( isset( $_GET['name_first'] ) ) $data['1_3'] = trim( $_GET['name_first'] );
		if ( isset( $_GET['name_last'] ) ) $data['1_6'] = trim( $_GET['name_last'] );
		if ( isset( $_GET['email'] ) ) $data[11] = trim( $_GET['email'] );
		if ( isset( $_GET['phone'] ) ) $data[3] = trim( $_GET['phone'] );

		$message = '';

		// Handle the post submission
		if ( isset ( $_POST['submit'] ) ):

			foreach ( $_POST as $key => $value ):
				if ( $key === 'submit' ) continue;
				$data[$key] = trim( $value );
			endforeach;

			$entry = $data;
			if ( $entry[23] === 'Work place or commercial installation' )
				$entry[30] = 'Business';

			foreach ( $entry as $key => $value )
			{
				$replaced_key = str_replace( '_', '.', $key );
				unset( $entry[$key] );
				$entry[$replaced_key] = $value;
			}

			$entry['form_id'] 	= 1;
			$entry[57] 			= isset( $_GET['source'] ) ? strtoupper( $_GET['source'] ) : 'I';

			if ( ! str_contains( $entry[29], 'kW' ) )
			{
				$entry[29] .= 'kW';
			}
			
			// Validate the data (phone, email) before process
			$validator = $this->validate( $entry );

			if ( is_wp_error( $validator ) )
			{
				$message = $validator->get_error_message();
			}
			else
			{
				// Validation correct
				// Insert form into #1
				$id 	= \GFAPI::add_entry( $entry );

				// Get inserted lead
				$lead 	= \GFAPI::get_entry( $id );

				// Run match leads function
				$form 	= \GFAPI::get_form( 1 );

				do_action( "gform_entry_created", $lead, $form );

				//Solar_Postcodes::after_submission( $lead );
				$redirect_to = isset( $_GET['redirect_to'] ) ? htmlentities( $_GET['redirect_to'] ) : '/solar-quotes/congratulations-2/';
 
				// Redirect to thank you page after complete
				wp_redirect( $redirect_to, 301 );

				exit( 0 );
			}
		endif;

		// Remember to save data to $data and $message properties. Because our form will use that
		$this->data     = $data;
		$this->message  = $message;
	}

	/**
	 * The form Markup
	 * @return string Output HTML
	 */
	public function shortcode()
	{
		$data 		= $this->data;
		$message 	= $this->message;

		$target 	= isset( $_GET['target'] ) ? htmlentities( $_GET['target'] ) : '_top';
		ob_start();
		?>
		<style type="text/css">
			@media (max-width: 680px){
				#solar-power-form{
					width: 96%;
				}
				#solar-power-form .span6{
					width: 100% !important;
					float: left;
					margin: 0;
				}
				.building-type img{
					margin-right: 5%;
				}
			}
		</style>

		<script type="text/javascript">
			jQuery(function($)
			{
				$( '#roof_pitch input' ).change( function()
				{
					$( '#roof-preview' ).attr( 'src', $( this ).data('preview') );
				} );

				$( '#field_29' ).change( function()
				{
					var system_size     = $(this).val();
					
					system_size_url = 'https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/systemsize' + system_size + '.png';
					
					$( '#system-size-preview' ).attr( 'src',  system_size_url );

					if ( system_size == 1 )
						$( '#outputsize' ).val( '1.5kW' );
					else if( system_size == 6 )
						$( '#outputsize' ).val( '5.0kW+' );
					else if( system_size == 7 )
						$( '#outputsize' ).val( 'I don\'t know yet' );
					else
						$( '#outputsize' ).val( system_size + 'kW' );
				} );

				$( '.ffq_btn input' ).click( function(){
					$( this ).parent().parent().find( '.ffq_btn' ).removeClass( 'active' );
					$( this ).parent().toggleClass( 'active' );
				} );

				$( '#ffq_next_btn' ).click( function()
				{
					$( '.form-body-step-1' ).fadeOut();
					$( '.form-body-step-2' ).fadeIn(500);
				} );

				$( '#ffq_back_btn' ).click( function()
				{
					$( '.form-body-step-2' ).fadeOut();
					$( '.form-body-step-1' ).fadeIn(500);
				} );
			} );
		</script>

		<form id="solar-power-form" method="post" target="<?php echo $target ?>">

		<header class="form-header">
			<h2>GET 3 FREE SOLAR QUOTES</h2>
		</header>

		<?php if ( ! empty ( $message ) ): ?>
			<section class="form-message">
				<div class="alert alert-error"><?php echo $message ?></div>
			</section>
		<?php endif; ?>

		<section class="form-body form-body-step-1 row-fluid">

			<div class="span6 pull-left">
				<header>
					<h3>Tell us abit about your property</h3>
					<p>This will help us point you in the right direction.</p>
				</header>

				<section id="building-types" class="form-section">
					<label>What type of building do you plan to have the system installed on? *</label>
					<div class="building-type">
						<div>
						<img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/home.png" alt="House" title="House" />
						</div>
						<div>
						<img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/garage.png" alt="Shed" title="Shed" />
						</div>
						<div>
						<img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/business.png" alt="Business" title="Business" />
						</div>
					</div>

					<div class="controls">
						<div class="ffq_btn-group ffqthreeway">
							<label class="ffq_btn <?php if ( $data['23'] === 'House' ) echo 'active' ?>">
								<input type="radio" <?php checked( $data['23'], 'House'); ?> name="23" value="House" id="buildingtype_0" required> House
							</label>
							<label class="ffq_btn <?php if ( $data['23'] === 'Shed or garage away from the home' ) echo 'active' ?>">
								<input type="radio" <?php checked( $data['23'], 'Shed or garage away from the home'); ?> name="23" value="Shed or garage away from the home" id="buildingtype_1"> Shed
							</label>
							<label class="ffq_btn <?php if ( $data['23'] === 'Work place or commercial installation' ) echo 'active' ?>">
								<input type="radio" <?php checked( $data['23'], 'Work place or commercial installation' ) ?> name="23" value="Work place or commercial installation" id="buildingtype_2"> Business
							</label>
						</div>
					</div>

				</section>

				<section id="age" class="control-group form-section">
					<label for="property_age">what is the age of your property?</label>
					<div class="controls">
						<select name="18" id="property_age" class="form-control" required>
							<option <?php selected( $data[18], 'Under 20 years' ) ?> value="Under 20 years">The property is less than 20 years old</option>
							<option <?php selected( $data[18], 'Over 20 years' ) ?> value="Over 20 years">The property is more than 20 years old</option>
							<option <?php selected( $data[18], 'U/C' ) ?> value="U/C">The property is under construction</option>
						</select>
					</div>
				</section>

				<section id="storey" class="form-section">
					<label>How many storeys is the property?</label>
					<span class="ffq_btn-group ffqtwoway">
					  <label class="ffq_btn <?php if ( $data[44] === 'Single' ) echo 'active' ?>">
						  <input type="radio" <?php checked( $data[44], 'Single' ) ?> name="44"  value="Single" id="propertytype_0" required>Single
					  </label>
					  <label class="ffq_btn <?php if ( $data[44] === 'Two Storey or more' ) echo 'active' ?>">
						  <input type="radio" <?php checked( $data[44], 'Two Storey or more' ) ?> name="44" value="Two Storey or more" id="propertytype_1"> Multi Storey
					  </label>
					</span>
				</section>

				<section id="roof" class="form-section">
					<label for="field_43">Describe the roof</label>
					<div class="controls">
						<select name="43" id="field_43" class="form-control">
							<option <?php selected( $data[43], 'Tin' ) ?> value="Tin">Tin or colourbond sheeting</option>
							<option <?php selected( $data[43], 'Tiled' ) ?> value="Tiled">Tiled roof</option>
							<option <?php selected( $data[43], 'Slate' ) ?> value="Slate">Slate roof</option>
							<option <?php selected( $data[43], 'Other' ) ?> value="Other">Other</option>
							<option <?php selected( $data[43], 'Notsure' ) ?> value="Notsure">Not sure</option>
						</select>
					</div>
				</section>

				<section id="roof_pitch" class="form-section">
					<label>What's the roof pitch</label>

					<div class="roof-preview">
						<img id="roof-preview" src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/flat.png" alt="preview" />
					</div>

					<div class="controls">
						<label>
							<input <?php checked( $data[42], 'Flat' ) ?> type="radio" name="42" value="Flat" id="roof_0" data-preview="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/flat.png" required> Flat
						</label>
						<label>
							<input <?php checked( $data[42], '15 degree pitch' ) ?> type="radio" name="42" value="15 degree pitch" data-preview="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/15degreepitch.png" id="roof_1"> 15 degree pitch
						</label>
						<label>
							<input <?php checked( $data[42], '25 degree pitch' ) ?> type="radio" name="42" value="25 degree pitch" id="roof_2" data-preview="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/25degreepitch.png"> 25 degree pitch
						</label>
						<label>
							<input <?php checked( $data[42], '35 degree pitch or higher' ) ?> type="radio" name="42" value="35 degree pitch or higher" data-preview="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/35degreepitchorhigher.png" id="roof_2"> 35 degree pitch or higher
						</label>
						<label>
							<input <?php checked( $data[42], 'Not_sure' ) ?> type="radio" name="42" value="Not_sure" id="roof_2" data-preview="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/notsure.png"> Not sure
						</label>
					</div>
				</section>
			</div>

			<div class="span6">
				<header>
					<h3>TYPE OF SYSTEM AND SERVICE</h3>
					<p>Let us know about your special needs.</p>
				</header>

				<section id="rights" class="form-section">
					<label>Do you own or rent the property?</label>

					<span class="ffq_btn-group ffqtwoway">
					  <label class="ffq_btn <?php if ( $data[14] === 'yes' ) echo 'active' ?>">
						  <input <?php checked( $data[14], 'yes' ) ?> type="radio" name="14" value="yes" id="propertyown_0" required>
						  I Own the Property</label>
					  <label class="ffq_btn <?php if ( $data[14] === 'no' ) echo 'active' ?>">
						  <input <?php checked( $data[14], 'no' ) ?> type="radio" name="14" value="no" id="propertyown_1">
						  I Rent the Property</label>
					</span>
				</section>

				<section id="system_size" class="form-section">
					<label>System size</label>

					<div class="system-size">
						<img id="system-size-preview" src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/systemsize4.png" alt="Sitem Size Preview" />
					</div>

					<span class="input">
						<input id="field_29" type="range" step="1" max="7" min="1" class="form-control" name="29" value="<?php echo $data[29] ?>">
						<output id="outputsize" name="vSystemSize" for="solarcalc-size">4.0KW</output>
					</span>
				</section>

				<section id="timeframe" class="control-group form-section">
					<label>What is your purchase time frame?</label>
					<select name="51" class="form-control">
						<option <?php selected( $data[51], 'Within the next month' ) ?> value="Within the next month">Within the next month</option>
						<option <?php selected( $data[51], 'Immediately' ) ?> value="Immediately">Immediately</option>
						<option <?php selected( $data[51], 'Within the next 3 months' ) ?> value="Within the next 3 months">Within the next 3 months</option>
						<option <?php selected( $data[51], 'Within the next 6 months' ) ?> value="Within the next 6 months">Within the next 6 months</option>
						<option <?php selected( $data[51], 'Undecided, i\'m just after information and prices' ) ?> value="Undecided, i'm just after information and prices">Undecided, i'm just after information and prices</option>
					</select>
				</section>

				<section id="company_types" class="control-group form-section">
					<label for="field_48">What type of companies?</label>
					<select name="48" class="form-control" id="field_48">
						<option <?php selected( $data[48], 'I_have_no_preference' ) ?> value="I_have_no_preference">I have no preference</option>
						<option <?php selected( $data[48], 'Local' ) ?> value="Local">Local</option>
						<option <?php selected( $data[48], 'State-wide' ) ?> value="State-wide">State-wide</option>
						<option <?php selected( $data[48], 'National' ) ?> value="National">National</option>
					</select>
				</section>

				<section class="control-group form-section">
					<label for="field_53">Who do you purchase electricity from?*</label>
					<?php
					$eletric_companies = array(
						'Ergon Energy', 'AGL', 'Australian Power &amp; Gas',
						'Click Energy',	'Dodo Power &amp; Gas','Energy Australia',
						'Integral Energy','Lumo Energy','Origin Energy','Powerdirect',
						'ActewAGL','Momentum Energy','Country Energy',
						'Red Energy',	'Alinta Energy','Diamond Energy',
						'Simply Energy','Synergy',	'Western Power','Perth Energy',
						'NT Power &amp; Water','Aurora Energy',	'Horizon Power','Other'
					);
					?>

					<select name="53" id="field_53" class="form-control">
						<?php foreach ( $eletric_companies as $index => $company ): ?>
							<option <?php selected( $data[53], $company ) ?> value="<?php echo $company ?>"><?php echo $company ?></option>
						<?php endforeach; ?>
					</select>

					<span class="help-block">This will help the installers inform you about the solar feed-in tariff</span>
				</section>
			</div><!--.span6-->
			
			<footer class="row-fluid form-footer pull-left">
				<div class="span8 pull-left">
					<small>The privacy of our viewers and subscibers is valued by Australian Solar Quotes and we will only handle your personal information in accordance with our <a href="http://www.australiansolarquotes.com.au/about-us/privacy-policy/">Privacy Policy</a></small>
				</div>
				
				<div class="span3 pull-right">
					<button type="button" class="ffq_btn-submit pull-right" id="ffq_next_btn"></button>
				</div>
			</footer>

		</section><!--form-body-step-1-->

		<section class="form-body form-body-step-2 hide">

			<div class="row-fluid">
				<div class="span12">
					<h3 class="sys-ins-title">WHERE DO YOU PLAN TO HAVE THE SOLAR POWER SYSTEM INSTALLED?</h3>
					<hr />
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="control-group form-section" id="installation_address">
						<label for="input_1_17_1">Installation Address</label>
						<div class="controls">
							<input id="input_1_17_1" value="<?php echo $data['17_1'] ?>" name="17.1" type="text" class="form-control" placeholder="Street Address" required />

							<div class="row-fluid">
								<div class="span6" id="input_1_17_5_container">
									<input id="input_1_17_5" name="17.5" value="<?php echo $data['17_5'] ?>" class="form-control" type="number" min="0" max="9999" placeholder="Post Code" />
								</div>
								<div class="span6" id="input_1_17_3_container">
									<input type="text" id="input_1_17_3" value="<?php echo $data['17_3'] ?>" class="form-control" name="17.3" readonly>
								</div>
							</div>
							
							<div class="row-fluid">
								<div class="span6" id="input_1_17_4_container">
									<input type="text" id="input_1_17_4" value="<?php echo $data['17_4'] ?>" class="form-control" name="17.4" readonly>
								</div>
							</div>
							
						</div>
					</div><!--#installation-address-->

					<div class="control_group form-section">
						<label>Finally, lets grab some contact details</label>
						<span class="help-block">So that we can get you in touch with our solar installers, we’ll need to grab some contact details from you.</span>

						<label for="field_1.3">Name</label>
						<div class="controls row-fluid">
							<div class="span6">
								<input type="text" value="<?php echo $data['1_3'] ?>" class="form-control" name="1.3" id="field_1.3" placeholder="First Name" required />
							</div>
							<div class="span6">
								<input type="text" value="<?php echo $data['1_6'] ?>" class="form-control" name="1.6" id="field_1.6" placeholder="Last Name" required />
							</div>
						</div>

						<label for="field_11">Email</label>
						<div class="controls">
							<input type="email" value="<?php echo $data[11] ?>" class="form-control" name="11" id="field_11" placeholder="Email" required />
						</div>

						<label for="field_40">What is the best time to get in touch?</label>
						<div class="controls">
							<select name="40" class="form-control" required>
								<option <?php selected( $data[40], 'Any time is fine' ) ?> value="Any_time_is_fine">Any time is fine</option>
								<option <?php selected( $data[40], '08am' ) ?> value="08am">08am</option>
								<option <?php selected( $data[40], '09am' ) ?> value="09am">09am</option>
								<option <?php selected( $data[40], '10am' ) ?> value="10am">10am</option>
								<option <?php selected( $data[40], '11am' ) ?> value="11am">11am</option>
								<option <?php selected( $data[40], '12am' ) ?> value="12am">12am</option>
								<option <?php selected( $data[40], '01pm' ) ?> value="01pm">01pm</option>
								<option <?php selected( $data[40], '02pm' ) ?> value="02pm">02pm</option>
								<option <?php selected( $data[40], '03pm' ) ?> value="03pm">03pm</option>
								<option <?php selected( $data[40], '04pm' ) ?> value="04pm">04pm</option>
								<option <?php selected( $data[40], '05pm' ) ?> value="05pm">05pm</option>
								<option <?php selected( $data[40], '06pm' ) ?> value="06pm">06pm</option>
								<option <?php selected( $data[40], '07pm' ) ?> value="07pm">07pm</option>
								<option <?php selected( $data[40], '08pm' ) ?> value="08pm">08pm</option>
							</select>
						</div>

						<label for="field_3">MOBILE PHONE NUMBER?*</label>
						<div class="controls">
							<input type="tel" class="form-control" name="3" value="<?php echo $data[3] ?>" required />
						</div>
					</div>
				</div><!--left-->

				<div class="span6">
					<div class="form-group form-section" id="real-testimonials">
						<label>Real people, Real testimonials</label>
						<div class="testimonials">
							<img src="https://www.australiansolarquotes.com.au/wp-content/themes/solar-listings/images/free-quotes/real1.png" alt="Real" title="Real"/>
							<h3 class="testimonial-title">Thank you Australian Solar Quotes</h3>
							<p> I had been to a few other websites when I began looking to have solar panels installed in my house. And, out of all the ones I found, none could compare to the service Australian Solar Quotes provided. They are by far the fastest, and most reliable site I’ve come across. I would recommend them to anyone that will listen.</p>
						</div>
					</div>

					<ul class="border-top">
						<li>Compare price,quallity &amp; service</li>
						<li>24/7 customer service and support</li>
						<li>Free solar savings calculator</li>
						<li>Free solar power buyers guide</li>
						<li>Top 20 questions to ask the installers</li>
						<li>Over 10,000  reviews and ratings</li>
					</ul>

					<div class="buttion_size">
						<input class="ffq-get-quotes" value="GET 3 QUOTES" type="submit" name="submit">
					</div>
				</div>
			</div>

			<footer class="form-footer">
				<button type="button" id="ffq_back_btn"></button>
				<small>The privacy of our viewers and subscibers is valued by Australian Solar Quotes and we will only handle your personal information in accordance with our <a href="http://www.australiansolarquotes.com.au/about-us/privacy-policy/">Privacy Policy</a></small>
			</footer>

		</section><!--.step-2-->

		</form>

		<?php
		return ob_get_clean();
	}

	public function validate( $lead )
	{
		foreach ( $lead as $key => $value )
		{
			if ( empty( $value ) )
				return new \WP_Error( 'missing', 'Please enter values to all required field!' );
		}

		if ( !filter_var( $lead[11], FILTER_VALIDATE_EMAIL ) )
			return new \WP_Error( "wrong", "The email you've entered is not correct!" );

		$email_check = array();

		$email_check["field_filters"][] = array( 'key' => '11', 'value' => $lead[11] );

		// Check if email is exists. If so, exit
		$emails = \GFAPI::get_entries( 1, $email_check );

		if ( count( $emails ) > 0 )
			return new \WP_Error( "duplicate", "We've already got your details recorded. We will be in touch with you shortly" );

		$phone_check = array();

		$phone_check["field_filters"][] = array( 'key' => '3', 'value' => $lead[3] );

		$phones = \GFAPI::get_entries( 1, $phone_check );

		if ( count( $phones ) > 0 )
			return new \WP_Error( "duplicate", "We've already got your details recorded. We will be in touch with you shortly." );
	}
}

new \ASQ\Shortcode\Quote_Form;