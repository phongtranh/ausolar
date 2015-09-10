<?php

/**
 * This class will hold all things for post management page
 * last edit: 5.0
 *
 * @package    WordPress
 * @subpackage 7Listings
 */
class Sl_Post_Frontend
{
	/**
	 * @var string Post type
	 */
	public $post_type = 'post';

	/**
	 * Class constructor
	 *
	 * @return Sl_Post_Frontend
	 */
	public function __construct()
	{
		add_filter( 'template_redirect', array( $this, 'show_homepage_modules' ) );

		add_filter( 'template_include', array( $this, 'archive_template' ), 30 );

		add_filter( 'sl_sidebar_layout', array( $this, 'archive_sidebar_layout' ) );

		// Show hidden entry meta
		add_filter( 'sl_singular-post_entry_top', array( $this, 'hidden_entry_meta' ) );

		// Set meta title tag
		add_filter( 'sl_meta_title', array( $this, 'meta_title' ), 10, 2 );

		// Set meta title as heading in featured title area
		add_filter( 'sl_featured_title_title', array( $this, 'meta_title' ) );

		/**
		 * Output featured image before post content
		 *
		 * @since 5.1.5
		 */
		add_action( 'sl_singular-post_entry_top', array( __CLASS__, 'featured_image' ) );

		/**
		 * Output additional information after post content
		 * Each hook below has a priority, the lower priority the sooner info is displayed
		 * - Author details
		 * - Related posts
		 * - Single post navigation (next / prev)
		 * - Recent posts
		 * - Popular posts
		 *
		 * We use hooks with static methods that allows developer to remove them easily
		 *
		 * @since 5.1.5
		 */
		add_action( 'sl_singular-post_entry_bottom', array( __CLASS__, 'author_details' ), 10 );
		add_action( 'sl_singular-post_entry_bottom', array( __CLASS__, 'related_posts' ), 20 );
		add_action( 'sl_singular-post_entry_bottom', array( __CLASS__, 'single_nav' ), 30 );
		add_action( 'sl_singular-post_entry_bottom', array( __CLASS__, 'recent_posts' ), 40 );
		add_action( 'sl_singular-post_entry_bottom', array( __CLASS__, 'popular_posts' ), 50 );

		/**
		 * Handle things in featured title area
		 *
		 * @since 5.0.10
		 */
		new Sl_Post_Featured_Title( 'post' );
	}

	/**
	 * Set meta title tag
	 * Also used to set heading (title) in featured title area
	 *
	 * @param string $title
	 * @param string $sep
	 *
	 * @return string "Naked" meta title, e.g. no appending site title. That will be handled by action in /inc/frontend/header.php
	 */
	public function meta_title( $title = '', $sep = '' )
	{
		/**
		 * Filter meta title only for:
		 * - Single post
		 * - Category and tag pages
		 * - Blog page (but not our theme homepage)
		 */
		$is_blog = is_home() && ! ( is_front_page() && sl_setting( 'homepage_enable' ) );
		if (
			( ! is_singular( $this->post_type ) )
			&& ! $is_blog
			&& ! is_category()
			&& ! is_tag()
		)
		{
			return $title;
		}

		$replacement = array(
			'%SEP%'     => $sep,
			'%CITY%'    => sl_setting( 'general_city' ),
			'%STATE%'   => sl_setting( 'state' ),
			'%COUNTRY%' => sl_setting( 'country' ),
		);

		if ( $is_blog )
		{
			$title = sl_setting( $this->post_type . '_blog_title' );
		}
		elseif ( is_category() )
		{
			$title = sl_setting( $this->post_type . '_category_title' );
			$term  = get_queried_object();

			$replacement['%TERM%']            = $term->name;
			$replacement['%CAT_NAME%']        = $term->name;
			$replacement['%PARENT_CAT_NAME%'] = '';

			if ( $term->parent )
			{
				$parent                           = get_term( $term->parent, 'category' );
				$replacement['%PARENT_CAT_NAME%'] = $parent->name;
			}
		}
		elseif ( is_tag() )
		{
			$title = sl_setting( $this->post_type . '_tag_title' );
			$term  = get_queried_object();

			$replacement['%TERM%'] = $term->name;
		}
		elseif ( is_singular( $this->post_type ) )
		{
			$title                  = sl_setting( $this->post_type . '_single_title' );
			$replacement['%TITLE%'] = get_the_title();
		}

		$title = strtr( $title, $replacement );

		return $title;
	}

	/**
	 * Show homepage modules
	 *
	 * @return void
	 */
	public function show_homepage_modules()
	{
		if ( is_front_page() && sl_setting( 'homepage_enable' ) )
		{
			require THEME_TPL . $this->post_type . '/home-modules.php';
			add_action( 'sl_homepage_show_module', 'sl_homepage_show_post_modules', 10, 1 );
		}
	}

	/**
	 * Get correct archive template
	 *
	 * @param string $template
	 *
	 * @return string
	 */
	public function archive_template( $template )
	{
		if ( $this->post_type == sl_is_listing_archive() )
		{
			$templates = array();
			if ( is_paged() )
				$templates[] = 'templates/' . $this->post_type . '/archive-' . sl_setting( $this->post_type . '_archive_layout' ) . '-paged.php';
			$templates[] = 'templates/' . $this->post_type . '/archive-' . sl_setting( $this->post_type . '_archive_layout' ) . '.php';
			$templates[] = 'templates/' . $this->post_type . '/archive.php';

			$template = locate_template( $templates );
		}

		return $template;
	}

	/**
	 * Display slider
	 *
	 * @param  array $args
	 *
	 * @return string
	 */
	public static function slider( $args )
	{
		$args = array_merge( array(
			'title'      => '',
			'number'     => 5,
			'cat'        => '',
			'orderby'    => 'date',
			'date'       => 1,
			'transition' => 'fade',
			'delay'      => 0,
			'speed'      => 1000,

			'container'  => 'div', // Container tag
		), $args );

		$query_args = array();
		sl_build_query_args( $query_args, $args );
		if ( $args['cat'] )
			$query_args['cat'] = $args['cat'];


		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() )
			return '';

		$html = '';

		$args['class']      = 'slide';
		$args['image_size'] = 'sl_pano_medium';
		$args['elements']   = array( 'post_title', 'date', 'excerpt' );
		while ( $query->have_posts() )
		{
			$query->the_post();
			$html .= sl_post_list_single( $args );
		}

		wp_reset_postdata();
		wp_enqueue_script( 'jquery-cycle2' );

		return sprintf(
			'<%s class="sl-list posts tours cycle-slideshow" data-cycle-slides="> article" data-cycle-fx="%s" data-cycle-delay="%s" data-cycle-speed="%s">%s</%s>',
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
	public static function post_list( $args )
	{
		$args = array_merge( array(
			'title'               => '',
			'number'              => 4,
			'orderby'             => 'date',
			'cat'                 => array(),
			'date'                => 1,
			'display'             => 'list',
			'columns'             => 2,
			'more_listings'       => 1,
			'more_listings_text'  => __( 'See more posts', '7listings' ),
			'more_listings_style' => 'button',

			'container'           => 'aside', // Container tag
		), $args );

		$query_args = array();
		sl_build_query_args( $query_args, $args );

		if ( ! empty( $args['cat'] ) && ! in_array( - 1, (array) $args['cat'] ) )
			$query_args['cat'] = implode( ',', (array) $args['cat'] );

		$query = new WP_Query( $query_args );

		if ( ! $query->have_posts() )
			return '';

		$html = '';

		$args['elements'] = array( 'post_title', 'date', 'excerpt' );
		while ( $query->have_posts() )
		{
			$query->the_post();
			$html .= sl_post_list_single( $args );
		}

		wp_reset_postdata();

		$class = 'sl-list posts';
		$class .= 'grid' == $args['display'] ? ' columns-' . $args['columns'] : ' list';

		$html = "<{$args['container']} class='$class'>$html</{$args['container']}>";

		/**
		 * Add 'View more listings' links
		 * Link to term archive page and fallback to post type archive page
		 * If the archive page does not have more listing, then don't show this link
		 */
		if ( $args['more_listings'] )
		{
			$show = true;

			// Get blog page or home page URL depends on WordPress settings
			$link = HOME_URL;
			if ( 'page' == get_option( 'show_on_front' ) && ( $blog_page = get_option( 'page_for_posts' ) ) )
				$link = get_permalink( $blog_page );

			// If set 'cat' and only 1 category, get link to that category page
			if ( ! empty( $args['cat'] ) && ! in_array( - 1, (array) $args['cat'] ) )
			{
				$first_cat = (array) $args['cat'];
				$first_cat = current( $first_cat );
				$term      = get_term( $first_cat, 'category' );
				if ( ! is_wp_error( $term ) )
				{
					// Don't show view more listings if the term doesn't have more listings
					if ( $term->count <= $args['number'] )
						$show = false;

					$term_link = get_term_link( $term, 'category' );
					if ( ! is_wp_error( $term_link ) )
						$link = $term_link;
				}
			}

			if ( $show )
			{
				$html .= sprintf(
					'<a%s href="%s">%s</a>',
					'button' == $args['more_listings_style'] ? ' class="button"' : '',
					$link,
					$args['more_listings_text']
				);
			}
		}

		return $html;
	}

	/**
	 * Get sidebar layout for blog archive page
	 *
	 * @param  string $layout
	 *
	 * @return string
	 */
	public function archive_sidebar_layout( $layout )
	{
		if (
			$this->post_type == sl_is_listing_archive() &&
			( $setting = sl_setting( $this->post_type . '_archive_sidebar_layout' ) )
		)
		{
			$layout = $setting;
		}

		return $layout;
	}

	/**
	 * Display hidden entry meta for posts
	 * @return void
	 */
	public function hidden_entry_meta()
	{
		// Author
		$author_url = sl_setting( 'googleplus' );
		if ( ! $author_url )
			$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
		printf( '<span class="hidden" itemprop="author" itemscope itemtype="http://schema.org/Person">
				<a href="%s" itemprop="name" rel="author">%s</a>
				<span itemprop="url">%s</span>
			</span>',
			$author_url, get_bloginfo( 'name' ),
			$author_url
		);

		// Published date
		printf( '<time class="hidden" itemprop="datePublished">%s</time>', get_the_date() );
	}

	/**
	 * Display next/prev links navigation for single post
	 *
	 * @since 5.0.10
	 * @return void
	 */
	public static function single_nav()
	{
		if ( ! sl_setting( 'post_nextprev' ) || is_attachment() )
			return;

		// Don't print empty markup if there's nowhere to navigate.
		$previous = get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
		?>
		<nav class="sl-list posts next-prev">
			<h3 class="screen-reader-text"><?php _e( 'Post navigation', '7listings' ); ?></h3>
			<?php
			$args = array(
				'image_size'     => 'sl_thumb_tiny',
				'date'           => 1,
				'excerpt'        => sl_setting( 'post_related_excerpt' ),
				'excerpt_length' => sl_setting( 'post_related_excerpt_length' ),
				'elements'       => array( 'post_title', 'date', 'excerpt' ),
			);

			global $post;

			if ( $previous )
			{
				$post = $previous;
				setup_postdata( $post );
				$args['rel']    = 'prev';
				$args['before'] = '<span class="entry-meta meta-nav">' . __( 'Previous Post', '7listings' ) . '</span>';
				echo sl_post_list_single( $args );
			}

			if ( $next )
			{
				$post = $next;
				setup_postdata( $next );
				$args['rel']    = 'next';
				$args['before'] = '<span class="entry-meta meta-nav">' . __( 'Next Post', '7listings' ) . '</span>';
				echo sl_post_list_single( $args );
			}

			wp_reset_postdata();
			?>
		</nav>
	<?php
	}

	/**
	 * Display related posts
	 *
	 * @since 5.0.10
	 * @return void
	 */
	public static function related_posts()
	{
		if ( ! sl_setting( 'post_related' ) )
			return;

		$args = array(
			'category__in'   => wp_get_post_categories( get_the_ID() ),
			'posts_per_page' => 3,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$html = self::similar_posts( $args, 'image_size=sl_pano_medium' );

		if ( ! $html )
			return;
		?>
		<section id="related" class="sl-list posts related columns-3">
			<h3><?php _e( 'Related Posts', '7listings' ); ?></h3>

			<?php echo $html; ?>
		</section>
	<?php
	}

	/**
	 * Display recent posts
	 *
	 * @since 5.0.10
	 * @return void
	 */
	public static function recent_posts()
	{
		if ( ! sl_setting( 'post_recent' ) )
			return;

		$args = array(
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => array( get_the_ID() )
		);
		$html = self::similar_posts( $args );

		if ( ! $html )
			return;
		?>
		<section id="recent" class="sl-list posts recent">
			<h3><?php _e( 'Recent Posts', '7listings' ); ?></h3>
			<?php echo $html; ?>
		</section>
	<?php
	}

	/**
	 * Display popular posts
	 *
	 * @since 5.0.10
	 * @return void
	 */
	public static function popular_posts()
	{
		if ( ! sl_setting( 'post_popular' ) )
			return;

		$args = array(
			'posts_per_page' => 3,
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_key'       => 'views',
			'post__not_in'   => array( get_the_ID() )
		);
		$html = self::similar_posts( $args );

		if ( ! $html )
			return;
		?>
		<section id="popular" class="sl-list posts popular">
			<h3><?php _e( 'Popular Posts', '7listings' ); ?></h3>
			<?php echo $html; ?>
		</section>
	<?php
	}

	/**
	 * Helper function to display all kind of similar posts: popular, related, recent
	 *
	 * @see sl_post_list_single()
	 *
	 * @param array $args      Query arguments
	 * @param array $list_args Arguments to display each post, will be passed to `sl_post_list_single()` function
	 *
	 * @return string
	 */
	public static function similar_posts( $args = array(), $list_args = array() )
	{
		global $post;

		$html = '';

		$posts = get_posts( $args );

		if ( empty( $posts ) )
			return $html;

		$list_args = wp_parse_args( $list_args, array(
			'image_size'     => 'sl_thumb_tiny',
			'date'           => 1,
			'excerpt'        => sl_setting( 'post_related_excerpt' ),
			'excerpt_length' => sl_setting( 'post_related_excerpt_length' ),
			'elements'       => array( 'post_title', 'date', 'excerpt' ),
		) );
		foreach ( $posts as $post )
		{
			setup_postdata( $post );
			$html .= sl_post_list_single( $list_args );
		}

		wp_reset_postdata();

		return $html;
	}

	/**
	 * Output featured image before post content
	 * But don't output for all post formats
	 *
	 * @return void
	 */
	public static function featured_image()
	{
		if ( ! sl_setting( 'post_single_featured' ) || ! has_post_thumbnail() )
			return;

		$format = get_post_format();
		if ( $format && 'standard' != $format )
			return;

		echo '<div id="featured-post-image">';
		echo sl_listing_element( 'thumbnail', array(
			'image_size'     => sl_setting( 'post_single_image_size' ),
			'thumbnail_link' => false
		) );
		echo '</div>';
	}

	/**
	 * Output author details box after post content
	 *
	 * @since 5.1.5
	 * @return void
	 */
	public static function author_details()
	{
		if ( ! sl_setting( 'post_author_details' ) )
			return;
		?>
		<section id="author-details">
			<h3><?php _e( 'The Author', '7listings' ); ?></h3>

			<?php echo sl_avatar( get_the_author_meta( 'ID' ), 80 ); ?>

			<div class="details">
				<h4 class="entry-meta author name" id="author-name">
					<?php the_author(); ?>
				</h4>
				<?php do_action( 'sl_author_details' ); ?>
				<p class="entry-content bio" id="author-bio"><?php the_author_meta( 'description' ); ?></p>
			</div>
		</section>
	<?php
	}
}

new Sl_Post_Frontend;
