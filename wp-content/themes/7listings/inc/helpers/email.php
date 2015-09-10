<?php
/**
 * Get template for a part in email
 *
 * @param string $name
 *
 * @return string
 */
function sl_email_template_part( $name )
{
	switch ( $name )
	{
		case 'guest_phone':
			return '<tr>
						<td class="row-odd phone label">' . __( 'Phone', '7listings' ) . '</td>
						<td class="row-odd phone info"><strong>%s</strong></td>
					</tr>
					<tr>
						<td height="20" colspan="2">&nbsp;</td>
					</tr>';
		case 'guest_email':
			return '<tr>
						<td class="row-even email label">' . __( 'Email', '7listings' ) . '</td>
						<td class="row-even email info"><strong>%s</strong></td>
					</tr>';
		case 'guest_info':
			return '<tr>
						<td colspan="2" class="passenger-head"><h4>' . __( 'Passenger %s', '7listings' ) . '</h4></td>
					</tr>
					<tr>
						<td width="20%%" class="row-odd name label">' . __( 'Name', '7listings' ) . '</td>
						<td class="row-odd name info"><strong>%s %s</strong></td>
					</tr>';
		default:
			return '';
	}
}

/**
 * Get email content
 *
 * @param string $option     Option name, used to get manual content in admin settings
 * @param string $template   Email template. If manual content is empty, get template content
 * @param array  $shortcodes Array of shortcodes used in email
 * @param array  $args       Array of available PHP variable used in email
 * @param bool   $preview    In preview mode?
 *
 * @return string
 */
function sl_email_content( $option = '', $template = '', $shortcodes = array(), $args = array(), $preview = false )
{
	// Use email template
	global $sl_email_use_template;
	$sl_email_use_template = true;

	ob_start();
	if ( ! empty( $args ) )
		extract( $args );

	if ( $preview )
		get_template_part( 'templates/email/header' );

	if ( $option && ( $content = sl_setting( $option ) ) )
	{
		echo wpautop( $content );
	}
	else
	{
		if ( $file = apply_filters( 'sl_email_template_content', '', $template ) )
		{
			include $file;
		}
		else
		{
			get_template_part( "templates/email/$template" );
		}
	}

	if ( $preview )
		get_template_part( 'templates/email/footer' );

	$content = ob_get_clean();

	return sl_email_replace( $content, $shortcodes );
}

/**
 * Replace a string with shortcodes in email
 *
 * @param string $str
 * @param array  $shortcodes
 *
 * @return string
 */
function sl_email_replace( $str, $shortcodes = array() )
{
	$shortcodes = array_merge( sl_email_shortcodes(), $shortcodes );

	return strtr( $str, $shortcodes );
}

/**
 * Get admin email to send to
 * Fallback: from option, theme 'email' option, WordPres admin email
 *
 * @param string $option
 *
 * @return array|string
 */
function sl_email_admin_email( $option )
{
	$to = sl_setting( $option );
	if ( $to )
	{
		$to = explode( ',', $to . ',' );
		$to = array_filter( array_map( 'trim', $to ) );
	}
	else
	{
		$to = sl_setting( 'email' );
		if ( ! $to )
			$to = get_option( 'admin_email' );
	}

	return $to;
}

/**
 * Get from email to send email to visitors
 * Fallback: from option, theme 'email' option, no-reply@domain.com
 *
 * @param string $option
 *
 * @return string
 */
function sl_email_from_name( $option )
{
	$from_name = sl_setting( $option );
	if ( ! $from_name )
		$from_name = get_bloginfo( 'name' );

	return $from_name;
}

/**
 * Get from email to send email to visitors
 * Fallback: from option, theme 'email' option, no-reply@domain.com
 *
 * @param string $option
 *
 * @return string
 */
function sl_email_from_email( $option )
{
	$from_email = sl_setting( $option );
	if ( ! $from_email )
		$from_email = sl_setting( 'email' );
	if ( ! $from_email )
		$from_email = 'no-reply@' . str_replace( array( 'http://', 'https://', 'www.' ), '', parse_url( HOME_URL, PHP_URL_HOST ) );

	return $from_email;
}

/**
 * Default (global email shortcodes)
 *
 * @return array
 */
function sl_email_shortcodes()
{
	$shortcodes = array(
		'[facebook]'   => '',
		'[twitter]'    => '',
		'[googleplus]' => '',
		'[phone]'      => '',
		'[fax]'        => '',
		'[address]'    => '',
		'[state]'      => '',
		'[postcode]'   => '',
	);
	foreach ( $shortcodes as $k => $v )
	{
		$key = str_replace( array( '[', ']' ), '', $k );
		if ( $value = sl_setting( $key ) )
			$shortcodes[$k] = $value;
	}
	$shortcodes['[city]'] = sl_setting( 'general_city' );

	$shortcodes['[site-title]'] = get_option( 'blogname' );
	$shortcodes['[tagline]']    = get_option( 'blogdescription' );

	$email = sl_setting( 'admin_email' );
	if ( ! $email )
		$email = sl_setting( 'email' );
	if ( ! $email )
		$email = get_option( 'admin_email' );
	$shortcodes['[email]'] = $email;

	$shortcodes['[url]']          = HOME_URL;
	$shortcodes['[current_year]'] = date( 'Y' );

	return $shortcodes;
}

/**
 * Get email preview link
 *
 * @param string $option   Option name which store email content
 * @param string $template Email content template file name
 *
 * @return string
 */
function sl_email_preview_link( $option = '', $template = '' )
{
	return wp_nonce_url( admin_url( "?sl-email-preview=$template&sl-option=$option" ), 'preview-email' );
}

/**
 * Detect if we should use a light or dark colour on a background colour
 *
 * @access public
 *
 * @param mixed  $color
 * @param string $dark  (default: '#000000')
 * @param string $light (default: '#FFFFFF')
 *
 * @return string
 */
function sl_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' )
{
	$hex = str_replace( '#', '', $color );

	$c_r        = hexdec( substr( $hex, 0, 2 ) );
	$c_g        = hexdec( substr( $hex, 2, 2 ) );
	$c_b        = hexdec( substr( $hex, 4, 2 ) );
	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155 ? $dark : $light;
}

/**
 * Hex darker/lighter/contrast functions for colours
 *
 * @access public
 *
 * @param mixed $color
 *
 * @return array
 */
function sl_rgb_from_hex( $color )
{
	$color = str_replace( '#', '', $color );
	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF"
	$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

	$rgb['R'] = hexdec( $color{0} . $color{1} );
	$rgb['G'] = hexdec( $color{2} . $color{3} );
	$rgb['B'] = hexdec( $color{4} . $color{5} );

	return $rgb;
}

/**
 * Hex darker/lighter/contrast functions for colours
 *
 * @access public
 *
 * @param mixed $color
 * @param int   $factor (default: 30)
 *
 * @return string
 */
function sl_hex_darker( $color, $factor = 30 )
{
	$base  = sl_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $v )
	{
		$amount      = $v / 100;
		$amount      = round( $amount * $factor );
		$new_decimal = $v - $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) :
			$new_hex_component = '0' . $new_hex_component;
		endif;
		$color .= $new_hex_component;
	}

	return $color;
}

/**
 * Hex darker/lighter/contrast functions for colours
 *
 * @access public
 *
 * @param mixed $color
 * @param int   $factor (default: 30)
 *
 * @return string
 */
function sl_hex_lighter( $color, $factor = 30 )
{
	$base  = sl_rgb_from_hex( $color );
	$color = '#';

	foreach ( $base as $v )
	{
		$amount      = 255 - $v;
		$amount      = $amount / 100;
		$amount      = round( $amount * $factor );
		$new_decimal = $v + $amount;

		$new_hex_component = dechex( $new_decimal );
		if ( strlen( $new_hex_component ) < 2 ) :
			$new_hex_component = '0' . $new_hex_component;
		endif;
		$color .= $new_hex_component;
	}

	return $color;
}
