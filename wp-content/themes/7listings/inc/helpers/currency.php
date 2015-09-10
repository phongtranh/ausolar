<?php

/**
 * This class contains helper functions for currency like getting list of currencies, get currency symbol, etc.
 * Some functions in this class is inspired by WooCommerce plugin.
 *
 * @since   5.2.2
 * @package 7listings
 */
class Sl_Currency
{
	/**
	 * Get full list of currency codes and names.
	 *
	 * @see    get_woocommerce_currencies()
	 * @return array
	 */
	public static function all()
	{
		$currencies = array(
			'AED' => __( 'United Arab Emirates Dirham', '7listings' ),
			'AUD' => __( 'Australian Dollars', '7listings' ),
			'BDT' => __( 'Bangladeshi Taka', '7listings' ),
			'BRL' => __( 'Brazilian Real', '7listings' ),
			'BGN' => __( 'Bulgarian Lev', '7listings' ),
			'CAD' => __( 'Canadian Dollars', '7listings' ),
			'CLP' => __( 'Chilean Peso', '7listings' ),
			'CNY' => __( 'Chinese Yuan', '7listings' ),
			'COP' => __( 'Colombian Peso', '7listings' ),
			'CZK' => __( 'Czech Koruna', '7listings' ),
			'DKK' => __( 'Danish Krone', '7listings' ),
			'EUR' => __( 'Euros', '7listings' ),
			'HKD' => __( 'Hong Kong Dollar', '7listings' ),
			'HRK' => __( 'Croatia kuna', '7listings' ),
			'HUF' => __( 'Hungarian Forint', '7listings' ),
			'ISK' => __( 'Icelandic krona', '7listings' ),
			'IDR' => __( 'Indonesia Rupiah', '7listings' ),
			'INR' => __( 'Indian Rupee', '7listings' ),
			'ILS' => __( 'Israeli Shekel', '7listings' ),
			'JPY' => __( 'Japanese Yen', '7listings' ),
			'KRW' => __( 'South Korean Won', '7listings' ),
			'MYR' => __( 'Malaysian Ringgits', '7listings' ),
			'MXN' => __( 'Mexican Peso', '7listings' ),
			'NGN' => __( 'Nigerian Naira', '7listings' ),
			'NOK' => __( 'Norwegian Krone', '7listings' ),
			'NZD' => __( 'New Zealand Dollar', '7listings' ),
			'PHP' => __( 'Philippine Pesos', '7listings' ),
			'PLN' => __( 'Polish Zloty', '7listings' ),
			'GBP' => __( 'Pounds Sterling', '7listings' ),
			'RON' => __( 'Romanian Leu', '7listings' ),
			'RUB' => __( 'Russian Ruble', '7listings' ),
			'SGD' => __( 'Singapore Dollar', '7listings' ),
			'ZAR' => __( 'South African rand', '7listings' ),
			'SEK' => __( 'Swedish Krona', '7listings' ),
			'CHF' => __( 'Swiss Franc', '7listings' ),
			'TWD' => __( 'Taiwan New Dollars', '7listings' ),
			'THB' => __( 'Thai Baht', '7listings' ),
			'TRY' => __( 'Turkish Lira', '7listings' ),
			'USD' => __( 'US Dollars', '7listings' ),
			'VND' => __( 'Vietnamese Dong', '7listings' ),
		);

		return array_unique( apply_filters( 'sl_currencies', $currencies ) );
	}

	/**
	 * Get full list of currency positions codes and names.
	 *
	 * @see    get_woocommerce_currency_position()
	 * @return array
	 */
	public static function positions()
	{
		$positions = array(
			'left'        => __( 'Left', '7listings' ) . ' ( ' . Sl_Currency::symbol() . '99.99 )',
			'right'       => __( 'Right', '7listings' ) . ' (99.99' . Sl_Currency::symbol() . ' )',
			'left_space'  => __( 'Left with space', '7listings' ) . ' (' . Sl_Currency::symbol() . ' 99.99)',
			'right_space' => __( 'Right with space', '7listings' ) . ' (99.99 ' . Sl_Currency::symbol() . ')',
		);

		return array_unique( apply_filters( 'sl_currency_positions', $positions ) );
	}

	/**
	 * Get currency symbol.
	 *
	 * @see get_woocommerce_currency_symbol()
	 *
	 * @param string $currency Currency code. Optional. If omitted, get from theme settings.
	 *
	 * @return string HTML entity for currency symbol.
	 */
	public static function symbol( $currency = '' )
	{
		if ( ! $currency )
			$currency = sl_setting( 'currency' );
		switch ( $currency )
		{
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'CLP' :
			case 'MXN' :
			case 'NZD' :
			case 'HKD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'CNY' :
			case 'RMB' :
			case 'JPY' :
				$currency_symbol = '&yen;';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'TRY' :
				$currency_symbol = '&#84;&#76;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'DKK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'INR' :
				$currency_symbol = 'Rs.';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr.';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			default    :
				$currency_symbol = '';
				break;
		}

		return apply_filters( 'sl_currency_symbol', $currency_symbol, $currency );
	}

	/**
	 * Format currency according theme settings.
	 *
	 * @param int          $amount Amount
	 * @param string|array $args   {
	 *     Display parameters.
	 *
	 *     @type string $before Markup to prepend to the currency. Optional. Default empty.
	 *     @type string $after  Markup to append to the currency. Optional. Default empty.
	 *     @type string $class  CSS class for wrapper. Optional. Default empty.
	 *     @type string $type   Return format type: 'price_html' (price html markup) or 'plain'. Optional. Default 'price_html'.
	 * }
	 * @return string HTML markup for currency output
	 */
	public static function format( $amount = 0, $args = array() )
	{
		$args = wp_parse_args( $args, array(
			'before' => '',
			'after'  => '',
			'class'  => '',
			'type'   => 'price_html',
		) );

		// Setup for price tag in booking page. Need to be done before changing $amount below
		if ( 'booking_price_html' == $args['type'] )
		{
			// Add CSS class and don't display 'from' for FREE resource
			$text = '';
			if ( ! $amount )
			{
				$args['class'] .= 'free-booking';
			}
			else
			{
				$text = __( 'from', '7listings' ) . '<br>';
			}
		}

		if ( $amount )
		{
			$amount   = sl_format_number( $amount );
			$position = sl_setting( 'currency_position' );
			if ( 'left' == $position )
			{
				$args['before'] .= Sl_Currency::symbol();
			}
			elseif ( 'right' == $position )
			{
				$args['after'] .= Sl_Currency::symbol();
			}
			elseif ( 'left_space' == $position )
			{
				$args['before'] .= Sl_Currency::symbol() . '&nbsp;';
			}
			elseif ( 'right_space' == $position )
			{
				$args['after'] .= '&nbsp;' . Sl_Currency::symbol();
			}
		}
		elseif ( false !== $amount )
		{
			$amount = __( 'Free', '7listings' );
		}
		else
		{
			$amount = '';
		}

		// Default output in plain format
		$output = $args['before'] . $amount . $args['after'];

		// HTML markup for price tag
		if ( 'price_html' == $args['type'] )
		{
			$output = sprintf(
				'<span class="price%s">%s<span class="amount">%s</span>%s</span>',
				$args['class'] ? ' ' . $args['class'] : '',
				$args['before'],
				$amount,
				$args['after']
			);
		}

		// HTML markup for booking price  tag
		if ( 'booking_price_html' == $args['type'] )
		{
			$output = sprintf(
				'<div%s id="lead-in-rate">%s<span class="price">%s<span class="amount">%s</span>%s</span></div>',
				$args['class'] ? ' class="' . $args['class'] . '"' : '',
				$text,
				$args['before'],
				$amount,
				$args['after']
			);
		}

		return $output;
	}

}
