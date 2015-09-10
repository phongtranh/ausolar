<?php

class Sl_Widget_Twitter extends WP_Widget
{
	/**
	 * @var array Widget defaults
	 */
	public $default = array(
		'title'               => '',
		'consumer_key'        => '',
		'consumer_secret'     => '',
		'access_token'        => '',
		'access_token_secret' => '',
		'username'            => '',
		'count'               => 2,
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Twitter
	 */
	function __construct()
	{
		parent::__construct(
			'sl-twitter',
			__( '7 - Twitter', '7listings' ),
			array(
				'classname'   => 'sl-list posts twitter-feed',
				'description' => __( 'Add a Twitter feed.', '7listings' ),
			)
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance )
	{
		$instance = array_merge( $this->default, $instance );

		if ( ! $instance['consumer_key'] || ! $instance['consumer_secret'] || ! $instance['access_token'] || ! $instance['access_token_secret'] || ! $instance['username'] )
			return;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		$transient_key = 'sl_tweets_' . md5( serialize( $instance ) );
		if ( false === ( $tweets = get_transient( $transient_key ) ) )
		{
			require_once THEME_DIR . 'lib/twitter-api-php.php';

			$options = array(
				'oauth_access_token'        => $instance['access_token'],
				'oauth_access_token_secret' => $instance['access_token_secret'],
				'consumer_key'              => $instance['consumer_key'],
				'consumer_secret'           => $instance['consumer_secret'],
			);

			$url    = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
			$fields = "?screen_name={$instance['username']}&count={$instance['count']}";
			$method = 'GET';

			$twitter = new TwitterAPIExchange( $options );
			$tweets  = $twitter->setGetfield( $fields )->buildOauth( $url, $method )->performRequest();
			$tweets  = @json_decode( $tweets );

			if ( empty( $tweets ) )
			{
				_e( 'Cannot retrieve tweets.', '7listings' );
				echo $args['after_widget'];

				return;
			}

			// Save our new transient, cache for 1 hour
			set_transient( $transient_key, $tweets, 3600 );
		}

		$tpl = '
			<article class="post tweet">
				<a class="tweet_avatar" href="http://twitter.com/%s">
					<figure class="thumbnail"><img class="photo" src="%s"></figure>
				</a>
				<div class="details">
					<p class="entry-content tweet-text">%s</p>
					<time class="entry-meta date entry-date tweet-date"><a href="https://twitter.com/%s/status/%s">%s</a></time>
				</div>
			</article>
		';
		foreach ( $tweets as $tweet )
		{
			printf(
				$tpl,
				$instance['username'],
				$tweet->user->profile_image_url,
				$this->convert_links( $tweet->text ),
				$instance['username'], $tweet->id, human_time_diff( strtotime( $tweet->created_at ) ) . __( ' ago', '7listings' )
			);
		}
		echo $args['after_widget'];
	}

	/**
	 * Replace link tweet
	 *
	 * @param $text
	 *
	 * @return string
	 */
	function convert_links( $text )
	{
		$text = utf8_decode( $text );
		$text = preg_replace( '#https?://[a-z0-9._/-]+#i', '<a rel="nofollow" target="_blank" href="$0">$0</a>', $text );
		$text = preg_replace( '#@([a-z0-9_]+)#i', '@<a rel="nofollow" target="_blank" href="http://twitter.com/$1">$1</a>', $text );
		$text = preg_replace( '# \#([a-z0-9_-]+)#i', ' #<a rel="nofollow" target="_blank" href="http://twitter.com/search?q=%23$1">$1</a>', $text );

		return $text;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance )
	{
		$instance                        = $old_instance;
		$instance['title']               = strip_tags( $new_instance['title'] );
		$instance['username']            = strip_tags( $new_instance['username'] );
		$instance['count']               = (int) $new_instance['count'];
		$instance['consumer_key']        = strip_tags( $new_instance['consumer_key'] );
		$instance['consumer_secret']     = strip_tags( $new_instance['consumer_secret'] );
		$instance['access_token']        = strip_tags( $new_instance['access_token'] );
		$instance['access_token_secret'] = strip_tags( $new_instance['access_token_secret'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );
		?>
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Username', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-prepend">
					<span class="add-on">@</span>
					<input class="widefat" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo $instance['username']; ?>">
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label class="input-label"><?php _e( 'Number', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input class="amount" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo $instance['count']; ?>">
					<span class="add-on"><?php _e( 'Tweets', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Consumer Key:', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" value="<?php echo $instance['consumer_key']; ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Consumer Secret:', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" value="<?php echo $instance['consumer_secret']; ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Access Token:', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'access_token' ); ?>" value="<?php echo $instance['access_token']; ?>">
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Access Token Secret:', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<input class="widefat" type="text" name="<?php echo $this->get_field_name( 'access_token_secret' ); ?>" value="<?php echo $instance['access_token_secret']; ?>">
			</div>
		</div>
	<?php
	}
}
