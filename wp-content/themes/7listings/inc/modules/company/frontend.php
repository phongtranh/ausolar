<?php

/**
 * Company Frontend modules
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */
class Sl_Company_Frontend extends Sl_Core_Frontend
{
	public $special_pages;

	/**
	 * Constructor
	 *
	 * @param string $post_type Post type
	 *
	 * @return Sl_Company_Frontend
	 */
	function __construct( $post_type )
	{
		parent::__construct( $post_type );

		$this->special_pages = apply_filters( 'company_special_pages', array(
			'signup'    => 'signup',
			'edit'      => 'user-admin/company',
			'dashboard' => 'user-admin/dashboard',
			'profile'   => 'user-admin/profile',
			'posts'     => 'user-admin/posts',
			'account'   => 'user-admin/account',
		) );

		add_filter( 'excerpt_length', array( $this, 'excerpt_length' ) );
		add_action( 'comment_post', array( $this, 'add_comment_rating' ), 1 );

		add_filter( 'sl_post_after_post_meta', array( $this, 'written_by' ) );

		add_filter( 'comments_template', array( $this, 'comments_template' ), 30 );

		add_action( 'template_redirect', array( $this, 'process_special_pages' ) );
		add_action( 'company_special_page_after_login', array( $this, 'process_edit' ) );
		add_action( 'company_special_page_after_login', array( $this, 'process_edit_profile' ) );
		add_action( 'company_special_page_after_login', array( $this, 'process_add_post' ) );
		add_action( 'company_special_page_after_login', array( $this, 'process_edit_account' ) );

		add_filter( 'the_content', array( $this, 'special_pages_content' ) );

		add_filter( 'show_admin_bar', array( $this, 'no_admin_bar' ) );

		add_action( "sl_singular-{$post_type}_schema_item_type", array( $this, 'schema_item_type' ) );

		// Notifications
		add_action( 'template_redirect', array( $this, 'add_notifications' ) );

		add_action( 'template_redirect', array( $this, 'show_homepage_modules' ) );

		// Special meta title for company archive page starting with a letter
		add_filter( 'sl_meta_title_special_pages', array( $this, 'meta_title_special_pages' ) );

		add_filter( 'post_class', array( $this, 'post_class' ) );

		add_filter( 'sl_listing_element', array( __CLASS__, 'average_rating' ), 10, 3 );
	}

	/**
	 * Helper function to check if is a page in settings
	 *
	 * @param  string $name
	 *
	 * @return boolean
	 * @since 4.12.1
	 */
	function is_page( $name = '' )
	{
		return is_page() && get_the_ID() == sl_setting( "{$this->post_type}_page_{$name}" );
	}

	/**
	 * Enqueue scripts for homepage
	 *
	 * @return array
	 * @since  4.12
	 */
	function enqueue_scripts()
	{
		// Archive
		if ( $this->post_type == sl_is_listing_archive() )
		{
			wp_enqueue_script( 'jquery-jsort', sl_locate_url( 'js/libs/jquery-jsort.min.js' ), array( 'jquery' ), '0.4', true );
			wp_enqueue_script( "{$this->post_type}-archive", sl_locate_url( "js/{$this->post_type}-archive.js" ), array( 'jquery-jsort' ), '', true );
		}

		// Single
		elseif ( is_singular( $this->post_type ) )
		{
			wp_enqueue_script( "sl-{$this->post_type}-radius", sl_locate_url( "js/{$this->post_type}-radius.js" ), array( 'jquery' ), '', false );
			wp_localize_script( "sl-{$this->post_type}-radius", 'SlCompanyRadius', array(
				'radius'        => get_post_meta( get_the_ID(), 'leads_service_radius', true ),
				'strokeColor'   => sl_setting( 'design_map_stroke_color' ),
				'strokeOpacity' => sl_setting( 'design_map_stroke_opacity' ) / 100,
				'fillColor'     => sl_setting( 'design_map_fill_color' ),
				'fillOpacity'   => sl_setting( 'design_map_fill_opacity' ) / 100,
			) );
		}

		// Front page
		elseif ( is_front_page() && 'posts' == get_option( 'show_on_front' ) && sl_setting( 'homepage_company_logos_active' ) )
		{
			wp_enqueue_script( 'jquery-lemmon-slider' );
			add_action( 'wp_footer', array( $this, 'home_script_footer' ), 1000 );
		}

		// Dashboard
		elseif ( $this->is_page( 'dashboard' ) )
		{
			wp_enqueue_script( 'sl-single', THEME_JS . 'single.js', array( 'jquery-cycle2' ), '', true );

			// Get company
			$views   = array();
			$company = get_posts( array(
				'post_type'      => 'company',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => 'user',
				'meta_value'     => get_current_user_id(),
			) );

			if ( ! empty( $company ) )
			{
				$company = current( $company );

				$views = get_post_meta( $company->ID, 'date_views', true );
				if ( empty( $views ) || ! is_array( $views ) )
					$views = array();

				// Add last 30 days if they're not present
				$past_day = time() - 29 * 86400;
				for ( $i = 29; $i; $i -- )
				{
					$key = 'views_' . date( 'Y_m_d', $past_day );
					if ( ! isset( $views[$key] ) )
						$views[$key] = 0;

					$past_day += 86400;
				}

				// Get last 30 days only, preserve keys
				if ( 30 < count( $views ) )
					$views = array_slice( $views, - 30, 30, true );
				ksort( $views );
			}

			wp_enqueue_script( "sl-{$this->post_type}-dashboard", sl_locate_url( "js/{$this->post_type}-dashboard.js" ), array( 'jquery', 'google-js-api', 'jquery-ui-autocomplete' ), '', true );
			wp_localize_script( "sl-{$this->post_type}-dashboard", 'SlCompany', array(
				'views' => $views,
			) );
		}

		// Signup
		elseif ( $this->is_page( 'signup' ) )
		{
			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_script( "sl-{$this->post_type}-signup", sl_locate_url( "js/{$this->post_type}-signup.js" ), array( 'sl-location-autocomplete' ), '', true );
			wp_localize_script( "sl-{$this->post_type}-signup", 'SlCompany', array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'signup' ),
				'nonceSave' => wp_create_nonce( "save-post-{$this->post_type}" ),
			) );

			wp_enqueue_script( 'sl-highlight', sl_locate_url( 'js/highlight.js' ), array( 'jquery' ), '', true );
		}

		// Edit
		elseif ( $this->is_page( 'edit' ) )
		{
			wp_enqueue_style( 'jquery-ui' );
			wp_enqueue_style( 'jquery-ui-timepicker' );
			wp_enqueue_script( 'sl-location-autocomplete' );
			wp_enqueue_script( 'sl-highlight', sl_locate_url( 'js/highlight.js' ), array( 'jquery' ), '', true );

			$company = get_posts( array(
				'post_type'      => 'company',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'meta_key'       => 'user',
				'meta_value'     => get_current_user_id(),
			) );

			if ( ! empty( $company ) )
			{
				$company = current( $company );

				wp_enqueue_script( "sl-{$this->post_type}-radius", sl_locate_url( "js/{$this->post_type}-radius.js" ), array( 'jquery' ), '', false );
				wp_localize_script( "sl-{$this->post_type}-radius", 'SlCompanyRadius', array(
					'radius'        => get_post_meta( $company->ID, 'leads_service_radius', true ),
					'strokeColor'   => sl_setting( 'design_map_stroke_color' ),
					'strokeOpacity' => sl_setting( 'design_map_stroke_opacity' ) / 100,
					'fillColor'     => sl_setting( 'design_map_fill_color' ),
					'fillOpacity'   => sl_setting( 'design_map_fill_opacity' ) / 100,
				) );
			}

			wp_enqueue_script( "sl-{$this->post_type}-edit", sl_locate_url( "js/{$this->post_type}-edit.js" ), array( 'jquery-ui-timepicker' ), '', true );
		}

		// Account
		elseif ( $this->is_page( 'account' ) )
		{
			wp_enqueue_script( "sl-{$this->post_type}-account", sl_locate_url( "js/{$this->post_type}-account.js" ), array( 'jquery' ), '', true );
			wp_localize_script( "sl-{$this->post_type}-account", 'SlCompanyAccount', array(
				'price_bronze_month' => sl_setting( "{$this->post_type}_membership_price_bronze" ),
				'price_silver_month' => sl_setting( "{$this->post_type}_membership_price_silver" ),
				'price_gold_month'   => sl_setting( "{$this->post_type}_membership_price_gold" ),
				'price_bronze_year'  => sl_setting( "{$this->post_type}_membership_price_year_bronze" ),
				'price_silver_year'  => sl_setting( "{$this->post_type}_membership_price_year_silver" ),
				'price_gold_year'    => sl_setting( "{$this->post_type}_membership_price_year_gold" ),
				'free'               => __( 'Free', '7listings' ),
				'currency'           => sl_setting( 'currency' ),
			) );

			wp_enqueue_script( "sl-{$this->post_type}-payment", sl_locate_url( "js/{$this->post_type}-payment.js" ), array( 'jquery' ), '', true );
			wp_localize_script( "sl-{$this->post_type}-payment", 'SlCompanyPayment', array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonceRenew'   => wp_create_nonce( 'renew' ),
				'nonceUpgrade' => wp_create_nonce( 'upgrade' ),
				'noncePay'     => wp_create_nonce( 'pay' ),
			) );
		}

		// Profile
		elseif ( $this->is_page( 'profile' ) )
		{
			wp_enqueue_script( 'sl-highlight', sl_locate_url( 'js/highlight.js' ), array( 'jquery' ), '', true );
		}
	}

	/**
	 * Change excerpt length
	 *
	 * @param int $length Excerpt length
	 *
	 * @return int New excerpt length
	 */
	function excerpt_length( $length )
	{
		if ( $this->post_type == get_post_type() )
			$length = 75;

		return $length;
	}

	/**
	 * Rating field for comments
	 *
	 * @param int $comment_id
	 *
	 * @return void
	 **/
	function add_comment_rating( $comment_id )
	{
		global $post;
		if ( $this->post_type != $post->post_type )
			return;

		$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
		foreach ( $names as $name )
		{
			if ( isset( $_POST[$name] ) )
			{
				$rate = intval( $_POST[$name] );
				$rate = ( $rate > 5 || $rate < 0 ) ? 0 : $rate;
				update_comment_meta( $comment_id, $name, $rate );
			}
		}
	}

	/**
	 * Review comments template
	 *
	 * @param       $comment
	 * @param array $args
	 * @param int   $depth
	 *
	 * @return void
	 */
	static function comments( $comment, $args, $depth )
	{
		$GLOBALS['comment'] = $comment;

		$names = array( 'rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price' );
		$total = 0;
		foreach ( $names as $name )
		{
			$rating = get_comment_meta( $comment->comment_ID, $name, true );
			$total += (int) $rating;
		}
		$average = $total / 6;
		?>
		<li itemprop="review" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<article id="comment-<?php comment_ID(); ?>" class="post comment">
			<span style="display:none" itemprop="itemReviewed"><?php the_title(); ?></span>

			<?php echo sl_avatar( $comment, 80 ); ?>

			<div class="details">
				<h4 class="author" itemprop="author"><?php comment_author(); ?></h4>

				<?php sl_star_rating( $average, 'type=rating' ); ?>

				<time class="entry-meta date" itemprop="datePublished" datetime="<?php comment_date( 'c' ); ?>"><?php comment_date(); ?></time>

				<?php
				if ( $comment->comment_approved == '0' )
					echo '<p class="pending"><em>' . __( 'Your review is awaiting approval', '7listings' ) . '</em></p>';
				?>
				<p class="entry-content" itemprop="text"><?php echo strip_tags( get_comment_text(), '<br><a><strong><em><b><i>' ); ?></p>

				<a class="comment-reply-link" href="#" data-comment_id="<?php echo $comment->comment_ID; ?>"><?php _e( 'Reply', '7listings' ); ?></a>
			</div>
		</article>
	<?php
	}

	/**
	 * Breadcrumb item for single page
	 *
	 * @param  string $item
	 * @param  string $tpl
	 * @param  string $post_type
	 *
	 * @return string
	 */
	function breadcrumbs( $item, $tpl = '', $post_type = '' )
	{
		if ( current_filter() == 'sl_breadcrumbs_general_text' && get_query_var( "sl_{$this->post_type}" ) == 'new' )
			return sprintf( __( 'Add New %s', '7listings' ), sl_setting( $this->post_type . '_label' ) );

		if ( $post_type != $this->post_type )
			return $item;

		$link = get_post_type_archive_link( $post_type );
		if ( strpos( $link, '?' ) )
			$link = home_url( sl_setting( $post_type . '_base_url' ) . '/' );

		switch ( current_filter() )
		{
			case 'sl_breadcrumbs_single':
			case 'sl_breadcrumbs_tax':
				return sprintf( $tpl, $link, __( 'Companies', '7listings' ) );
			case 'sl_breadcrumbs_post_type_archive':
				$text             = __( 'Companies', '7listings' );
				$current_location = get_query_var( 'location' );
				if ( $current_location )
				{
					$current_location = get_term_by( 'slug', $current_location, 'location' );
					if ( ! empty( $current_location ) && ! is_wp_error( $current_location ) )
						$text = sprintf( __( 'Companies in %s', '7listings' ), $current_location->name );
				}

				return sprintf( $tpl, $text );
			default:
				return '';
		}
	}

	/**
	 * Get rating of current post
	 *
	 * @param string $type Rating type
	 *
	 * @return float
	 */
	static function get_rating( $type = 'rating_sales' )
	{
		global $wpdb, $post;

		$rating = 0;

		$result = $wpdb->get_row( $wpdb->prepare( "
			SELECT COUNT(*) AS count, SUM(meta_value) AS total
			FROM $wpdb->commentmeta as m
			LEFT JOIN $wpdb->comments as c ON m.comment_id = c.comment_ID
			WHERE
				meta_key = '%s' AND
				comment_post_ID = %d AND
				comment_approved = 1
			", $type, $post->ID ), 'ARRAY_A' );

		if ( ! empty( $result ) && 0 != $result['count'] )
			$rating = number_format( ( float ) $result['total'] / $result['count'], 2 );

		return $rating;
	}

	/**
	 * Filter posts by alphabet
	 *
	 * @param string $where
	 *
	 * @return string
	 */
	static function filter_by_alphabet( $where )
	{
		if ( ! isset( $_GET['start'] ) || ! preg_match( '#^[a-z]$#', $_GET['start'] ) )
			return $where;

		$start = $_GET['start'];
		$where .= " AND LOWER(SUBSTR(post_title,1,1)) = '$start' ";

		return $where;
	}

	/**
	 * Add written by company to post meta
	 *
	 * @param  string $by
	 *
	 * @return string
	 */
	function written_by( $by )
	{
		if ( ! ( $company = get_post_meta( get_the_ID(), 'company', true ) ) )
			return '';

		return sprintf(
			'<div class="entry-meta written-by">%s <a href="%s">%s</a></div>',
			__( 'Written By', '7listings' ),
			get_permalink( $company ),
			get_the_title( $company )
		);
	}

	/**
	 * Get the reviews template (comments)
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	function comments_template( $template )
	{
		global $sl_is_company_account_page;
		if ( $sl_is_company_account_page === true )
			$template = locate_template( "templates/{$this->post_type}/user-admin/dashboard-reviews.php" );

		return $template;
	}

	/**
	 * Special pages for company
	 *
	 * @param string $content
	 *
	 * @return string
	 * @since  4.12
	 */
	function special_pages_content( $content )
	{
		foreach ( $this->special_pages as $k => $v )
		{
			if ( $this->is_page( $k ) )
			{
				ob_start();

				do_action( 'company_special_pages_content_before' );
				do_action( 'sl_notification', 'all' );

				get_template_part( "templates/{$this->post_type}/$v" );
				$content .= ob_get_clean();

				do_action( 'company_special_pages_content_after' );

				return $content;
			}
		}

		return $content;
	}

	/**
	 * Process login
	 *
	 * @return string
	 * @since  4.12
	 */
	function process_login()
	{
		if ( empty( $_POST['submit_login'] ) )
			return;

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'login' ) )
			die( __( 'Form is not properly submitted. Please try again!', '7listings' ) );

		global $errors;
		if ( ! is_array( $errors ) )
			$errors = array();

		if ( empty( $_POST['username'] ) || empty( $_POST['password'] ) )
		{
			$errors[] = __( 'Please enter username and password', '7listings' );

			return;
		}

		$user = wp_signon( array(
			'user_login'    => $_POST['username'],
			'user_password' => $_POST['password'],
			'remember'      => true,
		), false );
		if ( is_wp_error( $user ) )
		{
			$errors[] = __( 'Username and password do not match. Please try again.', '7listings' );

			return;
		}
		wp_redirect( add_query_arg( 'logged-in', 1 ) );
	}

	/**
	 * Check if we need to process special pages for company owner
	 * Check login first, then process forms
	 *
	 * @return void
	 */
	function process_special_pages()
	{
		$pages = $this->special_pages;

		// Add custom body class for signup page, because this page is not process further
		if ( $this->is_page( 'signup' ) )
			add_filter( 'body_class', array( $this, 'body_class' ) );

		// Do not process login for signup page
		unset( $pages['signup'] );
		$is_page = is_page_template( 'templates/company-admin.php' ); // Special page template
		foreach ( $pages as $k => $v )
		{
			if ( $this->is_page( $k ) )
			{
				$is_page = true;
				break;
			}
		}
		if ( ! $is_page )
			return;

		// Add custom body class
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// No social buttons
		add_filter( 'sl_social_buttons', '__return_false' );

		// If is special page, force user to login
		if ( ! is_user_logged_in() )
		{
			$this->process_login();

			return;
		}

		do_action( 'company_special_page_after_login' );
	}

	/**
	 * Process edit company
	 *
	 * @return string
	 * @since  4.12
	 */
	function process_edit()
	{
		if ( ! $this->is_page( 'edit' ) || empty( $_POST['submit'] ) )
			return;

		global $errors;
		if ( ! is_array( $errors ) )
			$errors = array();

		if (
			empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'edit-company' )
			|| empty( $_POST['post_id'] )
		)
			die( __( 'Form is not properly submitted. Please try again!', '7listings' ) );

		// WordPress Administration File API
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Required fields
		$required = array(
			'post_content' => __( 'Please enter company description.', '7listings' ),
		);
		foreach ( $required as $k => $v )
		{
			if ( empty( $_POST[$k] ) )
				$errors[] = $v;
		}

		if ( ! empty( $errors ) )
			return;

		// Update company
		$post_data = array(
			'ID'           => $_POST['post_id'],
			'post_content' => $_POST['post_content'],
		);
		$post_id   = wp_update_post( $post_data );
		if ( ! $post_id )
		{
			$errors[] = __( 'Error while updating company.', '7listings' );

			return;
		}

		// Country
		if ( isset( $_POST['country'] ) )
			update_post_meta( $post_id, 'country', $_POST['country'] );

		// Company products, brand
		$taxonomies = array(
			'products' => 'company_product',
			'services' => 'company_service',
			'brands'   => 'brand',
		);
		foreach ( $taxonomies as $k => $v )
		{
			$terms = isset( $_POST[$k] ) ? $_POST[$k] : array();
			wp_set_post_terms( $post_id, $terms, $v, false );
		}

		wp_redirect( add_query_arg( 'updated', 1 ) );
	}

	/**
	 * Process edit user
	 *
	 * @return string
	 * @since  4.12
	 */
	function process_edit_profile()
	{
		if ( ! $this->is_page( 'profile' ) || empty( $_POST['submit'] ) )
			return;

		global $errors;
		if ( ! is_array( $errors ) )
			$errors = array();

		if (
			empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'edit-user' )
		)
			die( __( 'Form is not properly submitted. Please try again!', '7listings' ) );

		if ( ! empty( $_POST['user_pass'] ) )
		{
			if ( empty( $_POST['password_confirm'] ) || $_POST['password_confirm'] != $_POST['user_pass'] )
			{
				$errors[] = __( 'Please enter same confirm password', '7listings' );

				return;
			}
		}
		else
		{
			unset( $_POST['user_pass'] );
		}

		do_action( 'company_edit_profile_before' );

		$user_data = $_POST;
		unset( $user_data['_wpnonce'], $user_data['_wp_http_referer'], $user_data['password_confirm'] );
		$user_data['ID'] = get_current_user_id();

		wp_update_user( $user_data );

		do_action( 'company_edit_profile_after' );

		wp_redirect( add_query_arg( 'updated', 1 ) );
	}

	/**
	 * Process add new company
	 *
	 * @return string
	 * @since  4.12
	 */
	function process_add_post()
	{
		if ( ! $this->is_page( 'posts' ) || empty( $_POST['submit'] ) )
			return;

		global $errors;
		if ( ! is_array( $errors ) )
			$errors = array();

		if (
			empty( $_POST['_wpnonce'] )
			|| ! wp_verify_nonce( $_POST['_wpnonce'], 'add-company-post' )
		)
			die( __( 'Form is not properly submitted. Please try again!', '7listings' ) );

		// Required fields
		$required = array(
			'post_title'   => __( 'Please enter post title.', '7listings' ),
			'post_content' => __( 'Please enter post content.', '7listings' ),
		);
		foreach ( $required as $k => $v )
		{
			if ( empty( $_POST[$k] ) )
				$errors[] = $v;
		}

		if ( ! empty( $errors ) )
			return;

		$user_id = get_current_user_id();

		// WordPress Administration File API
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Insert company
		$post_data = array(
			'post_title'   => $_POST['post_title'],
			'post_content' => $_POST['post_content'],
			'post_status'  => 'pending',
			'post_type'    => 'post',
			'post_author'  => $user_id,
		);
		$post_id   = wp_insert_post( $post_data );
		if ( is_wp_error( $post_id ) || ! $post_id )
		{
			$errors[] = __( 'Error while creating new company.', '7listings' );

			return;
		}

		// Add user, company
		update_post_meta( $post_id, 'user', $user_id );
		update_post_meta( $post_id, 'company', $_POST['company_id'] );

		// Save featured image
		$thumbnail = peace_handle_upload( 'thumbnail', false, $post_id );
		if ( $thumbnail )
			update_post_meta( $post_id, '_thumbnail_id', $thumbnail );

		// Send emails
		$subject = sprintf( __( 'New article has posted for %s', '7listings' ), $_POST['post_title'] );
		$message = sprintf(
			__( 'New article has posted for <b>%s</b>. <a href="%s">Click here</a> to view the article.', '7listings' ),
			$_POST['post_title'],
			get_permalink( $post_id )
		);

		// To company owner
		$user = get_userdata( $user_id );
		$ok   = wp_mail( $user->user_email, $subject, $message );
		if ( ! $ok )
		{
			$errors[] = __( 'Error while sending email notification.', '7listings' );

			return;
		}

		// To admin
		$to = get_bloginfo( 'admin_email' );
		$ok = wp_mail( $to, $subject, $message );
		if ( ! $ok )
		{
			$errors[] = __( 'Error while sending email notification to admin.', '7listings' );

			return;
		}

		wp_redirect( add_query_arg( 'updated', 1 ) );
	}

	/**
	 * Process edit account
	 *
	 * @return string
	 * @since  4.12
	 */
	function process_edit_account()
	{
		if ( ! $this->is_page( 'account' ) )
			return;

		$user_id = get_current_user_id();
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
			return;

		$company = current( $company );

		// Change invoice
		if ( ! empty( $_POST['submit_invoice'] ) )
		{
			$fields = array( 'invoice_name', 'invoice_email', 'invoice_phone', 'paypal_email' );
			$old    = array();
			$new    = array();
			foreach ( $fields as $field )
			{
				$old[$field] = get_post_meta( $company->ID, $field, true );
				$new[$field] = empty( $_POST[$field] ) ? '' : $_POST[$field];

				if ( $new[$field] )
					update_post_meta( $company->ID, $field, $new[$field] );
			}

			do_action( 'company_account_invoice_recipient', $user_id, $company, $old, $new );
		}

		// Close account
		if ( ! empty( $_POST['submit_close'] ) )
		{
			/**
			 * Don't set company status to 'pending'
			 * @since 5.0.5
			 */
			//			wp_update_post( array(
			//				'ID'          => $company->ID,
			//				'post_status' => 'pending',
			//			) );
			delete_user_meta( $user_id, 'membership' );

			// Remove his ownership of company
			delete_post_meta( $company->ID, 'user' );

			do_action( 'company_account_close', $user_id, $company );
		}

		// Change account
		if ( ! empty( $_POST['submit_account'] ) )
		{			
			$prev = get_user_meta( $user_id, 'membership', true );
			$type = isset( $_POST['membership'] ) ? $_POST['membership'] : '';

			$prev_time = get_user_meta( $user_id, 'membership_time', true );
			$time      = isset( $_POST['time'] ) ? $_POST['time'] : 'month';

			if ( $prev == $type && $prev_time == $time )
				return;

			do_action( 'company_account_change', $user_id, $company, $type, $prev, $time, $prev_time );

			// Redirect to Paypal
			if ( 'month' == $time )
				$amount = sl_setting( "company_membership_price_{$type}" );
			else
				$amount = sl_setting( "company_membership_price_year_{$type}" );

			$time = 'month' == $time ? __( 'Monthly', '7listings' ) : __( 'Yearly', '7listings' );
			
			$html = '';
			if ( $type !== 'bronze' ) :
				$html = Sl_Payment::paypal_form( 'upgrade-form', 'membership_upgrade_paypal', array(
					'item_name' => ucwords( sprintf( __( '%s Membership Payment (%s)', '7listings' ), $type, $time ) ),
					'custom'    => "$user_id,$type,$time",
					'amount'    => intval( $amount ),
					'return'    => get_permalink( sl_setting( 'company_page_dashboard' ) ),
				) );

				echo "
				<html>
				<body>
				<div style='display:none'>$html</div>
				<script>document.getElementById('upgrade-form').submit();</script>
				</body>
				</html>
				";
				die;
			endif;
		}
	}

	/**
	 * Change meta title
	 *
	 * @param string $title
	 * @param string $sep
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	function meta_title( $title = '', $sep = '' )
	{
		$title = parent::meta_title( $title, $sep );

		if ( is_post_type_archive( $this->post_type ) && get_query_var( 's' ) )
			$title = sprintf( __( 'Search results for: <strong>%s</strong>', '7listings' ), get_search_query() );

		if ( is_post_type_archive( $this->post_type ) && isset( $_GET['start'] ) )
			$title = sprintf( __( 'Companies starting with: <strong>%s</strong>', '7listings' ), ucfirst( $_GET['start'] ) );

		$action = get_query_var( "sl_{$this->post_type}" );
		if ( 'new' == $action )
			$title = __( 'Add New Company', '7listings' );

		// View companies in a state or city
		$tax = 'location';
		if ( $this->post_type == get_query_var( 'post_type' ) && ( $term = get_query_var( $tax ) ) )
		{
			$term = get_term_by( 'slug', $term, $tax );
			if ( empty( $term ) || is_wp_error( $term ) )
				return $title;

			$title = str_replace( '%TERM%', $term->name, sl_setting( "{$this->post_type}_{$tax}_title" ) );
		}

		$tax = 'company_service';
		if ( is_tax( $tax ) )
		{
			$term  = get_queried_object();
			$title = str_replace( '%TERM%', $term->name, sl_setting( "{$this->post_type}_service_title" ) );
		}

		$tax = 'company_product';
		if ( is_tax( $tax ) )
		{
			$term  = get_queried_object();
			$title = str_replace( '%TERM%', $term->name, sl_setting( "{$this->post_type}_product_title" ) );
		}

		return $title;
	}

	/**
	 * Change meta title for company archive page starting with a letter
	 *
	 * @param string $title
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	function meta_title_special_pages( $title )
	{
		if ( ! is_post_type_archive( $this->post_type ) || ! isset( $_GET['start'] ) )
			return $title;

		$title = sprintf( __( 'Companies starting with: <strong>%s</strong>', '7listings' ), ucfirst( $_GET['start'] ) );

		return $title;
	}

	/**
	 * Display slider
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	static function slider( $args )
	{
		$args = array_merge( array(
			'title'      => '',
			'number'     => 5,
			'location'   => '',

			'hierarchy'  => 0,     // Priority sorting
			'display'    => 'all',

			'orderby'    => 'date',
			'transition' => 'fade',
			'delay'      => 0,
			'speed'      => 1000,

			'container'  => 'div', // Container tag
		), $args );

		$query_args = array(
			'post_type' => 'company',
		);
		sl_build_query_args( $query_args, $args );

		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$args['class']           = 'slide';
		$args['image_size']      = 'sl_pano_medium';
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

		wp_enqueue_script( 'jquery-cycle2' );

		return sprintf(
			'<%s class="sl-list posts companies cycle-slideshow" data-cycle-slides="> article" data-cycle-fx="%s" data-cycle-delay="%s" data-cycle-speed="%s">%s</%s>',
			$args['container'],
			$args['transition'], $args['delay'], $args['speed'], $html,
			$args['container']
		);
	}

	/**
	 * Display post list
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	static function post_list( $args )
	{
		$args = array_merge( array(
			'title'               => '',
			'number'              => 5,
			'location'            => '',
			'orderby'             => 'date',
			'display'             => 'list',
			'columns'             => 1,
			'more_listings'       => 1,
			'more_listings_text'  => __( 'See more listings', '7listings' ),
			'more_listings_style' => 'button',

			'hierarchy'           => 0,       // Priority sorting
			'display_order'       => 'all',

			'container'           => 'aside', // Container tag
		), $args );

		$query_args = array(
			'post_type' => 'company',
		);

		// We build custom query args for 'orderby' rating
		$orderby = $args['orderby'];
		unset( $args['orderby'] );
		sl_build_query_args( $query_args, $args );

		switch ( $orderby )
		{
			case 'views':
				$query_args['orderby']  = 'meta_value_num';
				$query_args['meta_key'] = 'views';
				break;
			case 'rating':
				global $wpdb;
				$result                 = $wpdb->get_results( $wpdb->prepare( "
					SELECT SUM(cm.meta_value) / COUNT(cm.meta_value) AS average_rating, c.comment_post_ID AS post_id
					FROM $wpdb->commentmeta AS cm, $wpdb->comments AS c
					WHERE
						cm.comment_id = c.comment_ID
						AND meta_key IN ('rating_sales', 'rating_service', 'rating_installation', 'rating_quality', 'rating_timelyness', 'rating_price')
						AND comment_approved = '1'
					GROUP BY post_id
					ORDER BY average_rating DESC
					LIMIT %d
				", $args['number'] ) );
				$query_args['post__in'] = wp_list_pluck( $result, 'post_id' );
				break;
		}

		// Use output buffering to get the content by callback function
		// Because we use `sl_query_with_priority()` that doesn't return the output
		ob_start();

		// Use global variable to share argument between `sl_query_with_priority()` and callback function
		$args['elements']        = array( 'post_title', 'excerpt', 'rating' );
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

		$class = 'sl-list posts companies';
		$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

		$html = "<{$args['container']} class='$class'>$html</{$args['container']}>";

		/**
		 * Add 'View more listings' links
		 */
		if ( $args['more_listings'] )
		{
			$link = get_post_type_archive_link( 'company' );

			// Fix ugly post type archive link
			if ( strpos( $link, '?' ) )
				$link = home_url( sl_setting( 'company_base_url' ) . '/' );

			// Temporarily turn off because 'location' is used for all custom post types
			//if ( $args['location'] )
			//	$link = get_term_link( $args['location'], 'location' );

			$html .= sprintf(
				'<a%s href="%s">%s</a>',
				'button' == $args['more_listings_style'] ? ' class="button"' : '',
				$link,
				$args['more_listings_text']
			);
		}

		return $html;
	}

	/**
	 * Get dropdown list for company menu item
	 *
	 * @param string $ul
	 *
	 * @return string
	 * @since  4.12.4
	 */
	function menu_dropdown( $ul )
	{
		if ( 'locations' == sl_setting( $this->post_type . '_menu_dropdown' ) )
		{
			// Get only locations of companies
			global $wpdb;
			$query     = "SELECT t.term_id FROM $wpdb->terms AS t
				INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
				INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
				INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
				WHERE p.post_type = 'company' AND tt.taxonomy = 'location'
				GROUP BY t.term_id";
			$locations = $wpdb->get_col( $query );

			$states = get_terms( 'location', array(
				'include' => $locations,
				'parent'  => 0, // Only get top level - states
			) );
			if ( ! is_array( $states ) || empty( $states ) )
				return $ul;

			// Get current state to add class 'active'
			$current_location = get_query_var( 'location' );
			if ( $current_location )
				$current_location = get_term_by( 'slug', $current_location, 'location' );

			$current_state = $current_location;
			if ( ! empty( $current_state ) && ! is_wp_error( $current_state ) )
			{
				while ( $current_state->parent )
				{
					$current_state = get_term( $current_state->parent, 'location' );
				}
			}
			else
			{
				$current_state = '';
			}

			$ul = '<ul class="dropdown-menu">';
			foreach ( $states as $term )
			{
				$class = ! empty( $current_state ) && $term->term_id == $current_state->term_id ? ' class="active"' : '';
				$ul .= "<li$class><a href='" . home_url( sl_setting( 'company_base_url' ) . '/area/' . $term->slug ) . "'>{$term->name}</a></li>";
			}
			$ul .= '</ul>';

			return $ul;
		}
		$terms = null;
		switch ( sl_setting( $this->post_type . '_menu_dropdown' ) )
		{
			case 'services':
				$tax = $this->post_type . '_service';
				break;
			case 'products':
				$tax = $this->post_type . '_product';
				break;
			case 'brand':
				$tax = 'brand';

				// Get only brands of companies
				global $wpdb;
				$query = "SELECT t.term_id FROM $wpdb->terms AS t
					INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
					INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id
					INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id
					WHERE p.post_type = 'company' AND tt.taxonomy = 'brand'
					GROUP BY t.term_id";
				$terms = $wpdb->get_col( $query );
				break;
			default:
				return $ul;
		}

		$terms = get_terms( $tax, array(
			'orderby' => 'count',
			'order'   => 'DESC',
			'include' => $terms,
		) );
		if ( ! is_array( $terms ) || empty( $terms ) )
			return $ul;

		$ul = '<ul class="dropdown-menu">';
		foreach ( $terms as $term )
		{
			$class = is_tax( $tax, $term ) ? ' class="active"' : '';
			$ul .= "<li$class><a href='" . get_term_link( $term, $tax ) . "'>{$term->name}</a></li>";
		}
		$ul .= '</ul>';

		return $ul;
	}

	/**
	 * Hide admin bar for company owners
	 *
	 * @param  bool $show
	 *
	 * @return bool
	 */
	function no_admin_bar( $show )
	{
		if ( ! current_user_can( 'publish_posts' ) )
			$show = false;

		return $show;
	}

	/**
	 * Change item type for schema.org for company
	 *
	 * @param $item_type
	 *
	 * @return string
	 */
	function schema_item_type( $item_type )
	{
		return 'LocalBusiness';
	}

	/**
	 * Add notifications for company owner
	 *
	 * @return void
	 */
	function add_notifications()
	{
		$user_id = get_current_user_id();
		$company = get_posts( array(
			'post_type'      => 'company',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => 'user',
			'meta_value'     => $user_id,
		) );

		if ( empty( $company ) )
			return;

		$company = current( $company );

		$edit_page    = get_permalink( sl_setting( "{$this->post_type}_page_edit" ) );
		$account_page = get_permalink( sl_setting( "{$this->post_type}_page_account" ) );

		// Company
		if ( empty( $company->post_title ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Company Name</a>', '7listings' ), $edit_page ), 'company_name' );
		if ( empty( $company->post_content ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Company Description</a>', '7listings' ), $edit_page ), 'company_description' );
		if ( ! get_post_meta( $company->ID, 'address', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Street Address</a>', '7listings' ), $edit_page ), 'address' );
		if ( ! get_post_meta( $company->ID, 'city', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">City</a>', '7listings' ), $edit_page ), 'city' );
		if ( ! get_post_meta( $company->ID, 'state', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">State</a>', '7listings' ), $edit_page ), 'state' );
		if ( ! get_post_meta( $company->ID, 'postcode', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Postcode</a>', '7listings' ), $edit_page ), 'postcode' );
		if ( ! get_post_meta( $company->ID, 'website', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Website</a>', '7listings' ), $edit_page ), 'website' );
		if ( ! get_post_meta( $company->ID, 'email', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Company Email</a>', '7listings' ), $edit_page ), 'email' );
		if ( ! get_post_meta( $company->ID, 'phone', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Office Phone Number</a>', '7listings' ), $edit_page ), 'phone' );


		if ( ! get_post_meta( get_the_ID(), 'open_247', true ) )
		{
			$days = array(
				'mon' => __( 'Monday', '7listings' ),
				'tue' => __( 'Tuesday', '7listings' ),
				'wed' => __( 'Wednesday', '7listings' ),
				'thu' => __( 'Thursday', '7listings' ),
				'fri' => __( 'Friday', '7listings' ),
				'sat' => __( 'Saturday', '7listings' ),
				'sun' => __( 'Sunday', '7listings' ),
			);

			$open = false;
			foreach ( $days as $k => $v )
			{
				if ( get_post_meta( $company->ID, "business_hours_$k", true ) )
				{
					$open = true;
					break;
				}
			}
			if ( ! $open )
				Sl_Notification::add( sprintf( __( 'You haven\'t set your <a href="%s">hours of operation</a> yet!', '7listings' ), $edit_page ), 'business_hours' );
		}

		// Account settings
		if ( ! get_user_meta( $user_id, 'membership', true ) )
			Sl_Notification::add( sprintf( __( 'Please choose: <a href="%s">Membership Type</a> for your account.', '7listings' ), $account_page ), 'membership' );
		if ( ! get_post_meta( $company->ID, 'invoice_name', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Name</a> of invoice recipient.', '7listings' ), $account_page ), 'invoice_name' );
		if ( ! get_post_meta( $company->ID, 'invoice_email', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Email</a> of invoice recipient.', '7listings' ), $account_page ), 'invoice_email' );
		if ( ! get_post_meta( $company->ID, 'invoice_phone', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">Phone Number</a> of invoice recipient.', '7listings' ), $account_page ), 'invoice_phone' );
		if ( ! get_post_meta( $company->ID, 'paypal_email', true ) )
			Sl_Notification::add( sprintf( __( 'Please enter: <a href="%s">PayPal Email</a> for invoices', '7listings' ), $account_page ), 'paypal_email' );

		// Allow child theme to add more notifications
		do_action( 'company_notifications', $company, $user_id );
	}

	/**
	 * Echo scripts in footer for homepage logos carousel
	 *
	 * @return void
	 */
	function home_script_footer()
	{
		?>
		<script>
			jQuery( window ).load( function ()
			{
				var $slider = $( '#company-logos' ).find( '.slider' ),
					params = $slider.data( 'params' ),
					speed = params.speed || 3000,
					transitionSpeed = params.transitionSpeed || 600;

				speed = parseInt( speed );
				transitionSpeed = parseInt( transitionSpeed );
				speed += transitionSpeed;

				function autoplay()
				{
					$slider.trigger( 'nextSlide' );
				}

				$slider.lemmonSlider( {
					slideToLast    : true,
					infinite       : true,
					transitionSpeed: transitionSpeed
				} );
				setInterval( autoplay, speed );
			} );
		</script>
	<?php
	}

	/**
	 * Change body classes for user admin pages
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function body_class( $classes )
	{
		$classes[] = 'user-admin';

		return $classes;
	}

	/**
	 * Add company membership type to post class
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function post_class( $classes )
	{
		if ( $this->post_type == get_post_type() )
		{
			$membership = get_user_meta( get_post_meta( get_the_ID(), 'user', true ), 'membership', true );
			if ( $membership )
				$classes[] = $membership;
		}

		return $classes;
	}

	/**
	 * Change default star rating to average rating for company
	 *
	 * @param string $output  Output
	 * @param string $element Element name, must be 'rating'
	 * @param array  $args    Argument
	 *
	 * @return string
	 */
	public static function average_rating( $output, $element, $args )
	{
		if ( 'rating' != $element || 'company' != $args['post_type'] )
			return $output;

		return Sl_Company_Helper::show_average_rating( get_the_ID(), false );
	}
}

new Sl_Company_Frontend( 'company' );
