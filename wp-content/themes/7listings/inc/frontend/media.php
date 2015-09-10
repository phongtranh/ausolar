<?php
// Disable default WordPress inline style
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Resize image on the fly
 *
 * @link http://www.deluxeblogtips.com/2015/01/resize-image-fly-wordpress.html
 *
 * @param  int     $attachment_id Attachment ID
 * @param  int     $width         Width
 * @param  int     $height        Height
 * @param  boolean $crop          Crop or not
 *
 * @return string|bool            URL of resized image, false if error
 */
function sl_resize( $attachment_id, $width, $height, $crop = true )
{
	// Get upload directory info
	$upload_info = wp_upload_dir();
	$upload_dir  = $upload_info['basedir'];
	$upload_url  = $upload_info['baseurl'];
	// Get file path info
	$path      = get_attached_file( $attachment_id );
	$path_info = pathinfo( $path );
	$ext       = $path_info['extension'];
	$rel_path  = str_replace( array( $upload_dir, ".$ext" ), '', $path );
	$suffix    = "{$width}x{$height}";
	$dest_path = "{$upload_dir}{$rel_path}-{$suffix}.{$ext}";
	$url       = "{$upload_url}{$rel_path}-{$suffix}.{$ext}";

	// If file exists: do nothing
	if ( file_exists( $dest_path ) )
	{
		return $url;
	}

	// Generate thumbnail
	if ( image_make_intermediate_size( $path, $width, $height, $crop ) )
	{
		return $url;
	}

	// Fallback to full size
	return "{$upload_url}{$rel_path}.{$ext}";
}

add_action( 'wp_enqueue_scripts', 'sl_enqueue_scripts', 100 );

/**
 * Enqueue theme scripts
 * It's hooked with priority 100 < 100 = priority for styles
 * to make sure CSS is outputted before JS
 *
 * @return void
 */
function sl_enqueue_scripts()
{
	global $resource, $resource_index;

	// Scripts for IE
	wp_enqueue_script( 'html5shiv', '//cdn.jsdelivr.net/html5shiv/3.7.2/html5shiv.min.js', '', '3.7.2' );
	wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'respond', '//cdn.jsdelivr.net/respond/1.4.2/respond.min.js', '', '1.4.2' );
	wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );

	/**
	 * Main theme script, which depends on:
	 * - select2: for listings widget search
	 */
	wp_enqueue_script( 'sl', THEME_JS . 'script.min.js', array( 'select2' ), '', true );

	// Allow modules to add more params to global Sl variable
	$sl_params = apply_filters( 'sl_js_params', array(
		'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
		'currency'         => Sl_Currency::symbol(),
		'cart'             => intval( sl_setting( 'cart' ) ),
		'currencyPosition' => sl_setting( 'currency_position' ),
		'nonceSendEmail'   => wp_create_nonce( 'send-email' ),
	) );

	if ( is_page_template( 'templates/contact.php' ) )
	{
		$sl_params['contact']          = 1;
		$sl_params['nonceContactSend'] = wp_create_nonce( 'contact-send' );
	}

	// Single
	if ( is_single() )
	{
		if ( 'booking' == get_post_type() )
		{
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG )
			{
				wp_enqueue_style( 'sl-booking-report', THEME_LESS . 'admin/booking-report.less' );
			}
			else
			{
				wp_enqueue_style( 'sl-booking-report', THEME_CSS . 'admin/booking-report.css' );
			}
		}
		else
		{
			wp_enqueue_script( 'sl-single', THEME_JS . 'single.js', array( 'jquery-cycle2' ), '', true );
		}
	}

	if ( is_page() )
	{
		wp_enqueue_script( 'sl-page', THEME_JS . 'page.js', array( 'jquery' ), '', true );
	}

	// Booking page
	if ( get_query_var( 'book' ) )
	{
		wp_enqueue_style( 'sl-booking', THEME_LESS . 'booking.less' );

		/**
		 * Dependency of the booking script
		 * For tour and accommodation, we changed the time picker slider to dropdowns
		 * But still use time slider for rental, so we need to enqueue timepicker for
		 * rental only
		 */
		$dependency = 'jquery-ui-datepicker';
		if ( 'rental' == get_post_type() )
		{
			$dependency = 'jquery-ui-timepicker';
		}
		wp_enqueue_script( 'booking', THEME_JS . 'booking-' . get_post_type() . '.min.js', array( $dependency, 'sl', 'mobile-detect' ) );

		// Add booking validation error messages to main script
		$sl_params['bookingErrors'] = array(
			'name'            => __( 'Please enter your name.', '7listings' ),
			'email'           => __( 'Please enter your email.', '7listings' ),
			'invalidEmail'    => __( 'Invalid email address.', '7listings' ),
			'term'            => __( 'You have to agree with the terms and conditions.', '7listings' ),
			'cardName'        => __( 'Please enter card holder\'s name.', '7listings' ),
			'cardNumber'      => __( 'Please enter card number.', '7listings' ),
			'cardExpiryMonth' => __( 'Please select card expiry month.', '7listings' ),
			'cardExpiryYear'  => __( 'Please select card expiry year.', '7listings' ),
			'cardCvn'         => __( 'Please enter card CVN/CVV2.', '7listings' ),
			'payment'         => __( 'Please select a payment option.', '7listings' ),
		);

		// Text for pay button
		$sl_params['bookingTextFree'] = __( 'Book Now', '7listings' );
		$sl_params['bookingText']     = isset( $_GET['cart'] ) ? __( 'Done', '7listings' ) : __( 'Pay Now', '7listings' );

		// Javascript params
		$booking_params                    = $resource;
		$booking_params['post_id']         = get_the_ID();
		$booking_params['nonceAddBooking'] = wp_create_nonce( 'add-booking' );

		if ( 'rental' == get_post_type() )
		{
			$booking_params['unbookable'] = Sl_Rental_Helper::get_unbookable_dates( get_the_ID(), $resource, $resource_index );
		}
		elseif ( 'tour' == get_post_type() )
		{
			$booking_params['unbookable'] = Sl_Tour_Helper::get_unbookable_dates( get_the_ID(), $resource, $resource_index );
		}

		wp_localize_script( 'booking', 'Resource', $booking_params );
	}

	// Custom CSS of child theme
	if ( is_child_theme() )
	{
		if ( file_exists( CHILD_DIR . 'custom.css' ) )
		{
			wp_enqueue_style( 'sl-child-custom', CHILD_URL . 'custom.css' );
		}
		if ( file_exists( CHILD_DIR . 'css/custom.css' ) )
		{
			wp_enqueue_style( 'sl-child-custom', CHILD_URL . 'css/custom.css' );
		}
	}

	// Comment reply
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() && have_comments() )
	{
		wp_enqueue_script( 'comment-reply' );
	}

	// Add params to main script
	wp_localize_script( 'sl', 'Sl', $sl_params );
}

add_action( 'wp_enqueue_scripts', 'sl_enqueue_styles', 200 );

/**
 * Enqueue theme styles
 * It's hooked with priority 200 > 100 = priority for scripts
 * to make sure CSS is outputted before JS
 *
 * @return void
 */
function sl_enqueue_styles()
{
	$types = array(
		'7Pro'           => '7pro',
		'7Tours'         => '7tour',
		'7Accommodation' => '7accommodation',
		'7Rental'        => '7rental',
		'7Products'      => '7product',
		'7Comp'          => '7company',
		'Basic'          => '7basic',
		'7Network'       => '7network',
	);

	wp_enqueue_style( 'sl-main', THEME_LESS . $types[Sl_License::license_type()] . '.less' );

	// Styles for IE
	wp_enqueue_style( 'sl-ie', THEME_CSS . 'ie.css' );
	wp_style_add_data( 'sl-ie', 'conditional', 'lt IE 9' );
}

// Priority 1000 means running very late
add_action( 'wp_footer', 'sl_js_map_footer', 1000 );

/**
 * Display custom js map
 * It's hooked to 'wp_footer' to make sure it runs very late
 *
 * @return void
 */
function sl_js_map_footer()
{
	if ( empty( $GLOBALS['sl_map_js'] ) )
	{
		return;
	}
	$js = $GLOBALS['sl_map_js'];
	$js = defined( 'WP_DEBUG' ) && WP_DEBUG ? $js : sl_js_minify( $js );
	echo '<script>jQuery(function($){' . $js . '})</script>';
}

add_filter( 'script_loader_src', 'sl_unversion' );
add_filter( 'style_loader_src', 'sl_unversion' );

/**
 * Remove version for scripts and styles
 *
 * @param string $src
 *
 * @return string
 */
function sl_unversion( $src )
{
	return sl_setting( 'css_no_var' ) ? $src : remove_query_arg( 'ver', $src );
}

add_filter( 'post_thumbnail_html', 'sl_thumbnail_html', 10, 5 );

/**
 * Show default placeholder when no thumbnails
 * And wrap into div.thumbnail
 *
 * @param string $html              HTML markup for thumbnail
 * @param int    $post_id           Post ID
 * @param int    $post_thumbnail_id Thumbnail ID
 * @param string $size              Thumbnail size
 * @param array  $attr              Attributes
 *
 * @return string
 */
function sl_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr )
{
	// Use default thumbnail as fallback
	if ( ! $post_thumbnail_id )
	{
		$html = sl_image_placeholder( $size );
	}

	return '<figure class="thumbnail">' . $html . '</figure>';
}

/**
 * Get HTML for image placeholder
 * Image placholder is set in Settings \ Media page, if no image is set then use image placeholders in folder images/graphics/
 * Note: Default placeholders have suffix $width x $height
 *
 * @param string $size Image size
 * @param string $type Return type HTML or image source
 *
 * @return string
 */
function sl_image_placeholder( $size, $type = 'html' )
{
	if ( sl_setting( 'image_placeholder' ) )
	{
		list( $src ) = wp_get_attachment_image_src( sl_setting( 'image_placeholder' ), $size );
	}
	else
	{
		global $_wp_additional_image_sizes;
		$file = $default = 'noImage.png';
		if ( isset( $_wp_additional_image_sizes[$size] ) )
		{
			$info = $_wp_additional_image_sizes[$size];
			$file = "noImage-{$info['width']}x{$info['height']}.png";
			if ( ! file_exists( THEME_DIR . 'images/graphics/' . $file ) )
			{
				$file = $default;
			}
		}
		$src = THEME_IMG . 'graphics/' . $file;
	}

	return 'html' == $type ? '<img class="photo" src="' . $src . '" alt="no-image">' : $src;
}

add_filter( 'wp_get_attachment_image_attributes', 'sl_remove_wp_image_classes', 20, 2 );

/**
 * Remove WP image classes
 *
 * @param array $attr Attributes
 * @param       $attachment
 *
 * @return array
 */
function sl_remove_wp_image_classes( $attr, $attachment )
{
	// Change for places that not in post content
	if ( ! is_admin() )
	{
		$attr['class'] = 'photo';
	}

	return $attr;
}

/**
 * Display video
 *
 * @param int|string $video
 * @param int        $width
 *
 * @param int        $height
 *
 * @return void
 */
function sl_video( $video, $width = 400, $height = 300 )
{
	if ( ! $video )
	{
		return;
	}

	// Uploaded video
	if ( is_numeric( $video ) )
	{
		$video = wp_get_attachment_link( $video );
	}

	$output = wp_oembed_get( $video, array( 'width' => $width, 'height' => $height ) );
	if ( ! $output )
	{
		$output = do_shortcode( '[video width="' . $width . '" height="' . $height . '" src="' . $video . '"]' );
	}

	echo $output;
}
