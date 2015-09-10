<?php

/**
 * This class display list of posts in the frontend
 */
class Sl_Recently_Viewed  extends WP_Widget
{
	/**
	 * Default settings for widget
	 *
	 * @var array
	 */
	public $default = array(
		'post_types'     => array( 'company', 'accommodation', 'tour', 'rental', 'product', 'attraction' ),

		'title'          => '', // All
		'number'         => 5,  // All

		'hierarchy'      => 0,      // ATR, company
		'display_order'  => 'all',  // ATR, company

		'display'        => 'list', // All
		'columns'        => 1,      // All

		'post_title'     => 1,
		'thumbnail'      => 1,               // All
		'image_size'     => 'sl_thumb_tiny', // All
		'rating'         => 1,               // All except post
		'price'          => 1,               // ATR, product
		'booking'        => 1,               // ATR
		'excerpt'        => 1,               // All
		'excerpt_length' => 15,              // All
		'show_free'      => 1, // Product, Accommodation, Rental, Tour
	);

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->default['more_listings_text'] = __( 'See more listings', '7listings' );
		parent::__construct(
			'sl-recently-viewed',
			__( '7 - Recently Viewed', '7listings' ),
			array(
				'description' => __( 'List of posts that were visited by user.', '7listings' ),
			)
		);

		add_action( 'template_redirect', array( __CLASS__, 'posts_visited' ));
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	public function widget( $args, $instance )
	{
		$instance = array_merge( $this->default, $instance );

		if ( ! isset( $_COOKIE['sl_recent_posts'] ) )
			return;

		$posts = explode( ',', $_COOKIE['sl_recent_posts'] );


		if( is_single() )
		{
			// Search post current
			$post_not_in = array_search( get_the_ID(), $posts );

			// Remove post current from array posts
			unset( $posts[$post_not_in] );
		}

		$instance = array_merge( array( 'post__in' => $posts ), $instance );

		$html = self::post_list( $instance );

		if ( ! $html )
			return;

		echo $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $title )
			echo $args['before_title'] . $title . $args['after_title'];

		echo $html;

		echo $args['after_widget'];
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance               = $old_instance;
		$instance['post_types'] = $new_instance['post_types'];

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );

		$instance['location']      = strip_tags( $new_instance['location'] );
		$instance['display_order'] = strip_tags( $new_instance['display_order'] );

		$instance['image_size']     = strip_tags( $new_instance['image_size'] );
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

		$instance['display'] = strip_tags( $new_instance['display'] );
		$instance['columns'] = absint( $new_instance['columns'] );

		$checkboxes = array(
			'hierarchy',

			'post_title',
			'thumbnail',
			'price',
			'rating',
			'booking',
			'excerpt',
			'show_free',
		);
		foreach ( $checkboxes as $checkbox )
		{
			$instance[$checkbox] = intval( ! empty( $new_instance[$checkbox] ) );
		}

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	public function form( $instance )
	{
		$instance = array_merge( $this->default, $instance );

		// Get available post types
		$post_types   = sl_setting( 'listing_types' );
		$post_types[] = 'post';

		$field_post_type = esc_attr( $this->get_field_name( 'post_type' ) );
		?>
		<!-- Widget title and post type -->
		<?php include THEME_INC . 'widgets/tpl/title.php'; ?>
		<div class="sl-settings post-types">
			<div class="sl-label">
				<label><?php _e( 'Post Type', '7listings' ) ?></label>
			</div>
			<div class="sl-input post-type-input">
				<?php
				$types = array(
					'-1' => __( 'All', '7listings' ),
				);
				switch ( Sl_License::license_type() )
				{
					case '7Basic':
						$types['post'] = __( 'News', '7listings' );
						break;
					case '7Comp':
						$types['post']    = __( 'News', '7listings' );
						$types['company'] = __( 'Company', '7listings' );
						break;
					case '7Accommodation':
						$types['post']          = __( 'News', '7listings' );
						$types['accommodation'] = __( 'Accommodation', '7listings' );
						break;
					case '7Tours':
						$types['post'] = __( 'News', '7listings' );
						$types['tour'] = __( 'Tour', '7listings' );
						break;
					case '7Rental':
						$types['post']   = __( 'News', '7listings' );
						$types['rental'] = __( 'Rental', '7listings' );
						break;
					case '7Products':
						$types['post']    = __( 'News', '7listings' );
						$types['product'] = __( 'Product', '7listings' );
						break;
					case '7Pro':
					case '7Network':
						$types['post']          = __( 'Posts', '7listings' );
						$types['company']       = __( 'Company', '7listings' );
						$types['accommodation'] = __( 'Accommodation', '7listings' );
						$types['tour']          = __( 'Tour', '7listings' );
						$types['rental']        = __( 'Rental', '7listings' );
						$types['product']       = __( 'Product', '7listings' );
						$types['attraction']    = __( 'Attraction', '7listings' );
						break;
				}
				$html = array();
				foreach ( $types as $k => $v )
				{
					$class = '';
					if ( 'company' == $k )
						$class = 'companies';
					elseif ( 'post' == $k )
						$class = 'wp-posts';
					elseif ( '-1' == $k )
						$class = 'wp-all';

					printf( '<input type="checkbox" id="%s" class="checkbox-toggle" name="%s[]" value="%s" %s><label for="%s" class="%s icon" title=" %s">%s</label>',
						$k,
						$this->get_field_name( 'post_types' ),
						$k,
						checked( in_array( $k, $instance['post_types'] ), 1, false ),
						$k,
						$class ? $class : $k . 's',
						'wp-all' == $class ? __( 'Select all post types ', '7listing' ) : __( 'Select ' . $v, '7listing' ),
						$v
					);
				}
				?>
			</div>
		</div>

		<br>

		<!-- Hierarchy -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|company">
			<div class="sl-label">
				<label>
					<?php _e( 'Hierarchy', '7listings' ); ?>
					<?php echo do_shortcode( '[tooltip content="' . __( 'Display listings based on their hierarchy (Star &gt; Featured &gt; Normal)', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'hierarchy' ), $instance['hierarchy'] ); ?>
			</div>
		</div>

		<!-- Display -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|company">
			<div class="sl-label">
				<label>
					<?php _e( 'Display', '7listings' ); ?>
					<?php echo do_shortcode( '[tooltip content="' . __( 'Select what type and the order of listings you want to display', '7listings' ) . '" type="info"]<span class="icon"></span>[/tooltip]' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'display_order' ); ?>">
					<?php
					Sl_Form::options( $instance['display_order'], array(
						'star'            => __( 'Star', '7listings' ),
						'star-featured'   => __( 'Star &gt; Featured', '7listings' ),
						'featured'        => __( 'Featured', '7listings' ),
						'featured-normal' => __( 'Featured &gt; Normal', '7listings' ),
						'all'             => __( 'All', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>

		<br>

		<!-- Amount -->
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Amount', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="small-text" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>">
					<span class="add-on"><?php _e( 'slides', '7listings' ); ?></span>
				</span>
			</div>
		</div>

		<?php include THEME_INC . 'widgets/tpl/layout.php'; ?>

		<br>

		<?php include THEME_INC . 'widgets/tpl/thumbnail.php'; ?>

		<!-- Checkboxes for listing elements -->
		<?php
		$checkboxes = array(
			'post_title' => __( 'Title', '7listings' ),
		);
		include THEME_INC . 'widgets/tpl/checkboxes.php';
		?>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="post" data-reverse="1">
			<div class="sl-label">
				<label><?php _e( 'Rating', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'rating' ), $instance['rating'] ); ?>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|product">
			<div class="sl-label">
				<label><?php _e( 'Price', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'price' ), $instance['price'] ); ?>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental">
			<div class="sl-label">
				<label><?php _e( 'Booking Button', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'booking' ), $instance['booking'] ); ?>
			</div>
		</div>
		<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>
		<!-- Show free listing -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|product">
			<div class="sl-label">
				<label><?php _e( 'Free listings', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'show_free' ), $instance['show_free'] ); ?>
			</div>
		</div>
	<?php
	}

	/**
	 * Save post that were visited by user
	 */
	public static function posts_visited( )
	{
		if( ! is_single() )
			return;

		if( isset( $_COOKIE['sl_recent_posts'] ) && $_COOKIE['sl_recent_posts'] != '' )
		{
			$posts = explode( ',',$_COOKIE['sl_recent_posts'] );

			if (! is_array($posts))
			{
				// If array is fucked up...just build a new one.
				$posts = array( get_the_ID() );
			}
			else
			{
				// For removing current post in cookie
				$posts = array_diff( $posts, array( get_the_ID() ) );

				// Update cookie with current post
				array_unshift( $posts, get_the_ID() );
			}
		}
		else
		{
			$posts = array( get_the_ID() );
		}

		setcookie( 'sl_recent_posts', implode(',', $posts ), time() + ( DAY_IN_SECONDS * 31 ), '/' );
	}

	/**
	 * Display post list
	 * @param $args
	 *
	 * @return string
	 */
	public static function post_list( $args )
	{
		$args = array_merge( array(
			'number'              => 5,
			'type'                => '',
			'location'            => '',
			'orderby'             => 'post__in',
			'display'             => 'list',
			'columns'             => 1,
			'more_listings'       => 1,
			'more_listings_text'  => __( 'See more listings', '7listings' ),
			'more_listings_style' => 'button',

			'hierarchy'           => 0,       // Priority sorting
			'display_order'       => 'all',

			'container'           => 'aside', // Container tag

			'post__in'            => array()
		), $args );

		$query_args = array(
			'post_type' => $args['post_types'],
		);


		sl_build_query_args( $query_args, $args );

		if ( $args['post__in'] )
		{
			$query_args['post__in'] = $args['post__in'];
		}

		$query_args['orderby'] = 'post__in';

		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$GLOBALS['sl_list_args'] = $args;


		// Sort by priority
		if ( $args['hierarchy'] )
		{
			sl_query_with_priority( $query_args, 'sl_list_callback' );
		}
		else
		{
			$query = new WP_Query( $query_args );
			sl_list_callback( $query, $args['number'] );
			wp_reset_postdata();
		}

		// Get content
		$html = ob_get_clean();

		$class = 'sl-list posts tours';
		$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

		$html = "<{$args['container']} class='$class'>$html</{$args['container']}>";

		return $html;
	}
}