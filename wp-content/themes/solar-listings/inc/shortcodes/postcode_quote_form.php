<?php

namespace ASQ\Shortcode;

class Postcode_Quote_Form
{
	public function __construct()
	{
		add_shortcode( 'postcode_quote_form', array( $this, 'shortcode' ) );
	}

	public function shortcode()
	{
		?>
		<aside id="quote-form" class="widget gform_widget gform-short">
		  <div id="free">FREE Quotes</div>
		  <div class="gform_wrapper simple-quote multistep_wrapper" id="gform_wrapper_55">
		  	<a id="gf_55" class="gform_anchor"></a>
		  	<h3 class="side">Solar Quotes</h3>
		  	<h4 class="feat">Save money in only 30 seconds</h4>
		    <h6 class="side">from 5 <i class="icon-star"></i> rated companies</h6>
		    <form method="post" enctype="multipart/form-data" action="https://www.australiansolarquotes.com.au/solar-quotes-split/" id="gform_55" class="simple-quote multistep">
			  	<div class="quote-inputs gform_fields top_label">
			      	<input required name="postcode" id="input-postcode" type="number" min="100" max="9999" value="" class="solar-quote-input postcode" placeholder="Postcode" title="Please enter your postcode">
			    </div>
		      	<div class="gform_footer top_label">
		        	<input type="submit" id="gform_submit_button_55" class="button gform_button" value="Get 3 Quotes" tabindex="88">
		      	</div>
		    </form>
		  </div>
		</aside>
		<?php
	}
}

new \ASQ\Shortcode\Postcode_Quote_Form;