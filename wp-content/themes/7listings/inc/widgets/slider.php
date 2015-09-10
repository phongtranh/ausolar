<?php

/**
 * This class displays slider of listings in the frontend
 */
class Sl_Widget_Slider extends WP_Widget
{
	/**
	 * Default settings for widget
	 * @var array
	 */
	public $default = array(
		'post_type'          => 'accommodation',

		'title'              => '', // All
		'number'             => 5,  // All

		'type'               => '', // ATR, product (uses different types, not taxonomy)

		'feature'            => '', // All

		'location'           => '', // ATR, company

		'hierarchy'          => 0,      // ATR, company
		'display'            => 'all',  // ATR, company
		'orderby'            => 'date', // All. Company uses recent, popular, rating. Post uses recent, popular

		'post_title'         => 1,  // All
		'rating'             => 1,  // All except post
		'price'              => 1,  // ATR, product
		'booking'            => 1,  // ATR
		'excerpt'            => 1,  // All
		'excerpt_length'     => 25, // All

		'star_rating'        => 0,  // Accommodation

		'cat'                => '', // Product, post
		'brand'              => '', // Product
		'cart'               => '', // Product

		'date'               => 1, // Post

		'transition'         => 'fade', // All
		'delay'              => 0,      // All
		'speed'              => 1000,   // All

		'show_free'          => 1, // Product, Accommodation, Rental, Tour
	);

	/**
	 * Constructor
	 *
	 * @return Sl_Widget_Slider
	 */
	public function __construct()
	{
		parent::__construct(
			'sl-slider',
			__( '7 - Slider', '7listings' ),
			array(
				'description' => __( 'Slideshow of posts or listings.', '7listings' ),
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
	public function widget( $args, $instance )
	{
		$instance = array_merge( $this->default, $instance );
		$instance = self::normalize( $instance );
		$html     = '';

		if ( is_single() )
			$instance = array_merge( array( 'post__not_in' => array( get_the_ID() ) ), $instance );

		$class = 'Sl_' . ucfirst( $instance['post_type'] ) . '_Frontend';
		if ( class_exists( $class ) )
			$html = call_user_func( array( $class, 'slider' ), $instance );
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
		$instance              = $old_instance;
		$instance['post_type'] = strip_tags( $new_instance['post_type'] );

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );

		$instance['feature'] = strip_tags( $new_instance[  sl_meta_key( 'tax_feature', $instance['post_type'] ) ] );

		$instance['location'] = strip_tags( $new_instance['location'] );

		$instance['display'] = strip_tags( $new_instance['display'] );

		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );

		$instance['transition'] = strip_tags( $new_instance['transition'] );
		$instance['delay']      = absint( $new_instance['delay'] );
		$instance['speed']      = absint( $new_instance['speed'] );

		// Product
		$instance['brand'] = absint( $new_instance['brand'] );

		// Type
		$instance['type'] = strip_tags( $new_instance['type_' . $instance['post_type']] );

		// Category: post and product
		$instance['cat'] = strip_tags( $new_instance['cat'] );
		if ( 'product' == $instance['post_type'] )
		{
			$instance['cat'] = strip_tags( $new_instance['cat_product'] );
		}

		// Order by: special cases 'post' and 'company'
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		if ( 'post' == $instance['post_type'] )
		{
			$instance['orderby'] = strip_tags( $new_instance['orderby_post'] );
		}
		elseif ( 'company' == $instance['post_type'] )
		{
			$instance['orderby'] = strip_tags( $new_instance['orderby_company'] );
		}

		$checkboxes = array(
			'hierarchy',

			'post_title',
			'price',
			'rating',
			'booking',
			'excerpt',

			// Accommodation
			'star_rating',

			// Product
			'cart',

			// Post
			'date',

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
		<div class="sl-settings toggle-choices<?php echo 1 < count( $post_types ) ? '' : ' hidden'; ?>">
			<div class="sl-label">
				<label><?php _e( 'Post Type', '7listings' ); ?></label>
			</div>
			<div class="sl-input post-type-input">
				<?php
				foreach ( $post_types as $post_type )
				{
					$class = '';
					if ( 'company' == $post_type )
						$class = 'companie';
					elseif ( 'post' == $post_type )
						$class = 'wp-post';

					printf( '<input type="radio" id="%s" name="%s" value="%s" %s><label for="%s" class="%ss icon" title="Select Wordpress %s">%s</label>',
						$post_type,
						$field_post_type,
						$post_type,
						$post_type == $instance['post_type'] ? 'checked="checked"' : '',
						$post_type,
						$class ? $class : $post_type,
						ucfirst( $post_type ),
						ucfirst( $post_type )
					);
				}
				?>
			</div>
		</div>

		<!-- Accommodation type -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation">
			<div class="sl-label">
				<label><?php _e( 'Type', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => sl_meta_key( 'tax_type', 'accommodation' ),
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'type_accommodation' ),
					'selected'        => $instance['type'],
					'id'              => $this->get_field_id( 'type' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Tour type -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="tour">
			<div class="sl-label">
				<label><?php _e( 'Type', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => sl_meta_key( 'tax_type', 'tour' ),
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'type_tour' ),
					'selected'        => $instance['type'],
					'id'              => $this->get_field_id( 'type' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Rental type -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="rental">
			<div class="sl-label">
				<label><?php _e( 'Type', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => sl_meta_key( 'tax_type', 'rental' ),
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'type_rental' ),
					'selected'        => $instance['type'],
					'id'              => $this->get_field_id( 'type' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Product type -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="product">
			<div class="sl-label">
				<label><?php _e( 'Type', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'type_product' ); ?>">
					<?php
					Sl_Form::options( $instance['type'], array(
						'all'          => __( 'All', '7listings' ),
						'featured'     => __( 'Featured', '7listings' ),
						'on-sale'      => __( 'On Sale', '7listings' ),
						'top-rated'    => __( 'Top Rated', '7listings' ),
						'best-sellers' => __( 'Best Sellers', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>

		<!-- Product category -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="product">
			<div class="sl-label">
				<label><?php _e( 'Category', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'product_cat',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'cat_product' ),
					'selected'        => $instance['cat'],
					'id'              => $this->get_field_id( 'cat' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Product brand -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="product">
			<div class="sl-label">
				<label><?php _e( 'Brand', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'brand',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'brand' ),
					'selected'        => $instance['brand'],
					'id'              => $this->get_field_id( 'brand' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Post category -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="posts">
			<div class="sl-label">
				<label><?php _e( 'Category', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'cat' ),
					'selected'        => $instance['cat'],
					'id'              => $this->get_field_id( 'cat' ),
				) );
				?>
			</div>
		</div>
		<!-- Rental Feature -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="rental">
			<div class="sl-label">
				<label>
					<?php _e( 'Feature', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'feature',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'feature' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'feature' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Tour Feature -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="tour">
			<div class="sl-label">
				<label>
					<?php _e( 'Feature', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'features',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'features' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'features' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>

		<!-- Amenities -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation">
			<div class="sl-label">
				<label>
					<?php _e( 'Amenities', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'amenity',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'amenity' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'amenity' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>
		<!--Attraction Feature-->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="attraction">
			<div class="sl-label">
				<label>
					<?php _e( 'Feature', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'attraction_feature',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'attraction_feature' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'attraction_feature' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>
		<!-- Product_tag -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="product_tag">
			<div class="sl-label">
				<label>
					<?php _e( 'Tag', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'product_tag',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'product_tag' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'product_tag' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>
		<!-- Company_service -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="company_service">
			<div class="sl-label">
				<label>
					<?php _e( 'Service', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'company_service',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'company_service' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'company_service' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>
		<!-- Post_tag -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="post_tag">
			<div class="sl-label">
				<label>
					<?php _e( 'Tag', '7listings' ); ?>
				</label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'post_tag',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'post_tag' ),
					'selected'        => $instance['feature'],
					'id'              => $this->get_field_id( 'post_tag' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
				?>
			</div>
		</div>
		<!-- Location -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|company">
			<div class="sl-label">
				<label><?php _e( 'Location', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php
				wp_dropdown_categories( array(
					'show_option_all' => __( 'All', '7listings' ),
					'taxonomy'        => 'location',
					'hide_empty'      => 1,
					'name'            => $this->get_field_name( 'location' ),
					'selected'        => $instance['location'],
					'id'              => $this->get_field_id( 'location' ),
					'orderby'         => 'NAME',
					'order'           => 'ASC',
				) );
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
				<select name="<?php echo $this->get_field_name( 'display' ); ?>">
					<?php
					Sl_Form::options( $instance['display'], array(
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

		<!-- Sort By -->
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation|tour|rental|product">
			<div class="sl-label">
				<label><?php _e( 'Sort By', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'orderby' ); ?>">
					<?php
					Sl_Form::options( $instance['orderby'], array(
						'date'       => __( 'Recent', '7listings' ),
						'views'      => __( 'Popular', '7listings' ),
						'price-asc'  => __( 'Price (low-high)', '7listings' ),
						'price-desc' => __( 'Price (high-low)', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="company">
			<div class="sl-label">
				<label><?php _e( 'Sort By', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'orderby_company' ); ?>">
					<?php
					Sl_Form::options( $instance['orderby'], array(
						'date'   => __( 'Recent', '7listings' ),
						'views'  => __( 'Popular', '7listings' ),
						'rating' => __( 'Rating', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="post">
			<div class="sl-label">
				<label><?php _e( 'Sort By', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'orderby_post' ); ?>">
					<?php
					Sl_Form::options( $instance['orderby'], array(
						'date'  => __( 'Recent', '7listings' ),
						'views' => __( 'Popular', '7listings' ),
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
					<span class="add-on"><?php _e( 'listings', '7listings' ); ?></span>
				</span>
			</div>
		</div>

		<br>

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
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="accommodation">
			<div class="sl-label">
				<label><?php _e( 'Star Rating', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'star_rating' ), $instance['star_rating'] ); ?>
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
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="product">
			<div class="sl-label">
				<label><?php _e( 'Cart', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'cart' ), $instance['cart'] ); ?>
			</div>
		</div>
		<div class="sl-settings" data-name="<?php echo $field_post_type; ?>" data-value="post">
			<div class="sl-label">
				<label><?php _e( 'Date', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<?php Sl_Form::checkbox_general( $this->get_field_name( 'date' ), $instance['date'] ); ?>
			</div>
		</div>

		<?php include THEME_INC . 'widgets/tpl/excerpt.php'; ?>

		<br>

		<!-- Slider configuration -->
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Transition', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<select name="<?php echo $this->get_field_name( 'transition' ); ?>">
					<?php
					Sl_Form::options( $instance['transition'], array(
						'fade'       => __( 'Fade', '7listings' ),
						'scrollHorz' => __( 'Scroll Horizontally', '7listings' ),
					) );
					?>
				</select>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Delay', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="small-text" name="<?php echo $this->get_field_name( 'delay' ); ?>" value="<?php echo $instance['delay']; ?>">
					<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
				</span>
			</div>
		</div>
		<div class="sl-settings">
			<div class="sl-label">
				<label><?php _e( 'Speed', '7listings' ); ?></label>
			</div>
			<div class="sl-input">
				<span class="input-append">
					<input type="number" class="small-text" name="<?php echo $this->get_field_name( 'speed' ); ?>" value="<?php echo $instance['speed']; ?>">
					<span class="add-on"><?php _e( 'ms', '7listings' ); ?></span>
				</span>
			</div>
		</div>

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
	 * Normalize widget instance to remove redundant params and set only needed params
	 *
	 * @param array $instance
	 *
	 * @return array
	 */
	public static function normalize( $instance )
	{
		/**
		 * All params used for multiple post types will have format "$param_$posttype"
		 * So, we detech these params and remove them if needed
		 */
		$params = array(
			'type',
			'cat',
			'orderby'
		);

		foreach ( $params as $param )
		{
			if ( isset( $instance[$param . '_' . $instance['post_type']] ) )
				$instance[$param] = $instance[$param . '_' . $instance['post_type']];
		}

		return $instance;
	}
}
