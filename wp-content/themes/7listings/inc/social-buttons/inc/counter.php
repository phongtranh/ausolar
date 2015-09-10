<?php

/**
 * This class gets counter of shares on social networks
 * It uses transient to cache the counter to reduce number of requests to remote servers
 */
class Sl_Social_Buttons_Counter
{
	/**
	 * Twitter app key
	 *
	 * @see https://apps.twitter.com
	 * @var array
	 */
	public static $twitter_app_key = array(
		'consumer_key'              => 'llb0MYWNIcO6lhXb70pyweSgg',
		'consumer_secret'           => 'hPMdNIMSPVjJ3GVNJ3Pm6bzFHuNl4sUAKZ9vP4csSAux6KCSLc',
		'oauth_access_token'        => '14736998-9xN9cyQYgCJitBybs0633fPpG0BHDTgArUsBCUbJy',
		'oauth_access_token_secret' => 'fzrkb7BU2m5Zz2Inmugz1S0Js4jlq7GZwLND7pkmC37kc',
	);

	/**
	 * Google+ API key
	 *
	 * @see https://console.developers.google.com
	 * @var string
	 */
	public static $google_key = 'AIzaSyA1KPAFD3kapb5NtuCgOxPtb7GORleeR2Q';

	/**
	 * Instagram client key
	 *
	 * @see http://instagram.com/developer/clients/manage/
	 * @var string
	 */
	public static $instagram_client_key = '70beb69a13894ba1ae6061c3c6d63ae6';

	/**
	 * Get number of shares on social networks for an URL
	 * Use transient to cache the counter
	 *
	 * @param string $network Network name, in lowercase
	 * @param string $url     URL needs to get counter
	 * @param bool   $cache   Whether get counter from cache. Usually set to false in testing mode
	 *
	 * @return int|string
	 */
	public static function get( $network, $url, $cache = true )
	{
		if ( $cache )
		{
			$counter = self::get_cache( $url, $network );
			if ( false !== $counter && $counter )
				return $counter;
		}

		$counter = self::$network( $url );
		self::set_cache( $counter, $url, $network );

		return $counter;
	}

	/**
	 * Get number of Facebook shares for an URL
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function facebook( $url )
	{
		$query_url = add_query_arg( array(
			'query'  => rawurlencode( "select share_count from link_stat where url='$url'" ),
			'format' => 'json',
		), 'https://api.facebook.com/method/fql.query' );
		$content   = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter   = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content[0] ) && isset( $content[0]->share_count ) )
				$counter = $content[0]->share_count;
		}

		return $counter;
	}

	/**
	 * Get number of likes for a Facebook page
	 *
	 * @param string $url URL or name of Facebook page
	 *
	 * @return int
	 */
	public static function facebook_page( $url )
	{
		$query  = "SELECT like_count";
		$query .= " FROM link_stat WHERE url = '" . $url . "'";

		$response = file_get_contents( 'https://api.facebook.com/method/fql.query?format=json&query=' . urlencode( $query ) );

		$count = 0;

		if ( ! empty( $response ) )
		{
			$results    = json_decode( $response );
			$result     = $results[0];
			$count      = $result->like_count;
		}

		return $count;
	}

	/**
	 * Get number of tweets
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function twitter( $url )
	{
		$query_url = add_query_arg( array(
			'url' => rawurlencode( $url ),
		), 'http://urls.api.twitter.com/1/urls/count.json' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content->count ) )
				$counter = $content->count;
		}

		return $counter;
	}

	/**
	 * Get number of twitter followers
	 *
	 * @param string $url  URL to profile page or twitter username
	 * @param array  $args Twitter OAuth keys, get from https://apps.twitter.com
	 *
	 * @return int
	 */
	public static function twitter_followers( $url, $args = array() )
	{
		$name = $url;

		// If URL is passed to function
		if ( filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			$parts = parse_url( $url );
			$name  = trim( $parts['path'], '/' );
		}

		$args = array_merge( self::$twitter_app_key, $args );
		require_once THEME_DIR . 'lib/twitter-api-php.php';

		$url     = 'https://api.twitter.com/1.1/users/show.json';
		$field   = '?screen_name=' . $name;
		$method  = 'GET';
		$twitter = new TwitterAPIExchange( $args );
		$content = $twitter->setGetfield( $field )->buildOauth( $url, $method )->performRequest();

		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content->followers_count ) )
				$counter = $content->followers_count;
		}

		return $counter;
	}

	/**
	 * Get number of +1 for an URL
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function google( $url )
	{
		$query_url = add_query_arg( array(
			'url'   => rawurlencode( $url ),
			'count' => true,
		), 'https://plusone.google.com/_/+1/fastbutton' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$doc = new DOMDocument;
			@$doc->loadHTML( $content );
			if ( $doc )
			{
				$element = $doc->getElementById( 'aggregateCount' );
				if ( isset( $element->nodeValue ) )
					$counter = $element->nodeValue;
			}
		}

		return $counter;
	}

	/**
	 * Get number of followers on Google+
	 *
	 * @param string $url URL to profile page or profile username/ID
	 * @param string $key API key, get from https://console.developers.google.com
	 *
	 * @return int
	 */
	public static function google_followers( $url, $key = '' )
	{
		$id = $url;

		// If URL is passed to function
		if ( filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			$parts = parse_url( $url );
			$id    = trim( $parts['path'], '/' );
		}

		if ( ! $key )
			$key = self::$google_key;

		$query_url = add_query_arg( array(
			'key' => $key,
		), 'https://www.googleapis.com/plus/v1/people/' . $id );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content->circledByCount ) )
				$counter = $content->circledByCount;
		}

		return $counter;
	}

	/**
	 * Get number of shares on LinkedIn
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function linkedin( $url )
	{
		$query_url = add_query_arg( array(
			'url'    => rawurlencode( $url ),
			'format' => 'json',
		), 'http://www.linkedin.com/countserv/count/share' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content->count ) )
				$counter = $content->count;
		}

		return $counter;
	}

	/**
	 * Get number of pin
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function pinterest( $url )
	{
		$query_url = add_query_arg( array(
			'url' => rawurlencode( $url ),
		), 'http://api.pinterest.com/v1/urls/count.json' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = str_replace( array( 'receiveCount(', ')' ), '', $content );
			$content = @json_decode( $content );
			if ( $content && isset( $content->count ) )
				$counter = $content->count;
		}

		return $counter;
	}

	/**
	 * Get number of pinterest followers
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function pinterest_followers( $url )
	{
		$id = $url;

		// If URL is passed to function
		if ( filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			$parts = parse_url( $url );
			$id    = trim( $parts['path'], '/' );
		}

		$query_url = 'http://pinterest.com/' . $id;
		$counter   = 0;

		/**
		 * Check headers before getting meta tags
		 * get_headers does not throw a warning if URL invalid or not found but get_meta_tags does
		 */
		$headers = get_headers( $query_url );
		if ( false !== strpos( $headers[0], '200' ) )
		{
			$content = get_meta_tags( $query_url );
			if ( isset( $content['pinterestapp:followers'] ) )
			{
				$counter = $content['pinterestapp:followers'];
			}
		}

		return $counter;
	}

	/**
	 * Get number of stumbles
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function stumbleupon( $url )
	{
		$query_url = add_query_arg( array(
			'url' => rawurlencode( $url ),
		), 'http://www.stumbleupon.com/services/1.01/badge.getinfo' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			if ( $content && isset( $content->result->views ) )
				$counter = $content->result->views;
		}

		return $counter;
	}

	/**
	 * Get number of submits on reddit
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function reddit( $url )
	{
		$query_url = add_query_arg( array(
			'url' => rawurlencode( $url ),
		), 'http://www.reddit.com/api/info.json' );

		$content = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		$counter = 0;
		if ( $content )
		{
			$content = @json_decode( $content );
			$score   = $ups = $downs = 0;
			if ( $content && isset( $content->data->children ) )
			{
				foreach ( $content->data->children as $child )
				{
					$ups += (int) $child->data->ups;
					$downs += (int) $child->data->downs;
					//$score+= (int) $child->data->downs->score;
				}
				$score = $ups - $downs;
			}
			$counter = $score;
		}

		return $counter;
	}

	/**
	 * Get number of instagram followers
	 *
	 * @param string $url
	 *
	 * @return int
	 */
	public static function instagram_followers( $url, $key = '' )
	{
		$counter = 0;

		$id = $url;

		// If URL is passed to function
		if ( filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			$parts = parse_url( $url );
			$id    = trim( $parts['path'], '/' );
		}

		if ( ! $key )
			$key = self::$instagram_client_key;

		// Get ID from username
		$query_url = add_query_arg( array(
			'client_id' => $key,
			'q'         => $id,
			'count'     => 1,
		), 'https://api.instagram.com/v1/users/search' );
		$content   = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		if ( ! $content )
			return $counter;

		$content = @json_decode( $content );
		if ( ! isset( $content->data[0]->id ) )
			return $counter;

		$id = $content->data[0]->id;

		// Get number of followers
		$query_url = add_query_arg( array(
			'client_id' => $key,
		), 'https://api.instagram.com/v1/users/' . $id );
		$content   = wp_remote_retrieve_body( wp_remote_get( $query_url ) );
		if ( ! $content )
			return $counter;

		$content = @json_decode( $content );
		if ( isset( $content->data->counts->followed_by ) )
			$counter = $content->data->counts->followed_by;

		return $counter;
	}

	/**
	 * Get counter from cache
	 *
	 * @param string $url     URL to share
	 * @param string $network Name of the social network
	 *
	 * @return int
	 */
	public static function get_cache( $url, $network )
	{
		return get_transient( self::key( $url, $network ) );
	}

	/**
	 * Store counter in cache for faster access in the future
	 *
	 * @param int    $counter Number of shares
	 * @param string $url     URL to share
	 * @param string $network Name of the social network
	 * @param int    $time    Cache time
	 *
	 * @return void
	 */
	public static function set_cache( $counter, $url, $network, $time = 3600 )
	{
		set_transient( self::key( $url, $network ), $counter, $time );
	}

	/**
	 * Get transient key for an URL and network
	 * Used to get cached value of counter without querying remotely
	 *
	 * @param string $url
	 * @param string $network Social network name
	 *
	 * @return int
	 */
	public static function key( $url, $network )
	{
		// slsc = Sl (7listings) + Social Counter
		return 'slsc-' . md5( "$network-$url" );
	}

}
