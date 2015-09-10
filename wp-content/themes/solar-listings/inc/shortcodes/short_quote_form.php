<?php

namespace ASQ\Shortcode;

class Short_Quote_Form
{
	public function __construct()
	{
		add_shortcode( 'short_quote_form', array( $this, 'shortcode' ) );
	}

	public function shortcode()
	{
		return do_shortcode( '[gravityform id="53" ajax="true"]' );
	}
}

new \ASQ\Shortcode\Short_Quote_Form;