<?php
add_action( 'sl_header_bottom', 'sl_weather', 20 ); // 20 = after social buttons

/**
 * Show weather information in shortcode
 * last edit: 5.1.1
 *
 * @return void
 */
function sl_weather()
{
	if ( ! sl_setting( 'weather_active' ) || ! sl_setting( 'woeid' ) )
		return;

	$info = sl_get_weather_info( sl_setting( 'woeid' ), sl_setting( 'weather_unit' ) );
	if ( empty( $info ) )
		return;

	$code = intval( $info['code'] );
	$img  = '';

	/**
	 * Valid code is always < 100
	 * If code is invalid (or N/A) then don't display image
	 */
	if ( $code < 100 )
	{
		// Show weather icon
		if ( 'icon' == sl_setting( 'design_weather_style' ) )
		{
			$img = '<i class="mo ' . sl_weather_icon( $info['code'] ) . '"></i>';
		}
		// Show image from Yahoo! server
		else
		{
			// Get the correct time (with consideration of timezone)
			$saved_timezone = date_default_timezone_get();
			date_default_timezone_set( sl_setting( 'weather_timezone' ) );
			$hour = (int) date( 'H', strtotime( $info['time'] ) );
			$type = ( $hour >= 7 && $hour <= 19 ) ? 'd' : 'n'; // Day or night
			$img  = '<img src="http://l.yimg.com/a/i/us/nws/weather/gr/' . $code . $type . '.png" alt="weather">';
			date_default_timezone_set( $saved_timezone );
		}
	}

	$class = 'weather';
	if ( sl_setting( 'design_weather_color_scheme' ) )
		$class .= ' ' . sl_setting( 'design_weather_color_scheme' );

	printf(
		'<div id="weather" class="%s">
			<span class="temp">%s<sup>o</sup>%s</span>
			<span class="humi">%s <strong>%s%%</strong></span>
			<span class="wind">%s <strong> %s%s</strong></span>
			%s
		</div>',
		$class,
		$info['temp'], $info['units']['temp'],
		__( 'Humidity:', '7listings' ), $info['humi'],
		__( 'Wind:', '7listings' ), $info['wind'], $info['units']['speed'],
		$img
	);
}

/**
 * Show weather information
 *
 * @param string $w Area code
 * @param string $u Temperature unit
 *
 * @return bool|array False if error, array of information if success
 */
function sl_get_weather_info( $w = '', $u = 'c' )
{
	// Use transient first
	$key    = 'sl-weather';
	$output = get_transient( $key );
	if ( ! empty( $output ) )
		return $output;

	$yahoo_weather_url = add_query_arg( array( 'w' => $w, 'u' => $u ), 'http://weather.yahooapis.com/forecastrss' );

	$request = wp_remote_get( $yahoo_weather_url );
	$body    = wp_remote_retrieve_body( $request );

	// Try simplest method if WordPress HTTP API doesn't work
	if ( is_wp_error( $request ) || 200 != $request['response']['code'] || ! $body )
		$body = file_get_contents( $yahoo_weather_url );

	if ( ! $body )
		return false;

	$xml = @simplexml_load_string( $body );

	if ( ! is_object( $xml ) )
		return false;

	$description = $xml->channel->item->description;

	// No image
	if ( ! preg_match( '/src="(.*?)"/i', $description, $match ) )
		return false;

	$output = array(
		'img'   => $match[1],
		'units' => array(
			'temp'  => (string) $xml->channel->children( 'yweather', true )->units->attributes()->temperature,
			'disc'  => (string) $xml->channel->children( 'yweather', true )->units->attributes()->distance,
			'pres'  => (string) $xml->channel->children( 'yweather', true )->units->attributes()->pressure,
			'speed' => (string) $xml->channel->children( 'yweather', true )->units->attributes()->speed,
		),
		'temp'  => (int) $xml->channel->item->children( 'yweather', true )->condition->attributes()->temp,
		'wind'  => (int) $xml->channel->children( 'yweather', true )->wind->attributes()->speed,
		'humi'  => (int) $xml->channel->children( 'yweather', true )->atmosphere->attributes()->humidity,
		'code'  => (int) $xml->channel->item->children( 'yweather', true )->condition->attributes()->code,
		'time'  => (string) $xml->channel->lastBuildDate,
	);

	set_transient( $key, $output, 1800 );

	return $output;
}

/**
 * Find WOEID for Yahoo! weather API
 *
 * @param string $address
 *
 * @return mixed
 */
function sl_find_woeid( $address )
{
	// Use transient first
	$key = 'sl-woeid';
	if ( $woeid = get_transient( $key ) )
		return $woeid;

	$url = 'http://query.yahooapis.com/v1/public/yql?q=%s&format=json';
	$q   = 'select woeid from geo.places where text="%s"';
	$q   = sprintf( $q, $address );
	$url = sprintf( $url, rawurlencode( $q ) );

	$req     = wp_remote_get( $url );
	$content = wp_remote_retrieve_body( $req );

	if ( ! empty( $content ) )
	{
		$content = @json_decode( $content );
		$woeid   = @$content->query->results->place->woeid;
		$woeid   = @(string) $woeid;
		if ( $woeid )
		{
			set_transient( $key, $woeid, 1800 );

			return $woeid;
		}
	}

	// Try by GeoPlace API
	return sl_find_woeid_geoplace( $address );
}

/**
 * Find WOEID for Yahoo! weather API
 *
 * @param string $address
 *
 * @return mixed
 */
function sl_find_woeid_geoplace( $address )
{
	// Use transient first
	$key = 'sl-woeid';
	if ( $woeid = get_transient( $key ) )
		return $woeid;

	$woeid = false;
	$appid = '65z4RWvV34F0DWArx.xonlAqsESudVPtDQ67qkx8eRgdCP74CQbMwJekKA_2';
	$url   = sprintf(
		'http://where.yahooapis.com/v1/places.q(\'%s\')?appid=%s',
		rawurlencode( $address ),
		$appid
	);

	$req     = wp_remote_get( $url );
	$content = wp_remote_retrieve_body( $req );
	if ( ! empty( $content ) )
	{
		$xml   = @simplexml_load_string( $content );
		$woeid = @$xml->place->woeid;
		$woeid = @(string) $woeid;
		if ( $woeid )
		{
			set_transient( $key, $woeid, 1800 );

			return $woeid;
		}
	}

	return $woeid;
}

/**
 * Get the class for icon font corresponding the weather code
 * Use Meteocons icon font, classes are generated using IcoMoon App
 *
 * @since 5.1.1
 *
 * @param string $code Yahoo weather code
 *
 * @return string
 * @see   https://developer.yahoo.com/weather/documentation.html#codes for list of codes
 */
function sl_weather_icon( $code )
{
	$classes = array(
		0    => 'mo-windy3',
		1    => 'mo-windy3',
		2    => 'mo-windy3',
		3    => 'mo-lightning5',
		4    => 'mo-lightning4',
		5    => 'mo-snowy5',
		6    => 'mo-snowy5',
		7    => 'mo-snowy5',
		8    => 'mo-rainy3',
		9    => 'mo-rainy',
		10   => 'mo-rainy4',
		11   => 'mo-rainy4',
		12   => 'mo-rainy4',
		13   => 'mo-snowy',
		14   => 'mo-snowy2',
		15   => 'mo-snowy3',
		16   => 'mo-snowflake',
		17   => 'mo-rainy4',
		18   => 'mo-snowy5',
		19   => 'mo-wind',
		20   => 'mo-weather3',
		21   => 'mo-weather3',
		22   => 'mo-weather3',
		23   => 'mo-windy',
		24   => 'mo-windy2',
		25   => 'mo-snowflake',
		26   => 'mo-cloud2',
		27   => 'mo-cloud',
		28   => 'mo-cloudy',
		29   => 'mo-cloud',
		30   => 'mo-cloudy',
		31   => 'mo-moon',
		32   => 'mo-sun',
		33   => 'mo-moon',
		34   => 'mo-sun',
		35   => 'mo-weather5',
		36   => 'mo-sun',
		37   => 'mo-lightning2',
		38   => 'mo-lightning',
		39   => 'mo-lightning',
		40   => 'mo-rainy4',
		41   => 'mo-snowy3',
		42   => 'mo-snowy2',
		43   => 'mo-snowy3',
		44   => 'mo-cloud2',
		45   => 'mo-lightning5',
		46   => 'mo-snowy3',
		47   => 'mo-lightning5',
		3200 => 'mo-none',
	);

	return isset( $classes[$code] ) ? $classes[$code] : false;
}
