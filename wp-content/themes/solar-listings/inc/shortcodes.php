<?php

class Solar_Listings_Shortcodes
{
	function __construct()
	{
		$shortcodes = [
			'quote', 'intro', 'quote_widget', 'testimonial_header', 'quote_widget_short',
			'new_multistep_quote', 'postcode_quote', 'rating_widget'
		];
		// testimonial_header is here for easy migration and fallback
		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( "solar-$shortcode", array( $this, $shortcode ) );
		}
		add_shortcode( 'quotes_marquee', array( $this, 'quotes_marquee' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_footer', array( $this, 'footer_script' ), 1000 );
	}

	function quote()
	{
		$current_url = is_ssl() ? 'https://' : 'http://';
		if ( $_SERVER['SERVER_PORT'] != '80' )
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>
		<div id="solar-quote-wrapper">
			<h2 id="solar-quote-title">Free Solar Quotes</h2>

			<div id="slogan-slider">
				<h3>From 5 star rated companies</h3>

				<h3>Reputable installers</h3>

				<h3>No obligation quotes</h3>

				<h3>Absolutely FREE!</h3>
			</div>
			<section id="quote-form" class="main gform_widget">
				<script src="/wp-content/themes/solar-listings/js/gf-shortcode.js"></script>
				<script> var gf_global = {
						"gf_currency_config": {
							"name"              : "Australian Dollar",
							"symbol_left"       : "$",
							"symbol_right"      : "",
							"symbol_padding"    : " ",
							"thousand_separator": ",",
							"decimal_separator" : ".",
							"decimals"          : 2
						},
						"base_url"          : "\/wp-content\/plugins\/gravityforms",
						"number_formats"    : [],
						"spinnerUrl"        : "\/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
					}; </script>

				<div id="free">FREE Quotes</div>
				<div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_27">
					<a id="gf_27" class="gform_anchor"></a>
					<h3>Solar Quotes</h3>
					<h6>from 5 <i class="icon-star"></i> rated companies</h6>
					<form method="post" enctype="multipart/form-data" target="gform_ajax_frame_27" id="gform_27" class="simple-quote multistep" action="<?php echo $current_url; ?>">
						<div class="quote-inputs gform_fields top_label">
							<input required name="input_1" id="input_27_1" type="tel" min="100" max="9999" value="" class="solar-quote-input postcode" tabindex="77" placeholder="Postcode" title="Please enter your postcode" pattern="{4}" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">
							<input required name="input_2" id="input_27_2" type="text" value="" class='solar-quote-input name' tabindex='78' placeholder="Name" title="Please enter your name">
							<input required name="input_3" id="input_27_3" type='tel' value='' class='solar-quote-input phone' tabindex='79' placeholder="Phone" title="Please enter your phone number" pattern="{10}" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">
							<input required name="input_4" id="input_27_4" type="email" value="" class='solar-quote-input email' tabindex='80' placeholder="Email" title="Please enter you email" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">

							<div id="field_27_6" class="gfield step form-phone" style="display: none;">
				      	<span class="icon-stack">
				      		<i class="icon-chevron-sign-down"></i>
				      		<i class="icon-stack-base icon-circle white"></i>
				      	</span>

								<p class="title">How do you want to get your FREE quotes?</p>
								<div id="input_27_6" class="options">
									<p class="gchoice_6_0">
										<input required name="input_6" type="radio" value="long quote form" id="choice_6_0" tabindex="81" onClick="gf_apply_rules(27,[7]);">
										<label for="choice_6_0" id="label_6_0">complete a simple online form</label>
									</p>
									<p class="gchoice_6_1">
										<input name="input_6" type="radio" value="call back" id="choice_6_1" tabindex="82" onClick="gf_apply_rules(27,[7]);">
										<label for="choice_6_1" id="label_6_1">speak with a solar consultant</label>
									</p>
								</div>
							</div>

							<div id="field_27_7" class="gfield step call-back-times" style="display: none;">
				      	<span class="icon-stack">
				      		<i class="icon-chevron-sign-down"></i>
				      		<i class="icon-stack-base icon-circle white"></i>
				      	</span>

								<p class="title">When can we call you?</p>
								<p class="gchoice_7_0">
									<input name="input_7" type="radio" value="Morning" id="choice_7_0" class="morning" tabindex="83">
									<label for="choice_7_0" id="label_7_0">Morning</label>
								</p>
								<p class="gchoice_7_1">
									<input name="input_7" type="radio" value="Lunch time" id="choice_7_1" class="lunch" tabindex="84">
									<label for="choice_7_1" id="label_7_1">Lunch time</label>
								</p>
								<p class="gchoice_7_2">
									<input name="input_7" type="radio" value="Afternoon" id="choice_7_2" class="afternoon" tabindex="85">
									<label for="choice_7_2" id="label_7_2">Afternoon</label>
								</p>
								<p class="gchoice_7_3">
									<input name="input_7" type="radio" value="Late afternoon" id="choice_7_3" class="late" tabindex="86">
									<label for="choice_7_3" id="label_7_3">Late afternoon</label>
								</p>
								<p class="gchoice_7_4">
									<input name="input_7" type="radio" value="Any time is fine" id="choice_7_4" class="anytime" tabindex="87">
									<label for="choice_7_4" id="label_7_4">Any time is fine</label>
								</p>
							</div>
						</div>

						<div class="gform_footer top_label">
							<input type="submit" id="gform_submit_button_27" class="button gform_button" value="Get 3 Quotes" tabindex="88" onClick="if(window[&quot;gf_submitting_27&quot;]){return false;}  if( !jQuery(&quot;#gform_27&quot;)[0].checkValidity || jQuery(&quot;#gform_27&quot;)[0].checkValidity()){window[&quot;gf_submitting_27&quot;]=true;} ">
							<input type="hidden" name="gform_ajax" value="form_id=27&amp;title=&amp;description=&amp;tabindex=1">
							<input type="hidden" class="gform_hidden" name="is_submit_27" value="1">
							<input type="hidden" class="gform_hidden" name="gform_submit" value="27">
							<input type="hidden" class="gform_hidden" name="gform_unique_id" value="">
							<input type="hidden" class="gform_hidden" name="state_27" value="WyJbXSIsIjNiYTkzNTI5MTg4ZDMyZTc3YmQ4Zjc1Yjg0YzI3MWQxIl0=">
							<input type="hidden" class="gform_hidden" name="gform_target_page_number_27" id="gform_target_page_number_27" value="0">
							<input type="hidden" class="gform_hidden" name="gform_source_page_number_27" id="gform_source_page_number_27" value="1">
							<input type="hidden" name="gform_field_values" value="">
						</div>
					</form>
				</div>
				<iframe style="display:none;width:0px;height:0px;" src="about:blank" name="gform_ajax_frame_27" id="gform_ajax_frame_27"></iframe>
				<script>jQuery( document ).ready( function ( $ )
					{
						gformInitSpinner( 27, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
						jQuery( '#gform_ajax_frame_27' ).load( function ()
						{
							var contents = jQuery( this ).contents().find( '*' ).html();
							var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
							if ( !is_postback )
							{
								return;
							}
							var form_content = jQuery( this ).contents().find( '#gform_wrapper_27' );
							var is_redirect = contents.indexOf( 'gformRedirect(){' ) >= 0;
							var is_form = !(form_content.length <= 0 || is_redirect);
							if ( is_form )
							{
								jQuery( '#gform_wrapper_27' ).html( form_content.html() );
								jQuery( document ).scrollTop( jQuery( '#gform_wrapper_27' ).offset().top );
								if ( window['gformInitDatepicker'] )
								{
									gformInitDatepicker();
								}
								if ( window['gformInitPriceFields'] )
								{
									gformInitPriceFields();
								}
								var current_page = jQuery( '#gform_source_page_number_27' ).val();
								gformInitSpinner( 27, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
								jQuery( document ).trigger( 'gform_page_loaded', [27, current_page] );
								window['gf_submitting_27'] = false;
							}
							else if ( !is_redirect )
							{
								var confirmation_content = jQuery( this ).contents().find( '#gforms_confirmation_message' ).html();
								if ( !confirmation_content )
								{
									confirmation_content = contents;
								}
								setTimeout( function ()
								{
									jQuery( '#gform_wrapper_27' ).replaceWith( '<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_27\'' + '>' + confirmation_content + '<' + '/div' + '>' );
									jQuery( document ).scrollTop( jQuery( '#gforms_confirmation_message' ).offset().top );
									jQuery( document ).trigger( 'gform_confirmation_loaded', [27] );
									window['gf_submitting_27'] = false;
								}, 50 );
							}
							else
							{
								jQuery( '#gform_27' ).append( contents );
								if ( window['gformRedirect'] )
								{
									gformRedirect();
								}
							}
							jQuery( document ).trigger( 'gform_post_render', [27, current_page] );
						} );
					} );</script>
				<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
						"gf_currency_config": {
							"name"              : "Australian Dollar",
							"symbol_left"       : "$",
							"symbol_right"      : "",
							"symbol_padding"    : " ",
							"thousand_separator": ",",
							"decimal_separator" : ".",
							"decimals"          : 2
						},
						"base_url"          : "\/wp-content\/plugins\/gravityforms",
						"number_formats"    : [],
						"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
					};
					jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
					{
						if ( formId == 27 )
						{
							if ( window['jQuery'] )
							{
								if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
								window['gf_form_conditional_logic'][27] = {
									'logic'     : {
										6: {
											"field"     : {
												"actionType": "show",
												"logicType" : "all",
												"rules"     : [{
													"fieldId" : "4",
													"operator": "contains",
													"value"   : "@"
												}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
													"fieldId" : "3",
													"operator": "isnot",
													"value"   : ""
												}]
											},
											"nextButton": null,
											"section"   : null
										},
										7: {
											"field"        : {
												"actionType": "show",
												"logicType" : "all",
												"rules"     : [{
													"fieldId" : "6",
													"operator": "is",
													"value"   : "call back"
												}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
											}, "nextButton": null, "section": null
										}
									},
									'dependents': { 6: [6], 7: [7] },
									'animation' : 1,
									'defaults'  : []
								};
								if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
								jQuery( document ).ready( function ()
								{
									gf_apply_rules( 27, [6, 7], true );
									jQuery( '#gform_wrapper_27' ).show();
									jQuery( document ).trigger( 'gform_post_conditional_logic', [27, null, true] );
								} );
							}
						}
					} );
					jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
					{
					} );</script>
				<script> jQuery( document ).ready( function ()
					{
						jQuery( document ).trigger( 'gform_post_render', [27, 1] )
					} ); </script>
			</section>
		</div>
		<?php
		return ob_get_clean();
	}

	function intro()
	{
		ob_start();
		?>

		<div class="row-fluid solar-intro">
			<div class="span4">
				<div class="alert">
					<a href="/buyers-guide/"><img class="alignnone" title="Solar Power Buyer's Guide" alt="Solar Power Buyer's Guide" src="/wp-content/themes/solar-listings/images/solar-power-buyers-guide-family.jpg" width="212" height="172" /></a>
					<a href="/buyers-guide/" class="button yellow full">Buyer's Guide</a><span class="intro-description">Read through our easy to understand Australian solar power buyer's guide and avoid unexpected surprises.</span>
				</div>
			</div>

			<div class="span4">
				<div class="alert">
					<a href="resources/solar-savings-calculator/"><img class="alignnone" title="Solar Power Rebate" src="http://www.australiansolarquotes.com.au/wp-content/uploads/2014/01/Solar-Power-Savings-Calculator_212x172.jpg" alt="Solar-Power-Savings-Calculator ROI" width="212" height="172" /></a>
					<a class="button yellow full" href="resources/solar-savings-calculator/">Solar Calculator</a><span class="intro-description">Find out what your solar power return on investment is when you install solar panels with our free solar power savings calculator.</span>
				</div>
			</div>

			<div class="span4">
				<div class="alert">
					<a href="/solar-installers/"><img class="alignnone" title="Solar Installer Reviews" alt="Research your local solar installers and read through the solar installer reviews" src="/wp-content/themes/solar-listings/images/solar-installer-reviews.jpg" width="212" height="172" /></a>
					<a href="/solar-installers/" class="button yellow full">Reviews</a><span class="intro-description">Browse through our solar panel installer reviews and see what others are saying about your local solar panel installers.</span>
				</div>
			</div>
		</div>

		<?php
		return ob_get_clean();
	}

	function enqueue()
	{
		wp_enqueue_script( 'jquery' );
	}

	function footer_script()
	{
		?>
		<script>
			// First shortcode
			(function ( $ )
			{
				$( document ).trigger( 'gform_post_render', [26, 1] );
			})( jQuery );

			// Slider
			(function ( $ )
			{
				var $slider = $( '#slogan-slider' ),
					$slides = $slider.children(),
					$slide = $slides.first(),
					current = 0,
					total = $slides.length;

				$slides.hide();
				$slide.show();

				setInterval( function ()
				{
					current++;
					if ( current == total )
						current = 0;
					$slide.fadeOut();
					$slide = $slides.eq( current );
					$slide.fadeIn();
				}, 3000 );
			})( jQuery );
		</script>
		<?php
	}

	// New multistep widget
	function quote_widget()
	{
		$current_url = is_ssl() ? 'https://' : 'http://';
		if ( $_SERVER['SERVER_PORT'] != '80' )
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>

		<script>
			jQuery( document ).ready( function ( e )
			{
				gformInitSpinner( 27, "/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif" );
				jQuery( "#gform_ajax_frame_27" ).load( function ()
				{
					var e = jQuery( this ).contents().find( "*" ).html();
					var t = e.indexOf( "GF_AJAX_POSTBACK" ) >= 0;
					if ( !t )
					{
						return
					}
					var n = jQuery( this ).contents().find( "#gform_wrapper_27" );
					jQuery( this ).contents().find( "h6" ).append( '<h4 class="feat">Save money in 5 minutes</h4>' );
					jQuery( this ).contents().find( "h3, h6" ).hide();
					jQuery( this ).contents().find( "#field_27_6, #field_27_7" ).addClass( "step" ).find( ".gfield_label" ).remove().prepend( '<span class="icon-stack"><i class="icon-chevron-sign-down"></i><i class="icon-stack-base icon-circle white"></i></span>' );
					jQuery( this ).contents().find( "#field_27_6" ).prepend( '<p class="title">How do you want to get your FREE quotes?</p>' );
					jQuery( this ).contents().find( "#field_27_7" ).prepend( '<p class="title">When can we call you?</p>' );
					jQuery( this ).contents().find( ".validation_message" ).hide();
					var r = jQuery( this ).contents().find( ".validation_message" ).text();
					if ( r != "" )
					{
						jQuery( "#myModal" ).modal( "show" )
					}
					var i = e.indexOf( "gformRedirect(){" ) >= 0;
					var s = !(n.length <= 0 || i);
					if ( s )
					{
						jQuery( "#gform_wrapper_27" ).html( n.html() );
						jQuery( document ).scrollTop( jQuery( "#gform_wrapper_27" ).offset().top );
						if ( window["gformInitDatepicker"] )
						{
							gformInitDatepicker()
						}
						if ( window["gformInitPriceFields"] )
						{
							gformInitPriceFields()
						}
						var o = jQuery( "#gform_source_page_number_27" ).val();
						gformInitSpinner( 27, "/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif" );
						jQuery( document ).trigger( "gform_page_loaded", [27, o] );
						window["gf_submitting_27"] = false
					}
					else if ( !i )
					{
						var u = jQuery( this ).contents().find( "#gforms_confirmation_message" ).html();
						if ( !u )
						{
							u = e
						}
						setTimeout( function ()
						{
							jQuery( "#gform_wrapper_27" ).replaceWith( "<" + "div id='gforms_confirmation_message' class='gform_confirmation_message_27'" + ">" + u + "<" + "/div" + ">" );
							jQuery( document ).scrollTop( jQuery( "#gforms_confirmation_message" ).offset().top );
							jQuery( document ).trigger( "gform_confirmation_loaded", [27] );
							window["gf_submitting_27"] = false
						}, 50 )
					}
					else
					{
						jQuery( "#gform_27" ).append( e );
						if ( window["gformRedirect"] )
						{
							gformRedirect()
						}
					}
					jQuery( document ).trigger( "gform_post_render", [27, o] )
				} )
			} )
		</script>

		<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
				"gf_currency_config": {
					"name"              : "Australian Dollar",
					"symbol_left"       : "$",
					"symbol_right"      : "",
					"symbol_padding"    : " ",
					"thousand_separator": ",",
					"decimal_separator" : ".",
					"decimals"          : 2
				},
				"base_url"          : "\/wp-content\/plugins\/gravityforms",
				"number_formats"    : [],
				"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
			};
			jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
			{
				if ( formId == 27 )
				{
					if ( window['jQuery'] )
					{
						if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
						window['gf_form_conditional_logic'][27] = {
							'logic'     : {
								6: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "4",
											"operator": "contains",
											"value"   : "@"
										}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
											"fieldId" : "3",
											"operator": "isnot",
											"value"   : ""
										}]
									}, "nextButton": null, "section": null
								},
								7: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "6",
											"operator": "is",
											"value"   : "call back"
										}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
									}, "nextButton": null, "section": null
								}
							},
							'dependents': { 6: [6], 7: [7] },
							'animation' : 1,
							'defaults'  : []
						};
						if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
						jQuery( document ).ready( function ()
						{
							gf_apply_rules( 27, [6, 7], true );
							jQuery( '#gform_wrapper_27' ).show();
							jQuery( document ).trigger( 'gform_post_conditional_logic', [27, null, true] );
						} );
					}
				}
			} );
			jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
			{
			} );</script>
		<script> jQuery( document ).ready( function ()
			{
				jQuery( document ).trigger( 'gform_post_render', [27, 1] )
			} ); </script>

		<aside id="quote-form" class="widget gform_widget">
			<script src="/wp-content/themes/solar-listings/js/gf-shortcode.js"></script>
			<script> var gf_global = {
					"gf_currency_config": {
						"name"              : "Australian Dollar",
						"symbol_left"       : "$",
						"symbol_right"      : "",
						"symbol_padding"    : " ",
						"thousand_separator": ",",
						"decimal_separator" : ".",
						"decimals"          : 2
					},
					"base_url"          : "\/wp-content\/plugins\/gravityforms",
					"number_formats"    : [],
					"spinnerUrl"        : "\/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
				}; </script>

			<div id="free">FREE Quotes</div>
			<div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_27">
				<a id="gf_27" class="gform_anchor"></a>
				<h3 class="side">Solar Quotes</h3>
				<h4 class="feat">Save money in 5 minutes</h4>
				<h6 class="side">from 5 <i class="icon-star"></i> rated companies</h6>
				<form method="post" enctype="multipart/form-data" target="gform_ajax_frame_27" id="gform_27" class="simple-quote multistep" action="<?php echo $current_url; ?>">
					<div class="quote-inputs gform_fields top_label">
						<input required name="input_1" id="input_27_1" type="tel" min="100" max="9999" value="" class="solar-quote-input postcode" tabindex="77" placeholder="Postcode" title="Please enter your postcode" pattern="{4}" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">
						<input required name="input_2" id="input_27_2" type="text" value="" class='solar-quote-input name' tabindex='78' placeholder="Name" title="Please enter your name">
						<input required name="input_3" id="input_27_3" type='tel' value='' class='solar-quote-input phone' tabindex='79' placeholder="Phone" title="Please enter your phone number" pattern="{10}" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">
						<input required name="input_4" id="input_27_4" type="email" value="" class='solar-quote-input email' tabindex='80' placeholder="Email" title="Please enter you email" onChange="gf_apply_rules(27,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(27,[6])&quot;, 300);">

						<div id="field_27_6" class="gfield step form-phone" style="display: none;">
			      	<span class="icon-stack">
			      		<i class="icon-chevron-sign-down"></i>
			      		<i class="icon-stack-base icon-circle white"></i>
			      	</span>

							<p class="title">How do you want to get your FREE quotes?</p>
							<div id="input_27_6" class="options">
								<p class="gchoice_6_0">
									<input required name="input_6" type="radio" value="long quote form" id="choice_6_0" tabindex="81" onClick="gf_apply_rules(27,[7]);">
									<label for="choice_6_0" id="label_6_0">complete a simple online form</label>
								</p>
								<p class="gchoice_6_1">
									<input name="input_6" type="radio" value="call back" id="choice_6_1" tabindex="82" onClick="gf_apply_rules(27,[7]);">
									<label for="choice_6_1" id="label_6_1">speak with a solar consultant</label>
								</p>
							</div>
						</div>

						<div id="field_27_7" class="gfield step call-back-times" style="display: none;">
			      	<span class="icon-stack">
			      		<i class="icon-chevron-sign-down"></i>
			      		<i class="icon-stack-base icon-circle white"></i>
			      	</span>

							<p class="title">When can we call you?</p>
							<p class="gchoice_7_0">
								<input name="input_7" type="radio" value="Morning" id="choice_7_0" class="morning" tabindex="83">
								<label for="choice_7_0" id="label_7_0">Morning</label>
							</p>
							<p class="gchoice_7_1">
								<input name="input_7" type="radio" value="Lunch time" id="choice_7_1" class="lunch" tabindex="84">
								<label for="choice_7_1" id="label_7_1">Lunch time</label>
							</p>
							<p class="gchoice_7_2">
								<input name="input_7" type="radio" value="Afternoon" id="choice_7_2" class="afternoon" tabindex="85">
								<label for="choice_7_2" id="label_7_2">Afternoon</label>
							</p>
							<p class="gchoice_7_3">
								<input name="input_7" type="radio" value="Late afternoon" id="choice_7_3" class="late" tabindex="86">
								<label for="choice_7_3" id="label_7_3">Late afternoon</label>
							</p>
							<p class="gchoice_7_4">
								<input name="input_7" type="radio" value="Any time is fine" id="choice_7_4" class="anytime" tabindex="87">
								<label for="choice_7_4" id="label_7_4">Any time is fine</label>
							</p>
						</div>
					</div>

					<div class="gform_footer top_label">
						<input type="submit" id="gform_submit_button_27" class="button gform_button" value="Get 3 Quotes" tabindex="88" onClick="if(window[&quot;gf_submitting_27&quot;]){return false;}  if( !jQuery(&quot;#gform_27&quot;)[0].checkValidity || jQuery(&quot;#gform_27&quot;)[0].checkValidity()){window[&quot;gf_submitting_27&quot;]=true;} ">
						<input type="hidden" name="gform_ajax" value="form_id=27&amp;title=&amp;description=&amp;tabindex=1">
						<input type="hidden" class="gform_hidden" name="is_submit_27" value="1">
						<input type="hidden" class="gform_hidden" name="gform_submit" value="27">
						<input type="hidden" class="gform_hidden" name="gform_unique_id" value="">
						<input type="hidden" class="gform_hidden" name="state_27" value="WyJbXSIsIjNiYTkzNTI5MTg4ZDMyZTc3YmQ4Zjc1Yjg0YzI3MWQxIl0=">
						<input type="hidden" class="gform_hidden" name="gform_target_page_number_27" id="gform_target_page_number_27" value="0">
						<input type="hidden" class="gform_hidden" name="gform_source_page_number_27" id="gform_source_page_number_27" value="1">
						<input type="hidden" name="gform_field_values" value="">
					</div>
				</form>
			</div>
			<iframe style="display:none;width:0px;height:0px;" src="about:blank" name="gform_ajax_frame_27" id="gform_ajax_frame_27"></iframe>
			<script>jQuery( document ).ready( function ( $ )
				{
					gformInitSpinner( 27, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
					jQuery( '#gform_ajax_frame_27' ).load( function ()
					{
						var contents = jQuery( this ).contents().find( '*' ).html();
						var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
						if ( !is_postback )
						{
							return;
						}
						var form_content = jQuery( this ).contents().find( '#gform_wrapper_27' );
						var is_redirect = contents.indexOf( 'gformRedirect(){' ) >= 0;
						var is_form = !(form_content.length <= 0 || is_redirect);
						if ( is_form )
						{
							jQuery( '#gform_wrapper_27' ).html( form_content.html() );
							jQuery( document ).scrollTop( jQuery( '#gform_wrapper_27' ).offset().top );
							if ( window['gformInitDatepicker'] )
							{
								gformInitDatepicker();
							}
							if ( window['gformInitPriceFields'] )
							{
								gformInitPriceFields();
							}
							var current_page = jQuery( '#gform_source_page_number_27' ).val();
							gformInitSpinner( 27, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
							jQuery( document ).trigger( 'gform_page_loaded', [27, current_page] );
							window['gf_submitting_27'] = false;
						}
						else if ( !is_redirect )
						{
							var confirmation_content = jQuery( this ).contents().find( '#gforms_confirmation_message' ).html();
							if ( !confirmation_content )
							{
								confirmation_content = contents;
							}
							setTimeout( function ()
							{
								jQuery( '#gform_wrapper_27' ).replaceWith( '<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_27\'' + '>' + confirmation_content + '<' + '/div' + '>' );
								jQuery( document ).scrollTop( jQuery( '#gforms_confirmation_message' ).offset().top );
								jQuery( document ).trigger( 'gform_confirmation_loaded', [27] );
								window['gf_submitting_27'] = false;
							}, 50 );
						}
						else
						{
							jQuery( '#gform_27' ).append( contents );
							if ( window['gformRedirect'] )
							{
								gformRedirect();
							}
						}
						jQuery( document ).trigger( 'gform_post_render', [27, current_page] );
					} );
				} );</script>
			<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
					"gf_currency_config": {
						"name"              : "Australian Dollar",
						"symbol_left"       : "$",
						"symbol_right"      : "",
						"symbol_padding"    : " ",
						"thousand_separator": ",",
						"decimal_separator" : ".",
						"decimals"          : 2
					},
					"base_url"          : "\/wp-content\/plugins\/gravityforms",
					"number_formats"    : [],
					"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
				};
				jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
				{
					if ( formId == 27 )
					{
						if ( window['jQuery'] )
						{
							if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
							window['gf_form_conditional_logic'][27] = {
								'logic'     : {
									6: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "4",
												"operator": "contains",
												"value"   : "@"
											}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
												"fieldId" : "3",
												"operator": "isnot",
												"value"   : ""
											}]
										}, "nextButton": null, "section": null
									},
									7: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "6",
												"operator": "is",
												"value"   : "call back"
											}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
										}, "nextButton": null, "section": null
									}
								},
								'dependents': { 6: [6], 7: [7] },
								'animation' : 1,
								'defaults'  : []
							};
							if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
							jQuery( document ).ready( function ()
							{
								gf_apply_rules( 27, [6, 7], true );
								jQuery( '#gform_wrapper_27' ).show();
								jQuery( document ).trigger( 'gform_post_conditional_logic', [27, null, true] );
							} );
						}
					}
				} );
				jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
				{
				} );</script>
			<script> jQuery( document ).ready( function ()
				{
					jQuery( document ).trigger( 'gform_post_render', [27, 1] )
				} ); </script>
		</aside>

		<?php
		return ob_get_clean();
	}


	// Testimonials widget heading - use shortcode from plugin from now
	function testimonial_header()
	{
		ob_start();
		?>

		<img src="/wp-content/themes/solar-listings/images/testimonials-header.svg" alt="Real People, Real Testimonials">

		<?php
		return ob_get_clean();
	}

	/**
	 * Show latest quotes in a vertical marquee
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function quotes_marquee( $atts = array(), $content = '' )
	{
		$atts = shortcode_atts( array(
			'number' => 10,
			'delay'  => 60,
			'height' => 50,
		), $atts );

		$entries = GFFormsModel::get_leads( 1, 0, 'DESC', '', 0, $atts['number'] );
		$output  = '';
		foreach ( $entries as $entry )
		{
			$output .= sprintf(
				'<p>' . __( '%s from %s, %s requested a Solar Power Quote | %s ago', '7listings' ) . '</p>',
				$entry['1.3'],
				//                $entry['1.3'] . ' ' . $entry['1.6'], // First name + Last name
				$entry['17.3'], $entry['17.4'],
				human_time_diff( strtotime( $entry['date_created'] ) )
			);
		}
		return sprintf(
			'<marquee height="%s" scrollamount="1" scrolldelay="%s" direction="up" onmouseover="this.stop()" onmouseout="this.start()">%s</marquee>',
			$atts['height'],
			$atts['delay'],
			$output
		);
	}

	// New multistep widget
	function quote_widget_short()
	{
		$current_url = is_ssl() ? 'https://' : 'http://';
		if ( $_SERVER['SERVER_PORT'] != '80' )
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>
		<script src="/wp-content/themes/solar-listings/js/gf-shortcode.js"></script>

		<aside id="quote-form" class="widget gform_widget">
			<div id="free">FREE Quotes</div>
			<div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_54">
				<a id="gf_54" class="gform_anchor"></a>
				<h3 class="side">Solar Quotes</h3>
				<h4 class="feat">Save money in 5 minutes</h4>
				<h6 class="side">from 5 <i class="icon-star"></i> rated companies</h6>
				<form method="post" enctype="multipart/form-data" target="gform_ajax_frame_54" id="gform_54" class="simple-quote multistep" action="<?php echo $current_url; ?>">
					<div class="quote-inputs gform_fields top_label">
						<input required name="input_1" id="input_54_1" type="tel" min="100" max="9999" value="" class="solar-quote-input postcode" tabindex="69" placeholder="Postcode" title="Please enter your postcode" pattern="{4}">
						<input required name="input_2" id="input_54_2" type="text" value="" class='solar-quote-input name' tabindex='70' placeholder="Name" title="Please enter your name">
						<input required name="input_3" id="input_54_3" type='tel' value='' class='solar-quote-input phone' tabindex='71' placeholder="Phone" title="Please enter your phone number" pattern="{10}">
						<input required name="input_4" id="input_54_4" type="email" value="" class='solar-quote-input email' tabindex='72' placeholder="Email" title="Please enter you email">
					</div>

					<div class="gform_footer top_label">
						<input type="submit" id="gform_submit_button_54" class="button gform_button" value="Get 3 Quotes" tabindex="73" onClick="if(window[&quot;gf_submitting_54&quot;]){return false;}  if( !jQuery(&quot;#gform_54&quot;)[0].checkValidity || jQuery(&quot;#gform_54&quot;)[0].checkValidity()){window[&quot;gf_submitting_54&quot;]=true;} ">
						<input type="hidden" name="gform_ajax" value="form_id=54&amp;title=&amp;description=&amp;tabindex=1">
						<input type="hidden" class="gform_hidden" name="is_submit_54" value="1">
						<input type="hidden" class="gform_hidden" name="gform_submit" value="54">
						<input type="hidden" class="gform_hidden" name="gform_unique_id" value="">
						<input type="hidden" class="gform_hidden" name="state_54" value="WyJbXSIsIjNiYTkzNTI5MTg4ZDMyZTc3YmQ4Zjc1Yjg0YzI3MWQxIl0=">
						<input type="hidden" class="gform_hidden" name="gform_target_page_number_54" id="gform_target_page_number_54" value="0">
						<input type="hidden" class="gform_hidden" name="gform_source_page_number_54" id="gform_source_page_number_54" value="1">
						<input type="hidden" name="gform_field_values" value="">
					</div>
				</form>
			</div>
			<iframe style='display:none;width:0px;height:0px;' src='about:blank' name='gform_ajax_frame_54' id='gform_ajax_frame_54'></iframe>
			<script type='text/javascript'>jQuery( document ).ready( function ( $ )
				{
					gformInitSpinner( 54, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
					jQuery( '#gform_ajax_frame_54' ).load( function ()
					{
						var contents = jQuery( this ).contents().find( '*' ).html();
						var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
						if ( !is_postback )
						{
							return;
						}
						var form_content = jQuery( this ).contents().find( '#gform_wrapper_54' );
						var is_redirect = contents.indexOf( 'gformRedirect(){' ) >= 0;
						var is_form = !(form_content.length <= 0 || is_redirect);
						if ( is_form )
						{
							jQuery( '#gform_wrapper_54' ).html( form_content.html() );
							jQuery( document ).scrollTop( jQuery( '#gform_wrapper_54' ).offset().top );
							if ( window['gformInitDatepicker'] )
							{
								gformInitDatepicker();
							}
							if ( window['gformInitPriceFields'] )
							{
								gformInitPriceFields();
							}
							var current_page = jQuery( '#gform_source_page_number_54' ).val();
							gformInitSpinner( 54, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
							jQuery( document ).trigger( 'gform_page_loaded', [54, current_page] );
							window['gf_submitting_54'] = false;
						}
						else if ( !is_redirect )
						{
							var confirmation_content = jQuery( this ).contents().find( '#gforms_confirmation_message' ).html();
							if ( !confirmation_content )
							{
								confirmation_content = contents;
							}
							setTimeout( function ()
							{
								jQuery( '#gform_wrapper_54' ).replaceWith( '<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_54\'' + '>' + confirmation_content + '<' + '/div' + '>' );
								jQuery( document ).scrollTop( jQuery( '#gforms_confirmation_message' ).offset().top );
								jQuery( document ).trigger( 'gform_confirmation_loaded', [54] );
								window['gf_submitting_54'] = false;
							}, 50 );
						}
						else
						{
							jQuery( '#gform_54' ).append( contents );
							if ( window['gformRedirect'] )
							{
								gformRedirect();
							}
						}
						jQuery( document ).trigger( 'gform_post_render', [54, current_page] );
					} );
				} );</script>
			<script type='text/javascript'> jQuery( document ).ready( function ()
				{
					jQuery( document ).trigger( 'gform_post_render', [54, 1] )
				} ); </script>
		</aside>
		<?php
		return ob_get_clean();
	}

	function new_multistep_quote()
	{
		$current_url = is_ssl() ? 'https://' : 'http://';
		if ( $_SERVER['SERVER_PORT'] != '80' )
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>

		<script>
			jQuery( document ).ready( function ( e )
			{
				gformInitSpinner( 55, "/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif" );
				jQuery( "#gform_ajax_frame_55" ).load( function ()
				{
					var e = jQuery( this ).contents().find( "*" ).html();
					var t = e.indexOf( "GF_AJAX_POSTBACK" ) >= 0;
					if ( !t )
					{
						return
					}
					var n = jQuery( this ).contents().find( "#gform_wrapper_55" );
					jQuery( this ).contents().find( "h6" ).append( '<h4 class="feat">Save money in 5 minutes</h4>' );
					jQuery( this ).contents().find( "h3, h6" ).hide();
					jQuery( this ).contents().find( "#field_55_6, #field_55_7" ).addClass( "step" ).find( ".gfield_label" ).remove().prepend( '<span class="icon-stack"><i class="icon-chevron-sign-down"></i><i class="icon-stack-base icon-circle white"></i></span>' );
					jQuery( this ).contents().find( "#field_55_6" ).prepend( '<p class="title">How do you want to get your FREE quotes?</p>' );
					jQuery( this ).contents().find( "#field_55_7" ).prepend( '<p class="title">When can we call you?</p>' );
					jQuery( this ).contents().find( ".validation_message" ).hide();
					var r = jQuery( this ).contents().find( ".validation_message" ).text();
					if ( r != "" )
					{
						jQuery( "#myModal" ).modal( "show" )
					}
					var i = e.indexOf( "gformRedirect(){" ) >= 0;
					var s = !(n.length <= 0 || i);
					if ( s )
					{
						jQuery( "#gform_wrapper_55" ).html( n.html() );
						jQuery( document ).scrollTop( jQuery( "#gform_wrapper_55" ).offset().top );
						if ( window["gformInitDatepicker"] )
						{
							gformInitDatepicker()
						}
						if ( window["gformInitPriceFields"] )
						{
							gformInitPriceFields()
						}
						var o = jQuery( "#gform_source_page_number_55" ).val();
						gformInitSpinner( 55, "/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif" );
						jQuery( document ).trigger( "gform_page_loaded", [55, o] );
						window["gf_submitting_55"] = false
					}
					else if ( !i )
					{
						var u = jQuery( this ).contents().find( "#gforms_confirmation_message" ).html();
						if ( !u )
						{
							u = e
						}
						setTimeout( function ()
						{
							jQuery( "#gform_wrapper_55" ).replaceWith( "<" + "div id='gforms_confirmation_message' class='gform_confirmation_message_55'" + ">" + u + "<" + "/div" + ">" );
							jQuery( document ).scrollTop( jQuery( "#gforms_confirmation_message" ).offset().top );
							jQuery( document ).trigger( "gform_confirmation_loaded", [55] );
							window["gf_submitting_55"] = false
						}, 50 )
					}
					else
					{
						jQuery( "#gform_55" ).append( e );
						if ( window["gformRedirect"] )
						{
							gformRedirect()
						}
					}
					jQuery( document ).trigger( "gform_post_render", [55, o] )
				} )
			} )
		</script>

		<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
				"gf_currency_config": {
					"name"              : "Australian Dollar",
					"symbol_left"       : "$",
					"symbol_right"      : "",
					"symbol_padding"    : " ",
					"thousand_separator": ",",
					"decimal_separator" : ".",
					"decimals"          : 2
				},
				"base_url"          : "\/wp-content\/plugins\/gravityforms",
				"number_formats"    : [],
				"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
			};
			jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
			{
				if ( formId == 55 )
				{
					if ( window['jQuery'] )
					{
						if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
						window['gf_form_conditional_logic'][55] = {
							'logic'     : {
								6: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "4",
											"operator": "contains",
											"value"   : "@"
										}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
											"fieldId" : "3",
											"operator": "isnot",
											"value"   : ""
										}]
									}, "nextButton": null, "section": null
								},
								7: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "6",
											"operator": "is",
											"value"   : "call back"
										}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
									}, "nextButton": null, "section": null
								}
							},
							'dependents': { 6: [6], 7: [7] },
							'animation' : 1,
							'defaults'  : []
						};
						if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
						jQuery( document ).ready( function ()
						{
							gf_apply_rules( 55, [6, 7], true );
							jQuery( '#gform_wrapper_55' ).show();
							jQuery( document ).trigger( 'gform_post_conditional_logic', [55, null, true] );
						} );
					}
				}
			} );
			jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
			{
			} );</script>
		<script> jQuery( document ).ready( function ()
			{
				jQuery( document ).trigger( 'gform_post_render', [55, 1] )
			} ); </script>

		<aside id="quote-form" class="widget gform_widget gform-short">
			<script src="/wp-content/themes/solar-listings/js/gf-shortcode.js"></script>
			<script> var gf_global = {
					"gf_currency_config": {
						"name"              : "Australian Dollar",
						"symbol_left"       : "$",
						"symbol_right"      : "",
						"symbol_padding"    : " ",
						"thousand_separator": ",",
						"decimal_separator" : ".",
						"decimals"          : 2
					},
					"base_url"          : "\/wp-content\/plugins\/gravityforms",
					"number_formats"    : [],
					"spinnerUrl"        : "\/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
				}; </script>

			<div id="free">FREE Quotes</div>
			<div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_55">
				<a id="gf_55" class="gform_anchor"></a>
				<h3 class="side">Solar Quotes</h3>
				<h4 class="feat">Save money in only 30 seconds</h4>
				<h6 class="side">from 5 <i class="icon-star"></i> rated companies</h6>
				<form method="post" enctype="multipart/form-data" target="gform_ajax_frame_55" id="gform_55" class="simple-quote multistep" action="<?php echo $current_url; ?>">
					<div class="quote-inputs gform_fields top_label">
						<input required name="input_1" id="input_55_1" type="tel" min="100" max="9999" value="" class="solar-quote-input postcode" tabindex="77" placeholder="Postcode" title="Please enter your postcode" pattern="{4}" onChange="gf_apply_rules(55,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(55,[6])&quot;, 300);">
						<input required name="input_2" id="input_55_2" type="text" value="" class='solar-quote-input name' tabindex='78' placeholder="Name" title="Please enter your name">
						<input required name="input_3" id="input_55_3" type='tel' value='' class='solar-quote-input phone' tabindex='79' placeholder="Phone" title="Please enter your phone number" pattern="{10}" onChange="gf_apply_rules(55,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(55,[6])&quot;, 300);">
						<input required name="input_4" id="input_55_4" type="email" value="" class='solar-quote-input email' tabindex='80' placeholder="Email" title="Please enter you email" onChange="gf_apply_rules(55,[6]);" onKeyUp="clearTimeout(__gf_timeout_handle); __gf_timeout_handle = setTimeout(&quot;gf_apply_rules(55,[6])&quot;, 300);">

						<div id="field_55_6" class="gfield step form-phone" style="display: none;">
			      	<span class="icon-stack">
			      		<i class="icon-chevron-sign-down"></i>
			      		<i class="icon-stack-base icon-circle white"></i>
			      	</span>

							<p class="title">How do you want to get your FREE quotes?</p>
							<div id="input_55_6" class="options">
								<p class="gchoice_6_0">
									<input required name="input_6" type="radio" value="long quote form" id="choice_6_0" tabindex="81" onClick="gf_apply_rules(55,[7]);">
									<label for="choice_6_0" id="label_6_0">complete a simple online form</label>
								</p>
								<p class="gchoice_6_1">
									<input name="input_6" type="radio" value="call back" id="choice_6_1" tabindex="82" onClick="gf_apply_rules(55,[7]);">
									<label for="choice_6_1" id="label_6_1">speak with a solar consultant</label>
								</p>
							</div>
						</div>
					</div>

					<div class="gform_footer top_label">
						<input type="submit" id="gform_submit_button_55" class="button gform_button" value="Get 3 Quotes" tabindex="88" onClick="if(window[&quot;gf_submitting_55&quot;]){return false;}  if( !jQuery(&quot;#gform_55&quot;)[0].checkValidity || jQuery(&quot;#gform_55&quot;)[0].checkValidity()){window[&quot;gf_submitting_55&quot;]=true;} ">
						<input type="hidden" name="gform_ajax" value="form_id=55&amp;title=&amp;description=&amp;tabindex=1">
						<input type="hidden" class="gform_hidden" name="is_submit_55" value="1">
						<input type="hidden" class="gform_hidden" name="gform_submit" value="55">
						<input type="hidden" class="gform_hidden" name="gform_unique_id" value="">
						<input type="hidden" class="gform_hidden" name="state_55" value="WyJbXSIsIjNiYTkzNTI5MTg4ZDMyZTc3YmQ4Zjc1Yjg0YzI3MWQxIl0=">
						<input type="hidden" class="gform_hidden" name="gform_target_page_number_55" id="gform_target_page_number_55" value="0">
						<input type="hidden" class="gform_hidden" name="gform_source_page_number_55" id="gform_source_page_number_55" value="1">
						<input type="hidden" name="gform_field_values" value="">
					</div>
				</form>
			</div>
			<iframe style="display:none;width:0px;height:0px;" src="about:blank" name="gform_ajax_frame_55" id="gform_ajax_frame_55"></iframe>
			<script>jQuery( document ).ready( function ( $ )
				{
					gformInitSpinner( 55, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
					jQuery( '#gform_ajax_frame_55' ).load( function ()
					{
						var contents = jQuery( this ).contents().find( '*' ).html();
						var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
						if ( !is_postback )
						{
							return;
						}
						var form_content = jQuery( this ).contents().find( '#gform_wrapper_55' );
						var is_redirect = contents.indexOf( 'gformRedirect(){' ) >= 0;
						var is_form = !(form_content.length <= 0 || is_redirect);
						if ( is_form )
						{
							jQuery( '#gform_wrapper_55' ).html( form_content.html() );
							jQuery( document ).scrollTop( jQuery( '#gform_wrapper_55' ).offset().top );
							if ( window['gformInitDatepicker'] )
							{
								gformInitDatepicker();
							}
							if ( window['gformInitPriceFields'] )
							{
								gformInitPriceFields();
							}
							var current_page = jQuery( '#gform_source_page_number_55' ).val();
							gformInitSpinner( 55, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
							jQuery( document ).trigger( 'gform_page_loaded', [55, current_page] );
							window['gf_submitting_55'] = false;
						}
						else if ( !is_redirect )
						{
							var confirmation_content = jQuery( this ).contents().find( '#gforms_confirmation_message' ).html();
							if ( !confirmation_content )
							{
								confirmation_content = contents;
							}
							setTimeout( function ()
							{
								jQuery( '#gform_wrapper_55' ).replaceWith( '<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_55\'' + '>' + confirmation_content + '<' + '/div' + '>' );
								jQuery( document ).scrollTop( jQuery( '#gforms_confirmation_message' ).offset().top );
								jQuery( document ).trigger( 'gform_confirmation_loaded', [55] );
								window['gf_submitting_55'] = false;
							}, 50 );
						}
						else
						{
							jQuery( '#gform_55' ).append( contents );
							if ( window['gformRedirect'] )
							{
								gformRedirect();
							}
						}
						jQuery( document ).trigger( 'gform_post_render', [55, current_page] );
					} );
				} );</script>
			<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
					"gf_currency_config": {
						"name"              : "Australian Dollar",
						"symbol_left"       : "$",
						"symbol_right"      : "",
						"symbol_padding"    : " ",
						"thousand_separator": ",",
						"decimal_separator" : ".",
						"decimals"          : 2
					},
					"base_url"          : "\/wp-content\/plugins\/gravityforms",
					"number_formats"    : [],
					"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
				};
				jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
				{
					if ( formId == 55 )
					{
						if ( window['jQuery'] )
						{
							if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
							window['gf_form_conditional_logic'][55] = {
								'logic'     : {
									6: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "4",
												"operator": "contains",
												"value"   : "@"
											}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
												"fieldId" : "3",
												"operator": "isnot",
												"value"   : ""
											}]
										}, "nextButton": null, "section": null
									},
									7: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "6",
												"operator": "is",
												"value"   : "call back"
											}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
										}, "nextButton": null, "section": null
									}
								},
								'dependents': { 6: [6], 7: [7] },
								'animation' : 1,
								'defaults'  : []
							};
							if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
							jQuery( document ).ready( function ()
							{
								gf_apply_rules( 55, [6, 7], true );
								jQuery( '#gform_wrapper_55' ).show();
								jQuery( document ).trigger( 'gform_post_conditional_logic', [55, null, true] );
							} );
						}
					}
				} );
				jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
				{
				} );</script>
			<script> jQuery( document ).ready( function ()
				{
					jQuery( document ).trigger( 'gform_post_render', [55, 1] )
				} ); </script>
		</aside>
		<?php
		return ob_get_clean();
	}

	function postcode_quote()
	{
		$current_url = is_ssl() ? 'https://' : 'http://';
		if ( $_SERVER['SERVER_PORT'] != '80' )
			$current_url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
		else
			$current_url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		ob_start();
		?>

		<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
				"gf_currency_config": {
					"name"              : "Australian Dollar",
					"symbol_left"       : "$",
					"symbol_right"      : "",
					"symbol_padding"    : " ",
					"thousand_separator": ",",
					"decimal_separator" : ".",
					"decimals"          : 2
				},
				"base_url"          : "\/wp-content\/plugins\/gravityforms",
				"number_formats"    : [],
				"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
			};
			jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
			{
				if ( formId == 57 )
				{
					if ( window['jQuery'] )
					{
						if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
						window['gf_form_conditional_logic'][57] = {
							'logic'     : {
								6: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "4",
											"operator": "contains",
											"value"   : "@"
										}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
											"fieldId" : "3",
											"operator": "isnot",
											"value"   : ""
										}]
									}, "nextButton": null, "section": null
								},
								7: {
									"field"        : {
										"actionType": "show",
										"logicType" : "all",
										"rules"     : [{
											"fieldId" : "6",
											"operator": "is",
											"value"   : "call back"
										}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
									}, "nextButton": null, "section": null
								}
							},
							'dependents': { 6: [6], 7: [7] },
							'animation' : 1,
							'defaults'  : []
						};
						if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
						jQuery( document ).ready( function ()
						{
							gf_apply_rules( 57, [6, 7], true );
							jQuery( '#gform_wrapper_57' ).show();
							jQuery( document ).trigger( 'gform_post_conditional_logic', [57, null, true] );
						} );
					}
				}
			} );
			jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
			{
			} );</script>
		<script> jQuery( document ).ready( function ()
			{
				jQuery( document ).trigger( 'gform_post_render', [57, 1] )
			} ); </script>

		<aside id="quote-form" class="postcode widget gform_widget gform-short">
			<script src="/wp-content/themes/solar-listings/js/gf-shortcode.js"></script>

			<div id="free">FREE Quotes</div>
			<div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_57">
				<a id="gf_57" class="gform_anchor"></a>
				<h3 class="side">Solar Quotes</h3>
				<h4 class="feat">Save money in only 30 seconds</h4>
				<h6 class="side">from 5 <i class="icon-star"></i> rated companies</h6>
				<form method="post" enctype="multipart/form-data" target="gform_ajax_frame_57" id="gform_57" class="simple-quote multistep" action="<?php echo $current_url; ?>">
					<div class="quote-inputs gform_fields top_label" style="padding: 10px 0">
						<input required name="input_2" id="input_57_2" type="number" min="100" max="9999" value="" class="solar-quote-input postcode" tabindex="77" placeholder="Enter postcode" title="Please enter your postcode">
					</div>
					<div class="gform_footer top_label">
						<input type="submit" id="gform_submit_button_57" class="button gform_button" value="Get 3 Quotes" tabindex="88" onClick="if(window[&quot;gf_submitting_57&quot;]){return false;}  if( !jQuery(&quot;#gform_57&quot;)[0].checkValidity || jQuery(&quot;#gform_57&quot;)[0].checkValidity()){window[&quot;gf_submitting_57&quot;]=true;} ">
						<input type="hidden" name="gform_ajax" value="form_id=57&amp;title=&amp;description=&amp;tabindex=1">
						<input type="hidden" class="gform_hidden" name="is_submit_57" value="1">
						<input type="hidden" class="gform_hidden" name="gform_submit" value="57">
						<input type="hidden" class="gform_hidden" name="gform_unique_id" value="">
						<input type="hidden" class="gform_hidden" name="state_57" value="WyJbXSIsIjNiYTkzNTI5MTg4ZDMyZTc3YmQ4Zjc1Yjg0YzI3MWQxIl0=">
						<input type="hidden" class="gform_hidden" name="gform_target_page_number_57" id="gform_target_page_number_57" value="0">
						<input type="hidden" class="gform_hidden" name="gform_source_page_number_57" id="gform_source_page_number_57" value="1">
						<input type="hidden" name="gform_field_values" value="">
					</div>
				</form>
			</div>
			<iframe style="display:none;width:0px;height:0px;" src="about:blank" name="gform_ajax_frame_57" id="gform_ajax_frame_57"></iframe>
			<script>jQuery( document ).ready( function ( $ )
				{
					gformInitSpinner( 57, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
					jQuery( '#gform_ajax_frame_57' ).load( function ()
					{
						var contents = jQuery( this ).contents().find( '*' ).html();
						var is_postback = contents.indexOf( 'GF_AJAX_POSTBACK' ) >= 0;
						if ( !is_postback )
						{
							return;
						}
						var form_content = jQuery( this ).contents().find( '#gform_wrapper_57' );
						var is_redirect = contents.indexOf( 'gformRedirect(){' ) >= 0;
						var is_form = !(form_content.length <= 0 || is_redirect);
						if ( is_form )
						{
							jQuery( '#gform_wrapper_57' ).html( form_content.html() );
							jQuery( document ).scrollTop( jQuery( '#gform_wrapper_57' ).offset().top );
							if ( window['gformInitDatepicker'] )
							{
								gformInitDatepicker();
							}
							if ( window['gformInitPriceFields'] )
							{
								gformInitPriceFields();
							}
							var current_page = jQuery( '#gform_source_page_number_57' ).val();
							gformInitSpinner( 57, '/wp-content/themes/7listings/images/ui/ajax-loader-round-green.gif' );
							jQuery( document ).trigger( 'gform_page_loaded', [57, current_page] );
							window['gf_submitting_57'] = false;
						}
						else if ( !is_redirect )
						{
							var confirmation_content = jQuery( this ).contents().find( '#gforms_confirmation_message' ).html();
							if ( !confirmation_content )
							{
								confirmation_content = contents;
							}
							setTimeout( function ()
							{
								jQuery( '#gform_wrapper_57' ).replaceWith( '<' + 'div id=\'gforms_confirmation_message\' class=\'gform_confirmation_message_57\'' + '>' + confirmation_content + '<' + '/div' + '>' );
								jQuery( document ).scrollTop( jQuery( '#gforms_confirmation_message' ).offset().top );
								jQuery( document ).trigger( 'gform_confirmation_loaded', [57] );
								window['gf_submitting_57'] = false;
							}, 50 );
						}
						else
						{
							jQuery( '#gform_57' ).append( contents );
							if ( window['gformRedirect'] )
							{
								gformRedirect();
							}
						}
						jQuery( document ).trigger( 'gform_post_render', [57, current_page] );
					} );
				} );</script>
			<script> if ( typeof gf_global == 'undefined' ) var gf_global = {
					"gf_currency_config": {
						"name"              : "Australian Dollar",
						"symbol_left"       : "$",
						"symbol_right"      : "",
						"symbol_padding"    : " ",
						"thousand_separator": ",",
						"decimal_separator" : ".",
						"decimals"          : 2
					},
					"base_url"          : "\/wp-content\/plugins\/gravityforms",
					"number_formats"    : [],
					"spinnerUrl"        : "/wp-content\/plugins\/gravityforms\/images\/spinner.gif"
				};
				jQuery( document ).bind( 'gform_post_render', function ( event, formId, currentPage )
				{
					if ( formId == 57 )
					{
						if ( window['jQuery'] )
						{
							if ( !window['gf_form_conditional_logic'] )window['gf_form_conditional_logic'] = new Array();
							window['gf_form_conditional_logic'][57] = {
								'logic'     : {
									6: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "4",
												"operator": "contains",
												"value"   : "@"
											}, { "fieldId": "1", "operator": "isnot", "value": "" }, {
												"fieldId" : "3",
												"operator": "isnot",
												"value"   : ""
											}]
										}, "nextButton": null, "section": null
									},
									7: {
										"field"        : {
											"actionType": "show",
											"logicType" : "all",
											"rules"     : [{
												"fieldId" : "6",
												"operator": "is",
												"value"   : "call back"
											}, { "fieldId": "6", "operator": "isnot", "value": "long quote form" }]
										}, "nextButton": null, "section": null
									}
								},
								'dependents': { 6: [6], 7: [7] },
								'animation' : 1,
								'defaults'  : []
							};
							if ( !window['gf_number_format'] )window['gf_number_format'] = 'decimal_dot';
							jQuery( document ).ready( function ()
							{
								gf_apply_rules( 57, [6, 7], true );
								jQuery( '#gform_wrapper_57' ).show();
								jQuery( document ).trigger( 'gform_post_conditional_logic', [57, null, true] );
							} );
						}
					}
				} );
				jQuery( document ).bind( 'gform_post_conditional_logic', function ( event, formId, fields, isInit )
				{
				} );</script>
			<script> jQuery( document ).ready( function ()
				{
					jQuery( document ).trigger( 'gform_post_render', [57, 1] )
				} ); </script>
		</aside>
		<?php
		return ob_get_clean();
	}

	/**
	 * Company rating widget shortcode
	 * @return string
	 */
	public function rating_widget()
	{
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => get_current_user_id(),
		) );
		if ( empty( $company ) )
			return 'You do not own a company';

		$company = current( $company );
		ob_start();
		?>
		<p>&nbsp;</p>
		<div id="preview-wrapper" style="padding:50px">
			<div id="preview" style="width:300px;margin:0 auto">
				<iframe src="https://www.australiansolarquotes.com.au/company-rating-widget/?id=<?= $company->ID; ?>&theme=white" style="width:100%;height:165px" frameborder="no" scrolling="no"></iframe>
			</div>
		</div>
		<p>&nbsp;</p>
		<p><strong>1. Configure your ASQ Trust Score Badge</strong></p>
		<p>
			Width <input type="text" value="100%" id="widget-width" style="width:100px">&nbsp;&nbsp;&nbsp;&nbsp;
			Height <input type="text" value="165px" id="widget-height" style="width:100px">
		</p>
		<p>
			<label><input type="radio" value="white" name="widget-theme" checked> White color theme</label>
			<label><input type="radio" value="dark" name="widget-theme"> Dark color theme</label>
		</p>
		<p>&nbsp;</p>
		<p><strong>2. Installation and setup</strong></p>
		<p>Copy this code and paste <strong>before <code>&lt;/body&gt;</code></strong> tag of your websites' HTML
		</p>
		<p>
			<textarea style="width:100%;height:2em" onclick="this.select()"><?php echo esc_textarea( '<script defer src="https://www.australiansolarquotes.com.au/rating-widget.js"></script>' ); ?></textarea>
		</p>
		<p>&nbsp;</p>
		<p>Copy and paste this code into the HTML of your website where you'd like the widget to appear</p>
		<p>
			<textarea style="width:100%;height:4em" onclick="this.select()" id="rating-review"><?php echo esc_textarea( '<div class="asq-review" data-id="' . $company->ID . '" data-width="100%" data-height="165px" data-theme="white"></div>' ); ?></textarea>
		</p>
		<p>&nbsp;</p>
		<p><strong>3. How do you plan on installing your new ASQ Trust Score Badge?</strong></p>
		<p>
			<label><input type="radio" value="manual" name="widget-setup" checked> I can set this up myself</label>
			<label><input type="radio" value="email" name="widget-setup"> Email instructions to my web developer</label>
		</p>
		<div class="setup-email hidden">
			<p>Email <input type="email" id="widget-email"></p>
			<p><input type="submit" value="Send" id="widget-send"></p>
		</div>

		<script>
			jQuery( function ( $ )
			{
				// Toggle setup by email or manual
				$( '[name="widget-setup"]' ).on( 'change', function ()
				{
					var value = $( '[name="widget-setup"]:checked' ).val();
					if ( 'email' == value )
					{
						$( '.setup-email' ).removeClass( 'hidden' );
					}
					else
					{
						$( '.setup-' + value ).addClass( 'hidden' );
					}
				} ).trigger( 'change' );

				// Send email to developer
				$( '#widget-send' ).on( 'click', function ()
				{
					$( this ).replaceWith( '<i class="icon-spinner icon-spin icon-large"></i>' );
					$.post( Sl.ajaxUrl, {
						action: 'solar_widget_email',
						email : $( '#widget-email' ).val(),
						widget: $( '#rating-review' ).val(),
					}, function ( r )
					{
						var $div = $( '.setup-email' );
						$div.find( '.icon-spinner' ).remove();

						if ( r.success )
						{
							$div.append( '<p><i class="icon-ok green"></i> ' + r.data + '</p>' );
						}
						else
						{
							$div.append( '<p><i class="icon-remove red"></i> ' + r.data + '</p>' );
						}
					} );
				} );

				// Setup widget settings
				var $widget = $( "#rating-review" ),
					content = $widget.val(),
					$previewWrapper = $( '#preview-wrapper' ),
					$preview = $( '#preview' ),
					$iframe = $preview.find( 'iframe' ),
					$width = $( "#widget-width" ),
					$height = $( "#widget-height" ),
					$theme = $( "[name='widget-theme']" );

				$width.on( "change", function ()
				{
					content = content.replace( /data-width=".+?"/, "data-width=\"" + $width.val() + "\"" );
					$widget.val( content );

					var style = 'width:' + $width.val() + ';height:' + $height.val();
					$iframe.attr( 'style', style );
					$iframe[0].src += ''; // Force to reload
				} );
				$height.on( "change", function ()
				{
					content = content.replace( /data-height=".+?"/, "data-height=\"" + $height.val() + "\"" );
					$widget.val( content );

					var style = 'width:' + $width.val() + ';height:' + $height.val();
					$iframe.attr( 'style', style );
					$iframe[0].src += ''; // Force to reload
				} );
				$theme.on( "change", function ()
				{
					var theme = $theme.filter( ':checked' ).val();
					content = content.replace( /data-theme="[a-z]+"/, "data-theme=\"" + theme + "\"" );
					$widget.val( content );

					var src = 'https://www.australiansolarquotes.com.au/company-rating-widget/?id=<?= $company->ID; ?>&theme=' + theme;
					$iframe.attr( 'src', src );

					if ( 'white' == theme )
					{
						$previewWrapper.css( 'background', '#fff' );
					}
					else
					{
						$previewWrapper.css( 'background', '#3a3a3a' );
					}
				} );
			} );
		</script>
		<?php
		return ob_get_clean();
	}
}

new Solar_Listings_Shortcodes;
